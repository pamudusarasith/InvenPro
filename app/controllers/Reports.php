<?php

namespace App\Controllers;

use App;

class Reports
{
    public function index(): void
    {
        App\Utils::requireAuth();

        App\View::render('Template', [
            'title' => 'Reports',
            'view' => 'Reports',
            'stylesheets' => ['reports']
        ]);
    }
}
