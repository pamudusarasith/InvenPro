<?php

namespace App\Models;

use App\Core\Model;

class ProductModel extends Model
{
  public function getMeasuringUnits(): array
  {
    $sql = 'SELECT * FROM unit';
    $stmt = self::$db->query($sql);
    return $stmt->fetchAll();
  }

  public function getProductById(int $id): array
  {
    $sql = '
      SELECT
        p.*,
        u.unit_name,
        u.unit_symbol,
        bp.reorder_level,
        bp.reorder_quantity
      FROM product p
      INNER JOIN unit u ON p.unit_id = u.id
      INNER JOIN branch_product bp ON p.id = bp.product_id
      WHERE deleted_at IS NULL
        AND p.id = ?
        AND bp.branch_id = ?
    ';
    $stmt = self::$db->query($sql, [$id, $_SESSION['user']['branch_id']]);
    $product = $stmt->fetch();
    $product['categories'] = $this->getCategoriesByProductId($id);
    $product['batches'] = $this->getBatchesByProductId($id);
    return $product;
  }

  public function getCategoriesByProductId(int $id): array
  {
    $sql = '
      SELECT
        c.id,
        c.category_name
      FROM category c
      INNER JOIN product_category pc ON c.id = pc.category_id
      WHERE pc.product_id = ?
    ';
    $stmt = self::$db->query($sql, [$id]);
    return $stmt->fetchAll();
  }

  public function getSamePriceBatches(int $productId, string $price): array
  {
    $sql = '
      SELECT
        *
      FROM product_batch
      WHERE deleted_at IS NULL
        AND branch_id = ?
        AND product_id = ?
        AND unit_price = ?
      ORDER BY expiry_date ASC
    ';
    $stmt = self::$db->query($sql, [$_SESSION['user']['branch_id'], $productId, $price]);
    return $stmt->fetchAll();
  }

  public function getBatchesByProductId(int $id): array
  {
    $sql = '
      SELECT
        *
      FROM product_batch
      WHERE deleted_at IS NULL AND product_id = ? AND branch_id = ?
      ORDER BY expiry_date ASC
    ';
    $stmt = self::$db->query($sql, [$id, $_SESSION['user']['branch_id']]);
    return $stmt->fetchAll();
  }

  public function searchProduct(string $query): array
  {
    $sql = '
      SELECT
        p.*,
        u.unit_name,
        u.unit_symbol
      FROM product p
      INNER JOIN unit u ON p.unit_id = u.id
      WHERE p.deleted_at IS NULL AND (
        p.product_name LIKE ? OR
        p.product_code LIKE ?)
    ';
    $stmt = self::$db->query($sql, ["%$query%", "%$query%"]);
    $products = $stmt->fetchAll();

    foreach ($products as $i => $product) {
      $products[$i]['batches'] = $this->getBatchesByProductId($product['id']);
      if (empty($products[$i]['batches'])) {
        unset($products[$i]);
      }
    }

    return array_values($products);
  }

  public function searchPOSProducts(string $query): array
  {
    $sql = '
      SELECT
        p.id,
        p.product_code,
        p.product_name,
        GROUP_CONCAT(DISTINCT pb.unit_price) AS prices
      FROM product p
      INNER JOIN product_batch pb ON p.id = pb.product_id
      WHERE p.deleted_at IS NULL
        AND pb.deleted_at IS NULL
        AND pb.branch_id = ?
        AND (
          p.product_name LIKE ? OR
          p.product_code LIKE ?
        )
      GROUP BY p.id
    ';

    $stmt = self::$db->query($sql, [$_SESSION['user']['branch_id'], "%$query%", "%$query%"]);
    $products = $stmt->fetchAll();

    foreach ($products as &$product) {
      $product['prices'] = array_map('floatval', explode(',', $product['prices']));
    }

    return $products;
  }

