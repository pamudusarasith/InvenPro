<?php

namespace App\Models;

use App\Core\Model;

class ProductModel extends Model
{
  public function searchProduct(string $query): array
  {
    $sql = '
      SELECT
        p.id,
        p.product_code,
        p.product_name,
        u.unit_symbol
      FROM product p
      INNER JOIN unit u ON p.unit_id = u.id
      WHERE p.deleted_at IS NULL AND (
        p.product_name LIKE ? OR
        p.product_code LIKE ?)
    ';
    $stmt = self::$db->query($sql, ["%$query%", "%$query%"]);
    $products = $stmt->fetchAll();
    
    $sql = '
      SELECT
        pb.id,
        pb.batch_code,
        pb.manufactured_date,
        pb.expiry_date,
        pb.unit_price,
        pb.current_quantity
      FROM product_batch pb
      WHERE pb.deleted_at IS NULL AND pb.product_id = ? AND pb.branch_id = ?
      ORDER BY pb.expiry_date ASC
    ';
    foreach ($products as $i => $product) {
      $stmt = self::$db->query($sql, [$product['id'], $_SESSION['branch_id']]);
      $products[$i]['batches'] = $stmt->fetchAll();
      if (empty($products[$i]['batches'])) {
        unset($products[$i]);
      }
    }

    return array_values($products);
  }
}
