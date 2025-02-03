<?php

if (!function_exists('resend_api_request')) {
    function resend_api_request($endpoint, $method = 'GET', $data = null) {
        $api_key = getenv('RESEND_API_KEY');
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, "https://api.resend.com" . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ]);
        
        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($http_code >= 200 && $http_code < 300) {
            return json_decode($response, true);
        }
        
        log_message('error', 'Resend API Error: ' . $response);
        return false;
    }
}
