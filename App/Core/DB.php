<?php

namespace App\Core;

use PDO;
use PDOException;

class DB
{
  private static $instance = null;
  private $connection;

  private function __construct()
  {
    Utils::loadEnv();
    try {
      $dsn = "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME') . ";charset=utf8mb4";
      $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ];

      $this->connection = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), $options);
    } catch (PDOException $e) {
      error_log($e->getMessage() . "\n" . $e->getTraceAsString());
      View::redirect("/500.html");
    }
  }

  // Prevent cloning of the instance
  private function __clone() {}

  public static function getInstance()
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function getConnection()
  {
    return $this->connection;
  }

  public function query($sql, $params = [])
  {
    try {
      $stmt = $this->connection->prepare($sql);
      $stmt->execute($params);
      return $stmt;
    } catch (PDOException $e) {
      error_log($e->getMessage() . "\n" . $e->getTraceAsString());
      View::redirect("/500.html");
    }
  }

  public function lastInsertId()
  {
    return $this->connection->lastInsertId();
  }

  public function beginTransaction()
  {
    return $this->connection->beginTransaction();
  }

  public function commit()
  {
    return $this->connection->commit();
  }

  public function rollback()
  {
    return $this->connection->rollBack();
  }

  public function inTransaction()
  {
    return $this->connection->inTransaction();
  }
}
