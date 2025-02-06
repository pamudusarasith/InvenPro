<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\UserModel;
use App\Services\ValidationService;

class AuthController
{
  /**
   * Display the login page
   *
   * @return void
   */
  public function index(): void
  {
    if (isset($_SESSION['email'])) {
      switch ($_SESSION['role_name']) {
        case 'Cashier':
          View::redirect('/pos');
          break;
        default:
          View::redirect('/dashboard');
      }
    }
    View::render('LoginPage');
  }

  /**
   * Handle login form submission
   *
   * @return void
   */
  public function login(): void
  {
    $validation = new ValidationService();

    if (!$validation->validateLogin($_POST)) {
      View::render('LoginPage', [
        'error' => $validation->getError()
      ]);
      exit;
    }

    $userModel = new UserModel();
    $user = $userModel->findByEmail($_POST['email']);

    if (!$user || !password_verify($_POST['password'], $user['password'])) {
      View::render('LoginPage', [
        'error' => 'Invalid email or password'
      ]);
      exit;
    }

    $_SESSION['id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['display_name'] = $user['display_name'];
    $_SESSION['role_id'] = $user['role_id'];
    $_SESSION['role_name'] = $user['role_name'];
    $_SESSION['branch_id'] = $user['branch_id'];

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
