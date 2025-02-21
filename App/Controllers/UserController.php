<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\{UserModel, RoleModel, BranchModel};
use App\Services\ValidationService;

class UserController
{
  public function index(): void
  {
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;
    $userModal = new UserModel();
    $users = $userModal->getUsers($page, $itemsPerPage);
    $totalRecords = $userModal->getUsersCount();
    $totalPages = ceil($totalRecords / $itemsPerPage);

    $roleModal = new RoleModel();
    $roles = $roleModal->getRoles();

    $branchModal = new BranchModel();
    $branches = $branchModal->getBranches();

    View::renderTemplate('Users', [
      'title' => 'Users',
      'users' => $users,
      'page' => $page,
      'itemsPerPage' => $itemsPerPage,
      'totalPages' => $totalPages,
      'roles' => $roles,
      'branches' => $branches,
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
    $roles = $roleModal->getRoles();

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
    $validator = new ValidationService();
    if (!$validator->validateCreateUser($_POST)) {
      $this->index($validator->getError(), 'error');
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
    $validator = new ValidationService();
    if (!$validator->validateUpdateUser($_POST)) {
      $this->details($params, $validator->getError(), 'error');
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
