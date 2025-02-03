<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class AnalyticsModel{
    private $collection;
    private $database;

    function __construct(){
        $connection = new DatabaseConnector();
        $this->database = $connection->getDatabase();
        $this->collection = $this->database->analytics;
    }

    function insertAnalytics(){
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
                        'subject' => ['$first' => '$campaignData.subject'],
                        'templateHTML' => ['$first' => '$campaignData.templateHTML'],
                        'domain_id' => ['$first' => '$campaignData.domain_id'],
                        'templatePlainText' => ['$first' => '$campaignData.templatePlainText'],
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
                        'subject' => '$campaigns.subject',
                        'templateHTML' => '$campaigns.templateHTML',
                        'templatePlainText' => '$campaigns.templatePlainText',
                        'domain_id' => '$campaigns.domain_id',
                        'sent_at' => '$campaigns.sent_at',
                        'totalEmails' => ['$size' => '$campaigns.emails'],
                        'deliveryRate' => [
                            '$cond' => [
                                ['$gt' => [['$size' => '$campaigns.deliveredEmails'], 0]],
                                ['$multiply' => [['$divide' => [['$size' => '$campaigns.deliveredEmails'], ['$size' => '$campaigns.emails']]], 100]],
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
            $cursor = $this->database->emailEvents->aggregate($pipeline);
            $totalPerCampaign['data'] = iterator_to_array($cursor);
            $totalPerCampaign['generated_at'] = new \MongoDB\BSON\UTCDateTime();

            $this->collection->deleteMany([]);

            $this->collection->insertOne($totalPerCampaign);

        } catch (\MongoDB\Exception\RuntimeException $ex){
            show_error('Error while fetching eunique contacts sum', 500);
        }
    }

    function getAnalyticsByDomain($domainId = null){
        if ($domainId === null) {
            $activeDomain = get_active_domain();
            $domainId = $activeDomain ? $activeDomain['id'] : null;
        }

        try {
            $initialData = $this->collection->find()->toArray()[0];
            $campaigns = $initialData['data']->getArrayCopy();
            $filteredData = array_filter($campaigns, function($campaign) use ($domainId) {
                return $campaign['domain_id'] == $domainId;
            });        
            $result = [
                'generated_at' => $initialData['generated_at'],
                'data' => array_values($filteredData) // Réindexer les valeurs pour plus de clarté
            ];
            return $result;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching all campaigns: ' . $ex->getMessage(), 500);
        }
    }

}