<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\OrderModel;

class OrderController extends Controller
{
  public function index()
  {
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;
    $query = $_GET['q'] ?? '';
    $status = $_GET['status'] ?? '';
    $from = $_GET['from'] ?? '';
    $to = $_GET['to'] ?? '';
    $orderModel = new OrderModel();
    $orders = $orderModel->getOrders($page, $itemsPerPage, $query, $status, $from, $to);
    $totalRecords = $orderModel->getOrdersCount($query, $status, $from, $to);
    $totalPages = ceil($totalRecords / $itemsPerPage);
    View::renderTemplate('PurchaseOrders', [
      'title' => 'Purchase Orders',
      'orders' => $orders,
      'totalPages' => $totalPages,
    ]);
  }

  public function details(array $params)
  {
    $orderId = $params['id'] ?? null;
    if (!$orderId) {
      View::redirect('/orders');
      return;
    }

    $orderModel = new OrderModel();
    $order = $orderModel->getOrderDetails($orderId);

    if (!$order) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    View::renderTemplate('PurchaseOrderDetails', [
      'title' => 'Purchase Order Details',
      'order' => $order
    ]);
  }

  public function createOrder()
  {
    foreach ($_POST['items'] as &$item) {
      $item = json_decode($item, true);
    }

    $_POST['expected_date'] = $_POST['expected_date'] ?: null;
    $_POST['notes'] = $_POST['notes'] ?: null;

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

  public function updateOrder(array $params)
  {
    $orderId = $params['id'] ?? null;
    if (!$orderId) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    $orderModel = new OrderModel();
    $order = $orderModel->getOrderDetails($orderId);
    if (!$order) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    if ($order['status'] !== 'pending') {
      $_SESSION['message'] = 'Order cannot be updated';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    foreach ($_POST['items'] as &$item) {
      $item = json_decode($item, true);
    }

    $order = array_merge($order, $_POST);

    $_POST['expected_date'] = $_POST['expected_date'] ?: null;
    $_POST['notes'] = $_POST['notes'] ?: null;

    if (!$this->validator->validateUpdateOrder($_POST)) {
      $_SESSION['message'] = $this->validator->getError();
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders/' . $orderId);
      return;
    }

    $orderModel->updateOrder($orderId, $_POST);

    $_SESSION['message'] = 'Order updated successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/orders/' . $orderId);
  }

  public function deleteOrder(array $params)
  {
    $orderId = $params['id'] ?? null;
    if (!$orderId) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    $orderModel = new OrderModel();
    $order = $orderModel->getOrderDetails($orderId);
    if (!$order) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    if (in_array($order['status'], ['open', 'completed'])) {
      $_SESSION['message'] = 'Order cannot be deleted';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    $orderModel->deleteOrder($orderId);

    $_SESSION['message'] = 'Order deleted successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/orders');
  }

  public function approveOrder(array $params)
  {
    $orderId = $params['id'] ?? null;
    if (!$orderId) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    $orderModel = new OrderModel();
    $order = $orderModel->getOrderDetails($orderId);
    if (!$order) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    if ($order['status'] !== 'pending') {
      $_SESSION['message'] = 'Order cannot be approved';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    $orderModel->changeOrderStatus($orderId, 'open');

    $_SESSION['message'] = 'Order approved successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/orders/' . $orderId);
  }

  public function completeOrder(array $params)
  {
    $orderId = $params['id'] ?? null;
    if (!$orderId) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    $orderModel = new OrderModel();
    $order = $orderModel->getOrderDetails($orderId);
    if (!$order) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    if ($order['status'] !== 'open') {
      $_SESSION['message'] = 'Order cannot be completed';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    $orderModel->completeOrder($orderId);

    $_SESSION['message'] = 'Order completed successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/orders/' . $orderId);
  }

  public function cancelOrder(array $params)
  {
    $orderId = $params['id'] ?? null;
    if (!$orderId) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    $orderModel = new OrderModel();
    $order = $orderModel->getOrderDetails($orderId);
    if (!$order) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    if ($order['status'] !== 'pending') {
      $_SESSION['message'] = 'Order cannot be canceled';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    $orderModel->changeOrderStatus($orderId, 'canceled');

    $_SESSION['message'] = 'Order canceled successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/orders/' . $orderId);
  }

  public function receiveOrder(array $params)
  {
    $orderId = $params['id'] ?? null;
    if (!$orderId) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    $orderModel = new OrderModel();
    $order = $orderModel->getOrderDetails($orderId);
    if (!$order) {
      $_SESSION['message'] = 'Order not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    if ($order['status'] !== 'open') {
      $_SESSION['message'] = 'Order cannot be received';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
      return;
    }

    foreach ($_POST['batches'] as &$batch) {
      $batch['manufactured_date'] = $batch['manufactured_date'] ?: null;
      $batch['expiry_date'] = $batch['expiry_date'] ?: null;
    }

    if (!$this->validator->validateReceiveOrder($_POST)) {
      $_SESSION['message'] = $this->validator->getError();
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders/' . $orderId);
      return;
    }

    $orderModel->receiveOrderItems($orderId, $_POST);

    $_SESSION['message'] = 'Order received successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/orders/' . $orderId);
  }
}
