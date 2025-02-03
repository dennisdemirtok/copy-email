<?php

namespace App\Controllers;

use App\Models\ContactsModel; // Assurez-vous d'adapter le chemin selon l'emplacement réel du modèle

class ContactsController extends BaseController
{
    public function index(){
        $model = model(ContactsModel::class);

        $data = [
            'allContacts' => $model->getAllContacts() // Utilisation de la méthode pour obtenir tous les contacts
        ];
        echo view('Templates/header', ['currentPage' => 'contacts']);
        echo view('Contacts/index', $data); // Vue pour afficher la liste des contacts
        echo view('Templates/footer');
    }

    public function unsubscribe($contactId){
        $model = model(ContactsModel::class);

        // Vérifiez si le contact existe
        $contact = $model->getContact($contactId);
        if (!$contact) {
            return view('Contacts/unsubscribe', ['message' => 'Contact not found']);
        }

        // Mettez à jour le statut du contact en "unsubscribed"
        $model->updateContact($contactId, ['subscribed' => false]);

        // Redirigez vers la liste des contacts avec un message de succès
        return view('Contacts/unsubscribe', ['message' =>'Contact unsubscribed']); // Vue pour afficher la liste des contacts
    }

    // Ajoutez d'autres méthodes pour gérer les opérations CRUD sur les contacts, par exemple :
    // public function add() {}
    // public function edit($id) {}
    // public function delete($id) {}
}
