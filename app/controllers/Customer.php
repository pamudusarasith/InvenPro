<?php

namespace App\Controllers;

use App;
use App\Consts;

class Customer
{

    public function newCustomer()
    {
        App\Utils::requireAuth();
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        $customer = new App\Models\Customer();
        $customer->addCustomer($input);
        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'data' => 'Customer added successfully']);
    }
}
