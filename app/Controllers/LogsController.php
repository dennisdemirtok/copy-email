<?php

namespace App\Controllers;

use App\Models\EmailEventsModel;

class LogsController extends BaseController
{
    public function index()
    {
        $model = model(EmailEventsModel::class);

        $data = [
            'emailEvents' => $model->getEmailEvents()
        ];
        echo view('Templates/header', ['currentPage' => 'logs']);
        echo view('Logs/index', $data);
        echo view('Templates/footer');
    }

    public function explorer(){
        $model = model(EmailEventsModel::class);
        $emails = $model->getEventsGroupedByUniqueMail();
    
        // Définir l'ordre des types d'événements
        $order = ['sent', 'delivered', 'clicked', 'opened'];
    
        // Convertir BSONArray en tableau et trier les événements
        foreach ($emails as &$email) {
            // Conversion de BSONArray en tableau
            $eventsArray = json_decode(json_encode($email->events), true);
    
            // Tri des événements selon l'ordre spécifié
            usort($eventsArray, function ($a, $b) use ($order) {
                // Vous devrez peut-être adapter les chemins d'accès selon la structure de vos données
                return array_search($a['data']['type'], $order) <=> array_search($b['data']['type'], $order);
            });
    
            // Remettre le tableau trié
            $email->events = $eventsArray;
        }

        $data['emails'] = $emails;

        echo view('Templates/header', ['currentPage' => 'logs']);
        echo view('Logs/explorer', $data);
        echo view('Templates/footer');
    }
}
