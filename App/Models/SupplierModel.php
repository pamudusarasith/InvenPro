<?php

namespace App\Models;

use App\Core\Model;

class SupplierModel extends Model
{
  
  
  public function getSuppliers(int $page, int $itemsPerPage, ?string $search = '', ?string $branchId = null, ?string $status = ''): array
    {
        $offset = ($page - 1) * $itemsPerPage;
        $sql = '
            SELECT
                s.id,
                s.supplier_name,
                s.contact_person,
                s.email,
                s.phone,
                b.branch_name,
                IF(s.deleted_at IS NULL, "Active", "Inactive") AS status
            FROM supplier s
            LEFT JOIN branch b ON s.branch_id = b.id
            WHERE s.deleted_at IS NULL
        ';
        $params = [];

        // Add search filter
        if ($search) {
            $sql .= ' AND (s.supplier_name LIKE ? OR s.contact_person LIKE ? OR s.email LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Add branch filter
        if ($branchId) {
            $sql .= ' AND s.branch_id = ?';
            $params[] = $branchId;
        }

        // Add status filter
        if ($status) {
            if ($status === 'active') {
                $sql .= ' AND s.deleted_at IS NULL';
            } elseif ($status === 'inactive') {
                $sql .= ' AND s.deleted_at IS NOT NULL';
            }
        }

        $sql .= ' ORDER BY s.id LIMIT ? OFFSET ?';
        $params[] = $itemsPerPage;
        $params[] = $offset;

        $stmt = self::$db->query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


  public function getSuppliersCount(): int
  {
    $sql = 'SELECT COUNT(*) FROM supplier WHERE deleted_at IS NULL';
    $stmt = self::$db->query($sql);
    $params = [];
    return $stmt->fetchColumn();

    // Add search filter
    if ($search) {
      $sql .= ' AND (s.supplier_name LIKE ? OR s.contact_person LIKE ? OR s.email LIKE ?)';
      $params[] = "%$search%";
      $params[] = "%$search%";
      $params[] = "%$search%";
  }

  // Add branch filter
  if ($branchId) {
      $sql .= ' AND s.branch_id = ?';
      $params[] = $branchId;
  }

  // Add status filter
  if ($status) {
      if ($status === 'active') {
          $sql .= ' AND s.deleted_at IS NULL';
      } elseif ($status === 'inactive') {
          $sql .= ' AND s.deleted_at IS NOT NULL';
      }
  }

  $stmt = self::$db->query($sql, $params);
  return (int) $stmt->fetchColumn();
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
          po.order_date,
          po.status,
          po.total_amount,
          COUNT(DISTINCT poi.product_id) AS total_items
        FROM purchase_order po
        INNER JOIN supplier s ON po.supplier_id = s.id
        INNER JOIN purchase_order_item poi ON po.id = poi.po_id
        WHERE s.id = ?
          AND po.deleted_at IS NULL
          AND po.branch_id = ?
        GROUP BY po.id
        ORDER BY po.order_date DESC
      ';
      $stmt = self::$db->query($sql, [$supplireID, $_SESSION['user']['branch_id']]);
      $orders = $stmt->fetchAll();
      error_log(print_r($orders, true)); // Log the orders for debugging
      self::$db->commit();
      return $orders ?: [];
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
    DELETE FROM supplier_product
    WHERE product_id = ? AND supplier_id = ? AND branch_id = ?
';
    self::$db->query($sql, [$productId, $supplierId, $_SESSION['user']['branch_id']]);
}


public function getSupplierStats(int $supplierId): array
{
    // Fetch active products count
    $sqlActiveProducts = '
        SELECT COUNT(*) AS active_products
        FROM supplier_product sp
        INNER JOIN product p ON sp.product_id = p.id
        WHERE sp.supplier_id = ? AND p.deleted_at IS NULL
    ';
    $activeProducts = self::$db->query($sqlActiveProducts, [$supplierId])->fetchColumn();

    // Fetch total orders count
    $sqlTotalOrders = '
        SELECT COUNT(*) AS total_orders
        FROM purchase_order
        WHERE supplier_id = ? AND deleted_at IS NULL
    ';
    $totalOrders = self::$db->query($sqlTotalOrders, [$supplierId])->fetchColumn();

    // Fetch total spend
    $sqlTotalSpend = '
        SELECT SUM(total_amount) AS total_spend
        FROM purchase_order
        WHERE supplier_id = ? AND deleted_at IS NULL
    ';
    $totalSpend = self::$db->query($sqlTotalSpend, [$supplierId])->fetchColumn();

    // Fetch last order date
    $sqlLastOrder = '
        SELECT MAX(order_date) AS last_order
        FROM purchase_order
        WHERE supplier_id = ? AND deleted_at IS NULL
    ';
    $lastOrder = self::$db->query($sqlLastOrder, [$supplierId])->fetchColumn();

    return [
        'active_products' => (int) $activeProducts,
        'total_orders' => (int) $totalOrders,
        'total_spend' => (float) $totalSpend,
        'last_order' => $lastOrder ? date('Y-m-d', strtotime($lastOrder)) : null,
    ];
}

}

