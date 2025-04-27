<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\{RoleModel, PermissionModel, UserModel};
use App\Services\RBACService;
use App\Services\ValidationService;
use Error;

class RoleController extends Controller
{
	private $roleModel;
	private $permissionModel;
	private $userModel;

	public function __construct()
	{
		parent::__construct();
		RBACService::requireAuthentication();
		RBACService::requirePermission('role_view');
		$this->validator = new ValidationService();
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
		RBACService::requirePermission('role_create');
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {

			if (!$this->validator->validateRole($_POST)) {
				$_SESSION['message'] = $this->validator->getError();
				$_SESSION['message_type'] = 'error';
				View::redirect('/roles');
			}

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
						? 'Failed to create role'
						: 'Role created successfully';
					$_SESSION['message_type'] = $result === false ? 'error' : 'success';
				}
			}

			View::redirect('/roles');
		} else {
			View::redirect('/roles');
		}
	}

	/**
	 * Update an existing role
	 * @return void
	 */
	public function updateRole($params)
	{
		RBACService::requirePermission('role_update');
		if (!$this->validator->validateRole($_POST)) {
			$_SESSION['message'] = $this->validator->getError();
			$_SESSION['message_type'] = 'error';
			View::redirect('/roles');
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data = [
				'role_id' => $params['id'] ?? null,
				'role_name' => $_POST['role_name'] ?? '',
				'description' => $_POST['description'] ?? '',
				'addedPermissions' => json_decode($_POST['added_permissions'] ?? '[]', true),
				'removedPermissions' => json_decode($_POST['removed_permissions'] ?? '[]', true),
			];

			if (empty($data['role_id'])) {
				$_SESSION['message'] = 'Role ID is required';
				$_SESSION['message_type'] = 'error';
				View::redirect('/roles');
			}

			if (empty($data['role_name'])) {
				$_SESSION['message'] = 'Role name is required';
				$_SESSION['message_type'] = 'error';
				View::redirect('/roles');
			}

			$currentRole = $this->roleModel->getRoleById($data['role_id']);
			error_log(print_r($currentRole, true));

			if (!$currentRole) {
				$_SESSION['message'] = 'Role not found';
				$_SESSION['message_type'] = 'error';
				View::redirect('/roles');
			}

			$existingRole = $this->roleModel->getRoleByName($data['role_name']);
			error_log(print_r($existingRole, true));

			if ($existingRole && $existingRole['role_id'] != $data['role_id']) {
				$_SESSION['message'] = 'Role name already exists';
				$_SESSION['message_type'] = 'error';
				View::redirect('/roles');
			}

			$result = $this->roleModel->updateRole($data);

			if ($result === false) {
				$_SESSION['message'] = 'Failed to update role';
				$_SESSION['message_type'] = 'error';
			} else {
				$_SESSION['message'] = 'Role updated successfully';
				$_SESSION['message_type'] = 'success';
			}

			View::redirect('/roles');
		} else {
			View::redirect('/roles');
		}
	}


	/**
	 * Delete a role
	 * @return void
	 */
	public function deleteRole()
	{
		RBACService::requirePermission('role_delete');
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$roleId = $_POST['role_id'] ?? null;

			if (empty($roleId)) {
				$_SESSION['message'] = 'Role ID is required';
				$_SESSION['message_type'] = 'error';
			} elseif ($this->roleModel->getUserCountByRole($roleId) > 0) {
				$_SESSION['message'] = 'Cannot delete role with assigned users';
				$_SESSION['message_type'] = 'error';
			} else {
				$result = $this->roleModel->deleteRole((int)$roleId);
				$_SESSION['message'] = $result === false
					? 'Failed to delete role'
					: 'Role deleted successfully';
				$_SESSION['message_type'] = $result === false ? 'error' : 'success';
			}

			View::redirect('/roles');
		} else {
			View::redirect('/roles');
		}
	}
}
