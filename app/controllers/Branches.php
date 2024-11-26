<?php

namespace App\Controllers;

use App;

class Branches
{
    public function index(): void
    {
        App\Utils::requireAuth();

        App\View::render('Template', ['title' => 'BranchSelect', 'view' => 'BranchSelect']);
    }
}