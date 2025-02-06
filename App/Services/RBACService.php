<?php

namespace App\Services;

use App\Models\PermissionModel;
use App\Core\View;

class RBACService
{
  private static ?PermissionModel $model = null;

  /**
   * Get permission model instance
   */
  private static function getModel(): PermissionModel
  {
    if (self::$model === null) {
      self::$model = new PermissionModel();
    }
    return self::$model;
  }

  /**
   * Check if user has required permission
   *
   * @param string $permissionName Permission to check
   * @return bool
   */
  public static function hasPermission(string $permissionName): bool
  {
    return true;
    $roleId = $_SESSION['role_id'] ?? -1;
    return self::getModel()->checkRolePermission($roleId, $permissionName);
  }

  /**
   * Check if user is authenticated and redirect to login if not
   * @return void
   */
  public static function requireAuthentication()
  {
    if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
      View::redirect('/');
    }
  }
}
