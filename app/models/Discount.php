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
    $stmt = $this->dbh->prepare("SHOW COLUMNS FROM discount LIKE 'type';");
    $stmt->execute();
    $row = $stmt->fetch();
    return explode("','", substr($row["Type"], 6, -2));
  }
}
