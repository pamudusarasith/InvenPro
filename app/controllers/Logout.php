<?php

namespace App\Controllers;

session_start();

class Logout
{
    public function index(): void
    {
        session_destroy();
        header("Location: /");
        exit();
    }
}
