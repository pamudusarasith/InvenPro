<?php

namespace App\Controllers;

use App;

class Login
{
    public function index(): void
    {
        if (isset($_SESSION["email"])) {
            header("Location: /dashboard");
            exit();
        }

        $this->renderLogin();
    }

    public function login(): void
    {
        $employee = new App\Models\Employee();
        $email = $_POST["email"];
        $password = $_POST["password"];

        if (!$employee->emailExists($email)) {
            $this->renderLogin("Incorrect Email");
            return;
        }

        if ($password != $employee->getPassword($email)) {
            $this->renderLogin("Incorrect Password");
            return;
        }

        $_SESSION["email"] = $_POST["email"];
        $empData = $employee->getEmployee();
        $_SESSION["id"] = $empData["id"];
        $_SESSION["role_id"] = $empData["role_id"];
        $_SESSION["branch_id"] = $empData["branch_id"];
        $_SESSION["full_name"] = $empData["full_name"];
        header("Location: /dashboard");
        exit();
    }

    private function renderLogin($errorMessage = null): void
    {
        App\View::render('Template', [
            'title' => 'Invenpro',
            'view' => 'Login',
            'data' => ['errorMessage' => $errorMessage]
        ]);
    }
}
