<?php

namespace App\Controllers;

use App\Models\EmailEventsModel;
use App\Models\ContactsModel;
use App\Models\CampaignsModel;

class Home extends BaseController
{
    public function index()
    {
        $emailEventsModel = model(EmailEventsModel::class);
        $contactsModel = model(ContactsModel::class);
        $campaignsModel = model(CampaignsModel::class);

        $data = [
            'totalContacts' => $emailEventsModel->getUniqueContactsSum(),
            'totalSubscribedContacts' => $contactsModel->countSubscribedContacts(),
            'totalPerEventType' => $emailEventsModel->getTotalPerEventType(),
            'allCampaigns' => $campaignsModel->getCampaignsByDomain()
        ];
        echo view('Templates/header', ['currentPage' => 'home']);
        echo view('home', $data);
        echo view('real-time');
        echo view('Templates/footer');
    }
}
