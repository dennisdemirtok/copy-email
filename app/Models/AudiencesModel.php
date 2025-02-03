<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class AudiencesModel
{
    private $collection;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->collection = $database->audiences;
    }

    public function insertAudience($data) {
        try {
            $this->collection->insertOne($data);
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while inserting audience: ' . $ex->getMessage(), 500);
        }
    }
    
    public function updateAudience($id, $data) {
        try {
            $this->collection->updateOne(['_id' => new \MongoDB\BSON\ObjectId($id)], ['$set' => $data]);
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while updating audience: ' . $ex->getMessage(), 500);
        }
    }
    
    public function deleteAudience($id) {
        try {
            $this->collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting audience: ' . $ex->getMessage(), 500);
        }
    }
    
    public function getAudience($id) {
        try {
            return $this->collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching audience: ' . $ex->getMessage(), 500);
        }
    }

    public function getAudienceContacts($id) {
        try {
            // Requête pour obtenir les contacts associés à une audience spécifique
            $pipeline = [
                [
                    '$match' => [
                        '_id' => new \MongoDB\BSON\ObjectId($id)
                    ]
                ],
                [
                    '$lookup' => [
                        'from' => 'contacts', // Nom de la collection des contacts
                        'localField' => 'contacts',
                        'foreignField' => '_id',
                        'as' => 'audienceContacts'
                    ]
                ],
                [
                    '$unwind' => '$audienceContacts'
                ],
                [
                    '$replaceRoot' => ['newRoot' => '$audienceContacts']
                ]
            ];
    
            $cursor = $this->collection->aggregate($pipeline);
    
            // Stockage des contacts de l'audience spécifiée
            $audienceContacts = [];
            foreach ($cursor as $document) {
                $audienceContacts[] = $document;
            }
    
            return $audienceContacts;
        } catch (\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching audience contacts: ' . $ex->getMessage(), 500);
        }
    }
    
    public function getAllAudiences() {
        try {
            $activeDomain = get_active_domain();
            $domainId = $activeDomain ? $activeDomain['id'] : null;

            $pipeline = [
                [
                    '$match' => [
                        'domain_id' => $domainId
                    ]
                ],
                [
                    '$project' => [
                        'name' => 1,
                        'contactsCount' => ['$size' => '$contacts']
                    ]
                ]
            ];
    
            $cursor = $this->collection->aggregate($pipeline);
    
            $allAudiences = [];
            foreach ($cursor as $document) {
                $audienceData = [
                    '_id' => $document['_id'],
                    'name' => $document['name'],
                    'contactsCount' => $document['contactsCount']
                ];
                $allAudiences[] = $audienceData;
            }
            
            return $allAudiences;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching all audiences: ' . $ex->getMessage(), 500);
        }
    }

    public function getUniqueContactsFromAudiences($audienceIds) {
        try {
            $uniqueContacts = [];
    
            foreach ($audienceIds as $id) {
                // Obtention des contacts pour chaque audience
                $audienceContacts = $this->getAudienceContacts($id);
    
                // Itération à travers les contacts et ajout des contacts uniques
                foreach ($audienceContacts as $contact) {
                    $contactId = (string)$contact['_id'];
                    // Vérification et ajout des contacts uniques
                    if (!isset($uniqueContacts[$contactId]) && $contact->subscribed == true) {
                        $uniqueContacts[$contactId] = $contact;
                    }
                }
            }
    
            return array_values($uniqueContacts); // Retourne uniquement les valeurs pour obtenir la liste des contacts uniques
        } catch (\Exception $ex) {
            // Gérer l'erreur ici
        }
    }
}