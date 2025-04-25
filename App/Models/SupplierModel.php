<?php

namespace App\Models;

use App\Core\Model;

class SupplierModel extends Model
{
  public function getSuppliers(int $page, int $itemsPerPage): array
  {
    $offset = ($page - 1) * $itemsPerPage;
    $sql = '
      SELECT
        s.id,
        s.supplier_name,
        s.contact_person,
        s.email,
        s.phone,
        b.branch_name
      FROM supplier s
      LEFT JOIN branch b ON s.branch_id = b.id
      WHERE s.deleted_at IS NULL
      ORDER BY s.id
      LIMIT ? OFFSET ?
    ';
    $stmt = self::$db->query($sql, [$itemsPerPage, $offset]);
    return $stmt->fetchAll();
  }

  public function getSuppliersCount(): int
  {
    $sql = 'SELECT COUNT(*) FROM supplier WHERE deleted_at IS NULL';
    $stmt = self::$db->query($sql);
    return $stmt->fetchColumn();
  }

  public function getSupplier(int $id): ?array
  {
    $sql = '
      SELECT
        s.id,
        s.supplier_name,
        s.contact_person,
        s.email,
        s.phone,
        b.branch_name,
        s.address
      FROM supplier s
      LEFT JOIN branch b ON s.branch_id = b.id
      WHERE s.id = ? AND s.deleted_at IS NULL
    ';
    $stmt = self::$db->query($sql, [$id]);
    return $stmt->fetch();
  }


  public function createSupplier(array $data): void
  {
    $sql = '
      INSERT INTO supplier (supplier_name, contact_person, email, phone, branch_id, address)
      VALUES (?, ?, ?, ?, ?, ?)
    ';
    self::$db->query($sql, [
      $data['supplier_name'],
      $data['contact_person'],
      $data['email'],
      $data['phone'],
      $data['branch_id'],
      $data['address'],
    ]);
  }

  public function updateSupplier(int $id, array $data): void
  {
    $sql = '
      UPDATE supplier
      SET supplier_name = ?, contact_person = ?, email = ?, phone = ?, branch_id = ?, address = ?
      WHERE id = ?
    ';
    self::$db->query($sql, [
      $data['supplier_name'],
      $data['contact_person'],
      $data['email'],
      $data['phone'],
      $data['branch_id'],
      $data['address'],
      $id,
    ]);
  }

  public function deleteSupplier(int $id): void
  {
    $sql = 'UPDATE supplier SET deleted_at = NOW() WHERE id = ?';
    self::$db->query($sql, [$id]);
  }



  public function searchSuppliers(string $query, int $page, int $itemsPerPage): array
  {
    $offset = ($page - 1) * $itemsPerPage;
    $sql = '
      SELECT
        s.id,
        s.supplier_name,
        s.contact_person,
        s.email,
        s.phone
      FROM supplier s
      WHERE s.deleted_at IS NULL
        AND s.branch_id = ?
        AND (s.supplier_name LIKE ? OR s.contact_person LIKE ? OR s.email LIKE ? OR s.phone LIKE ?)
      ORDER BY s.id
      LIMIT ? OFFSET ?
    ';
    $stmt = self::$db->query($sql, [
      $_SESSION['user']['branch_id'],
      "%$query%",
      "%$query%",
      "%$query%",
      "%$query%",
      $itemsPerPage,
      $offset,
    ]);
    return $stmt->fetchAll();
  }

  public function searchProducts(int $id, string $query, int $page, int $itemsPerPage): array
  {
    $offset = ($page - 1) * $itemsPerPage;
    $sql = '
      SELECT
        p.id,
        p.product_name,
        u.unit_symbol,
        u.is_int
      FROM supplier_product sp
      INNER JOIN product p ON sp.product_id = p.id
      INNER JOIN unit u ON p.unit_id = u.id
      WHERE p.deleted_at IS NULL
        AND sp.supplier_id = ?
        AND sp.branch_id = ?
        AND p.product_name LIKE ?
      ORDER BY p.id
      LIMIT ? OFFSET ?
    ';
    $stmt = self::$db->query($sql, [
      $id,
      $_SESSION['user']['branch_id'],
      "%$query%",
      $itemsPerPage,
      $offset,
    ]);
    return $stmt->fetchAll();
  }

 public function assignProduct(int $supplierId, array $data): void
{
  $sql = '
    INSERT INTO supplier_product (supplier_id, product_id, branch_id, is_preferred_supplier)
    VALUES (?, ?, ?, ?)
  ';

  self::$db->query($sql, [
    $supplierId,
    $data['product_id'],
    $_SESSION['user']['branch_id'] ?? null,
    1 // Hardcoded as preferred; adjust as needed
  ]);
}

  public function getActiveSuppliersCount(): int
  {
    if ($_SESSION['user']['branch_id'] == 1) {
      $sql = 'SELECT COUNT(*) FROM supplier WHERE deleted_at IS NULL';
      $stmt = self::$db->query($sql);
      return $stmt->fetchColumn();
    } else {
      $sql = 'SELECT COUNT(*) FROM supplier WHERE deleted_at IS NULL AND branch_id = ?';
      $stmt = self::$db->query($sql, [$_SESSION['user']['branch_id']]);
      return $stmt->fetchColumn();
    }
  }
  


public function getSupplierProducts(int $supplierId): array
{
    $sql = '
        SELECT
            sp.product_id,
            p.product_code,
            p.product_name,
            sp.is_preferred_supplier
        FROM product p
        INNER JOIN supplier_product sp ON sp.product_id = p.id
        WHERE sp.supplier_id = ? AND sp.branch_id = ?
    ';
    $stmt = self::$db->query($sql, [$supplierId, $_SESSION['user']['branch_id']]);
    return $stmt->fetchAll(); 
}

public function getOrderDetails(int $supplireID): array
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
      $stmt = self::$db->query($sql, [$supplireID, $_SESSION['user']['branch_id']]);
      return $stmt->fetch();

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
        $stmt = self::$db->query($sql, [$supplireID]);
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
        $stmt = self::$db->query($sql, [$supplireID, $item['product_id']]);
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

public function deleteAssignedProduct(int $productId, int $supplierId): void
{
    error_log("Deleting assigned product ID: " . $productId);

    $sql = '
    UPDATE supplier_product
    SET deleted_at = NOW()
    WHERE product_id = ? AND supplier_id = ? AND branch_id = ?
';
    self::$db->query($sql, [$productId, $supplierId, $_SESSION['user']['branch_id']]);
}


}

