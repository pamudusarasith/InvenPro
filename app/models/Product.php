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

    public function getAllCategories(): array
    {
        $stmt = $this->dbh->prepare("SELECT id, name FROM category");
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
            pd.*
        FROM
            (
            SELECT
                p.id,
                p.name,
                p.measure_unit,
                IF(
                    COUNT(pb.price) > 1,
                    \"Multiple\",
                    pb.price
                ) AS price,
                SUM(i.quantity) AS quantity
            FROM
                product p
            INNER JOIN inventory i ON
                p.id = i.product_id AND i.branch_id = :branch_id AND i.quantity > 0
            INNER JOIN product_batch pb ON
                p.id = pb.product_id AND i.batch_no = pb.batch_no
            GROUP BY
                p.id
        ) pd
        INNER JOIN product_category pc ON
            pd.id = pc.product_id AND pc.category_id = :category_id;");
        $stmt->execute(['branch_id' => $_SESSION["branch_id"], 'category_id' => $category_id]);
        return $stmt->fetchAll();
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
            pb.batch_no, i.quantity, pb.price, pb.manufacture_date, pb.expiry_date
        FROM
            (
            SELECT
                *
            FROM
                inventory
            WHERE
                product_id = :id AND branch_id = :branch_id
        ) i
        INNER JOIN product_batch pb ON
        pb.product_id = i.product_id AND pb.batch_no = i.batch_no");
        $stmt->execute(['id' => $id, 'branch_id' => $_SESSION["branch_id"]]);
        return $stmt->fetchAll();
    }

    public function getProductDetails(int $id): array
    {
        $stmt = $this->dbh->prepare("SELECT id, name, description, measure_unit, image FROM product WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($result["image"]);
        $result["image"] = "data:$mime;base64," . base64_encode($result["image"]);
        $result['categories'] = $this->getProductCategories($id);
        $result['batches'] = $this->getProductBatches($id);

        return $result;
    }
}
