<?php

namespace ttm4135\webapp;

use ttm4135\webapp\models\User;

if(!defined('DB_PATH')){
    define('DB_PATH', __DIR__ . '/../../db/app.db');
}

class Sql extends \PDO{

    public static $__instance = null;

    public static function getDB(){
        if (self::$__instance == null) {
            self::$__instance = new self();
        }
        return self::$__instance;
    }

    public function __construct()
    {
        parent::__construct('sqlite:' . DB_PATH);
        parent::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Create tables.
     */
    public static function up()
    {
        if(self::$__instance === null){
            self::$__instance = self::getDB();
        }

        $q1 = "CREATE TABLE users (id INTEGER PRIMARY KEY, username VARCHAR(50), password VARCHAR(256), salt VARCHAR(50), email varchar(50),  bio varhar(50), isadmin INTEGER);";

        self::$__instance->exec($q1);

        print "[ttm4135] Done creating all SQL tables." . PHP_EOL;

        self::insertDummyUsers();
    }

    static function insertDummyUsers()
    {
        if(self::$__instance === null){
            self::$__instance = self::getDB();
        }

        $admin_salt = User::genRandomStr();
        $admin_pw = User::hashPassword('admin', $admin_salt);
        $query = "INSERT INTO users (username, password, salt, isadmin) VALUES('admin','$admin_pw','$admin_salt', '1' )";
        self::$__instance->query($query);

        $bob_salt = User::genRandomStr();
        $bob_pw = User::hashPassword('bob', $bob_salt);
        $query = "INSERT INTO users (username, password, salt, isadmin) VALUES('bob', '$bob_pw', '$bob_salt', '0')";
        self::$__instance->query($query);

        print "[ttm4135] Done inserting dummy users." . PHP_EOL;
    }


    static function down()
    {
        if(self::$__instance === null){
            self::$__instance = self::getDB();
        }

        $q1 = "DROP TABLE users";

        self::$__instance->exec($q1);

        print "[ttm4135] Done deleting all SQL tables." . PHP_EOL;
    }

}

Sql::$__instance = Sql::getDB();