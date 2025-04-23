<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\{UserModel, RoleModel, BranchModel, AuditLogModel};

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

    $auditLogModel = new AuditLogModel();
    $activities = $auditLogModel->getAuditLogsById($user['id']);

    //error_log(print_r($user, true)); // Debugging line
    //error_log(print_r($activities, true)); // Debugging line

    $branchModal = new BranchModel();
    $branches = $branchModal->getBranches();

    View::renderTemplate('UserDetails', [
      'title' => 'User Details',
      'user' => $user,
      'roles' => $roles,
      'branches' => $branches,
      'activities' => $activities,
    ]);
  }

  public function profile(): void
  {
    $userModal = new UserModel();
    $user = $userModal->getUserById($_SESSION['user']['id']);

    if (!$user) {
      $_SESSION['message'] = 'User not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/');
    }

    $roleModal = new RoleModel();
    $roles = $roleModal->getAllRoles();

    $branchModal = new BranchModel();
    $branches = $branchModal->getBranches();

    $auditLogModel = new AuditLogModel();
    $activities = $auditLogModel->getAuditLogsById($user['id']);

    View::renderTemplate('Profile', [
      'title' => 'Profile',
      'user' => $user,
      'roles' => $roles,
      'branches' => $branches,
      'activities' => $activities,
    ]);
  }

  public function updateProfile(): void
  {
    if (!$this->validator->validateUpdateProfile($_POST)) {
      $this->profile($this->validator->getError(), 'error');
      exit;
    }

    $_POST['is_locked'] = $_POST['status'] == 'locked' ? 1 : 0;

    $userModal = new UserModel();
    $userModal->updateUser($_SESSION['user']['id'], $_POST);

    $_SESSION['message'] = 'Profile updated successfully';
    $_SESSION['message_type'] = 'success';

    View::redirect('/profile');
  }

  public function updateProfilePassword(): void
  {
    if (!$this->validator->validateUpdatePassword($_POST)) {
      $this->profile($this->validator->getError(), 'error');
      exit;
    }

    $userModal = new UserModel();
    if (!password_verify($_POST['old_password'], $_SESSION['user']['password'])) {
      $_SESSION['message'] = 'Old password is incorrect';
      $_SESSION['message_type'] = 'error';
      View::redirect('/profile');
    }

    $userModal = new UserModel();
    $userModal->updateUserPassword($_SESSION['user']['id'], $_POST['password']);
    $userModal->resetFailedLoginAttemptsById($_SESSION['user']['id']);


    $_SESSION['user']['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $_SESSION['user']['is_locked'] = 0; // Reset the lock status
    
    $_SESSION['message'] = 'Password updated successfully';
    $_SESSION['message_type'] = 'success';

    View::redirect('/profile');
  }
  
  public function updatePassword(array $params): void
  {
    if (!$this->validator->validateUpdatePassword($_POST)) {
      $this->details($params, $this->validator->getError(), 'error');
      exit;
    }

    $userModal = new UserModel();
    $userModal->updateUserPassword($params['id'], $_POST['password']);

    $_SESSION['message'] = 'Password updated successfully';
    $_SESSION['message_type'] = 'success';

    View::redirect('/users/' . $params['id']);
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
