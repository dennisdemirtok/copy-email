<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;
use App\Models\CampaignsModel;

class EmailEventsModel {
    private $collection;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->collection = $database->emailEvents;
    }

    function getEmailEvents($limit = 50) {
        try {
            // Ajout d'un filtre pour ne sélectionner que les documents
            // ayant un header avec le name 'campaign_id'
            $filter = [
                'data.data.headers' => [
                    '$elemMatch' => [
                        'name' => 'Campaign_id'
                    ]
                ]
            ];
            $options = [
                'limit' => $limit, 
                'sort' => ['data.created_at' => -1]
            ];

            $cursor = $this->collection->find($filter, $options);
            $emailEvents = $cursor->toArray();

            return $emailEvents;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching emailEvents: ' . $ex->getMessage(), 500);
        }
    }

    function getEmailEvent($id) {
        try {
            $emailEvent = $this->collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            return $emailEvent;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching emailEvent with ID: ' . $id . $ex->getMessage(), 500);
        }
    }

    function getUniqueContactsSum(){
        try {
            $pipeline = [
                ['$group' => ['_id' => null, 'totalContacts' => ['$addToSet' => '$data.data.to']]],
                ['$unwind' => '$totalContacts'],
                ['$group' => ['_id' => null, 'totalContacts' => ['$sum' => 1]]],
                ['$project' => ['_id' => 0, 'totalContacts' => 1]]
            ];

            $cursor = $this->collection->aggregate($pipeline);

            $uniqueContactsSum = 0;
            foreach ($cursor as $document) {
                $uniqueContactsSum = $document['totalContacts'];
            }
            
            return $uniqueContactsSum;
        } catch (\MongoDB\Exception\RuntimeException $ex){
            show_error('Error while fetching eunique contacts sum', 500);
        }
    }

    function getTotalPerEventType(){
        try {
            $pipeline = [
                [
                    '$match' => [
                        'data.type' => ['$in' => ['email.delivered', 'email.opened', 'email.clicked']],
                    ]
                ],
                [
                    '$unwind' => '$data.data.headers'
                ],
                [
                    '$match' => [
                        'data.data.headers.name' => 'Campaign_id'
                    ]
                ],
                [
                    '$group' => [
                        '_id' => [
                            'type' => '$data.type',
                        ],
                        'count' => ['$sum' => 1]
                    ]
                ],
                [
                    '$project' => [
                        '_id' => 0,
                        'eventType' => '$_id.type',
                        'count' => 1
                    ]
                ]
            ];
            
            // Exécution de la requête d'agrégation
            $cursor = $this->collection->aggregate($pipeline);
            $totalPerEventType = iterator_to_array($cursor);
            
            return $totalPerEventType;
        } catch (\MongoDB\Exception\RuntimeException $ex){
            show_error('Error while fetching eunique contacts sum', 500);
        }
    }

    function getAnalytics(){
        try {
            $pipeline = [
                [
                    '$lookup' => [
                        'from' => 'emailCampaigns',
                        'localField' => 'data.data.tags.campaign_id',
                        'foreignField' => 'id',
                        'as' => 'campaignData'
                    ]
                ],
                [
                    '$unwind' => '$campaignData'
                ],
                [
                    '$group' => [
                        '_id' => '$campaignData.id',
                        'campaignName' => ['$first' => '$campaignData.name'],
                        'sent_at' => ['$first' => '$campaignData.sent_at'],
                        'emails' => ['$addToSet' => '$data.data.to'],
                        'deliveredEmails' => [
                            '$addToSet' => [
                                '$cond' => [
                                    ['$eq' => ['$data.type', 'email.delivered']], 
                                    '$data.data.email_id', 
                                    '$$REMOVE'
                                ]
                            ]
                        ],
                        'openedEmails' => [
                            '$addToSet' => [
                                '$cond' => [
                                    ['$eq' => ['$data.type', 'email.opened']], 
                                    '$data.data.email_id', 
                                    '$$REMOVE'
                                ]
                            ]
                        ],
                        'clickedEmails' => [
                            '$addToSet' => [
                                '$cond' => [
                                    ['$eq' => ['$data.type', 'email.clicked']], 
                                    '$data.data.email_id', 
                                    '$$REMOVE'
                                ]
                            ]
                        ],
                    ]
                ],
                [
                    '$match' => [
                        'emails' => ['$exists' => true, '$ne' => []]
                    ]
                ],
                [
                    '$sort' => [
                        'sent_at' => -1
                    ]
                ],
                [
                    '$group' => [
                        '_id' => 'null',
                        'campaigns' => ['$push' => '$$ROOT'],
                        'totalEmails' => ['$sum' => ['$size' => '$emails']]
                    ]
                ],
                [
                    '$unwind' => '$campaigns'
                ],
                [
                    '$project' => [
                        '_id' => '$campaigns._id',
                        'campaignId' => '$campaigns._id',
                        'campaignName' => '$campaigns.campaignName',
                        'sent_at' => '$campaigns.sent_at',
                        'totalEmails' => ['$size' => '$campaigns.emails'],
                        'deliveryRate' => [
                            '$cond' => [
                                ['$gt' => ['$campaigns.deliveredEmails', 0]],
                                ['$multiply' => [['$divide' => ['$campaigns.deliveredEmails', ['$size' => '$campaigns.emails']]], 100]],
                                0
                            ]
                        ],
                        'openRate' => [
                            '$cond' => [
                                ['$gt' => [['$size' => '$campaigns.openedEmails'], 0]],
                                ['$multiply' => [['$divide' => [['$size' => '$campaigns.openedEmails'], ['$size' => '$campaigns.emails']]], 100]],
                                0
                            ]
                        ],
                        'clickRate' => [
                            '$cond' => [
                                ['$gt' => [['$size' => '$campaigns.clickedEmails'], 0]],
                                ['$multiply' => [['$divide' => [['$size' => '$campaigns.clickedEmails'], ['$size' => '$campaigns.emails']]], 100]],
                                0
                            ]
                        ]
                    ]
                ]
            ];
            
            // Exécution de la requête d'agrégation
            $cursor = $this->collection->aggregate($pipeline);
            $totalPerCampaign = iterator_to_array($cursor);
            
            return $totalPerCampaign;
        } catch (\MongoDB\Exception\RuntimeException $ex){
            show_error('Error while fetching eunique contacts sum', 500);
        }
    }

    public function getEventsGroupedByUniqueMail()
    {
        $cursor = $this->collection->aggregate([
            ['$sort' => ['data.created_at' => 1]], // Tri ascendant par date de création
            ['$unwind' => '$data.data.to'],
            ['$group' => [
                '_id' => '$data.data.to',
                'events' => ['$push' => '$$ROOT']
            ]]
        ]);
    
        return $cursor->toArray();
    }

    public function insertResendEvents($resendEvents, $campaignId) {
        try {
            $campaign = (new CampaignsModel())->getCampaign($campaignId);
            if (!$campaign) {
                throw new \Exception("Campaign not found with ID: $campaignId");
            }

            $insertedCount = 0;
            $skippedCount = 0;
            $errors = [];

            // Préparer un tableau des IDs et types d'événements à vérifier
            $eventsToCheck = [];
            foreach ($resendEvents as $event) {
                $eventType = 'email.' . $event['last_event'];
                $eventsToCheck[] = [
                    'data.data.email_id' => $event['id'],
                    'data.type' => $eventType
                ];
            }

            // Vérifier tous les événements existants en une seule requête
            $existingEvents = [];
            if (!empty($eventsToCheck)) {
                $cursor = $this->collection->find(['$or' => $eventsToCheck]);
                foreach ($cursor as $doc) {
                    $key = $doc['data']['data']['email_id'] . '_' . $doc['data']['type'];
                    $existingEvents[$key] = true;
                }
            }

            // Traiter chaque événement
            foreach ($resendEvents as $event) {
                $eventType = 'email.' . $event['last_event'];
                $key = $event['id'] . '_' . $eventType;

                if (isset($existingEvents[$key])) {
                    $skippedCount++;
                    continue;
                }

                try {
                    $mongoEvent = $this->convertResendToMongoFormat($event, $campaignId);
                    $this->collection->insertOne($mongoEvent);
                    $insertedCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'email_id' => $event['id'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            return [
                'success' => true,
                'inserted_count' => $insertedCount,
                'skipped_count' => $skippedCount,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function getExistingEvent($emailId, $eventType) {
        try {
            $result = $this->collection->findOne([
                'data.data.email_id' => $emailId,
                'data.type' => $eventType
            ]);

            if ($result) {
                return [
                    'exists' => true,
                    'type' => $result['data']['type'],
                    'created_at' => $result['data']['created_at']
                ];
            }

            return null;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            throw new \Exception('Error while checking email event: ' . $ex->getMessage());
        }
    }

    private function convertResendToMongoFormat($resendEmail, $campaignId) {
        return [
            'data' => [
                'created_at' => date('c'),
                'data' => [
                    'created_at' => $resendEmail['created_at'],
                    'email_id' => $resendEmail['id'],
                    'from' => 'Flattered <notifications@flattered.com>',
                    'headers' => [
                        [
                            'name' => 'Campaign_id',
                            'value' => $campaignId
                        ]
                    ],
                    'subject' => $resendEmail['subject'],
                    'to' => array_map(function($to) {
                        return "Dennis Demirtok <$to>";
                    }, $resendEmail['to']),
                    'tags' => [
                        [
                            'campaign_id' => $campaignId
                        ]
                    ]
                ],
                'type' => 'email.' . ($resendEmail['last_event'])
            ]
        ];
    }
}