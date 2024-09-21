<?php

namespace App\Controllers;

use App;

session_start();

class Dashboard
{
    public function index(): void
    {
        App\Utils::requireAuth();

        App\View::render('Template', ['title' => 'Dashboard', 'view' => 'Dashboard']);
    }
}
