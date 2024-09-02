<?php

namespace App\Models;

use App;

class Employee
{
    private $dbh = null;

    public function __construct()
    {
        $this->dbh = App\DB::getConnection();
    }

    public function emailExists($email): bool
    {
        $stmt = $this->dbh->prepare("SELECT email FROM employee WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    public function getPassword($email)
    {
        $stmt = $this->dbh->prepare("SELECT password FROM employee WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn();
    }
}
