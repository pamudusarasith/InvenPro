<?php

namespace App\Models;

use App;

class Customer
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = App\DB::getConnection();
    }

    public function addCustomer(array $customer): void
    {
        $stmt = $this->dbh->prepare("INSERT INTO customer (full_name,email,phone_no,address,date_of_birth,gender) VALUES (:name,:email,:phone ,:address ,:dob ,:gender)");
        $stmt->execute([
            'name' => $customer["name"],
            'email' => $customer["email"],
            'phone' => $customer["phone"],
            'address' => $customer["address"],
            'dob' => $customer["dob"],
            'gender' => $customer["gender"]
        ]);
    }
}
