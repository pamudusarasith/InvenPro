<?php

namespace App\Models;

use App;

class Employee
{
    private $db = null;

    public function __construct()
    {
        $this->db = App\DB::getConnection();
    }

    public function emailExists($email): bool
    {
        $query = $this->db->prepare("SELECT email FROM employee WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();
        return $result->num_rows > 0;
    }

    public function getPassword($email) {
        $query = $this->db->prepare("SELECT password FROM employee WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        return $row["password"];
    }
}
