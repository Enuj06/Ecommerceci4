<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        //
    }

    public function register()
    {
        helper(['form']);
        $data = [];
        echo view('signup', $data);

        helper(['form']);
        $rules = [
            'username'  => 'required|min_length[4]|max_length[100]valid_email|is_unique[users.email]',
            'password' =>   'required|min_length[4]|max_length[50]',
            'confirmpassword'   =>  'matches[password]'
        ];
        if($this->validate($rules))
        {
            $userModel = new UserModel();
            $data = [
                'username'    => $this->request->getVar('username'),
                'password'  =>  password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
            ];
            $userModel->save($data);
            return redirect()->to('/signin');
        }
        else
        {
            $data['validation'] = $this->validator;
            echo view('signup', $data);
        }
    }

    public function Login()
    {
        helper(['form']);
        echo view('signin');
    }

    public function LoginAuth()
    {
        $session = session();
        $userModel = new UserModel();
        $username = $this->request->getvar('username');
        $password = $this->request->getvar('password');

        $data = $userModel->where('username', $username)->first();

        if($data)
        {
            $pass = $data['password'];
            $authenticatePassword = password_verify($password, $pass);
            if($authenticatePassword)
            {
                $ses_data = [
                    'id' => $data['id'],
                    'username' => $data['username'],
                    'isLoggedin' => TRUE
                ];
                $session->set($ses_data);
                return redirect()->to('/profile');

            }
            else
            {
                $session->setFlashdata('msg', 'Password is incorrect.');
                return redirect()->to('/signin');
            }
            
        }else
            {
                $session->setFlashdata('msg', 'Email does not exist');
                return redirect()->to('/signin');
            }
        }
    }
