<?php

namespace App\Controllers;

use App;

session_start();

class Suppliers
{
    public function index(): void
    {
        if (!isset($_SESSION["email"])) {
            header("Location: /");
            return;
        }

        App\View::render('Template', [
            'title' => 'Suppliers',
            'view' => 'Suppliers'
        ]);
    }

    public function details(): void
    {
        if (!isset($_SESSION["email"])) {
            header("Location: /");
            return;
        }

        App\View::render('Template', [
            'title' => 'Supplier Details',
            'view' => 'SupplierDetails'
        ]);
    }

    public function add(): void
    {
        if (!isset($_SESSION["email"])) {
            header("Location: /");
            return;
        }

        App\View::render('AddSupplierForm');
    }
}
