<?php

namespace App\Models;

use App;

class Discount
{
  private $dbh;

  public function __construct()
  {
    $this->dbh = App\DB::getConnection();
  }

  public function getTypes()
  {
    $stmt = $this->dbh->prepare("SELECT id, name FROM discount_type");
    $stmt->execute();
    return $stmt->fetchAll();
  }
}
