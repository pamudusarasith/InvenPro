<?php

namespace App\Controllers;

use App\Models\Supplier;
use App\Utils;
use App\View;
use App\Consts;
use App;

class Suppliers
{
    public function index(): void
    {
        Utils::requireAuth();
        View::render('Template', [
            'title' => 'Suppliers',
            'view' => 'Suppliers'
        ]);
    }

    public function details(): void
    {
        Utils::requireAuth();
        View::render('Template', [
            'title' => 'Supplier Details',
            'view' => 'SupplierDetails'
        ]);
    }

    public function add(): void
    {
        Utils::requireAuth();
        View::render('Template', [
            'title' => 'Add Supplier',
            'view' => 'AddSupplierForm'
        ]);
    }

    public function addSupplier()
    {
        App\Utils::requireAuth();

        $supplier = new App\Models\Supplier();
        $supplier->addSupplier();

        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'data' => 'Supplier added successfully']);
        

    }
}
?>