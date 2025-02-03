<?php

namespace App\Controllers;

use App\Models\EmailEventsModel;
use App\Models\AudiencesModel;
use App\Models\CampaignsModel;
use App\Models\AnalyticsModel;
use App\Models\DomainsModel;

class CampaignsController extends BaseController
{
    protected $domainsModel;
    protected $campaignsModel;
    protected $audiencesModel;

    public function __construct()
    {
        $this->domainsModel = new DomainsModel();
        $this->campaignsModel = new CampaignsModel();
        $this->audiencesModel = new AudiencesModel();
        helper('domain');
    }

    public function index(){
        $emailEventsModel = model(EmailEventsModel::class);
        $analyticsModel = model(AnalyticsModel::class);

        $data = [
            'totalPerCampaign' => $analyticsModel->getAnalyticsByDomain(),
            'allCampaigns' => $this->campaignsModel->getCampaignsByDomain()
        ];

        echo view('Templates/header', ['currentPage' => 'campaigns']);
        echo view('Campaigns/index', $data);
        echo view('Templates/footer');
    }

    public function create(){
        if ($this->request->getMethod() === 'post') {
            $activeDomain = get_active_domain();
            if (!$activeDomain) {
                return redirect()->back()->with('error', 'Please select an active domain first');
            }

            $campaignName = $this->request->getPost('campaign_name');
            $subject = $this->request->getPost('subject');
            $templateHTML = $this->request->getPost('contentHTML');
            $templatePlainText = $this->request->getPost('contentPlainText');
            $audiences = $this->request->getPost('audiences');

            // Ajouter les paramètres UTM aux liens dans le HTML
            $utmParams = [
                'utm_source' => 'newsletter',
                'utm_medium' => 'email',
                'utm_campaign' => $campaignName
            ];
            $templateHTML = $this->addUTMParametersToLinks($templateHTML, $utmParams);

            $data = [
                'name' => $campaignName,
                'subject' => $subject,
                'status' => 'unsent',
                'templateHTML' => $templateHTML,
                'templatePlainText' => $templatePlainText,
                'templateTitle' => $subject,
                'audiences' => $audiences,
                'domain_id' => $activeDomain['id'],
                'created_at' => new \MongoDB\BSON\UTCDateTime()
            ];

            if ($this->campaignsModel->insert($data)) {
                return redirect()->to('/campaigns')->with('success', 'Campaign created successfully');
            }
            return redirect()->back()->withInput()->with('error', 'Error creating campaign');
        }

        $data = [
            'domains' => $this->domainsModel->getActiveDomains(),
            'audiences' => $this->audiencesModel->getAllAudiences(),
            'templates' => $this->campaignsModel->getTemplatesWithContent()
        ];

        echo view('Templates/header', ['currentPage' => 'campaigns']);
        echo view('Campaigns/create', $data);
        echo view('Templates/footer');
    }

    public function reloadAnalytics(){
        $analyticsModel = model(AnalyticsModel::class);
        $analyticsModel->insertAnalytics();
        session()->setFlashdata('success', 'Data reloaded successfully');
        return redirect()->route('campaigns');
    }

    public function edit($campaignId) {
        $data = [
            'audiences' => $this->audiencesModel->getAllAudiences(),
            'campaign' => $this->campaignsModel->getCampaign($campaignId)
        ];

        echo view('Templates/header', ['currentPage' => 'campaigns']);
        echo view('Campaigns/edit', $data);
        echo view('Templates/footer');
    }

    public function delete($id) {
        $model = model(CampaignsModel::class);
    
        $model->deleteCampaign($id);
    
        return redirect()->route('campaigns');
    }

