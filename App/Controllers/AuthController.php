<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\UserModel;

class AuthController extends Controller
{
  /**
   * Display the login page
   *
   * @return void
   */
  public function index(): void
  {
    if (isset($_SESSION['user']['email'])) {
      switch ($_SESSION['user']['role_name']) {
        case 'Cashier':
          View::redirect('/pos');
          break;
        default:
          View::redirect('/dashboard');
      }
    }
    View::renderTemplate('LoginPage');
  }

  /**
   * Handle login form submission
   *
   * @return void
   */
  public function login(): void
  {
    if (!$this->validator->validateLogin($_POST)) {
      $_SESSION['message'] = $this->validator->getError();
      $_SESSION['message_type'] = 'error';
      View::renderTemplate('LoginPage');
      return;
    }

    $userModel = new UserModel();
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user = $userModel->findByEmail($email);

    if (!$user || !password_verify($password, $user['password'])) {
      $userModel->recordFailedLoginAttempt($email);
      $attempts = $userModel->getFailedLoginAttempts($email);

      if ($attempts >= 3) {
        $userModel->lockUser($user['id'] ?? 0);

        $_SESSION['message'] = 'Account locked due to too many failed login attempts. Please contact support.';
        $_SESSION['message_type'] = 'error';

        View::redirect('/');
        return;
      } else {

        $_SESSION['message'] = 'Invalid email or password. Please try again.';
        $_SESSION['message_type'] = 'error';

        View::redirect('/');
        return;
      }
    }

    if ($user['is_locked']) {
      $_SESSION['message'] = 'Your account is locked. Please contact support.';
      $_SESSION['message_type'] = 'error';

      View::redirect('/');
      return;
    }

    // Login successful
    $userModel->resetFailedLoginAttempts($email);

    $_SESSION['user'] = $user;
    $userModel->recordLastLogin($user['id']);

    View::redirect('/');
  }

  /**
   * Log out the current user
   *
   * @return void
   */
  public function logout(): void
  {
    session_unset();
    session_destroy();
    View::redirect('/');
  }
}
