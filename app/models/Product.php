<?php

namespace App\Models;

use App;

class Product
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = App\DB::getConnection();
    }

    public function getPrimaryCategories(): array
    {
        $stmt = $this->dbh->prepare("SELECT id, name FROM category WHERE parent_id IS NULL");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProductCategories(string $product_id): array
    {
        $stmt = $this->dbh->prepare("SELECT category_id FROM product_category WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        $category_ids = array_column($stmt->fetchAll(), 'category_id');
        $placeholders = implode(',', array_fill(0, count($category_ids), '?'));

        $stmt = $this->dbh->prepare("SELECT id, name FROM category WHERE id IN ($placeholders)");
        $stmt->execute($category_ids);
        return $stmt->fetchAll();
    }

    public function getProductsByCategory(string $category_id): array
    {
        $stmt = $this->dbh->prepare("
        SELECT
            p.id,
            p.name,
            p.description,
            p.measure_unit,
            p.weight,
            p.dimensions,
            p.barcode,
            p.is_active,
            SUM(i.quantity) as quantity,
            IF(
                COUNT(DISTINCT i.selling_price) > 1,
                'Multiple',
                i.selling_price
            ) as selling_price,
            GROUP_CONCAT(DISTINCT c.name) as categories,
            -- Add threshold details
            t.min_threshold,
            t.max_threshold,
            t.reorder_point,
            t.reorder_quantity
        FROM
            product p
            LEFT JOIN inventory i ON p.id = i.product_id
            LEFT JOIN product_category pc ON p.id = pc.product_id
            LEFT JOIN category c ON pc.category_id = c.id
            LEFT JOIN threshold t ON p.id = t.product_id
        WHERE
            p.is_active = 1
            AND p.branch_id = :branch_id
            AND pc.category_id = :category_id
        GROUP BY
            p.id
        ORDER BY
            p.name;");
        $stmt->execute(['branch_id' => $_SESSION["branch_id"], 'category_id' => $category_id]);
        $result = $stmt->fetchAll();
        foreach ($result as &$product) {
            $product['quantity'] = is_numeric($product['quantity']) ? (int) $product['quantity'] : 0;
        }
        return $result;
    }

    public function addProduct(array $product): void
    {
        $stmt = $this->dbh->prepare("INSERT INTO product (id, name, description, measure_unit) VALUES (:id, :name, :description, :unit)");
        $stmt->execute([
            'id' => $product["id"],
            'name' => $product["name"],
            'description' => $product["description"],
            'unit' => $product["unit"]
        ]);
    }

    public function search(string $query): array
    {
        $result = [];
        $stmt = $this->dbh->prepare("SELECT id, name FROM product WHERE id LIKE :query");
        $stmt->execute(['query' => "%$query%"]);
        $result = array_merge($result, $stmt->fetchAll());

        $stmt = $this->dbh->prepare("SELECT id, name FROM product WHERE name LIKE :query");
        $stmt->execute(['query' => "%$query%"]);
        $result = array_merge($result, $stmt->fetchAll());

        return $result;
    }

    public function posSearch(string $query): array
    {
        $result = [];
        $stmt = $this->dbh->prepare("SELECT id FROM product WHERE id LIKE :query");
        $stmt->execute(['query' => "%$query%"]);
        $rows = $stmt->fetchAll();
        $tmp = [];
        foreach ($rows as $row) {
            $tmp[] = $this->getProductDetails($row["id"]);
        }
        $result = array_merge($result, $tmp);

        $stmt = $this->dbh->prepare("SELECT id FROM product WHERE name LIKE :query");
        $stmt->execute(['query' => "%$query%"]);
        $rows = $stmt->fetchAll();
        $tmp = [];
        foreach ($rows as $row) {
            $tmp[] = $this->getProductDetails($row["id"]);
        }
        $result = array_merge($result, $tmp);

        return $result;
    }

    public function getProductBatches(int $id): array
    {
        $stmt = $this->dbh->prepare("
        SELECT
            pb.batch_no, i.quantity, pb.selling_price, pb.manufacture_date, pb.expiry_date
        FROM
            (
            SELECT
                *
            FROM
                inventory
            WHERE
                product_id = :id AND branch_id = :branch_id
        ) i
        INNER JOIN inventory pb ON
        pb.product_id = i.product_id AND pb.batch_no = i.batch_no");
        $stmt->execute(['id' => $id, 'branch_id' => $_SESSION["branch_id"]]);
        return $stmt->fetchAll();
    }

    public function getProductDetails(int $id): array
    {
        $stmt = $this->dbh->prepare("
        SELECT
            p.id,
            p.name,
            p.description,
            p.measure_unit,
            p.weight,
            p.dimensions,
            p.shelf_life_days,
            p.storage_requirements,
            p.barcode,
            p.image,
            p.is_active,
            GROUP_CONCAT(DISTINCT c.name) as categories,
            -- Add threshold details
            t.min_threshold,
            t.max_threshold,
            t.reorder_point,
            t.reorder_quantity,
            t.alert_email
        FROM
            product p
            LEFT JOIN product_category pc ON p.id = pc.product_id
            LEFT JOIN category c ON pc.category_id = c.id
            LEFT JOIN threshold t ON p.id = t.product_id
        WHERE
            p.is_active = 1
            AND p.branch_id = :branch_id
            AND p.id = :id;");
        $stmt->execute(['id' => $id, 'branch_id' => $_SESSION["branch_id"]]);
        $result = $stmt->fetch();
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($result["image"]);
        $result["image"] = "data:$mime;base64," . base64_encode($result["image"]);
        // $result['batches'] = $this->getProductBatches($id);

        return $result;
    }
}
