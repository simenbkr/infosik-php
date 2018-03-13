<?php

namespace ttm4135\webapp\models;

use ttm4135\webapp\Sql;

class User
{
    protected $id = null;
    protected $username;
    protected $password;
    protected $email;
    protected $salt;
    protected $bio = 'Bio is empty.';
    protected $isAdmin = 0;

    static $app;

    public static function make($id, $username, $password, $salt, $email, $bio, $isAdmin )
    {
        $user = new User();
        $user->id = $id;
        $user->username = $username;
        $user->password = $password;
        $user->email = $email;
        $user->bio = $bio;
        $user->isAdmin = $isAdmin;
        $user->salt = $salt;

        return $user;
    }

    public static function makeEmpty()
    {
        return new User();
    }

    /**
     * Insert or update a user object to db.
     */
    public function save()
    {

        if($this->id === null) {
            $st = Sql::getDB()->prepare('INSERT INTO users (username, password, salt, email, bio, isadmin)
                                                  VALUES            (:username, :pw, :salt, :email, :bio, :isadmin)');
        } else {
            $st = Sql::getDB()->prepare('UPDATE users SET username=:username, password=:pw, salt=:salt, email=:email, 
                                                  bio=:bio, isadmin=:isadmin WHERE id=:id');

            $st->bindParam(':id', $this->id);
        }

        $st->bindParam(':username', $this->username);
        $st->bindParam(':pw', $this->password);
        $st->bindParam(':salt', $this->salt);
        $st->bindParam(':email', $this->email);
        $st->bindParam(':bio', $this->bio);
        $st->bindParam(':isadmin', $this->isAdmin);
        $st->execute();
    }

    public function delete()
    {
        $st = Sql::getDB()->prepare('DELETE FROM users WHERE id=:id');
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function isAdmin()
    {
        return $this->isAdmin === "1";
    }

    public function getSalt(){
        return $this->salt;
    }

    public function setSalt($salt){
        $this->salt = $salt;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
    }
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }


    /**
     * Get user in db by userid
     *
     * @param string $userid
     * @return mixed User or null if not found.
     */
    public static function findById($userid)
    {

        $st = Sql::getDB()->prepare('SELECT * FROM users WHERE id=:id');
        $st->bindParam(':id', $userid);
        $st->execute();

        $row = $st->fetch();

        if($row == false) {
            return null;
        }

        return User::makeFromSql($row);
    }

    /**
     * Find user in db by username.
     *
     * @param string $username
     * @return mixed User or null if not found.
     *
     *
     */
    public static function findByUser($username)
    {

        $st = Sql::getDB()->prepare('SELECT * FROM users WHERE username=:username');
        $st->bindParam(':username', $username);
        $st->execute();

        $row = $st->fetch();

        if($row == false) {
            return null;
        }

        return User::makeFromSql($row);
    }

    
    public static function all()
    {
        $query = "SELECT * FROM users";
        $results = self::$app->db->query($query);

        $users = [];

        foreach ($results as $row) {
            $user = User::makeFromSql($row);
            $users[] = $user;
        }

        return $users;
    }

    public static function makeFromSql($row)
    {
        return User::make(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['salt'],
            $row['email'],
            $row['bio'],
            $row['isadmin']
        );
    }

    public static function hashPassword($password, $salt){

        $options = array(
            'salt' => $salt
        );

        return password_hash($password, PASSWORD_DEFAULT, $options);
    }

    public function verifyPassword($password){
        return password_verify($password, $this->password);
    }

    public static function genRandomStr($length = 22){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = strlen($chars);
        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= substr($chars, $index, 1);
        }
        return $result;
    }

}


User::$app = \Slim\Slim::getInstance();

