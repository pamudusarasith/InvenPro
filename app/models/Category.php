<?php

namespace App\Models;

use App;

class Category
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

    public function search(string $query): array
    {
        $result = [];

        $stmt = $this->dbh->prepare("SELECT id, name FROM category WHERE name LIKE :query");
        $stmt->execute(['query' => "%$query%"]);
        $result = array_merge($result, $stmt->fetchAll());

        return $result;
    }
}
