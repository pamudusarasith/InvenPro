<?php

namespace App\Controllers;

use App;
use App\Utils;

class Login
{
    public function index(): void
    {
        if (isset($_SESSION["email"])) {
            Utils::redirect("/dashboard");
        }

        $this->renderLogin();
    }

    public function login(): void
    {
        $email = $_POST["email"];
        $password = $_POST["password"];

        if (empty($email) || empty($password)) {
            $this->renderLogin("Email and Password are required");
            return;
        }

        $employee = new App\Models\User();
        $user = $employee->getUserByEmail($email);

        if (!$user) {
            $this->renderLogin("Invalid Email or Password");
            return;
        }

        if (password_verify($password, $user["password"])) {
            $this->renderLogin("Invalid Email or Password");
            return;
        }

        session_regenerate_id();
        
        $_SESSION["email"] = $_POST["email"];
        $_SESSION["id"] = $user["id"];
        $_SESSION["role_id"] = $user["role_id"];
        $_SESSION["role_name"] = $user["role_name"];
        $_SESSION["branch_id"] = $user["branch_id"];
        $_SESSION["full_name"] = $user["full_name"];

        if ($user["role_name"] == "Cashier") {
            Utils::redirect("/pos");
        } else {
            Utils::redirect("/dashboard");
        }
    }

    private function renderLogin($errorMessage = null): void
    {
        App\View::render('Template', [
            'title' => 'Invenpro',
            'view' => 'Login',
            'stylesheets' => ['admin'],
            'data' => ['errorMessage' => $errorMessage]
        ]);
    }
}
