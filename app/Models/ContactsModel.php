<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class ContactsModel {
    private $collection;

    function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->collection = $database->contacts;
    }

    public function insertContact($data) : string {
        try {
            $insertResult = $this->collection->insertOne($data);
            $id = $insertResult->getInsertedId();
            return $id;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while inserting contact: ' . $ex->getMessage(), 500);
        }
    }

    public function insertManyContacts($data) : array {
        try {
            $insertResult = $this->collection->insertMany($data);
            $ids = $insertResult->getInsertedIds();
            return $ids;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while inserting contact: ' . $ex->getMessage(), 500);
        }
    }

    public function updateManyContacts($operations) {
        try {
            // Préparation des paramètres de l'opération bulk
            $bulk = new \MongoDB\Driver\BulkWrite;
            foreach ($operations as $op) {
                $bulk->update(
                    $op['filter'],
                    $op['update'],
                    ['multi' => true, 'upsert' => false]
                );
            }

            // Exécution des opérations de mise à jour en masse
            $result = $this->collection->getManager()->executeBulkWrite($this->collection->getNamespace(), $bulk);
            return $result->getModifiedCount();  // Retourne le nombre de documents modifiés
        } catch (\MongoDB\Driver\Exception\Exception $ex) {
            show_error('Error while updating multiple contacts: ' . $ex->getMessage(), 500);
        }
    }
    
    public function updateContact($id, $data) {
        try {
            $this->collection->updateOne(['_id' => new \MongoDB\BSON\ObjectId($id)], ['$set' => $data]);
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while updating contact: ' . $ex->getMessage(), 500);
        }
    }
    
    public function deleteContact($id) {
        try {
            $this->collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting contact: ' . $ex->getMessage(), 500);
        }
    }
    
    public function getContact($id) {
        try {
            return $this->collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching contact: ' . $ex->getMessage(), 500);
        }
    }

    public function getContactByEmail($email)
    {
        try {
            return $this->collection->findOne(['email' => $email]);
        } catch (\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching contact by email: ' . $ex->getMessage(), 500);
        }
    }

    
    public function getAllContacts() {
        try {
            return $this->collection->find()->toArray();
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching all contacts: ' . $ex->getMessage(), 500);
        }
    }

    public function countSubscribedContacts() {
        try {
            return $this->collection->count(['subscribed' => true]);
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while counting subscribed contacts: ' . $ex->getMessage(), 500);
        }
    }
}