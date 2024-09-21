<?php

namespace App\Controllers;

use App;

session_start();

class POS
{
    public function index(): void
    {
        App\Utils::requireAuth();

        App\View::render('POS');
    }
}
