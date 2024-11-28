<?php

namespace App\Models;

use App;

class Supplier
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = App\DB::getConnection();
    }

    public function addSupplier(): void
    {
        $stmt = $this->dbh->prepare("INSERT INTO `supplier_details` (`supplierID`, `supplierName`, `productCategories`, `products`, `address`, `contactNo`, `email`, `specialNotes`) VALUES 
            (:supplierID, :supplierName, :productCategories, :products, :addr, :contactNo, :email, :specialNotes);");

        $stmt->execute([
                'supplierID' => $_POST["supplier-id"],
                'supplierName' => $_POST["supplier-name"],
                'productCategories' => $_POST["product-categories"],
                'products' => $_POST["products"],
                'addr' => $_POST["address"],
                'contactNo' => $_POST["contact-no"],
                'email' => $_POST["email"],
                'specialNotes' => $_POST["special-notes"],
            ]);
    }

    public function deleteSupplier(string $supplierID): bool
    {
        $stmt = $this->dbh->prepare("DELETE FROM `supplier_details` WHERE `supplierID` = :supplierID");

        $stmt->bindParam(':supplierID', $supplierID);

        try {
            return $stmt->execute(); // Returns true if successful, false otherwise
        } catch (\PDOException $e) {
            // Handle any errors (optional logging or error reporting)
            error_log("Error deleting supplier: " . $e->getMessage());
            return false;
        }
    }

    public function getSupplierDetails(string $supplierID): ?array
    {
        $stmt = $this->dbh->prepare("SELECT * FROM supplier_details WHERE supplierID = :supplierID");
        $stmt->execute(['supplierID' => $supplierID]);

        $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $supplier ?: null; // Return null if no supplier is found
    }


    public function updateSupplier(string $supplierID, array $data): bool
{
    $stmt = $this->dbh->prepare("UPDATE `supplier_details` 
        SET `supplierName` = :supplierName, 
            `productCategories` = :productCategories,
            `products` = :products, 
            `address` = :address,
            `contactNo` = :contactNo,
            `email` = :email,
            `specialNotes` = :specialNotes
        WHERE `supplierID` = :supplierID");

    try {
        return $stmt->execute([
            'supplierID' => $supplierID,
            'supplierName' => $data["supplier-name"],
            'productCategories' => $data["product-categories"],
            'products' => $data["products"],
            'address' => $data["address"],
            'contactNo' => $data["contact-no"],
            'email' => $data["email"],
            'specialNotes' => $data["special-notes"],
        ]);
    } catch (\PDOException $e) {
        error_log("Error updating supplier: " . $e->getMessage());
        return false;
    }
}


}
