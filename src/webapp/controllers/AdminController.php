<?php

namespace ttm4135\webapp\controllers;

use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;

class AdminController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()     
    {
        if (Auth::isAdmin() && hash_equals($_SESSION['token'], $request->post('token'))) {
            $users = User::all();
            $this->render('users.twig', ['users' => $users]);
        } else {
            if(Auth::user() != null && hash_equals($_SESSION['token'], $request->post('token'))) {
                $username = Auth::user()->getUserName();
                $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
                $this->app->redirect('/');
            } else {
                $this->app->flash('info', 'You do not have access this resource. You are not logged in.');
                $this->app->redirect('/');
            }
        }
    }

    function create()
    {
        if (Auth::isAdmin() && hash_equals($_SESSION['token'], $request->post('token'))) {
          $user = User::makeEmpty();
          $this->render('showuser.twig', [
            'user' => $user
          ]);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }


}
