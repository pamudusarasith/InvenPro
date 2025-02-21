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
        branch_name
      FROM branch
    ';
    $stmt = self::$db->query($sql);
    return $stmt->fetchAll();
  }
}
