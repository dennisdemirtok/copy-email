<?php

use App\Models\DomainsModel;

if (!function_exists('get_active_domain')) {
    function get_active_domain()
    {
        $request = \Config\Services::request();
        $response = \Config\Services::response();
        
        // Essayer de récupérer l'ID du domaine depuis le cookie
        $activeDomainId = get_cookie('active_domain_id');
        
        if (!$activeDomainId) {
            // Si pas de domaine actif, on prend le premier domaine vérifié
            $domainsModel = new \App\Models\DomainsModel();
            $domains = $domainsModel->getActiveDomains();
            if (!empty($domains)) {
                set_active_domain($domains[0]['id']);
                return $domains[0];
            }
            return null;
        }
        
        if ($activeDomainId) {
            $domainsModel = new \App\Models\DomainsModel();
            $domain = $domainsModel->verify($activeDomainId);
            if ($domain) {
                return $domain;
            }
        }
        
        return null;
    }
}

if (!function_exists('set_active_domain')) {
    function set_active_domain($domainId)
    {
        // Définir le cookie pour 24 heures (86400 secondes)
        $cookie = [
            'name'     => 'active_domain_id',
            'value'    => $domainId,
            'expire'   => 86400,
            'path'     => '/',
            'secure'   => false,
            'httponly' => false
        ];
        
        set_cookie($cookie);
    }
}

if (!function_exists('get_all_active_domains')) {
    function get_all_active_domains()
    {
        $domainsModel = new \App\Models\DomainsModel();
        return $domainsModel->getActiveDomains();
    }
}
