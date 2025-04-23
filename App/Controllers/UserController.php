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
    $userModel = new UserModel();
    $user = $userModel->getUserById($params['id']);

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
    error_log(print_r($activities, true)); // Debugging line

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

  public function createUser(): void
  {
    if (!$this->validator->validateCreateUser($_POST)) {
      $this->index($this->validator->getError(), 'error');
      exit;
    }

    // Generate a random password for the new user
    $_POST['password'] = $this->generateRandomPassword();

    $userModel = new UserModel();
    $userId = $userModel->createUser($_POST);

    if ($userId) {
      // Send email with credentials
      $this->sendNewAccountEmail($_POST['email'], $_POST['password'], $_POST['role_id']);

      $_SESSION['message'] = 'User created successfully. Login credentials have been sent to their email.';
      $_SESSION['message_type'] = 'success';
    } else {
      $_SESSION['message'] = 'Failed to create user';
      $_SESSION['message_type'] = 'error';
    }

    View::redirect('/users');
  }

  /**
   * Generate a secure random password
   *
   * @param int $length Length of the password
   * @return string Random password
   */
  private function generateRandomPassword(int $length = 10): string
  {
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '0123456789';
    $specialChars = '!@#$%^&*()_-+=<>?';

    $characters = $lowercase . $uppercase . $numbers . $specialChars;
    $password = '';

    // Fill the password
    for ($i = 0; $i < $length; $i++) {
      $password .= $characters[random_int(0, strlen($characters) - 1)];
    }

    // Shuffle the password to make it more random
    return str_shuffle($password);
  }

  /**
   * Send account credentials email to new user
   *
   * @param string $email User's email address
   * @param string $password Temporary password
   * @param int $roleId User's role ID
   * @return bool Whether the email was sent successfully
   */
  private function sendNewAccountEmail(string $email, string $password, int $roleId): bool
  {
    try {
      // Get role name from role ID
      $roleModel = new RoleModel();
      $role = $roleModel->getRoleById($roleId);
      $roleName = $role ? $role['role_name'] : 'User';

      // Prepare data for the template
      $data = [
        'email' => $email,
        'password' => $password,
        'role' => $roleName,
        'login_url' => 'http://' . $_SERVER['HTTP_HOST']
      ];

      // Template path
      $templatePath = __DIR__ . '/../Views/emails/NewAccount.php';

      // Send email using EmailService with PHP template
      $emailService = \App\Services\EmailService::getInstance();
      return $emailService->sendPhpTemplate(
        $email,
        'Your New InvenPro Account',
        $templatePath,
        $data
      );
    } catch (\Exception $e) {
      error_log('Failed to send new account email: ' . $e->getMessage());
      return false;
    }
  }

  public function updateUser(array $params): void
  {
    if (!$this->validator->validateUpdateUser($_POST)) {
      $this->details($params, $this->validator->getError(), 'error');
      exit;
    }

    $_POST['is_locked'] = $_POST['status'] == 'locked' ? 1 : 0;

    $userModel = new UserModel();
    $userModel->updateUser($params['id'], $_POST);

    $_SESSION['message'] = 'User updated successfully';
    $_SESSION['message_type'] = 'success';

    View::redirect('/users/' . $params['id']);
  }

  public function deleteUser(array $params): void
  {
    $userModel = new UserModel();
    $userModel->deleteUser($params['id']);

    $_SESSION['message'] = 'User deleted successfully';
    $_SESSION['message_type'] = 'success';

    View::redirect('/users');
  }
}
