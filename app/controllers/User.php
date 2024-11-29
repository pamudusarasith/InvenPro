<?php

namespace App\Controllers;

use App;
use App\Consts;
use App\Utils;

class User
{
  public function index(): void
  {
    App\Utils::requireAuth();
    if (!App\Utils::can("manage_users")) {
      Utils::error(403);
    }

    $userModel = new App\Models\User();
    $users = $userModel->getAllUsers();

    $rolesModel = new App\Models\Roles();
    $roles = $rolesModel->getAllRoles();

    App\View::render('Template', [
      'title' => 'Users',
      'view' => 'Users',
      'data' => [
        'users' => $users,
        'roles' => $roles
      ],
    ]);
  }

  public function addUser()
  {
    App\Utils::requireAuth();
    $input = file_get_contents('php://input');
    $userData = json_decode($input, true);

    // if (!$this->validateUserData($userData)) {
    //   header(Consts::HEADER_JSON);
    //   echo json_encode(['success' => false, 'error' => 'Invalid user data']);
    //   return;
    // }

    $user = new App\Models\User();
    $userId = $user->addUser($userData);

    header(Consts::HEADER_JSON);
    if ($userId) {
      $newUser = $user->getUserById($userId);
      echo json_encode(['success' => true, 'data' => $newUser]);
    } else {
      echo json_encode(['success' => false, 'error' => 'Failed to add user']);
    }
  }

  public function updateUser()
  {
    App\Utils::requireAuth();

    $userData = json_decode(file_get_contents(Consts::INPUT_STREAM), true);

    if (!$this->validateUserData($userData, true)) {
      header(Consts::HEADER_JSON);
      echo json_encode(['success' => false, 'error' => 'Invalid user data']);
      return;
    }

    $user = new App\Models\User();
    $success = $user->updateUser($userData);

    header(Consts::HEADER_JSON);
    if ($success) {
      $updatedUser = $user->getUserById($userData['id']);
      echo json_encode(['success' => true, 'data' => $updatedUser]);
    } else {
      echo json_encode(['success' => false, 'error' => 'Failed to update user']);
    }
  }

  private function validateUserData(array $userData, bool $isUpdate = false): bool
  {
    $requiredFields = ['full_name', 'email', 'phone', 'role_id', 'branch_id'];
    if ($isUpdate) {
      $requiredFields[] = 'id';
    }

    foreach ($requiredFields as $field) {
      if (!isset($userData[$field]) || empty($userData[$field])) {
        return false;
      }
    }

    if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
      return false;
    }

    if (!preg_match('/^\+?[\d\s-]{10,}$/', $userData['phone'])) {
      return false;
    }

    return true;
  }

  public function deleteUser()
  {
    App\Utils::requireAuth();

    $data = json_decode(file_get_contents(Consts::INPUT_STREAM), true);

    if (!isset($data['id']) || !is_numeric($data['id'])) {
      header(Consts::HEADER_JSON);
      echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
      return;
    }

    $user = new App\Models\User();
    $success = $user->deleteUser($data['id']);

    header(Consts::HEADER_JSON);
    echo json_encode([
      'success' => $success,
      'message' => $success ? 'User deleted successfully' : 'Failed to delete user'
    ]);
  }
}
