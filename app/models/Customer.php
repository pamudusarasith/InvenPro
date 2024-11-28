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
        $stmt = $this->dbh->prepare("INSERT INTO customer (full_name,email,phone_number,address,date_of_birth,gender) VALUES (:name,:email,:phone ,:address ,:dob ,:gender)");
        $stmt->execute([
            'name' => $customer["name"],
            'email' => $customer["email"],
            'phone' => $customer["phone"],
            'address' => $customer["address"],
            'dob' => $customer["dob"],
            'gender' => $customer["gender"]
        ]);
    }

    public function getCustomerByPhone($phone)
    {
        $stmt = $this->dbh->prepare("SELECT * FROM customer WHERE phone_number = :phone");
        $stmt->execute(['phone' => $phone]);
        $result = $stmt->fetch();

        return $result;
    }

    public function deleteCustomerByPhone($phone)
    {
        $stmt = $this->dbh->prepare("DELETE FROM customer WHERE phone_number = ?");
        return $stmt->execute([$phone]);
    }


    public function updateCustomer($name, $email, $phone, $address, $dob, $gender)
    {
        $query = "UPDATE customer SET full_name = ?, email = ?, phone_number = ?, address = ?, date_of_birth = ?, gender = ? WHERE phone_number = ?";
        $stmt = $this->dbh->prepare($query);
        return $stmt->execute([$name, $email, $phone, $address, $dob, $gender, $phone]);
    }
}