    public function store()
    {
        if ($this->request->getMethod() === 'post') {
            $activeDomain = get_active_domain();
            if (!$activeDomain) {
                return redirect()->back()->with('error', 'Please select an active domain first');
            }

            $campaignName = $this->request->getPost('campaign_name');
            $subject = $this->request->getPost('subject');
            $templateHTML = $this->request->getPost('contentHTML');
            $templatePlainText = $this->request->getPost('contentPlainText');
            $audiences = $this->request->getPost('audiences');

            // Ajouter les paramètres UTM aux liens dans le HTML
            $utmParams = [
                'utm_source' => 'newsletter',
                'utm_medium' => 'email',
                'utm_campaign' => $campaignName
            ];
            $templateHTML = $this->addUTMParametersToLinks($templateHTML, $utmParams);

            $data = [
                'name' => $campaignName,
                'subject' => $subject,
                'status' => 'unsent',
                'templateHTML' => $templateHTML,
                'templatePlainText' => $templatePlainText,
                'templateTitle' => $subject,
                'audiences' => $audiences,
                'domain_id' => $activeDomain['id'],
                'created_at' => new \MongoDB\BSON\UTCDateTime()
            ];

            error_log('tioto');
            if ($this->campaignsModel->insertCampaign($data)) {
                session()->setFlashdata('success', 'Campaign created successfully');
                return redirect()->to('/campaigns');
            }
            return redirect()->back()->withInput()->with('error', 'Error creating campaign');
        }
    }

    public function update()
    {
        if ($this->request->getMethod() === 'post') {
            $campaignId = $this->request->getPost('id');
            $campaignName = $this->request->getPost('campaign_name');
            $subject = $this->request->getPost('subject');
            $templateHTML = $this->request->getPost('contentHTML');
            $templatePlainText = $this->request->getPost('contentPlainText');
            $audiences = $this->request->getPost('audiences');

            // Ajouter les paramètres UTM aux liens dans le HTML
            $utmParams = [
                'utm_source' => 'newsletter',
                'utm_medium' => 'email',
                'utm_campaign' => $campaignName
            ];
            $templateHTML = $this->addUTMParametersToLinks($templateHTML, $utmParams);

            $data = [
                'name' => $campaignName,
                'subject' => $subject,
                'templateTitle' => $subject,
                'templateHTML' => $templateHTML,
                'templatePlainText' => $templatePlainText,
                'audiences' => $audiences
            ];

            if ($this->campaignsModel->updateCampaign($campaignId, $data)) {
                return redirect()->to('/campaigns')->with('success', 'Campaign updated successfully');
            }
            return redirect()->back()->withInput()->with('error', 'Error updating campaign');
        }
    }

