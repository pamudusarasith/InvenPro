<?php

namespace App\Controllers;

use App\Models\PurchaseOrder;
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
            'stylesheets' => ['purchaseOrders','search'],
            'scripts' => ['purchaseOrders'],
        ]);


    }

    public function addformview(): void
    {
        Utils::requireAuth();
        View::render('Template', [
            'title' => 'Add Order',
            'view' => 'AddOrderForm',
            'stylesheets' => ['purchaseOrders']
        ]);
    }


    public function details(): void
    {
        Utils::requireAuth();
        View::render('Template', [
            'title' => 'PurchaseOrder Details',
            'view' => 'PurchaseOrderDetails',
            'stylesheets' => ['purchaseOrderDeatils'] // Pass the data to the view
        ]);
}
}