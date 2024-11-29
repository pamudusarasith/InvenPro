<?php

namespace App\Models;

use App;
use App\Consts;

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

    public function createProduct(array $data)
    {
        try {
            $this->dbh->beginTransaction();

            // Insert product
            $query = "INSERT INTO product (
                branch_id, name, description, measure_unit, 
                weight, dimensions, shelf_life_days,
                storage_requirements, barcode, image, is_active
            ) VALUES (
                :branch_id, :name, :description, :measure_unit,
                :weight, :dimensions, :shelf_life_days,
                :storage_requirements, :barcode, null, 1
            )";

            $stmt = $this->dbh->prepare($query);
            $stmt->execute([
                'branch_id' => $_SESSION['branch_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'measure_unit' => $data['measure_unit'],
                'weight' => $data['weight'] ?? null,
                'dimensions' => $data['dimensions'] ?? null,
                'shelf_life_days' => $data['shelf_life_days'] ?? null,
                'storage_requirements' => $data['storage_requirements'] ?? null,
                'barcode' => $data['barcode'] ?? null
            ]);

            $productId = $this->dbh->lastInsertId();

            // Insert threshold
            $query = "INSERT INTO threshold (
                product_id, min_threshold, max_threshold,
                reorder_point, reorder_quantity, alert_email
            ) VALUES (
                :product_id, :min_threshold, :max_threshold,
                :reorder_point, :reorder_quantity, :alert_email
            )";

            $stmt = $this->dbh->prepare($query);
            $stmt->execute([
                'product_id' => $productId,
                'min_threshold' => $data['min_threshold'],
                'max_threshold' => $data['max_threshold'],
                'reorder_point' => $data['reorder_point'],
                'reorder_quantity' => $data['reorder_quantity'],
                'alert_email' => $data['alert_email'] ?? null
            ]);

            // Insert categories
            if (!empty($data['categories'])) {
                $query = "INSERT INTO product_category (product_id, category_id) 
                         VALUES (:product_id, :category_id)";
                $stmt = $this->dbh->prepare($query);

                foreach (explode(',', $data['categories']) as $categoryId) {
                    $stmt->execute([
                        'product_id' => $productId,
                        'category_id' => $categoryId
                    ]);
                }
            }

            $this->dbh->commit();
            header(Consts::HEADER_JSON);
            echo json_encode(['success' => true, 'data' => 'Product updated successfully']);
        } catch (\Exception $e) {
            $this->dbh->rollBack();
            header(Consts::HEADER_JSON);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function updateProduct(int $id, array $data)
    {
        try {
            $this->dbh->beginTransaction();
            // Update product
            $query = "UPDATE product SET 
                name = :name,
                description = :description,
                measure_unit = :measure_unit,
                weight = :weight,
                dimensions = :dimensions,
                shelf_life_days = :shelf_life_days,
                storage_requirements = :storage_requirements,
                barcode = :barcode" .
                " WHERE id = :id AND branch_id = :branch_id";

            $params = [
                'id' => $id,
                'branch_id' => $_SESSION['branch_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'measure_unit' => $data['measure_unit'],
                'weight' => $data['weight'] ?? null,
                'dimensions' => $data['dimensions'] ?? null,
                'shelf_life_days' => $data['shelf_life_days'] ?? null,
                'storage_requirements' => $data['storage_requirements'] ?? null,
                'barcode' => $data['barcode'] ?? null
            ];

            $stmt = $this->dbh->prepare($query);
            $stmt->execute($params);

            // Update threshold
            $query = "UPDATE threshold SET 
                min_threshold = :min_threshold,
                max_threshold = :max_threshold,
                reorder_point = :reorder_point,
                reorder_quantity = :reorder_quantity,
                alert_email = :alert_email
                WHERE product_id = :product_id";

            $stmt = $this->dbh->prepare($query);
            $stmt->execute([
                'product_id' => $id,
                'min_threshold' => $data['min_threshold'],
                'max_threshold' => $data['max_threshold'],
                'reorder_point' => $data['reorder_point'],
                'reorder_quantity' => $data['reorder_quantity'],
                'alert_email' => $data['alert_email'] ?? null
            ]);

            // Update categories
            $stmt = $this->dbh->prepare("DELETE FROM product_category WHERE product_id = ?");
            $stmt->execute([$id]);

            if (!empty($data['categories'])) {
                $categoryIds = [];
                $stmt = $this->dbh->prepare("SELECT id FROM category WHERE name IN (?)");
                foreach (explode(',', $data['categories']) as $categoryName) {
                    $stmt->execute([$categoryName]);
                    $categoryIds[] = $stmt->fetchColumn();
                }

                $query = "INSERT INTO product_category (product_id, category_id) 
                         VALUES (:product_id, :category_id)";
                $stmt = $this->dbh->prepare($query);

                foreach ($categoryIds as $categoryId) {
                    $stmt->execute([
                        'product_id' => $id,
                        'category_id' => $categoryId
                    ]);
                }
            }

            $this->dbh->commit();
            header(Consts::HEADER_JSON);
            echo json_encode(['success' => true, 'data' => 'Product updated successfully']);
        } catch (\Exception $e) {
            $this->dbh->rollBack();
            header(Consts::HEADER_JSON);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function deleteProduct(int $id)
    {
        try {
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("UPDATE product SET is_active = 0 WHERE id = ?");
            $stmt->execute([$id]);
            $this->dbh->commit();
            header(Consts::HEADER_JSON);
            echo json_encode(['success' => true, 'data' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            $this->dbh->rollBack();
            header(Consts::HEADER_JSON);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
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
