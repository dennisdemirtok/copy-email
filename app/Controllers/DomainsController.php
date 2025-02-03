<?php

namespace App\Controllers;

use App\Models\DomainsModel;

class DomainsController extends BaseController
{
    protected $domainsModel;

    public function __construct()
    {
        $this->domainsModel = new DomainsModel();
    }

    public function index()
    {
        $data['domains'] = $this->domainsModel->findAll();
        
        echo view('Templates/header', ['currentPage' => 'domains']);
        echo view('Domains/index', $data);
        echo view('Templates/footer');
    }

    public function setActive($id)
    {
        set_active_domain($id);
        return $this->response->setJSON(['success' => true]);
    }

    public function import()
    {
        $resendDomains = $this->domainsModel ->getAllDomains();
        $importCount = 0;

        foreach ($resendDomains as $domain) {
            // Check if domain already exists in MongoDB
            $existingDomain = $this->domainsModel->collection->findOne(['domain_id' => $domain['domain_id']]);
            
            if (!$existingDomain) {
                // Prepare domain data for MongoDB
                $mongoData = [
                    'domain_id' => $domain['domain_id'],
                    'domain_name' => $domain['domain_name'],
                    'status' => $domain['status'],
                    'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime($domain['created_at']) * 1000),
                    'updated_at' => new \MongoDB\BSON\UTCDateTime(time() * 1000)
                ];

                // Insert into MongoDB
                $this->domainsModel->collection->insertOne($mongoData);
                $importCount++;
            }
        }

        if ($importCount > 0) {
            session()->setFlashdata('success', "{$importCount} domain(s) successfully imported from Resend");
        } else {
            session()->setFlashdata('info', "No new domains to import from Resend");
        }

        return redirect()->to('/domains');
    }

    public function edit($id = null)
    {
        if ($id === null) {
            return redirect()->to('/domains');
        }

        $domain = $this->domainsModel->collection->findOne(['domain_id' => $id]);
        if (!$domain) {
            session()->setFlashdata('error', 'Domain not found');
            return redirect()->to('/domains');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'sender_email' => 'required|valid_email',
                'pretty_name' => 'required|min_length[3]'
            ];

            if ($this->validate($rules)) {
                $updateData = [
                    'sender_email' => $this->request->getPost('sender_email'),
                    'pretty_name' => $this->request->getPost('pretty_name'),
                    'updated_at' => new \MongoDB\BSON\UTCDateTime(time() * 1000)
                ];

                $this->domainsModel->collection->updateOne(
                    ['domain_id' => $id],
                    ['$set' => $updateData]
                );

                session()->setFlashdata('success', 'Domain updated successfully');
                return redirect()->to('/domains');
            } else {
                session()->setFlashdata('error', 'Please check your input');
            }
        }
        echo view('Templates/header', ['currentPage' => 'domains']);
        echo view('Domains/edit', ['domain' => (array)$domain]);
        return view('Templates/footer');
    }
}
