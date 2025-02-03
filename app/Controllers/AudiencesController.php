<?php 

namespace App\Controllers;

use App\Models\AudiencesModel;
use App\Models\ContactsModel;

class AudiencesController extends BaseController {
    protected $audiencesModel;
    protected $contactsModel;

    public function __construct()
    {
        $this->audiencesModel = new AudiencesModel();
        $this->contactsModel = new ContactsModel();
        helper('domain');
    }

    public function index(){
        $data['allAudiences'] = $this->audiencesModel->getAllAudiences();
        
        echo view('Templates/header', ['currentPage' => 'audiences']);
        echo view('Audiences/index', $data);
        echo view('Templates/footer');
    }

    public function create(){
        if ($this->request->getMethod() === 'post') {
            $activeDomain = get_active_domain();
            if (!$activeDomain) {
                return redirect()->back()->with('error', 'Please select an active domain first');
            }

            $audienceName = $this->request->getPost('name');
            $csvFile = $this->request->getFile('csvFile');

            // Delimiters to be replaced: pipe, comma, semicolon, caret, tabs
            $delimiters = array('|', ';', '^', "\t");
            $delimiter = ',';

            $str = file_get_contents($csvFile);
            $str = str_replace($delimiters, $delimiter, $str);
            file_put_contents($csvFile, $str);

            if ($csvFile->isValid() && $csvFile->getExtension() === 'csv') {
                $newName = $csvFile->getRandomName();
                $csvFile->move(WRITEPATH . 'uploads/audience_csv', $newName);

                $filePath = WRITEPATH . 'uploads/audience_csv/' . $newName;
                $file = fopen($filePath, "r");
                $contacts = [];
                while (($data = fgetcsv($file,1000,',')) !== FALSE) {                  
                    $subscribed_value = strtoupper($data[3]);
                    
                    $contacts[] = [
                        'email' => $data[0], 
                        'firstName' => $data[1] ?? '', 
                        'lastName' => $data[2] ?? '', 
                        'subscribed' => $subscribed_value == 'SUBSCRIBED' ? true : false,
                    ];
                }
                fclose($file);

                $allExistingContacts = $this->contactsModel->getAllContacts();
                $existingContactsByEmail = [];

                // Construction d'un tableau associatif des contacts existants par e-mail
                foreach ($allExistingContacts as $existingContact) {
                    $existingContactsByEmail[$existingContact['email']] = $existingContact;
                }

                $updateOperations = [];
                $contactsToInsert = [];
        
                $contactsIds = [];

                foreach ($contacts as $contact) {
                    $email = $contact['email'];
                    if (isset($existingContactsByEmail[$email])) {
                        $updateOperations[] = [
                            'filter' => ['_id' => $existingContactsByEmail[$email]['_id']],
                            'update' => ['$set' => $contact]
                        ];
                        $contactsIds[] = $existingContactsByEmail[$email]['_id'];
                    } else {
                        $contactsToInsert[] = $contact;
                    }
                }
        
                // Exécutez updateMany si des mises à jour sont nécessaires
                if (!empty($updateOperations)) {
                    $this->contactsModel->updateManyContacts($updateOperations);
                }

                // Vérification si le tableau $contactsToInsert n'est pas vide
                if (!empty($contactsToInsert)) {
                    $contactsIds[] = $this->contactsModel->insertManyContacts($contactsToInsert);
                }

                $audienceData = [
                    'name' => $audienceName,
                    'domain_id' => $activeDomain['id'],
                    'contacts' => $contactsIds,
                ];

                $this->audiencesModel->insertAudience($audienceData);

                return redirect()->route('audiences');
            }
        }

        echo view('Templates/header', ['currentPage' => 'audiences']);
        echo view('Audiences/create');
        echo view('Templates/footer');
    }

    public function store()
    {
        set_time_limit(3000);
        if ($this->request->getMethod() === 'post') {
            $audienceName = $this->request->getPost('name');
            $csvFile = $this->request->getFile('csvFile');

            // Delimiters to be replaced: pipe, comma, semicolon, caret, tabs
            $delimiters = array('|', ';', '^', "\t");
            $delimiter = ',';

            $str = file_get_contents($csvFile);
            $str = str_replace($delimiters, $delimiter, $str);
            file_put_contents($csvFile, $str);

            if ($csvFile->isValid() && $csvFile->getExtension() === 'csv') {
                $newName = $csvFile->getRandomName();
                $csvFile->move(WRITEPATH . 'uploads/audience_csv', $newName);

                $filePath = WRITEPATH . 'uploads/audience_csv/' . $newName;
                $file = fopen($filePath, "r");
                $contacts = [];
                while (($data = fgetcsv($file,1000,',')) !== FALSE) {                  
                    $subscribed_value = strtoupper($data[3]);
                    
                    $contacts[] = [
                        'email' => $data[0], 
                        'firstName' => $data[1] ?? '', 
                        'lastName' => $data[2] ?? '', 
                        'subscribed' => $subscribed_value == 'SUBSCRIBED' ? true : false,
                    ];
                }
                fclose($file);

                $allExistingContacts = $this->contactsModel->getAllContacts();
                $existingContactsByEmail = [];

                // Construction d'un tableau associatif des contacts existants par e-mail
                foreach ($allExistingContacts as $existingContact) {
                    $existingContactsByEmail[$existingContact['email']] = $existingContact;
                }

                $updateOperations = [];
                $contactsToInsert = [];
        
                $contactsIds = [];

                foreach ($contacts as $contact) {
                    $email = $contact['email'];
                    if (isset($existingContactsByEmail[$email])) {
                        $updateOperations[] = [
                            'filter' => ['_id' => $existingContactsByEmail[$email]['_id']],
                            'update' => ['$set' => $contact]
                        ];
                        $contactsIds[] = $existingContactsByEmail[$email]['_id'];
                    } else {
                        $contactsToInsert[] = $contact;
                    }
                }
        
                // Exécutez updateMany si des mises à jour sont nécessaires
                if (!empty($updateOperations)) {
                    $this->contactsModel->updateManyContacts($updateOperations);
                }

                // Vérification si le tableau $contactsToInsert n'est pas vide
                if (!empty($contactsToInsert)) {
                    $contactsIds[] = $this->contactsModel->insertManyContacts($contactsToInsert);
                }

                $audienceData = [
                    'name' => $audienceName,
                    'contacts' => $contactsIds,
                    'domain_id' => get_active_domain()['id']
                ];

                $this->audiencesModel->insertAudience($audienceData);

                return redirect()->route('audiences');
            }
        }
        set_time_limit(30);
    }

