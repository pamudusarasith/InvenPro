<?php

namespace App\Controllers;

use App;

session_start();

class Login
{
    public function index(): void
    {
        if (isset($_SESSION["email"])) {
            header("Location: /dashboard");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

            $_SESSION["email"] = $email;
            header("Location: /dashboard");
            exit();
        }

        $this->renderLogin();
    }

    private function renderLogin($errorMessage = null): void
    {
        // Assuming App\View::render() takes an associative array for variables
        App\View::render('Login', ['errorMessage' => $errorMessage]);
    }
}
