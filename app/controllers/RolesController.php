<?php

namespace App\Controllers;

use App;
use App\Consts;

class RolesController
{
    public function index()
    {
        App\Utils::requireAuth();

        App\View::render('Template', [
            'title' => 'Roles & Permissions',
            'view' => 'Roles',
            'stylesheets' => ['roles', 'search'],
            'scripts' => ['roles', 'search'],
        ]);
    }
}