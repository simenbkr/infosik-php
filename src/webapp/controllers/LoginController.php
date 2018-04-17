<?php

namespace ttm4135\webapp\controllers;
use ttm4135\webapp\Auth;
use ttm4135\webapp\models\User;

class LoginController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (Auth::check()) {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        } else {
            $username_field = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
            $this->render('login.twig', ['title'=>"Login", 'username' => $username_field]);
        }
    }

    function login()
    {
        $request = $this->app->request;
        $username = filter_var($request->post('username'), FILTER_SANITIZE_STRING);
        $password = filter_var($request->post('password'), FILTER_SANITIZE_STRING);

        if ( Auth::checkCredentials($username, $password) && hash_equals($_SESSION['token'], $request->post('token'))) {
            setcookie("username", $username, time()+3600);
            $user = User::findByUser($username);
            $_SESSION['userid'] = $user->getId();
            $this->app->flash('info', "You are now successfully logged in as " . $user->getUsername() . ".");
            $this->app->redirect('/');
        }
        elseif(!hash_equals($_SESSION['token'], $request->post('token'))) {
            $this->app->flashNow('error', 'CSRF-token error!');
            $this->render('login.twig', []);
        } else {
            $this->app->flashNow('error', 'Incorrect username/password combination.');
            $this->render('login.twig', []);
        }
    }

    function logout()
    {   
        Auth::logout();
        $this->app->flashNow('info', 'Logged out successfully!!');
        $this->render('base.twig', []);
        return;
       
    }
}
