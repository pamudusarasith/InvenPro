<?php

namespace App\Models;

use App\Core\Model;

class BranchModel extends Model
{
  public function getBranches(): array
  {
    $sql = '
      SELECT
        id,
        branch_code,
        branch_name,
        address,
        phone,
        email
      FROM branch
    ';
    $stmt = self::$db->query($sql);
    return $stmt->fetchAll();
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

  
}
