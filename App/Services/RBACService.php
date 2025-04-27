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
    $roleId = $_SESSION['user']['role_id'] ?? -1;
    return self::getModel()->checkRolePermission($roleId, $permissionName);
  }

  /**
   * Check if user has required permission and redirect to error page if not
   *
   * @param string $permissionName Permission to check
   * @return void
   */
  public static function requirePermission(string $permissionName)
  {
    if (!self::hasPermission($permissionName)) {
      View::renderError(403);
    }
  }

  /**
   * Check if user is authenticated and redirect to login if not
   * @return void
   */
  public static function requireAuthentication()
  {
    if (!isset($_SESSION['user']['id']) || empty($_SESSION['user']['id'])) {
      View::redirect('/');
    }
  }
}
