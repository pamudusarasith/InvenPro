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

  public function getBatchById(int $id): array
  {
    $sql = '
      SELECT
        *
      FROM product_batch
      WHERE id = ?
    ';
    $stmt = self::$db->query($sql, [$id]);
    return $stmt->fetch();
  }

  public function getSamePriceBatches(int $batchId): array
  {
    $sql = '
      SELECT
        *
      FROM product_batch
      WHERE deleted_at IS NULL
        AND branch_id = ?
        AND product_id = (SELECT product_id FROM product_batch WHERE id = ?)
        AND unit_price = (SELECT unit_price FROM product_batch WHERE id = ?)
      ORDER BY expiry_date ASC
    ';
    $stmt = self::$db->query($sql, [$_SESSION['user']['branch_id'], $batchId, $batchId]);
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

  public function searchPOSProduct(string $query): array
  {
    $products = $this->searchProduct($query);
    $products = self::filterFields(
      $products,
      ['id', 'product_code', 'product_name', 'unit_symbol', 'batches']
    );

    foreach ($products as &$product) {
      $product['batches'] = self::filterFields(
        $product['batches'],
        ['id', 'batch_code', 'unit_price']
      );

      $prices = [];
      foreach ($product['batches'] as $i => $batch) {
        if (isset($prices[$batch['unit_price']])) {
          unset($product['batches'][$i]);
        } else {
          $prices[$batch['unit_price']] = true;
        }
      }
      $product['batches'] = array_values($product['batches']);
    }

    return $products;
  }
}
