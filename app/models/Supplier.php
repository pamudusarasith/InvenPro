<?php

namespace App\Models;

use App;

class Supplier {
    private $dbh;

    public function __construct() {
        $this->dbh = App\DB::getConnection();
    }

    public function addSupplier(array $data): int {
        // Validate required fields
        $requiredFields = ['name', 'company_name', 'email', 'address'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Missing required field: $field");
            }
        }

        $stmt = $this->dbh->prepare("
            INSERT INTO supplier (
                name,
                company_name,
                contact_person,
                email,
                phone_number,
                address,
                payment_terms,
                credit_limit,
                rating,
                is_active
            ) VALUES (
                :name,
                :company_name,
                :contact_person,
                :email,
                :phone_number,
                :address,
                :payment_terms,
                :credit_limit,
                :rating,
                :is_active
            )
        ");

        $stmt->execute([
            'name' => $data['name'],
            'company_name' => $data['company_name'],
            'contact_person' => $data['contact_person'] ?? null,
            'email' => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'address' => $data['address'],
            'payment_terms' => $data['payment_terms'] ?? null,
            'credit_limit' => $data['credit_limit'] ?? null,
            'rating' => $data['rating'] ?? null,
            'is_active' => $data['is_active'] ?? 1
        ]);

        return (int)$this->dbh->lastInsertId();
    }

    public function updateSupplier(int $id, array $data): bool {
        $stmt = $this->dbh->prepare("
            UPDATE supplier 
            SET 
                name = :name,
                company_name = :company_name,
                contact_person = :contact_person,
                email = :email,
                phone_number = :phone_number,
                address = :address,
                payment_terms = :payment_terms,
                credit_limit = :credit_limit,
                rating = :rating,
                is_active = :is_active
            WHERE id = :id
        ");

        try {
            return $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'company_name' => $data['company_name'],
                'contact_person' => $data['contact_person'] ?? null,
                'email' => $data['email'],
                'phone_number' => $data['phone_number'] ?? null,
                'address' => $data['address'],
                'payment_terms' => $data['payment_terms'] ?? null,
                'credit_limit' => $data['credit_limit'] ?? null,
                'rating' => $data['rating'] ?? null,
                'is_active' => $data['is_active'] ?? 1
            ]);
        } catch (\PDOException $e) {
            error_log("Error updating supplier: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteSupplier(int $id): bool {
        // Soft delete by setting is_active to 0
        $stmt = $this->dbh->prepare("
            UPDATE supplier 
            SET is_active = 0 
            WHERE id = :id
        ");

        try {
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            error_log("Error deleting supplier: " . $e->getMessage());
            throw $e;
        }
    }

    public function getSupplierDetails(int $id): ?array {
        $stmt = $this->dbh->prepare("
            SELECT s.*,
                   (SELECT COUNT(*) FROM product_supplier WHERE supplier_id = s.id) as product_count,
                   (SELECT COUNT(*) FROM purchase_order WHERE supplier_id = s.id) as order_count
            FROM supplier s
            WHERE s.id = :id
        ");

        $stmt->execute(['id' => $id]);
        $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$supplier) {
            return null;
        }

        // Get associated products
        $stmt = $this->dbh->prepare("
            SELECT p.*, ps.supply_price, ps.lead_time_days, ps.minimum_order_quantity, 
                   ps.is_primary_supplier, ps.last_supply_date
            FROM product p
            JOIN product_supplier ps ON p.id = ps.product_id
            WHERE ps.supplier_id = :supplier_id
        ");
        $stmt->execute(['supplier_id' => $id]);
        $supplier['products'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $supplier;
    }

    public function getAllSuppliers(bool $activeOnly = true): array {
        $query = "SELECT * FROM supplier";
        if ($activeOnly) {
            $query .= " WHERE is_active = 1";
        }
        $query .= " ORDER BY name";

        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addProductToSupplier(int $supplierId, int $productId, array $data): bool {
        $stmt = $this->dbh->prepare("
            INSERT INTO product_supplier (
                product_id,
                supplier_id,
                supply_price,
                lead_time_days,
                minimum_order_quantity,
                is_primary_supplier
            ) VALUES (
                :product_id,
                :supplier_id,
                :supply_price,
                :lead_time_days,
                :minimum_order_quantity,
                :is_primary_supplier
            )
        ");

        try {
            return $stmt->execute([
                'product_id' => $productId,
                'supplier_id' => $supplierId,
                'supply_price' => $data['supply_price'],
                'lead_time_days' => $data['lead_time_days'] ?? null,
                'minimum_order_quantity' => $data['minimum_order_quantity'] ?? null,
                'is_primary_supplier' => $data['is_primary_supplier'] ?? 0
            ]);
        } catch (\PDOException $e) {
            error_log("Error adding product to supplier: " . $e->getMessage());
            throw $e;
        }
    }
}