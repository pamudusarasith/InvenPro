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

    public function getTotalUsers(): int
    {
        // Replace with actual database query to get total users
        return 1000; // Example value
    }

    public function getActiveUsers(): int
    {
        // Replace with actual database query to get active users
        return 250; // Example value
    }

    public function getNewSignUps(): int
    {
        // Replace with actual database query to get new sign-ups for the month
        return 50; // Example value
    }
}
