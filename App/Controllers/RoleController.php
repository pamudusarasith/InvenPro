<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\RoleModel;

class RoleController extends Controller
{
    private $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
    }

    /**
     * Display the roles index page with all roles and related data
     */
    public function index()
    {
        $roles = $this->roleModel->getAllRoles();
        $data = [
            'title' => 'Roles',
            'roles' => $roles,
            'permissionCategories' => $this->roleModel->getPermissionCategories(),
            'permissionsByCategory' => $this->roleModel->getAllPermissionsGrouped(),
            'rolePermissionsDetails' => [],
            'rolePermissionCategories' => [],
            'rolePermissions' => [], // Add this
            'roleUserCounts' => $this->roleModel->getUserCountByRole(),
            'csrf_token' => $this->generateCsrfToken()
        ];

        // Populate rolePermissions, rolePermissionsDetails, and rolePermissionCategories
        foreach ($roles as $role) {
            $roleId = $role['id'];
            $data['rolePermissionCategories'][$roleId] = $this->roleModel->getRolePermissionCategories($roleId);
            $data['rolePermissionsDetails'][$roleId] = $this->roleModel->getPermissionsByRole($roleId);
            $data['rolePermissions'][$roleId] = $this->roleModel->getRolePermissionNames($roleId); // Add this
        }

        //error_log("Role Permission Categories: " . print_r($data['rolePermissionCategories'], true));
        error_log("Role Permissions Details: " . print_r($data['rolePermissionsDetails'], true));
        error_log("Role Permissions: " . print_r($data['rolePermissions'], true));

        View::renderTemplate('Roles', $data);
    }

    /**
     * Display the form to create a new role
     */
    public function create()
    {
        $data = [
            'title' => 'Create Role',
            'permissionCategories' => $this->roleModel->getPermissionCategories(),
            'permissionsByCategory' => $this->roleModel->getAllPermissionsGrouped(),
            'csrf_token' => $this->generateCsrfToken()
        ];

        View::renderTemplate('Roles/Create', $data);
    }

    /**
     * Handle the creation of a new role
     */
    public function store()
    {
      header('Content-Type: application/json'); // Set JSON response

      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
          http_response_code(405);
          echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
          return;
      }

      if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
          echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
          return;
      }

      $data = [
          'role_name' => trim($_POST['role_name'] ?? ''),
          'description' => trim($_POST['description'] ?? ''),
          'created_at' => $_POST['created_at'] ?? date('Y-m-d H:i:s')
      ];

      // Validation
      if (empty($data['role_name']) || empty($data['description'])) {
          echo json_encode(['success' => false, 'message' => 'Role name and description are required']);
          return;
      }

      if (strlen($data['role_name']) > 255 || strlen($data['description']) > 1000) {
          echo json_encode(['success' => false, 'message' => 'Role name or description is too long']);
          return;
      }

      try {
          $roleId = $this->roleModel->createRole($data);

          // Handle permissions
          $permissionIds = array_filter($_POST['permissions'] ?? [], 'is_numeric');
          if (!empty($permissionIds)) {
              $this->roleModel->updateRolePermissions($roleId, $permissionIds);
          }

          echo json_encode(['success' => true, 'message' => 'Role created successfully']);
      } catch (\Exception $e) {
          error_log('Error creating role: ' . $e->getMessage());
          echo json_encode(['success' => false, 'message' => 'Failed to create role']);
      }
    }

    /**
     * Display the form to edit an existing role
     * @param int $id
     */
    public function edit($id)
    {
        $role = $this->roleModel->getRoleById((int)$id);

        if (!$role) {
            View::renderError(404);
            return;
        }

        $data = [
            'title' => 'Edit Role: ' . htmlspecialchars($role['role_name']),
            'role' => $role,
            'permissionCategories' => $this->roleModel->getPermissionCategories(),
            'permissionsByCategory' => $this->roleModel->getAllPermissionsGrouped(),
            'rolePermissions' => $this->roleModel->getRolePermissionNames($id),
            'csrf_token' => $this->generateCsrfToken()
        ];

        View::renderTemplate('Roles/Edit', $data);
    }

    /**
     * Handle the update of an existing role
     * @param int $id
     */
    public function update($id)
    {
      header('Content-Type: application/json');

      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
          http_response_code(405);
          echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
          return;
      }

      if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
          echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
          return;
      }

      $role = $this->roleModel->getRoleById((int)$id);

      if (!$role) {
          http_response_code(404);
          echo json_encode(['success' => false, 'message' => 'Role not found']);
          return;
      }

      $data = [
          'role_name' => trim($_POST['role_name'] ?? ''),
          'description' => trim($_POST['description'] ?? ''),
          'created_at' => $_POST['created_at'] ?? date('Y-m-d H:i:s')
      ];

      // Validation
      if (empty($data['role_name']) || empty($data['description'])) {
          echo json_encode(['success' => false, 'message' => 'Role name and description are required']);
          return;
      }

      if (strlen($data['role_name']) > 255 || strlen($data['description']) > 1000) {
          echo json_encode(['success' => false, 'message' => 'Role name or description is too long']);
          return;
      }

      try {
          $success = $this->roleModel->updateRole((int)$id, $data);

          // Handle permissions
          $permissionIds = array_filter($_POST['permissions'] ?? [], 'is_numeric');
          $this->roleModel->updateRolePermissions((int)$id, $permissionIds);

          echo json_encode(['success' => true, 'message' => 'Role updated successfully']);
      } catch (\Exception $e) {
          error_log('Error updating role: ' . $e->getMessage());
          echo json_encode(['success' => false, 'message' => 'Failed to update role']);
      }
    }

    /**
     * Generate a CSRF token
     * @return string
     */
    private function generateCsrfToken(): string
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify a CSRF token
     * @param string $token
     * @return bool
     */
    private function verifyCsrfToken(string $token): bool
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    /**
     * Set a flash message for display on the next page
     * @param string $type
     * @param string $message
     */
    private function setFlashMessage(string $type, string $message)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}
?>