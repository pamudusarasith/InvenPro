<?php

namespace App\Models;

use App\Core\Model;

class PermissionModel extends Model
{
  /**
   * Check if role has permission
   *
   * @param int $roleId
   * @param string $permissionName
   * @return bool
   */
  public function checkRolePermission(int $roleId, string $permissionName): bool
  {
    $sql = '
      SELECT COUNT(*)
      FROM role_permission rp
      JOIN permission p ON p.id = rp.permission_id
      WHERE rp.role_id = ? AND p.permission_name = ?
    ';
    $stmt = self::$db->query($sql, [$roleId, $permissionName]);
    return (bool) $stmt->fetchColumn();
  }
}
