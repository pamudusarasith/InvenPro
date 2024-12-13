<?php

namespace App\Models;

use App;

class Searchavailability
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = App\DB::getConnection();
    }
}
