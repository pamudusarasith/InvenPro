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

  /**
     * Get permission categories
     * @return array
     */
    public function getAllPermissionCategories(): array
    {
        $sql = 'SELECT id, category_name FROM permission_category ORDER BY category_name';
        $stmt = self::$db->query($sql);
        $categories = $stmt->fetchAll();

        $result = [];
        foreach ($categories as $category) {
            $result[$category['id']] = $category['category_name'];
        }

        return $result;
    }

  /**
   * Get all permissions by category
   * @return array
   */

  public function getAllPermissionsByCategory(): array
  {

    $sql = '
    SELECT pc.id AS category_id, pc.category_name, p.id, p.permission_name, p.description
    FROM permission_category pc
    JOIN permission p ON pc.id = p.category_id
    ORDER BY pc.category_name, p.permission_name';

    $stmt = self::$db->query($sql);
    $permissions = $stmt->fetchAll();

    $result = [];
    foreach ($permissions as $permission) {
      $result[$permission['category_name']][] = [
        'id' => $permission['id'],
        'permission_name' => $permission['permission_name'],
        'description' => $permission['description'],
        'category_id' => $permission['category_id']
      ];
    }

    return $result;
  }

  /**
  * Count valid permissions by their IDs.
   *
   * @param array $permissionIds
   * @return int
   */
  public function countValidPermissions(array $permissionIds): int
  {
    if (empty($permissionIds)) {
      return 0;
    }
    $placeholders = implode(',', array_fill(0, count($permissionIds), '?'));
    $sql = "SELECT COUNT(*) as count FROM permission WHERE id IN ($placeholders)";
    $result = self::$db->query($sql, $permissionIds); // Use self::$db
    return (int) $result->fetchColumn() ?? 0;
  }

  /**
   * Get permission by role_ID
   * @param int $id
   * @return array|null
   */
  public function getPermissionsByRoleId(int $id): ?array
  {
    $sql = 'SELECT p.id, p.permission_name FROM role_permission rp JOIN permission p ON rp.permission_id = p.id WHERE rp.role_id = ?';
    $stmt = self::$db->query($sql, [$id]);
    return $stmt->fetchAll() ?: null;
  }
}
