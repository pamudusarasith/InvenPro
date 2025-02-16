<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\UserModel;
use App\Services\ValidationService;

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
    View::render('LoginPage');
  }

  /**
   * Handle login form submission
   *
   * @return void
   */
  public function login(): void
  {
    if (!$this->validator->validateLogin($_POST)) {
      View::render('LoginPage', [
        'error' => $this->validator->getError()
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

    $_SESSION['user'] = $user;

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
