<?php

namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
  public function findByEmail(string $email)
  {
    $sql = '
      SELECT
          u.*,
          r.role_name,
          b.branch_name
      FROM user u
      LEFT JOIN role r ON u.role_id = r.id
      LEFT JOIN branch b ON u.branch_id = b.id
      WHERE u.email = ?
      AND u.deleted_at IS NULL
    ';
    $stmt = self::$db->query($sql, [$email]);
    return $stmt->fetch();
  }
}
