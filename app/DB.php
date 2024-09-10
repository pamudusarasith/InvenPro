<?php

namespace App;

class DB
{
    private static $instance = null;
    private $dbh = null;

    private function __construct()
    {
        Utils::loadDotEnv();
        $this->dbh = new \PDO('mysql:host='. $_ENV["DB_HOST"] .';dbname=' . $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);
    }

    public static function getConnection()
    {
        if (self::$instance == null) {
            self::$instance = new DB();
        }
        return self::$instance->dbh;
    }
}
