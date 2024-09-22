<?php

namespace App\Controllers;

class Logout
{
    public function index(): void
    {
        session_destroy();
        header("Location: /");
        exit();
    }
}
