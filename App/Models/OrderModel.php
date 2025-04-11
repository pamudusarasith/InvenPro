<?php

namespace App\Models;

use App\Core\Model;

class OrderModel extends Model
{
  public function getOrders($page, $itemsPerPage)
  {
    $offset = ($page - 1) * $itemsPerPage;
    $sql = '
      SELECT
        po.id,
        po.reference,
        s.supplier_name,
        po.order_date,
        po.status,
        po.total_amount,
        COUNT(*) AS items
      FROM purchase_order po
      INNER JOIN supplier s ON po.supplier_id = s.id
      INNER JOIN purchase_order_item poi ON po.id = poi.po_id
      WHERE po.deleted_at IS NULL
        AND po.branch_id = ?
      GROUP BY po.id
      ORDER BY po.id DESC
      LIMIT ? OFFSET ?
    ';
    $stmt = self::$db->query($sql, [$_SESSION['user']['branch_id'], $itemsPerPage, $offset]);
    return $stmt->fetchAll();
  }

  public function createOrder(array $data): void
  {
    self::$db->beginTransaction();
    $sql = '
      INSERT INTO purchase_order (reference, branch_id, supplier_id, order_date, expected_date, notes, created_by)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    ';

    self::$db->query($sql, [
      $data['reference'] ?: null,
      $_SESSION['user']['branch_id'] ?: null,
      $data['supplier_id'] ?: null,
      $data['order_date'] ?: null,
      $data['expected_date'] ?: null,
      $data['notes'] ?: null,
      $_SESSION['user']['id'] ?: null,
    ]);
    $orderId = self::$db->lastInsertId();

    $sql = '
      INSERT INTO purchase_order_item (po_id, product_id, order_qty)
      VALUES (?, ?, ?)
    ';

    foreach ($data['order_items'] as $item) {
      self::$db->query($sql, [
        $orderId ?: null,
        $item['id'] ?: null,
        $item['quantity'] ?: null,
      ]);
    }

    self::$db->commit();
  }
}
