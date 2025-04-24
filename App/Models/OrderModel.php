<?php

namespace App\Models;

use App\Core\Model;

class OrderModel extends Model
{
  public function getOrders(int $page, int $itemsPerPage, $query, $status, $from, $to): array
  {
    $whereClauses = [];
    $params = [$_SESSION['user']['branch_id']];

    if ($query) {
      $whereClauses[] = '(po.reference LIKE ? OR s.supplier_name LIKE ?)';
      $params[] = "%$query%";
      $params[] = "%$query%";
    }

    if ($status) {
      $whereClauses[] = 'po.status = ?';
      $params[] = $status;
    }

    if ($from) {
      $whereClauses[] = 'po.order_date >= ?';
      $params[] = $from;
    }

    if ($to) {
      $whereClauses[] = 'po.order_date <= ?';
      $params[] = $to;
    }

    $whereClause = implode(' AND ', $whereClauses);
    if ($whereClause) {
      $whereClause = 'AND ' . $whereClause;
    }

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
      LEFT JOIN purchase_order_item poi ON po.id = poi.po_id
      WHERE po.deleted_at IS NULL
        AND po.branch_id = ?
      ' . $whereClause . '
      GROUP BY po.id
      ORDER BY po.id DESC
      LIMIT ? OFFSET ?
    ';
    array_push($params, $itemsPerPage, $offset);

    $stmt = self::$db->query($sql, $params);
    return $stmt->fetchAll();
  }

  public function getOrdersCount($query, $status, $from, $to): int
  {
    $whereClauses = [];
    $params = [$_SESSION['user']['branch_id']];

    if ($query) {
      $whereClauses[] = '(po.reference LIKE ? OR s.supplier_name LIKE ?)';
      $params[] = "%$query%";
      $params[] = "%$query%";
    }

    if ($status) {
      $whereClauses[] = 'po.status = ?';
      $params[] = $status;
    }

    if ($from) {
      $whereClauses[] = 'po.order_date >= ?';
      $params[] = $from;
    }

    if ($to) {
      $whereClauses[] = 'po.order_date <= ?';
      $params[] = $to;
    }

    $whereClause = implode(' AND ', $whereClauses);
    if ($whereClause) {
      $whereClause = 'AND ' . $whereClause;
    }

    $sql = '
      SELECT COUNT(*) AS total
      FROM purchase_order po
      INNER JOIN supplier s ON po.supplier_id = s.id
      WHERE po.deleted_at IS NULL
      AND po.branch_id = ?
      ' . $whereClause;

    $stmt = self::$db->query($sql, $params);
    return (int) $stmt->fetchColumn();
  }

  public function getOrderDetails(int $orderId): array
  {
    try {
      self::$db->beginTransaction();

      // Fetch order details
      $sql = '
        SELECT
          po.id,
          po.reference,
          po.supplier_id,
          s.supplier_name,
          po.order_date,
          po.expected_date,
          po.status,
          po.total_amount,
          po.notes,
          u.display_name AS created_by
        FROM purchase_order po
        INNER JOIN supplier s ON po.supplier_id = s.id
        INNER JOIN user u ON po.created_by = u.id
        WHERE po.id = ?
          AND po.deleted_at IS NULL
          AND po.branch_id = ?
      ';
      $stmt = self::$db->query($sql, [$orderId, $_SESSION['user']['branch_id']]);
      $order = $stmt->fetch();

      if ($order) {
        // Fetch order items
        $sql = '
          SELECT
            poi.product_id,
            p.product_name,
            poi.order_qty,
            poi.received_qty,
            unit.unit_symbol,
            unit.is_int
          FROM purchase_order_item poi
          INNER JOIN product p ON poi.product_id = p.id
          INNER JOIN unit ON p.unit_id = unit.id
          WHERE poi.po_id = ?
        ';
        $stmt = self::$db->query($sql, [$orderId]);
        $order['items'] = $stmt->fetchAll();
      }

      $sql = '
        SELECT
          id,
          batch_code,
          manufactured_date,
          expiry_date,
          unit_cost,
          unit_price,
          initial_quantity as quantity
        FROM product_batch
        WHERE deleted_at IS NULL
          AND po_id = ?
          AND product_id = ?
      ';

      foreach ($order['items'] as &$item) {
        $stmt = self::$db->query($sql, [$orderId, $item['product_id']]);
        $item['batches'] = $stmt->fetchAll();
      }

      self::$db->commit();
      return $order ?: [];
    } catch (\Exception $e) {
      self::$db->rollBack();
      // Log the exception or handle it as needed
      throw $e;
    }
  }

