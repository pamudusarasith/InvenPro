<?php

namespace App;

use PDO;

class DB
{
    private static $instance = null;
    private $dbh = null;

    private function __construct()
    {
        Utils::loadDotEnv();
        
        $this->dbh = new PDO('mysql:host=' . $_ENV["DB_HOST"] . ';dbname=' . $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);
        $this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public static function getConnection(): PDO
    {
        if (self::$instance == null) {
            self::$instance = new DB();
        }
        return self::$instance->dbh;
    }
}
