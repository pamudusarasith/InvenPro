<?php

namespace App\Controllers;

use App;

class Branches
{
    public function index(): void
    {
        App\Utils::requireAuth();

        App\View::render('Template', [
            'title' => 'BranchSelect', 
            'view' => 'BranchSelect',
            'stylesheets' => ['branch']
        ]);
    }

    public function addformview(): void
    {
        App\Utils::requireAuth();
        App\View::render('Template', [
            'title' => 'Add Branch',
            'view' => 'AddBranchForm',
            'stylesheets' => ['branch']
        ]);
    }
}