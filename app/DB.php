<?php

namespace App;

class DB
{
    private static $instance = null;
    private $db = null;

    private function __construct()
    {
        Utils::loadDotEnv();
        $this->db = new \mysqli('localhost', $_ENV["DB_USER"], $_ENV["DB_PASS"], 'invenpro');
        if ($this->db->connect_error) {
            throw new \Exception("Connection failed: " . $this->db->connect_error);
        }
    }

    public static function getConnection()
    {
        if (self::$instance == null) {
            self::$instance = new DB();
        }
        return self::$instance->db;
    }
}
