<?php

namespace App\Controllers;

use App\Models\Supplier;
use App\Utils;
use App\View;
use App\Consts;
use App;

class PurchaseOrders
{
    public function index(): void
    {
        Utils::requireAuth();
        App\View::render('Template', [
            'title' => 'PurchaseOrders',
            'view' => 'PurchaseOrders',
            'stylesheets' => ['purchaseOrders'],
            'scripts' => ['purchaseOrders'],
        ]);


    }
}