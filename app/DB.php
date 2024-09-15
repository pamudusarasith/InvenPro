<?php

namespace App;

use PDO;

class DB
{
    private static $instance = null;
    private $dbh = null;

    private function __construct()
    {
        try {
            Utils::loadDotEnv();
        } catch (\Exception) {
        }
        $this->dbh = new PDO('mysql:host=' . $_ENV["DB_HOST"] . ';dbname=' . $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);
    }

    public static function getConnection(): PDO
    {
        if (self::$instance == null) {
            self::$instance = new DB();
        }
        return self::$instance->dbh;
    }
}
