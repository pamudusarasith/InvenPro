<?php

namespace App\Controllers;

use App;

class Discounts
{
    public function index(): void
    {
        App\Utils::requireAuth();

        $discount = new App\Models\Discount();
        $types = $discount->getTypes();
        
        App\View::render('Template', [
            'title' => 'Discounts',
            'view' => 'Discounts',
            'data' => ['types' => $types],
            'stylesheets' => ['discounts', 'search'],
            'scripts' => ['discounts', 'search'],
        ]);
    }
}