    public function sendWithGoogleCloudFunction($campaignId) {
        syslog(LOG_INFO,'Request to Cloud Function in preparation');
        set_time_limit(3000);
        $campaignsModel = model(CampaignsModel::class);
        $audienceModel = model(AudiencesModel::class);
    
        $campaign = $campaignsModel->getCampaign($campaignId);
        $uniqueContacts = $audienceModel->getUniqueContactsFromAudiences($campaign->audiences);
        
        $api_url = getenv('CLOUD_FUNCTION_RESEND_QUEUE');     
        
        $start_time = microtime(true); 

        $activeDomain = get_active_domain();
        if (!$activeDomain) {
            throw new \RuntimeException('No active domain configured');
        }

        // Check if domain is properly configured
        if ($activeDomain['pretty_name'] === 'N/A' || $activeDomain['sender_email'] === 'N/A') {
            throw new \RuntimeException('Domain not properly configured. Please set a pretty name and sender email in domain settings.');
        }

        $postData = [
            "contacts" => $uniqueContacts,
            "from" => " ". $activeDomain['pretty_name'] ." <".$activeDomain['sender_email'].">",
            "subject" => $campaign->subject,
            "html" => $campaign->templateHTML,
            "text" => $campaign->templatePlainText,
            "campaign_id" => (string)$campaign['_id'],
            "unsubscribeText" => '<a href="'. base_url('/unsubscribe/') .'{% id %}" style="color:#000; font-style:normal; font-weight:normal; text-decoration:underline">Unsubscribe</a>'
        ];

        try {
            $updatedData = [
                'status' => 'sent',
                'sent_at' => new \MongoDB\BSON\UTCDateTime()
            ];
            $campaignsModel->updateCampaign($campaignId, $updatedData);
        } catch (\RuntimeException $e) {
            $errorMessage = $e->getMessage();
            $updatedData = [
                'status' => 'failed',
                'error' => $errorMessage
            ];
            $campaignsModel->updateCampaign($campaignId, $updatedData);
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMessage
            ]);
        } catch (\MongoDB\Exception\RuntimeException $ex) {
            show_error('Error while updating campaign: ' . $ex->getMessage(), 500);
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer Uei0gpKfq7BpIRZuGaSLTlSgW12EiFcSYEjxHcXVvfPdJQdhBE3hES7LYMMVb7Bi'
        ];
        $options = ['headers' => $headers];

        $logMessage = sprintf(
            "Send Request POST to %s with headers: %s and body: %s",
            $api_url,
            json_encode($headers),
            json_encode($postData)
        );

        $responses = \WpOrg\Requests\Requests::post($api_url, $headers, json_encode($postData), $options);

                // Log de la requête
        syslog(LOG_INFO, $logMessage);

        // Préparation du log de la réponse
        $responseLog = sprintf(
            "Responses %d : %s",
            $responses->status_code,
            $responses->body // Log seulement les 200 premiers caractères pour éviter un log trop volumineux
        );

        // Log de la réponse
        syslog(LOG_INFO, $responseLog);



        $end_time = microtime(true); 
        $execution_time = ($end_time - $start_time);  
        echo "<p> Execution time of script = ".number_format($execution_time,2)." sec </p>"; 
        echo "<p> You can close this modal window (click anywhere or on the cross at the top right)</p>"; 
        set_time_limit(30);
    }

    public function showSync()
    {
        $data = [
            'campaigns' => $this->campaignsModel->getCampaignsByDomain()
        ];

        echo view('Templates/header', ['currentPage' => 'campaigns']);
        echo view('Campaigns/sync', $data);
        echo view('Templates/footer');
    }

    public function syncEvents()
    {
        // Augmenter le temps d'exécution maximum à 5 minutes
        ini_set('max_execution_time', 300);
        set_time_limit(300);

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/campaigns/sync');
        }

        $campaignId = $this->request->getPost('campaignId');
        $resendEventsJson = $this->request->getPost('resendEvents');

        try {
            $resendEvents = json_decode($resendEventsJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
            }

            $emailEventsModel = model(EmailEventsModel::class);
            
            // Process events in batches of 100
            $chunks = array_chunk($resendEvents, 100);
            $totalResult = [
                'success' => true,
                'inserted_count' => 0,
                'skipped_count' => 0,
                'errors' => []
            ];

            foreach ($chunks as $chunk) {
                $result = $emailEventsModel->insertResendEvents($chunk, $campaignId);
                
                if (!$result['success']) {
                    session()->setFlashdata('error', $result['message']);
                    return redirect()->to('/campaigns/sync');
                }

                $totalResult['inserted_count'] += $result['inserted_count'];
                $totalResult['skipped_count'] += $result['skipped_count'];
                $totalResult['errors'] = array_merge($totalResult['errors'], $result['errors']);
            }

            if ($totalResult['inserted_count'] > 0) {
                session()->setFlashdata('success', "{$totalResult['inserted_count']} events successfully synchronized. {$totalResult['skipped_count']} events skipped (already exist).");
            } else {
                session()->setFlashdata('warning', "No new events synchronized. {$totalResult['skipped_count']} events skipped (already exist).");
            }

            return redirect()->to('/campaigns/sync')->with('result', $totalResult);

        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->to('/campaigns/sync');
        }
    }

    private function addUTMParameters($url, $params) {
        $parsedUrl = parse_url($url);
        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
        parse_str($query, $queryParams);
        $queryParams = array_merge($queryParams, $params);
        $queryString = http_build_query($queryParams);

        return (isset($parsedUrl['scheme']) ? "{$parsedUrl['scheme']}:" : '') . 
               ((isset($parsedUrl['user']) || isset($parsedUrl['host'])) ? '//' : '') . 
               (isset($parsedUrl['user']) ? "{$parsedUrl['user']}" : '') . 
               (isset($parsedUrl['pass']) ? ":{$parsedUrl['pass']}" : '') . 
               (isset($parsedUrl['user']) ? '@' : '') . 
               (isset($parsedUrl['host']) ? "{$parsedUrl['host']}" : '') . 
               (isset($parsedUrl['port']) ? ":{$parsedUrl['port']}" : '') . 
               (isset($parsedUrl['path']) ? "{$parsedUrl['path']}" : '') . 
               "?$queryString" . 
               (isset($parsedUrl['fragment']) ? "#{$parsedUrl['fragment']}" : '');
    }

    private function addUTMParametersToLinks($html, $utmParams)
    {
        // Utiliser DOMDocument pour parser le HTML et ajouter les paramètres UTM aux liens
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);

        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $newHref = $this->addUTMParameters($href, $utmParams);
            $link->setAttribute('href', $newHref);
        }

        return $dom->saveHTML();
    }
    
}
