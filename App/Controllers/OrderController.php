<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\OrderModel;

class OrderController extends Controller
{
  public function index()
  {
    $page = $_GET['page'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;
    $orderModel = new OrderModel();
    $orders = $orderModel->getOrders($page, $itemsPerPage);
    View::renderTemplate('Orders', [
      'title' => 'Purchase Orders',
      'orders' => $orders
    ]);
  }

  public function createOrder()
  {
    foreach ($_POST['order_items'] as &$item) {
      $item = json_decode($item, true);
    }

    if (!$this->validator->validateCreateOrder($_POST)) {
      $_SESSION['message'] = $this->validator->getError();
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }
    
    $orderModel = new OrderModel();
    $orderModel->createOrder($_POST);
    
    $_SESSION['message'] = 'Order created successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/orders');
  }
}
