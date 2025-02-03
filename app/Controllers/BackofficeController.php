<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class BackofficeController extends Controller
{
    public function index()
    {
        $data = $this->getData();

        echo view('Backoffice/index', $data);
    }

    private function getData()
    {
        // Ajoutez votre logique pour obtenir les données ici
        $repertoire = isset($_POST['repertoire']) ? $_POST['repertoire'] : '';
        $contenu = is_dir($repertoire) ? $this->listFilesAndDirectories($repertoire) : [];

        return [
            'repertoire' => $repertoire,
            'contenu' => $contenu
        ];
    }

    private function listFilesAndDirectories($directory)
    {
        $content = scandir($directory);
        $content = array_diff($content, array('..', '.'));

        $files = [];
        $directories = [];

        foreach ($content as $element) {
            $path = $directory . '/' . $element;

            if (is_dir($path)) {
                $directories[] = $element;
            } else {
                $files[] = $element;
            }
        }

        return ['directories' => $directories, 'files' => $files];
    }
}
