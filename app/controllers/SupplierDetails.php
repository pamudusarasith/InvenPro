<?php

namespace App\Controllers;

use App;

session_start();

class SupplierDetails
{
    public function index(): void
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
}
