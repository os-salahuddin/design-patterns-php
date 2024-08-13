<?php
class DbCon {
    private static $instance;
    private function __construct()
    {

    }

    public static function getInstance()
    {
        if(self::$instance == null) {
            self::$instance = new DbCon();
        }

        return self::$instance;
    }
}


$dbCon = DbCon::getInstance();
$dbCon2 = DbCon::getInstance();

var_dump($dbCon === $dbCon2);