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

    public function getCategories(): array
    {
        $stmt = $this->dbh->prepare("SELECT name FROM category");
        $stmt->execute();
        $result = $stmt->fetchAll();
        return array_map(function ($category) {
            return $category["name"];
        }, $result);
    }

    public function getProductsByCategory(string $category): array
    {
        $stmt = $this->dbh->prepare("SELECT id FROM category WHERE name = :category");
        $stmt->execute(['category' => $category]);
        $result = $stmt->fetch();
        $id = $result["id"];

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
        $stmt->execute(['branch_id' => $_SESSION["branch_id"], 'category_id' => $id]);
        return $stmt->fetchAll();
    }
}
