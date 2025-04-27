<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\OrderModel;
use App\Models\UserModel;
use App\Services\NotificationService;
use App\Services\RBACService;

class OrderController extends Controller
{
  private $orderModel;
  private $userModel;

  public function __construct()
  {
    parent::__construct();
    RBACService::requireAuthentication();
    $this->orderModel = new OrderModel();
    $this->userModel = new UserModel();
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
    $orders = $this->orderModel->getOrders($page, $itemsPerPage, $query, $status, $from, $to);
    $totalRecords = $this->orderModel->getOrdersCount($query, $status, $from, $to);
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

    $order = $this->orderModel->getOrderDetails($orderId);

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

    $orderId = $this->orderModel->createOrder($_POST);
    if ($orderId) {
      $this->orderModel->saveOrderAction($orderId, 'create');
      
      // Send notification to users with approve_purchase_orders permission
      $this->notifyOrderCreation($orderId, $_POST);
      
      $_SESSION['message'] = 'Order created successfully';
      $_SESSION['message_type'] = 'success';
      View::redirect('/orders');
    } else {
      $_SESSION['message'] = 'Order creation failed';
      $_SESSION['message_type'] = 'error';
      View::redirect('/orders');
    }
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

    $order = $this->orderModel->getOrderDetails($orderId);
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

    $this->orderModel->updateOrder($orderId, $order);
    $this->orderModel->saveOrderAction($orderId, 'update');
    
    // Send notification about order update
    $this->notifyOrderUpdate($orderId, $order);
    
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

    $order = $this->orderModel->getOrderDetails($orderId);
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

    $this->orderModel->deleteOrder($orderId);
    $this->orderModel->saveOrderAction($orderId, 'delete');
    
    // Send notification about order deletion
    $this->notifyOrderDeletion($orderId, $order);
    
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

    $order = $this->orderModel->getOrderDetails($orderId);
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

    $this->orderModel->approveOrder($orderId);
    $this->orderModel->saveOrderAction($orderId, 'approve');
    
    // Send notification about order approval
    $this->notifyOrderApproval($orderId, $order);
    
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

    $order = $this->orderModel->getOrderDetails($orderId);
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

    $this->orderModel->completeOrder($orderId);
    $this->orderModel->saveOrderAction($orderId, 'complete');
    
    // Send notification about order completion
    $this->notifyOrderCompletion($orderId, $order);
    
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

    $order = $this->orderModel->getOrderDetails($orderId);
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

    $this->orderModel->changeOrderStatus($orderId, 'canceled');
    $this->orderModel->saveOrderAction($orderId, 'cancel');
    
    // Send notification about order cancellation
    $this->notifyOrderCancellation($orderId, $order);
    
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

    $order = $this->orderModel->getOrderDetails($orderId);
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

    $this->orderModel->receiveOrderItems($orderId, $_POST);
    $this->orderModel->saveOrderAction($orderId, 'receive');
    
    // Send notification about items received
    $this->notifyOrderItemsReceived($orderId, $order, $_POST);
    
    $_SESSION['message'] = 'Received items added successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/orders/' . $orderId);
  }

  /**
   * Send notification about order creation
   *
   * @param int $orderId The order ID
   * @param array $orderData The order data
   */
  private function notifyOrderCreation(int $orderId, array $orderData): void
  {
    // Get users with permission to approve orders
    $usersWithPermission = $this->userModel->getUsersByPermission('approve_purchase_orders');
    
    // If supplier is specified, also notify the order creator
    $creatorId = $_SESSION['user']['id'];
    
    // Create the order reference for the notification
    $orderRef = "#{$orderData['reference']}";
    
    // Send notification to each approver
    foreach ($usersWithPermission as $user) {
      // Don't notify the creator twice if they also have approval permission
      if ($user['id'] != $creatorId) {
        NotificationService::sendToUser(
          $user['id'],
          'New Purchase Order',
          "A new purchase order {$orderRef} has been created and needs approval.",
          'info',
          'normal',
          [
            'order_id' => $orderId,
            'reference' => $orderData['reference'],
            'action' => 'view_order'
          ]
        );
      }
    }
    
    // Notify the creator for confirmation
    NotificationService::sendToUser(
      $creatorId,
      'Purchase Order Created',
      "Your purchase order {$orderRef} has been created successfully and is awaiting approval.",
      'success',
      'normal',
      [
        'order_id' => $orderId,
        'reference' => $orderData['reference'],
        'action' => 'view_order'
      ]
    );
  }

  /**
   * Send notification about order update
   *
   * @param int $orderId The order ID
   * @param array $orderData The order data
   */
  private function notifyOrderUpdate(int $orderId, array $orderData): void
  {
    // Get users with permission to approve orders
    $usersWithPermission = $this->userModel->getUsersByPermission('approve_purchase_orders');
    
    // Create the order reference for the notification
    $orderRef = "#{$orderData['reference']}";
    
    // Send notification to each approver
    foreach ($usersWithPermission as $user) {
      NotificationService::sendToUser(
        $user['id'],
        'Purchase Order Updated',
        "Purchase order {$orderRef} has been updated and requires review.",
        'info',
        'normal',
        [
          'order_id' => $orderId,
          'reference' => $orderData['reference'],
          'action' => 'view_order'
        ]
      );
    }
  }

  /**
   * Send notification about order deletion
   *
   * @param int $orderId The order ID
   * @param array $orderData The order data
   */
  private function notifyOrderDeletion(int $orderId, array $orderData): void
  {
    // Notify the order creator if they didn't delete it themselves
    $creatorId = $orderData['created_by'];
    $currentUserId = $_SESSION['user']['id'];
    
    if ($creatorId != $currentUserId) {
      NotificationService::sendToUser(
        $creatorId,
        'Purchase Order Deleted',
        "Purchase order #{$orderData['reference']} has been deleted.",
        'warning',
        'normal',
        [
          'reference' => $orderData['reference']
        ]
      );
    }
  }

  /**
   * Send notification about order approval
   *
   * @param int $orderId The order ID
   * @param array $orderData The order data
   */
  private function notifyOrderApproval(int $orderId, array $orderData): void
  {
    // Notify the order creator
    $creatorId = $orderData['created_by'];
    
    NotificationService::sendToUser(
      $creatorId,
      'Purchase Order Approved',
      "Your purchase order #{$orderData['reference']} has been approved.",
      'success',
      'normal',
      [
        'order_id' => $orderId,
        'reference' => $orderData['reference'],
        'action' => 'view_order'
      ]
    );
    
    // Notify users with receive_purchase_orders permission
    $usersWithPermission = $this->userModel->getUsersByPermission('receive_purchase_orders');
    
    foreach ($usersWithPermission as $user) {
      // Don't notify the creator twice if they are also have receiving permission
      if ($user['id'] != $creatorId) {
        NotificationService::sendToUser(
          $user['id'],
          'Purchase Order Ready for Receiving',
          "Purchase order #{$orderData['reference']} has been approved and is ready for receiving.",
          'info',
          'normal',
          [
            'order_id' => $orderId,
            'reference' => $orderData['reference'],
            'action' => 'view_order'
          ]
        );
      }
    }
  }

  /**
   * Send notification about order cancellation
   *
   * @param int $orderId The order ID
   * @param array $orderData The order data
   */
  private function notifyOrderCancellation(int $orderId, array $orderData): void
  {
    // Notify the order creator if they didn't cancel it themselves
    $creatorId = $orderData['created_by'];
    $currentUserId = $_SESSION['user']['id'];
    
    if ($creatorId != $currentUserId) {
      NotificationService::sendToUser(
        $creatorId,
        'Purchase Order Cancelled',
        "Purchase order #{$orderData['reference']} has been cancelled.",
        'warning',
        'normal',
        [
          'order_id' => $orderId,
          'reference' => $orderData['reference'],
          'action' => 'view_order'
        ]
      );
    }
  }

  /**
   * Send notification about order completion
   *
   * @param int $orderId The order ID
   * @param array $orderData The order data
   */
  private function notifyOrderCompletion(int $orderId, array $orderData): void
  {
    // Notify the order creator
    $creatorId = $orderData['created_by'];
    
    NotificationService::sendToUser(
      $creatorId,
      'Purchase Order Completed',
      "Purchase order #{$orderData['reference']} has been marked as completed.",
      'success',
      'normal',
      [
        'order_id' => $orderId,
        'reference' => $orderData['reference'],
        'action' => 'view_order'
      ]
    );
    
    // Notify users with inventory_manager role
    $inventoryManagers = $this->userModel->getUsersByRole(3); // Assuming 3 is inventory_manager role ID
    
    foreach ($inventoryManagers as $user) {
      // Don't notify the creator twice if they are also an inventory manager
      if ($user['id'] != $creatorId) {
        NotificationService::sendToUser(
          $user['id'],
          'Purchase Order Completed',
          "Purchase order #{$orderData['reference']} has been completed and inventory has been updated.",
          'info',
          'normal',
          [
            'order_id' => $orderId,
            'reference' => $orderData['reference'],
            'action' => 'view_order'
          ]
        );
      }
    }
  }

  /**
   * Send notification about items received
   *
   * @param int $orderId The order ID
   * @param array $orderData The order data
   * @param array $receivedData The received items data
   */
  private function notifyOrderItemsReceived(int $orderId, array $orderData, array $receivedData): void
  {
    // Notify the order creator
    $creatorId = $orderData['created_by'];
    
    // Count received items
    $itemsCount = count($receivedData['batches']);
    
    NotificationService::sendToUser(
      $creatorId,
      'Order Items Received',
      "{$itemsCount} items from purchase order #{$orderData['reference']} have been received.",
      'info',
      'normal',
      [
        'order_id' => $orderId,
        'reference' => $orderData['reference'],
        'items_count' => $itemsCount,
        'action' => 'view_order'
      ]
    );
    
    // Notify inventory managers
    $inventoryManagers = $this->userModel->getUsersByRole(3); // Assuming 3 is inventory_manager role ID
    
    foreach ($inventoryManagers as $user) {
      // Don't notify the creator twice if they are also an inventory manager
      if ($user['id'] != $creatorId) {
        NotificationService::sendToUser(
          $user['id'],
          'New Inventory Received',
          "{$itemsCount} items from purchase order #{$orderData['reference']} have been received and added to inventory.",
          'info',
          'normal',
          [
            'order_id' => $orderId,
            'reference' => $orderData['reference'],
            'items_count' => $itemsCount,
            'action' => 'view_order'
          ]
        );
      }
    }
  }
}
