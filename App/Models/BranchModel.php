<?php

namespace App\Models;

use App\Core\Model;

class BranchModel extends Model
{
  
  public function getBranches(int $page, int $itemsPerPage, ?string $search = '', ?string $status = ''): array
{
    $offset = ($page - 1) * $itemsPerPage;
    $sql = '
        SELECT
            b.id,
            b.branch_code,
            b.branch_name,
            b.address,
            b.phone,
            b.email,
            IF(b.deleted_at IS NULL, "Active", "Inactive") AS status
        FROM branch b
        WHERE 1 = 1
    ';
    $params = [];

    // Add search filter
    if ($search) {
        $sql .= ' AND (b.branch_name LIKE ? OR b.branch_code LIKE ? OR b.email LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    // Add status filter
    if ($status) {
        if ($status === 'active') {
            $sql .= ' AND b.deleted_at IS NULL';
        } elseif ($status === 'inactive') {
            $sql .= ' AND b.deleted_at IS NOT NULL';
        }
    }

    $sql .= ' ORDER BY b.id LIMIT ? OFFSET ?';
    $params[] = $itemsPerPage;
    $params[] = $offset;

    $stmt = self::$db->query($sql, $params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}


public function getBranchesCount(?string $search = '', ?string $status = ''): int
{
    $sql = 'SELECT COUNT(*) FROM branch WHERE 1 = 1';
    $params = [];

    // Add search filter
    if ($search) {
        $sql .= ' AND (branch_name LIKE ? OR branch_code LIKE ? OR email LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    // Add status filter
    if ($status) {
        if ($status === 'active') {
            $sql .= ' AND deleted_at IS NULL';
        } elseif ($status === 'inactive') {
            $sql .= ' AND deleted_at IS NOT NULL';
        }
    }

    $stmt = self::$db->query($sql, $params);
    return (int) $stmt->fetchColumn();
}


  public function createBranch(array $data): void
  {
    $sql = '
      INSERT INTO branch (branch_code, branch_name, address, phone, email)
      VALUES (?, ?, ?, ?, ?)
    ';
    self::$db->query($sql, [
      $data['branch_code'],
      $data['branch_name'],
      $data['address'],
      $data['phone'],
      $data['email'],
    ]);
  }

  public function updateBranch(array $data)
{
    $sql = '
      UPDATE Branch
      SET
        branch_code = ?,
        branch_name = ?,
        phone = ?,
        email = ?,
        address = ?
      WHERE id = ?  
    ';

    self::$db->query($sql, [
      $data['branch_code'],
      $data['branch_name'],
      $data['phone'],
      $data['email'],
      $data['address'],
      $data['id']
    ]);
}

public function updateDeletedAt($branchId, $deletedAt)
{
    $sql = 'UPDATE Branch SET deleted_at = ? WHERE id = ?';
    self::$db->query($sql, [$deletedAt, $branchId]);
}


public function searchBranches(string $query, int $page, int $itemsPerPage): array
{
    $offset = ($page - 1) * $itemsPerPage;
    $sql = '
        SELECT
            b.id,
            b.branch_code,
            b.branch_name,
            b.address,
            b.phone,
            b.email
        FROM branch b
        WHERE b.deleted_at IS NULL
          AND (b.branch_name LIKE ? OR b.branch_code LIKE ? OR b.email LIKE ?)
        ORDER BY b.id
        LIMIT ? OFFSET ?
    ';
    $stmt = self::$db->query($sql, [
        "%$query%",
        "%$query%",
        "%$query%",
        $itemsPerPage,
        $offset,
    ]);
    return $stmt->fetchAll();
}
  
}
