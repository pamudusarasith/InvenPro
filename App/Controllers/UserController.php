<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\{UserModel, RoleModel, BranchModel};

class UserController extends Controller
{
  public function index(): void
  {
    $page = max(1, (int) ($_GET['p'] ?? 1));
    $itemsPerPage = (int) ($_GET['ipp'] ?? 10);
    $search = $_GET['search'] ?? '';
    $roleId = $_GET['role'] ?? '';
    $branchId = $_GET['branch'] ?? '';
    $status = $_GET['status'] ?? '';

    $userModel = new UserModel();
    $users = $userModel->getUsers($page, $itemsPerPage, $search, $roleId, $branchId, $status);
    $totalRecords = $userModel->getUsersCount($search, $roleId, $branchId, $status);
    $totalPages = ceil($totalRecords / $itemsPerPage);

    $roleModel = new RoleModel();
    $roles = $roleModel->getAllRoles();

    $branchModel = new BranchModel();
    $branches = $branchModel->getBranches();

    View::renderTemplate('Users', [
      'title' => 'Users',
      'users' => $users,
      'page' => $page,
      'itemsPerPage' => $itemsPerPage,
      'totalPages' => $totalPages,
      'totalRecords' => $totalRecords,
      'roles' => $roles,
      'branches' => $branches,
      'search' => $search,
      'roleId' => $roleId,
      'branchId' => $branchId,
      'status' => $status
    ]);
  }

  public function details(array $params): void
  {
    $userModal = new UserModel();
    $user = $userModal->getUserById($params['id']);

    if (!$user) {
      $_SESSION['message'] = 'User not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/users');
    }

    $roleModal = new RoleModel();
    $roles = $roleModal->getAllRoles();

    $branchModal = new BranchModel();
    $branches = $branchModal->getBranches();

    View::renderTemplate('UserDetails', [
      'title' => 'User Details',
      'user' => $user,
      'roles' => $roles,
      'branches' => $branches,
    ]);
  }

  public function createUser(): void
  {
    if (!$this->validator->validateCreateUser($_POST)) {
      $this->index($this->validator->getError(), 'error');
      exit;
    }

    $_POST['password'] = "1234";
    // TODO: Make a random password and email it to the user

    $userModal = new UserModel();
    $userModal->createUser($_POST);

    $_SESSION['message'] = 'User created successfully';
    $_SESSION['message_type'] = 'success';

    View::redirect('/users');
  }

  public function updateUser(array $params): void
  {
    if (!$this->validator->validateUpdateUser($_POST)) {
      $this->details($params, $this->validator->getError(), 'error');
      exit;
    }

    $_POST['is_locked'] = $_POST['status'] == 'locked' ? 1 : 0;

    $userModal = new UserModel();
    $userModal->updateUser($params['id'], $_POST);

    $_SESSION['message'] = 'User updated successfully';
    $_SESSION['message_type'] = 'success';

    View::redirect('/users/' . $params['id']);
  }

  public function deleteUser(array $params): void
  {
    $userModal = new UserModel();
    $userModal->deleteUser($params['id']);

    $_SESSION['message'] = 'User deleted successfully';
    $_SESSION['message_type'] = 'success';

    View::redirect('/users');
  }
}
