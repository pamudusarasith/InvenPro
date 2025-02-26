<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\CustomerModel;

class CustomerController extends Controller
{
  public function createCustomer(): void
  {
    $this->validator->validateCreateCustomer($_POST);
    $customerModel = new CustomerModel();
    $customerModel->createCustomer($_POST);

    $_SESSION['message'] = 'Customer created successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/pos');
  }

  public function search(): void
  {
    $query = $_GET['q'];
    if (empty($query)) {
      self::sendJSON([
        'success' => false,
        'message' => 'Query cannot be empty',
      ]);
    }

    $customerModel = new CustomerModel();
    $customer = $customerModel->getCustomerByPhone($_GET['q']);

    if ($customer === false) {
      self::sendJSON([
        'success' => false,
        'message' => 'Customer not found',
      ]);
    } else {
      self::sendJSON([
        'success' => true,
        'data' => $customer,
      ]);
    }
  }
}