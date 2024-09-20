<?php

namespace App\Controllers;

use App;

session_start();

class Dashboard
{
    public function index(): void
    {
        if (!isset($_SESSION["email"])) {
            header("Location: /");
            exit();
        }

        App\View::render('Template', ['title' => 'Dashboard', 'view' => 'Dashboard']);
    }
}
