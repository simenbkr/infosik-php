<?php

namespace ttm4135\webapp\controllers;

use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;

class UserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()     
    {
        if (Auth::guest()) {
            $this->render('newUserForm.twig', []);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function create()		  
    {
        if($_SERVER['SSL_CLIENT_VERIFY'] == 'SUCCESS' && hash_equals($_SESSION['token'], $request->post('token'))) {

            $request = $this->app->request;
            $username = filter_var($request->post('username'), FILTER_SANITIZE_STRING);
            $password = filter_var($request->post('password'), FILTER_SANITIZE_STRING);
            $user = User::makeEmpty();

            $user->setUsername($username);
            $user->setSalt(User::genRandomStr());
            $user->setPassword(User::hashPassword($password, $user->getSalt()));
            $user->setIsAdmin(0);

            if ($request->post('email')) {
                $email = filter_var($request->post('email'), FILTER_SANITIZE_EMAIL);
                $user->setEmail($email);
            }

            if ($request->post('bio')) {
                $bio = filter_var($request->post('bio'), FILTER_SANITIZE_STRING);
                $user->setBio($bio);
            }


            $user->save();
            $this->app->flash('info', 'Thanks for creating a user. You may now log in.');
            $this->app->redirect('/login');
        } else {
            $this->app->flash('info', 'You are not allowed to create a user!');
            $this->app->redirect('/');
        }
    }

    function delete($tuserid)
    {
        if(Auth::userAccess($tuserid) && hash_equals($_SESSION['token'], $request->post('token')))
        {
            $user = User::findById($tuserid);
            $user->delete();
            $this->app->flash('info', 'User ' . $user->getUsername() . '  with id ' . $tuserid . ' has been deleted.');
            $this->app->redirect('/admin');
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function deleteMultiple()
    {
      if(Auth::isAdmin() && hash_equals($_SESSION['token'], $request->post('token'))){
          $request = $this->app->request;
          $userlist = $request->post('userlist'); 
          $deleted = [];

          if($userlist == NULL){
              $this->app->flash('info','No user to be deleted.');
          } else {
               foreach( $userlist as $duserid)
               {
                    $user = User::findById($duserid);
                    if(  $user->delete() == 1) { //1 row affect by delete, as expect..
                      $deleted[] = $user->getId();
                    }
               }
               $this->app->flash('info', 'Users with IDs  ' . implode(',',$deleted) . ' have been deleted.');
          }

          $this->app->redirect('/admin');
      } else {
          $username = Auth::user()->getUserName();
          $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
          $this->app->redirect('/');
      }
    }


    function show($tuserid)   
    {
        if(Auth::userAccess($tuserid) && hash_equals($_SESSION['token'], $request->post('token')))
        {
          $user = User::findById($tuserid);
          $this->render('showuser.twig', [
            'user' => $user
          ]);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function newuser()
    { 

        $user = User::makeEmpty();

        if (Auth::isAdmin() && hash_equals($_SESSION['token'], $request->post('token'))) {


            $request = $this->app->request;

            $username   = filter_var($request->post('username'), FILTER_SANITIZE_STRING);
            $password   = filter_var($request->post('password'), FILTER_SANITIZE_STRING);
            $salt       = User::genRandomStr();
            $password   = User::hashPassword($password, $salt);
            $email      = filter_var($request->post('email'), FILTER_SANITIZE_EMAIL);
            $bio        = filter_var($request->post('bio'),  FILTER_SANITIZE_STRING);

            $isAdmin = ($request->post('isAdmin') != null ? 1 : 0);
            

            $user->setUsername($username);
            $user->setPassword($password);
            $user->setBio($bio);
            $user->setEmail($email);
            $user->setIsAdmin($isAdmin);

            $user->save();
            $this->app->flashNow('info', 'Your profile was successfully saved.');

            $this->app->redirect('/admin');


        } else {
            $username = $user->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function edit($tuserid)    
    { 

        $user = User::findById($tuserid);

        if (! $user) {
            throw new \Exception("Unable to fetch logged in user's object from db.");
        } elseif (Auth::userAccess($tuserid) && hash_equals($_SESSION['token'], $request->post('token')) {


            $request = $this->app->request;

            $username = $request->post('username');
            $password = $request->post('password');
            $email = $request->post('email');
            $bio = $request->post('bio');

            $isAdmin = ($request->post('isAdmin') != null);
            

            $user->setUsername($username);
            $user->setPassword($password);
            $user->setBio($bio);
            $user->setEmail($email);
            $user->setIsAdmin($isAdmin);

            $user->save();
            $this->app->flashNow('info', 'Your profile was successfully saved.');

            $user = User::findById($tuserid);

            $this->render('showuser.twig', ['user' => $user]);


        } else {
            $username = $user->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

}
