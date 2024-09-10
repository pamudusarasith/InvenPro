<?php

namespace App\Controllers;

use App;

class Login
{
    public function index(): void
    {
        if (isset($_SESSION["email"])) {
            header("Location: /dashboard");
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $employee = new App\Models\Employee();
            if (!$employee->emailExists($_POST["email"])) {
                header("Location: /");
                return;
            }

            if ($_POST["password"] != $employee->getPassword($_POST["email"])) {
                header("Location: /");
                return;
            }

            $_SESSION["email"] = $_POST["email"];
            $empData = $employee->getEmployee();
            $_SESSION["id"] = $empData["id"];
            $_SESSION["role_id"] = $empData["role_id"];
            $_SESSION["branch_id"] = $empData["branch_id"];
            $_SESSION["full_name"] = $empData["full_name"];
            header("Location: /dashboard");
            return;
        }

        App\View::render('Login');
    }
}
