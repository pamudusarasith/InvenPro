<?php

namespace App;

class DB
{
    private static $instance = null;
    private $dbh = null;

    private function __construct()
    {
        Utils::loadDotEnv();
        $this->dbh = new \PDO('mysql:host=localhost;dbname=invenpro', $_ENV["DB_USER"], $_ENV["DB_PASS"]);
    }

    public static function getConnection()
    {
        if (self::$instance == null) {
            self::$instance = new DB();
        }
        return self::$instance->dbh;
    }
}
