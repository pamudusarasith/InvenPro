<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\OrderModel;
use App\Services\RBACService;

class OrderController extends Controller
{
  public function __construct()
  {
    parent::__construct();
    RBACService::requireAuthentication();
  }

  public function index()
  {
    if (!RBACService::hasPermission('view_purchase_orders')) {
      $_SESSION['message'] = 'You do not have permission to view orders';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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
    if (!RBACService::hasPermission('view_purchase_orders')) {
      $_SESSION['message'] = 'You do not have permission to view orders';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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
    if (!RBACService::hasPermission('create_purchase_orders')) {
      $_SESSION['message'] = 'You do not have permission to create orders';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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
    if (!RBACService::hasPermission('edit_purchase_orders')) {
      $_SESSION['message'] = 'You do not have permission to edit orders';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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

    $order['order_date'] = $order['order_date'] ? explode(' ', $order['order_date'])[0] : null;
    $order['expected_date'] = $order['expected_date'] ?: null;
    $order['notes'] = $order['notes'] ?: null;
    error_log(print_r($order, true));
    if (!$this->validator->validateUpdateOrder($order)) {
      $_SESSION['message'] = $this->validator->getError();
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders/' . $orderId);
      return;
    }

    $orderModel->updateOrder($orderId, $order);

    $_SESSION['message'] = 'Order updated successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/orders/' . $orderId);
  }

  public function deleteOrder(array $params)
  {
    if (!RBACService::hasPermission('delete_purchase_orders')) {
      $_SESSION['message'] = 'You do not have permission to delete orders';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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
    if (!RBACService::hasPermission('approve_purchase_orders')) {
      $_SESSION['message'] = 'You do not have permission to approve orders';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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

    if ($order['expected_date'] && $order['expected_date'] < date('Y-m-d')) {
      $_SESSION['message'] = 'Order cannot be approved due to past expected date';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders/' . $orderId);
      return;
    }

    $orderModel->approveOrder($orderId);

    $_SESSION['message'] = 'Order approved successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/orders/' . $orderId);
  }

  public function completeOrder(array $params)
  {
    if (!RBACService::hasPermission('complete_purchase_orders')) {
      $_SESSION['message'] = 'You do not have permission to complete orders';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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
    if (!RBACService::hasPermission('cancel_purchase_orders')) {
      $_SESSION['message'] = 'You do not have permission to cancel orders';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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
    if (!RBACService::hasPermission('receive_purchase_orders')) {
      $_SESSION['message'] = 'You do not have permission to receive orders';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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

    $_SESSION['message'] = 'Received items added successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/orders/' . $orderId);
  }
}
