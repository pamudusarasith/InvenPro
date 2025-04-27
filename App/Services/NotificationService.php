<?php

namespace App\Services;

use App\Models\NotificationModel;
use App\Models\UserModel;

/**
 * Service class for managing notifications in the application
 */
class NotificationService
{
  private static ?NotificationService $instance = null;
  private NotificationModel $notificationModel;

  /**
   * Private constructor - prevents direct instantiation
   */
  private function __construct()
  {
    $this->notificationModel = new NotificationModel();
  }

  /**
   * Get singleton instance
   *
   * @return NotificationService The singleton instance
   */
  public static function getInstance(): NotificationService
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Get the current user ID from session
   *
   * @return int|null The current user ID or null if not logged in
   */
  private function getCurrentUserId(): ?int
  {
    return $_SESSION['user']['id'] ?? null;
  }

  /**
   * Send a notification to a user
   *
   * @param int $userId The user ID
   * @param string $title The notification title
   * @param string $message The notification message
   * @param string $type The notification type (info, warning, error, success)
   * @param string $priority The priority level (low, normal, high)
   * @param array|null $metadata Additional data
   * @param string|null $expiresAt Expiration timestamp
   * @return int The notification ID
   */
  public static function sendToUser(
    int $userId,
    string $title,
    string $message,
    string $type = 'info',
    string $priority = 'normal',
    ?array $metadata = null,
    ?string $expiresAt = null
  ): int {
    $instance = self::getInstance();
    return $instance->notificationModel->create(
      $userId,
      $title,
      $message,
      $type,
      $priority,
      $metadata,
      $expiresAt
    );
  }

  /**
   * Send a notification to multiple users
   *
   * @param array $userIds Array of user IDs
   * @param string $title The notification title
   * @param string $message The notification message
   * @param string $type The notification type
   * @param string $priority The priority level
   * @param array|null $metadata Additional data
   * @param string|null $expiresAt Expiration timestamp
   * @return array Array of created notification IDs
   */
  public static function sendToUsers(
    array $userIds,
    string $title,
    string $message,
    string $type = 'info',
    string $priority = 'normal',
    ?array $metadata = null,
    ?string $expiresAt = null
  ): array {
    $notificationIds = [];

    foreach ($userIds as $userId) {
      $notificationIds[] = self::sendToUser(
        $userId,
        $title,
        $message,
        $type,
        $priority,
        $metadata,
        $expiresAt
      );
    }

    return $notificationIds;
  }

  /**
   * Send a notification to all users with a specific role
   *
   * @param int $roleId The role ID
   * @param string $title The notification title
   * @param string $message The notification message
   * @param string $type The notification type
   * @param string $priority The priority level
   * @param array|null $metadata Additional data
   * @param string|null $expiresAt Expiration timestamp
   * @return array Array of created notification IDs
   */
  public static function sendToRole(
    int $roleId,
    string $title,
    string $message,
    string $type = 'info',
    string $priority = 'normal',
    ?array $metadata = null,
    ?string $expiresAt = null
  ): array {
    // Get all users with the specified role
    $userModel = new UserModel();
    $users = $userModel->getUsersByRole($roleId);

    if (empty($users)) {
      return [];
    }

    $userIds = array_column($users, 'id');
    return self::sendToUsers(
      $userIds,
      $title,
      $message,
      $type,
      $priority,
      $metadata,
      $expiresAt
    );
  }

  /**
   * Send a notification to all users in a specific branch
   *
   * @param int $branchId The branch ID
   * @param string $title The notification title
   * @param string $message The notification message
   * @param string $type The notification type
   * @param string $priority The priority level
   * @param array|null $metadata Additional data
   * @param string|null $expiresAt Expiration timestamp
   * @return array Array of created notification IDs
   */
  public static function sendToBranch(
    int $branchId,
    string $title,
    string $message,
    string $type = 'info',
    string $priority = 'normal',
    ?array $metadata = null,
    ?string $expiresAt = null
  ): array {
    // Get all users in the specified branch
    $userModel = new UserModel();
    $users = $userModel->getUsersByBranch($branchId);

    if (empty($users)) {
      return [];
    }

    $userIds = array_column($users, 'id');
    return self::sendToUsers(
      $userIds,
      $title,
      $message,
      $type,
      $priority,
      $metadata,
      $expiresAt
    );
  }

  /**
   * Send a notification for a low stock alert
   *
   * @param int $userId The user ID
   * @param array $product The product data (must contain 'product_name', 'current_quantity', 'reorder_level')
   * @return int The notification ID
   */
  public static function sendLowStockAlert(int $userId, array $product): int
  {
    return self::sendToUser(
      $userId,
      'Low Stock Alert',
      "Product {$product['product_name']} is low on stock ({$product['current_quantity']} remaining).",
      'warning',
      'high',
      [
        'product_id' => $product['id'] ?? null,
        'product_name' => $product['product_name'],
        'current_quantity' => $product['current_quantity'],
        'reorder_level' => $product['reorder_level'],
        'action' => 'view_product'
      ]
    );
  }

  /**
   * Send a notification for a new order
   *
   * @param int $userId The user ID
   * @param array $order The order data (must contain 'id', 'reference')
   * @return int The notification ID
   */
  public static function sendNewOrderNotification(int $userId, array $order): int
  {
    return self::sendToUser(
      $userId,
      'New Order Received',
      "A new purchase order #{$order['reference']} has been created.",
      'info',
      'normal',
      [
        'order_id' => $order['id'],
        'reference' => $order['reference'],
        'action' => 'view_order'
      ]
    );
  }

  /**
   * Get all notifications for the current user
   *
   * @param bool $unreadOnly Whether to only get unread notifications
   * @param int|null $limit Max number of notifications to return
   * @return array Notifications
   */
  public static function getNotifications(bool $unreadOnly = false, ?int $limit = null): array
  {
    $instance = self::getInstance();
    $userId = $instance->getCurrentUserId();
    if (!$userId) {
      return [];
    }

    return $instance->notificationModel->getForUser($userId, $unreadOnly, $limit);
  }

  /**
   * Mark a notification as read
   *
   * @param int $id The notification ID
   * @return bool Success
   */
  public static function markAsRead(int $id): bool
  {
    $instance = self::getInstance();
    $userId = $instance->getCurrentUserId();
    if (!$userId) {
      return false;
    }

    return $instance->notificationModel->markAsRead($id, $userId);
  }

  /**
   * Mark all notifications as read for the current user
   *
   * @return int Number of notifications marked as read
   */
  public static function markAllAsRead(): int
  {
    $instance = self::getInstance();
    $userId = $instance->getCurrentUserId();
    if (!$userId) {
      return 0;
    }

    return $instance->notificationModel->markAllAsRead($userId);
  }

  /**
   * Get the count of unread notifications for the current user
   *
   * @return int Count of unread notifications
   */
  public static function getUnreadCount(): int
  {
    $instance = self::getInstance();
    $userId = $instance->getCurrentUserId();
    if (!$userId) {
      return 0;
    }

    return $instance->notificationModel->countUnread($userId);
  }
}
