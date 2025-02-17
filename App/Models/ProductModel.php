<?php

namespace App\Models;

use App\Core\Model;

class ProductModel extends Model
{
  public function getProductById(int $id): array
  {
    $sql = '
      SELECT
        p.*,
        u.unit_name,
        u.unit_symbol
      FROM product p
      INNER JOIN unit u ON p.unit_id = u.id
      WHERE p.id = ?
    ';
    $stmt = self::$db->query($sql, [$id]);
    $product = $stmt->fetch();
    $product['batches'] = $this->getBatchesByProductId($id);
    return $product;
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
}
