<?php

namespace App\Models;

use App\Core\Model;

class RoleModel extends Model
{
  public function getRoles(): array
  {
    $sql = '
      SELECT
        id,
        role_name
      FROM role
    ';
    $stmt = self::$db->query($sql);
    return $stmt->fetchAll();
  }
}
