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

    public function getEmployee()
    {
        if (!isset($_SESSION["email"]))
            return null;
        $stmt = $this->dbh->prepare("SELECT id, role_id, branch_id, full_name FROM employee WHERE email = ?");
        $stmt->execute([$_SESSION["email"]]);
        return $stmt->fetch();
    }

    public function getPermissionCategories()
    {
        if (!isset($_SESSION["role_id"]))
            return [];
        $stmt = $this->dbh->prepare("SELECT DISTINCT pc.name FROM role_permission rp INNER JOIN permission p ON rp.permission_id = p.id INNER JOIN permission_category pc ON p.category_id = pc.id WHERE rp.role_id = ?");
        $stmt->execute([$_SESSION["role_id"]]);
        $rows = $stmt->fetchAll();
        $categories = array();
        foreach ($rows as $row) {
            array_push($categories, $row["name"]);
        }
        return $categories;
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