  public function getProductsByCategoryId(int $categoryId): array
  {
    $sql = '
      SELECT
        p.id,
        p.product_code,
        p.product_name,
        SUM(pb.current_quantity) AS quantity,
        IF(COUNT(DISTINCT pb.unit_price) > 1, "Multiple", pb.unit_price) AS price,
        (CASE
          WHEN SUM(pb.current_quantity) > bp.reorder_level THEN "In Stock"
          WHEN SUM(pb.current_quantity) > 0 THEN "Low Stock"
          ELSE "Out of Stock"
        END) AS status
      FROM product p
      INNER JOIN product_category pc ON p.id = pc.product_id
      INNER JOIN category c ON pc.category_id = c.id
      INNER JOIN branch_product bp ON p.id = bp.product_id
      LEFT JOIN product_batch pb ON p.id = pb.product_id AND pb.branch_id = bp.branch_id
      WHERE p.deleted_at IS NULL
        AND bp.branch_id = ?
        AND (pc.category_id = ? OR c.parent_id = ?)
      GROUP BY p.id
      LIMIT 10
    ';
    $stmt = self::$db->query($sql, [$_SESSION['user']['branch_id'], $categoryId, $categoryId]);
    return $stmt->fetchAll();
  }

  public function getCountByCategoryId(int $categoryId): int
  {
    $sql = '
      SELECT
        COUNT(DISTINCT p.id) AS count
      FROM product p
      INNER JOIN product_category pc ON p.id = pc.product_id
      INNER JOIN category c ON pc.category_id = c.id
      INNER JOIN branch_product bp ON p.id = bp.product_id
      WHERE p.deleted_at IS NULL
        AND bp.branch_id = ?
        AND (pc.category_id = ? OR c.parent_id = ?)
    ';
    $stmt = self::$db->query($sql, [$_SESSION['user']['branch_id'], $categoryId, $categoryId]);
    return $stmt->fetch()['count'];
  }

  public function createProduct(array $data): void
  {
    self::$db->beginTransaction();
    $sql = '
      INSERT INTO product (product_code, product_name, description, unit_id, image_path)
      VALUES (?, ?, ?, ?, ?)
    ';
    self::$db->query($sql, [
      $data['product_code'],
      $data['product_name'],
      $data['description'],
      $data['unit_id'],
      $data['image_path']
    ]);

    $productId = self::$db->lastInsertId();

    $sql = '
      INSERT INTO product_category (product_id, category_id)
      VALUES (?, ?)
    ';
    foreach ($data['categories'] as $categoryId) {
      self::$db->query($sql, [$productId, $categoryId]);
    }

    $sql = '
      INSERT INTO branch_product (branch_id, product_id, reorder_level, reorder_quantity)
      VALUES (?, ?, ?, ?)
    ';
    self::$db->query($sql, [$_SESSION['user']['branch_id'], $productId, $data['reorder_level'], $data['reorder_quantity']]);
    self::$db->commit();
  }

  public function updateProduct(int $id, array $data): void
  {
    self::$db->beginTransaction();
    $sql = '
      UPDATE product
      SET product_code = ?, product_name = ?, description = ?, unit_id = ?, image_path = ?
      WHERE id = ?
    ';
    self::$db->query($sql, [
      $data['product_code'],
      $data['product_name'],
      $data['description'],
      $data['unit_id'],
      $data['image_path'],
      $id
    ]);

    $sql = '
      DELETE FROM product_category
      WHERE product_id = ?
    ';
    self::$db->query($sql, [$id]);

    $sql = '
      INSERT INTO product_category (product_id, category_id)
      VALUES (?, ?)
    ';
    foreach ($data['categories'] as $categoryId) {
      self::$db->query($sql, [$id, $categoryId]);
    }

    $sql = '
      UPDATE branch_product
      SET reorder_level = ?, reorder_quantity = ?
      WHERE product_id = ? AND branch_id = ?
    ';
    self::$db->query($sql, [$data['reorder_level'], $data['reorder_quantity'], $id, $_SESSION['user']['branch_id']]);
    self::$db->commit();
  }

  public function deleteProduct(int $id): void
  {
    $sql = '
      UPDATE product
      SET deleted_at = NOW()
      WHERE id = ?
    ';
    self::$db->query($sql, [$id]);
  }
}