    public function update(){
        set_time_limit(300);
        if ($this->request->getMethod() === 'post') {
            $audienceId = $this->request->getPost('id');
            $audienceName = $this->request->getPost('name');
            $csvFile = $this->request->getFile('csvFile');

            
            // Delimiters to be replaced: pipe, comma, semicolon, caret, tabs
            $delimiters = array('|', ';', '^', "\t");
            $delimiter = ',';

            $str = file_get_contents($csvFile);
            $str = str_replace($delimiters, $delimiter, $str);
            file_put_contents($csvFile, $str);

            if ($csvFile->isValid() && $csvFile->getExtension() === 'csv') {
                $newName = $csvFile->getRandomName();
                $csvFile->move(WRITEPATH . 'uploads/audience_csv', $newName);

                // Lecture du fichier CSV
                $filePath = WRITEPATH . 'uploads/audience_csv/' . $newName;
                $file = fopen($filePath, "r");
                $contacts = [];
                while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $subscribed_value = strtoupper($data[3]);

                    $contacts[] = [
                        'email' => $data[0], 
                        'firstName' => $data[1] ?? '', 
                        'lastName' => $data[2] ?? '', 
                        'subscribed' => $subscribed_value == 'SUBSCRIBED' ? true : false
                    ];
                }
                fclose($file);

                $updateOperations = [];
                $contactsToInsert = [];

                $allExistingContacts = $this->contactsModel->getAllContacts();
                $existingContactsByEmail = [];

                // Construction d'un tableau associatif des contacts existants par e-mail
                foreach ($allExistingContacts as $existingContact) {
                    $existingContactsByEmail[$existingContact['email']] = $existingContact;
                }
        
                $contactsIds = [];
                foreach ($contacts as $contact) {
                    $email = $contact['email'];
                    if (isset($existingContactsByEmail[$email])) {
                        $updateOperations[] = [
                            'filter' => ['_id' => $existingContactsByEmail[$email]['_id']],
                            'update' => ['$set' => $contact]
                        ];
                        $contactsIds[] = $existingContactsByEmail[$email]['_id'];
                    } else {
                        $contactsToInsert[] = $contact;
                    }
                }
        
                // Exécutez updateMany si des mises à jour sont nécessaires
                if (!empty($updateOperations)) {
                    $this->contactsModel->updateManyContacts($updateOperations);
                }

                // Vérification si le tableau $contactsToInsert n'est pas vide
                if (!empty($contactsToInsert)) {
                    $newContactIds = $this->contactsModel->insertManyContacts($contactsToInsert);
                    $contactsIds = array_merge($contactsIds, $newContactIds);
                }

                $audienceData = [
                    'name' => $audienceName,
                    'contacts' => $contactsIds,
                ];

                $this->audiencesModel->updateAudience($audienceId, $audienceData);

                
                return redirect()->route('audiences');
            }
        }
        set_time_limit(30);
    }

    public function delete($id) {
        $model = model(AudiencesModel::class);
    
        $model->deleteAudience($id);
    
        return redirect()->route('audiences');
    }

    public function details($id) {
        $model = model(AudiencesModel::class);

        $data = [
            'audience' => $model->getAudience($id),
            'audienceContacts' => $model->getAudienceContacts($id)
        ];

        echo view('Templates/header',['currentPage' => 'audiences']);
        echo view('Audiences/details', $data);
        echo view('Templates/footer');
    }

    public function edit($id)
    {
        $model = model(AudiencesModel::class);

        $audience = $model->getAudience($id);

        if ($audience) {
            echo view('Templates/header',['currentPage' => 'audiences']);
            echo view('Audiences/edit', ['audience' => $audience]);
            return view('Templates/footer');
        } else {
            return redirect()->to('/audiences')->with('error', 'Audience not found');
        }
    }

}
