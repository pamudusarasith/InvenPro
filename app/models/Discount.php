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
}
