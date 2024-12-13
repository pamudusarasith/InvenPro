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


    public function delete()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $phone = $data['phone'] ?? null;

        if ($phone) {
            $customerModel = new App\Models\Customer();
            $result = $customerModel->deleteCustomerByPhone($phone);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid phone number']);
        }
    }


    public function update()
    {
        $customerModel = new App\Models\Customer();
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];

        $result = $customerModel->updateCustomer($name, $email, $phone, $address, $dob, $gender);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update customer']);
        }
    }

    public function retrieve()
    {
        $customerModel = new App\Models\Customer();
        $phone = $_POST['phone'];

        $result = $customerModel->getCustomerByPhone($phone);
        if ($result) {
            echo json_encode(['success' => true, 'customer' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not find the customer.']);
        }
    }
}
