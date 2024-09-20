<?php

namespace App\Controllers;

use App;

session_start();

class POS
{
    public function index(): void
    {
        if (!isset($_SESSION["email"])) {
            header("Location: /");
            return;
        }

        App\View::render('POS');
    }
}
