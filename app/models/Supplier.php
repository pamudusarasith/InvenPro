<?php

namespace App\Models;

use mysqli;

class Supplier
{
    private mysqli $conn;

    public function __construct()
    {
        $this->conn = new mysqli('localhost', 'root', '', 'invenpro');
        if ($this->conn->connect_error) {
            die('Connection Failed: ' . $this->conn->connect_error);
        }
    }

    public function addSupplier(array $data): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO supplier_details (supplierID, supplierName, productCategories, products, address, contactNo, email, specialNotes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssssssss",
            $data['supplierID'],
            $data['supplierName'],
            $data['productCategories'],
            $data['products'],
            $data['address'],
            $data['contactNo'],
            $data['email'],
            $data['specialNotes']
        );

        $result = $stmt->execute();
        $stmt->close();
        $this->conn->close();

        return $result;
    }
}
