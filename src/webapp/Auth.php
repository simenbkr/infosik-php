<?php

namespace ttm4135\webapp;

use ttm4135\webapp\models\User;

class Auth
{
    public static function checkCredentials($username, $password)
    {
        $user = User::findByUser($username);

        if ($user === null) {
            return false;
        }

        return $user->getPassword() === $password;
    }

    /**
     * Check if is logged in.
     */
    public static function check()
    {
        return isset($_SESSION['userid']);
    }

    /**
     * Check if the person is a guest.
     */
    public static function guest()
    {
        return self::check() === false;
    }

    /**
     * Get currently logged in user.
     */
    public static function user()
    {
        if (self::check()) {
            return User::findById($_SESSION['userid']);         
        }
        return null;
    }

    /**
     * Is currently logged in user admin?
     */
    public static function isAdmin()
    {
        if (self::check()) {
          return self::user()->isAdmin();	// uses this classes user() method to retrieve the user from sql, then call isadmin on that object.
        }

        return false;
    }

    /** 
     * Does the logged in user have r/w access to user details identified by $tuserid
     */
    public static function userAccess($tuserid)
    {
        if(self::user()->getId() === $tuserid)   //a user can change their account
        {
          return true;
        }

        if (self::isAdmin())           //admins can change any account
        {
          return true;
        }
        return false;

    }
    
    static function logout()
    {
        session_unset();
        session_destroy();	
        session_regenerate_id();
    }
}
