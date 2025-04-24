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
  
}
