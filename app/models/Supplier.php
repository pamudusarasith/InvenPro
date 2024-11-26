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
    $stmt = $this->dbh->prepare("DELETE FROM supplier_details WHERE supplierID = :supplierID");
    return $stmt->execute(['supplierID' => $supplierID]);
}


    public function getSupplierDetails(string $supplierID): ?array
    {
        $stmt = $this->dbh->prepare("SELECT * FROM supplier_details WHERE supplierID = :supplierID");
        $stmt->execute(['supplierID' => $supplierID]);

        $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $supplier ?: null; // Return null if no supplier is found
    }

}
