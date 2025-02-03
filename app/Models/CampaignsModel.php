<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class CampaignsModel {
    private $collection;

    public function __construct() {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->collection = $database->emailCampaigns;
    }

    public function insertCampaign($data) {
        try {
            // Insérer les données dans la collection
            $insertResult = $this->collection->insertOne($data);
    
            // Récupérer l'_id attribué par MongoDB
            $insertedId = $insertResult->getInsertedId();
    
            // Mettre à jour les données insérées avec la valeur _id
            $this->collection->updateOne(
                ['_id' => $insertedId],
                ['$set' => ['id' => (string) $insertedId]]
            );
            return true;
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while inserting campaign: ' . $ex->getMessage(), 500);
        }
    }

    public function updateCampaign($id, $data) {
        try {
            $this->collection->updateOne(['_id' => new \MongoDB\BSON\ObjectId($id)], ['$set' => $data]);
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while updating campaign: ' . $ex->getMessage(), 500);
        }
    }

    public function deleteCampaign($id) {
        try {
            $this->collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while deleting campaign: ' . $ex->getMessage(), 500);
        }
    }

    public function getCampaign($id) {
        try {
            return $this->collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching campaign: ' . $ex->getMessage(), 500);
        }
    }

    public function getAllCampaigns() {
        try {
            $options = [
                'sort' => ['created_at' => 1] // 1 pour tri ascendant, -1 pour tri descendant
            ];
    
            return $this->collection->find([], $options)->toArray();
        } catch(\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while fetching all campaigns: ' . $ex->getMessage(), 500);
        }
    }

    public function getCampaignsByDomain($domainId = null) {
        if ($domainId === null) {
            $activeDomain = get_active_domain();
            $domainId = $activeDomain ? $activeDomain['id'] : null;
        }

        if ($domainId) {
            return $this->collection->find(['domain_id' => $domainId])->toArray();
        }

        return $this->collection->find()->toArray();
    }

    function getTemplatesWithContent() {
        $templatesDirectory = WRITEPATH . 'uploads/templates';
        $templates = [];
    
        if (is_dir($templatesDirectory)) {
            $files = scandir($templatesDirectory);
    
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
                    $filePath = $templatesDirectory . DIRECTORY_SEPARATOR . $file;
                    $fileContent = file_get_contents($filePath);
                    
                    $templates[] = [
                        'title' => $file,
                        'content' => $fileContent
                    ];
                }
            }
        }
        
        return $templates;
    }
}
