<?php

namespace App\Controllers;

use App\View;

class RolesController
{
    public function index(): void
    {

        View::render('roles');
    }
}