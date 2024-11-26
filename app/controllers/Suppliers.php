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
            'stylesheets' => ['suppliers'],
            'scripts' => ['suppliers'],
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

        header("Location: /suppliers");
        exit;

        //header(Consts::HEADER_JSON);
        //echo json_encode(['success' => true, 'data' => 'Supplier added successfully']);

        

    }

    public function deleteSupplier(): void
    {
        App\Utils::requireAuth();

        // Ensure the supplier ID is provided in the request (e.g., as a POST or GET parameter)
        $supplierID = $_POST['supplier-id'] ?? null; // Use POST for secure deletion
        if (!$supplierID) {
            // If no supplier ID is provided, handle the error
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Missing supplier ID']);
            return;
        }

        // Call the deleteSupplier method from the Supplier model
        $supplier = new App\Models\Supplier();
        $result = $supplier->deleteSupplier($supplierID);

        if ($result) {
            // Redirect or send success response
            http_response_code(200);
            header("Location: /suppliers");
            exit;
            //echo json_encode(['success' => true, 'message' => 'Supplier deleted successfully']);
        } else {
            // Handle deletion failure
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Failed to delete supplier']);
        }
    }
}
?>