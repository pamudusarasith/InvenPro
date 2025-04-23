<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\{RoleModel, PermissionModel, UserModel};

class RoleController extends Controller
{
  private $roleModel;
  private $permissionModel;
  private $userModel;

  public function __construct()
  {
    $this->roleModel = new RoleModel();
    $this->permissionModel = new PermissionModel();
    $this->userModel = new UserModel();
  }


  /**
   * Display the roles and permissions page.
   *
   * @return void
   */
  public function index()
  {
    $roles = $this->roleModel->getAllRolesPermissionsGrouped();
    $permissionCategories = $this->permissionModel->getAllPermissionCategories();
    $allPermissionsByCategory = $this->permissionModel->getAllPermissionsByCategory();

    // error_log("Roles: " . print_r($roles, true)); // Log the roles for debugging
    // error_log("Permission Categories: " . print_r($permissionCategories, true)); // Log the permission categories for debugging
    // error_log("All Permissions by Category: " . print_r($allPermissionsByCategory, true)); // Log the permissions for debugging

    View::renderTemplate("Roles", [
      "title" => "Roles",
      'roles' => $roles,
      'permissionCategories' => $permissionCategories,
      'allPermissionsByCategory' => $allPermissionsByCategory
    ]);
  }


  /**
   * Display the role details page.
   *
   * @param array $params
   * @return void
   */
  public function details(array $params)
  {
    $roleId = $params['id'] ?? null;
    if (!$roleId) {
      $_SESSION['message'] = 'Role not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/roles');
    }

    $role = $this->roleModel->getRoleById($roleId);
    if (!$role) {
      $_SESSION['message'] = 'Role not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/roles');
    }

  }

  /**
   * Add a new role
   * @return void
   */
  public function addRole()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'role_name' => $_POST['role_name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'permissions' => $_POST['permissions'] ?? []
        ];

        if (empty($data['role_name'])) {
            $_SESSION['message'] = 'Role name is required';
            $_SESSION['message_type'] = 'error';

        } elseif ($this->roleModel->getRoleByName($data['role_name'])) {
            $_SESSION['message'] = 'Role name already exists';
            $_SESSION['message_type'] = 'error';

        } else {
            $invalidPerms = false;

            if (!empty($data['permissions'])) {
              $permCount = $this->permissionModel->countValidPermissions($data['permissions']);
              if ($permCount !== count($data['permissions'])) {
                  $invalidPerms = true;
              }
            }

            if ($invalidPerms) {
                $_SESSION['message'] = 'Invalid permissions provided';
                $_SESSION['message_type'] = 'error';
            } else {
                $result = $this->roleModel->addRole($data);
                $_SESSION['message'] = $result === false
                    ? 'Failed to add role'
                    : 'Role added successfully';
                $_SESSION['message_type'] = $result === false ? 'error' : 'success';
            }
        }

        View::redirect('/roles');
    } else {
        View::redirect('/roles');
    }
  }
}


