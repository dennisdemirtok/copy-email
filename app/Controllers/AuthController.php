<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function login()
    {
        $session = session();

        if ($session->has('isLoggedIn')) {
            return redirect()->to('/');
        }

        helper(['form']);

        echo view('Auth/login');
    }

    public function doLogin()
    {
        $session = session();

        $rules = [
            'username'    => 'required',
            'password' => 'required',
        ];

        if ($this->validate($rules)) {
            $username    = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $envUser = getenv('USER_APP');
            $envPass = getenv('PASSWORD_APP');
            // Remplacez ces conditions par vos propres conditions d'authentification
            if ($username == $envUser && $password == $envPass) {
                $session->set('isLoggedIn', true);
                return redirect()->to('/');
            } else {
                $session->setFlashdata('error', 'Invalid login credentials');
            }
        } else {
            $session->setFlashdata('error', 'Validation errors occurred');
        }

        return redirect()->to('/login')->withInput();
    }

    public function logout()
    {
        $session = session();
        $session->remove('isLoggedIn');
        return redirect()->to('/login');
    }
}
