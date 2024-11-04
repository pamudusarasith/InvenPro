<?php

namespace App\Controllers;

use App;

class Discounts
{
    public function index()
    {
        App\Utils::requireAuth();

        App\View::render('Template', [
            'title' => 'Discounts',
            'view' => 'Discounts',
            'stylesheets' => ['discounts'],
            'scripts' => ['discounts'],
        ]);
    }
}
