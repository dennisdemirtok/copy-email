<?php

namespace App\Models;

use App\Libraries\DatabaseConnector;

class DomainsModel
{
    public $collection;

    public function __construct()
    {
        $connection = new DatabaseConnector();
        $database = $connection->getDatabase();
        $this->collection = $database->domains;
    }

    public function getActiveDomains()
    {
        helper('resend');
        $response = resend_api_request('/domains');
        if ($response && isset($response['data'])) {
            return array_filter($response['data'], function($domain) {
                return $domain['status'] !== 'failure' || $domain['status'] !== 'not_started';
            });
            // Enrichir avec les données MongoDB
            return array_map(function($domain) {
                $mongoData = $this->collection->findOne(['domain_id' => $domain['id']]);
                if ($mongoData) {
                    return array_merge((array)$mongoData, $domain);
                }
                return $domain;
            }, $resendDomains);
        }
        
        return [];
    }

    public function getAllDomains()
    {
        helper('resend');
        $response = resend_api_request('/domains');
        
        if ($response && isset($response['data'])) {
            return array_map(function($domain) {
                return [
                    'domain_id' => $domain['id'],
                    'domain_name' => $domain['name'],
                    'status' => $domain['status'],
                    'created_at' => $domain['created_at'],
                    'region' => $domain['region'] ?? null,
                    'dns_provider' => $domain['dns_provider'] ?? null
                ];
            }, $response['data']);
        }
        
        return [];
    }

    public function findAll()
    {
        $cursor = $this->collection->find([]);
        $domains = [];
        
        foreach ($cursor as $document) {
            $domains[] = (array)$document;
        }
        
        return $domains;
    }

    public function find($id)
    {
        helper('resend');
        $response = resend_api_request('/domains/' . $id);
        if ($response && isset($response['data'])) {
            $mongoData = $this->collection->findOne(['domain_id' => $id]);
            if ($mongoData) {
                return array_merge((array)$mongoData, $response['data']);
            }
            return $response['data'];
        }
        return null;
    }

    public function verify($id)
    {
        helper('resend');
        $response = resend_api_request('/domains/' . $id . '/verify', 'POST');
        if ($response) {
            $mongoData = $this->collection->findOne(['domain_id' => $id]);
            if ($mongoData) {
                return array_merge((array)$mongoData, $response);
            }
            return $response;
        }
        return null;
    }
}
