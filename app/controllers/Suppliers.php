<?php

namespace App\Controllers;


use App;
use App\Utils;
use App\View;
use App\Consts;
use App\Models\Supplier;


class Suppliers
{
    public function index(): void
    {
        Utils::requireAuth();
        App\View::render('Template', [
            'title' => 'Suppliers',
            'view' => 'Suppliers',
            'stylesheets' => ['suppliers','search'],
            'scripts' => ['suppliers'],
        ]);


    }

    public function details(): void
    {
        Utils::requireAuth();

        // Retrieve supplier ID from the query parameter
        $supplierID = $_GET['id'] ?? null;

        if (!$supplierID) {
            // Redirect to the suppliers list if no ID is provided
            header("Location: /suppliers");
            exit;
        }

        // Use the Supplier model to fetch the supplier details
        $supplier = new App\Models\Supplier();
        $supplierDetails = $supplier->getSupplierDetails($supplierID);

        // If supplier not found, redirect or show an error
        if (!$supplierDetails) {
            View::render('Template', [
                'title' => 'Supplier Not Found',
                'view' => 'Error',
                'errorMessage' => 'Supplier not found.'
            ]);
            return;
        }

        // Render the SupplierDetails view with the supplier details
        View::render('Template', [
            'title' => 'Supplier Details',
            'view' => 'SupplierDetails',
            'stylesheets' => ['supplierDetails'],
            'supplier' => $supplierDetails,
            
        ]);
}


    public function add(): void
    {
        Utils::requireAuth();
        View::render('Template', [
            'title' => 'Add Supplier',
            'view' => 'AddSupplierForm',
            'stylesheets' => ['suppliers']
        ]);
    }

    public function addSupplier()
    {
        App\Utils::requireAuth();

        $supplier = new App\Models\Supplier();
        $supplier->addSupplier();

        header("Location: /suppliers");
        exit;

        //header(Consts::HEADER_JSON);
        //echo json_encode(['success' => true, 'data' => 'Supplier added successfully']);

        

    }

    public function deleteSupplier(): void
    {
        App\Utils::requireAuth();

        $supplierID = $_POST['supplier-id'] ?? null; // Securely fetch supplier ID

        if (!$supplierID) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing supplier ID']);
            return;
        }

        $supplier = new App\Models\Supplier();
        $result = $supplier->deleteSupplier($supplierID);

        if ($result) {
            header("Location: /suppliers"); // Redirect to supplier list after successful deletion
            exit;
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete supplier']);
        }
    }

    public function edit(): void
{
    Utils::requireAuth();

    // Retrieve supplier ID from the query parameter
    $supplierID = $_GET['id'] ?? null;

    if (!$supplierID) {
        // Redirect if no ID is provided
        header("Location: /suppliers");
        exit;
    }

    // Fetch existing supplier details
    $supplier = new App\Models\Supplier();
    $supplierDetails = $supplier->getSupplierDetails($supplierID);

    // If supplier not found, redirect or show error
    if (!$supplierDetails) {
        View::render('Template', [
            'title' => 'Supplier Not Found',
            'view' => 'Error',
            'errorMessage' => 'Supplier not found.'
        ]);
        return;
    }

    // Render the form with the existing supplier details
    View::render('Template', [
        'title' => 'Edit Supplier Details',
        'view' => 'AddSupplierForm', // Reuse the same form view
        'stylesheets' => ['suppliers'],
        'supplier' => $supplierDetails // Pass details to populate form
    ]);
}

public function updateSupplier(): void
{
    Utils::requireAuth();

    $supplierID = $_POST['supplier-id'] ?? null;

    if (!$supplierID) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing supplier ID']);
        return;
    }

    $supplier = new App\Models\Supplier();
    $result = $supplier->updateSupplier($supplierID, $_POST);

    if ($result) {
        // Redirect to suppliers page after update
        header("Location: /suppliers/details?id=" . $supplierID);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update supplier']);
    }
}

}
?>