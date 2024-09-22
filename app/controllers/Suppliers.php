<?php

namespace App\Controllers;

use App;

class Suppliers
{
    public function index(): void
    {
        App\Utils::requireAuth();

        App\View::render('Template', [
            'title' => 'Suppliers',
            'view' => 'Suppliers'
        ]);
    }

    public function details(): void
    {
        App\Utils::requireAuth();

        App\View::render('Template', [
            'title' => 'Supplier Details',
            'view' => 'SupplierDetails'
        ]);
    }

    public function add(): void
    {
        App\Utils::requireAuth();

        App\View::render('AddSupplierForm');
    }
}
