<?php

namespace ttm4135\webapp;

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
        parent::__construct('sqlite:' . '../db/app.db');
        parent::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Create tables.
     */
    public static function up()
    {
        $q1 = "CREATE TABLE users (id INTEGER PRIMARY KEY, username VARCHAR(50), password VARCHAR(50), email varchar(50),  bio varhar(50), isadmin INTEGER);";

        self::$__instance->exec($q1);

        print "[ttm4135] Done creating all SQL tables." . PHP_EOL;

        self::insertDummyUsers();
    }

    static function insertDummyUsers()
    {


        $q1 = "INSERT INTO users(username, password, isadmin) VALUES ('admin', 'admin', 1)";
        $q2 = "INSERT INTO users(username, password) VALUES ('bob', 'bob')";

        self::$__instance->exec($q1);
        self::$__instance->exec($q2);

        print "[ttm4135] Done inserting dummy users." . PHP_EOL;
    }


    static function down()
    {
        $q1 = "DROP TABLE users";

        self::$__instance->exec($q1);

        print "[ttm4135] Done deleting all SQL tables." . PHP_EOL;
    }

}