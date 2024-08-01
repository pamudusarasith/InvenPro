<?php

namespace App\Controllers;
use App;

session_start();

class Login {
    public function index(): void {
        if (isset($_SESSION["username"])) {
            header("Location: /dashboard");
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_POST["username"] == "admin" && $_POST["password"] == "admin") {
                $_SESSION["username"] = $_POST["username"];
                header("Location: /dashboard");
                return;
            }
        }
        
        App\View::render('Login');
    }
}

?>