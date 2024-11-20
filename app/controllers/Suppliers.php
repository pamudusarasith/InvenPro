<?php

namespace App\Controllers;

use App\Models\Supplier;
use App\Utils;
use App\View;

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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Data Validation
            $data = filter_input_array(INPUT_POST, [
                'supplierID' => FILTER_SANITIZE_STRING,
                'supplierName' => FILTER_SANITIZE_STRING,
                'productCategories' => FILTER_SANITIZE_STRING,
                'products' => FILTER_SANITIZE_STRING,
                'address' => FILTER_SANITIZE_STRING,
                'contactNo' => FILTER_SANITIZE_STRING,
                'email' => FILTER_VALIDATE_EMAIL,
                'specialNotes' => FILTER_SANITIZE_STRING,
            ]);
            
            // Check Required Fields
            if (in_array(null, $data, true) || in_array(false, $data, true)) {
                View::render('AddSupplierForm', [
                    'title' => 'Add Supplier',
                    'error' => 'Please fill out all fields correctly.'
                ]);
                return;
            }

            // Add Supplier to Database
            $supplierModel = new Supplier();
            $success = $supplierModel->addSupplier($data);

            if ($success) {
                header('Location: /suppliers');
                exit;
            } else {
                View::render('AddSupplierForm', [
                    'title' => 'Add Supplier',
                    'error' => 'Failed to add supplier. Please try again.'
                ]);
            }
        } else {
            View::render('AddSupplierForm', [
                'title' => 'Add Supplier'
            ]);
        }
    }
}