  public function createOrder(array $data): void
  {
    try {
      self::$db->beginTransaction();

      // Insert order details
      $sql = '
        INSERT INTO purchase_order (reference, branch_id, supplier_id, order_date, expected_date, notes, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
      ';
      self::$db->query($sql, [
        $data['reference'] ?? null,
        $_SESSION['user']['branch_id'] ?? null,
        $data['supplier_id'] ?? null,
        $data['order_date'] ?? null,
        $data['expected_date'] ?? null,
        $data['notes'] ?? null,
        $_SESSION['user']['id'] ?? null,
      ]);
      $orderId = self::$db->lastInsertId();

      // Insert order items
      $sql = '
        INSERT INTO purchase_order_item (po_id, product_id, order_qty)
        VALUES (?, ?, ?)
      ';
      foreach ($data['items'] as $item) {
        self::$db->query($sql, [
          $orderId,
          $item['id'] ?? null,
          $item['quantity'] ?? null,
        ]);
      }

      self::$db->commit();
    } catch (\Exception $e) {
      self::$db->rollBack();
      // Log the exception or handle it as needed
      throw $e;
    }
  }

  public function updateOrder(int $orderId, array $data): void
  {
    try {
      self::$db->beginTransaction();

      // Update order details
      $sql = '
        UPDATE purchase_order
        SET expected_date = ?, notes = ?
        WHERE id = ? AND branch_id = ?
      ';
      self::$db->query($sql, [
        $data['expected_date'] ?? null,
        $data['notes'] ?? null,
        $orderId,
        $_SESSION['user']['branch_id'] ?? null,
      ]);

      // Insert or update items
      $sql = '
        INSERT INTO purchase_order_item (po_id, product_id, order_qty)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE order_qty = ?
      ';
      foreach ($data['items'] as $item) {
        self::$db->query($sql, [
          $orderId,
          $item['id'] ?? null,
          $item['quantity'] ?? null,
          $item['quantity'] ?? null,
        ]);
      }

      // Delete items that are not in the new list
      $sql = '
        DELETE FROM purchase_order_item
        WHERE po_id = ? AND product_id NOT IN (?)
      ';
      $placeholders = implode(',', array_fill(0, count($data['items']), '?'));
      $sql = str_replace('(?)', "($placeholders)", $sql);
      $params = array_merge([$orderId], array_column($data['items'], 'id'));
      self::$db->query($sql, $params);

      self::$db->commit();
    } catch (\Exception $e) {
      self::$db->rollBack();
      // Log the exception or handle it as needed
      throw $e;
    }
  }

  public function deleteOrder(int $orderId): void
  {
    $sql = '
      UPDATE purchase_order
      SET deleted_at = NOW()
      WHERE id = ?
    ';
    self::$db->query($sql, [$orderId]);
  }

  public function changeOrderStatus(int $orderId, string $status): void
  {
    $sql = '
      UPDATE purchase_order
      SET status = ?
      WHERE id = ?
    ';
    self::$db->query($sql, [$status, $orderId]);
  }

  public function approveOrder(int $orderId)
  {
    $sql = '
      UPDATE purchase_order
      SET status = ?, order_date = NOW()
      WHERE id = ?
    ';
    self::$db->query($sql, ['open', $orderId]);
  }

  public function receiveOrderItems(int $orderId, array $data): void
  {
    try {
      self::$db->beginTransaction();

      $sql = '
        DELETE FROM product_batch
        WHERE po_id = ?
      ';
      self::$db->query($sql, [$orderId]);

      $sql = '
        INSERT INTO product_batch (
          product_id, branch_id, po_id, batch_code, manufactured_date,
          expiry_date, unit_cost, unit_price, initial_quantity,
          current_quantity, is_active
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      ';
      foreach ($data['batches'] as $batch) {
        self::$db->query($sql, [
          $batch['product_id'] ?? null,
          $_SESSION['user']['branch_id'] ?? null,
          $orderId,
          $batch['batch_code'] ?? null,
          $batch['manufactured_date'] ?? null,
          $batch['expiry_date'] ?? null,
          $batch['unit_cost'] ?? null,
          $batch['unit_price'] ?? null,
          $batch['received_qty'] ?? null,
          $batch['received_qty'] ?? null,
          0
        ]);
      }

      // Calculate total received quantities by product
      $productQuantities = [];
      foreach ($data['batches'] as $batch) {
        $productId = $batch['product_id'];
        if (!isset($productQuantities[$productId])) {
          $productQuantities[$productId] = 0;
        }
        $productQuantities[$productId] += $batch['received_qty'];
      }

      // Update received quantities in purchase_order_item
      $updateSql = '
        UPDATE purchase_order_item
        SET received_qty = ?
        WHERE po_id = ? AND product_id = ?
      ';

      foreach ($productQuantities as $productId => $receivedQty) {
        self::$db->query($updateSql, [$receivedQty, $orderId, $productId]);
      }

      self::$db->commit();
    } catch (\Exception $e) {
      self::$db->rollBack();
      // Log the exception or handle it as needed
      throw $e;
    }
  }

  public function completeOrder(int $orderId): void
  {
    try {
      self::$db->beginTransaction();

      // Update order status to 'completed'
      $sql = '
        UPDATE purchase_order
        SET status = ?, total_amount = (SELECT SUM(unit_cost * initial_quantity) FROM product_batch WHERE po_id = ?)
        WHERE id = ?
      ';
      self::$db->query($sql, ['completed', $orderId, $orderId]);

      // Update product batches to active
      $sql = '
        UPDATE product_batch
        SET is_active = 1
        WHERE po_id = ?
      ';
      self::$db->query($sql, [$orderId]);

      self::$db->commit();
    } catch (\Exception $e) {
      self::$db->rollBack();
      // Log the exception or handle it as needed
      throw $e;
    }
  }

  public function getPendingAndOpenOrdersCount(): array
  {
    $params = ['pending', 'open'];
    $branchCondition = '';

    if ($_SESSION['user']['branch_id'] != 1) {
      $branchCondition = ' AND branch_id = ?';
      $params[] = $_SESSION['user']['branch_id'];
    }

    $sql = '
      SELECT 
        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS open_count
      FROM purchase_order
      WHERE deleted_at IS NULL' . $branchCondition;

    $result = self::$db->query($sql, $params)->fetch();
    return [
      'pending' => (int) $result['pending_count'],
      'open' => (int) $result['open_count']
    ];
  }
}
