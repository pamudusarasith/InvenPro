<?php

namespace App\Models;

use App\Core\Model;

class NotificationModel extends Model
{
  /**
   * Create a new notification
   *
   * @param int $userId The user ID who will receive the notification
   * @param string $title The notification title
   * @param string $message The notification message
   * @param string $type The notification type (info, warning, error, success)
   * @param string $priority The notification priority (low, normal, high)
   * @param array|null $metadata Additional data as JSON
   * @param string|null $expiresAt Optional expiration timestamp
   * @return int The ID of the created notification
   */
  public function create(
    int $userId,
    string $title,
    string $message,
    string $type = 'info',
    string $priority = 'normal',
    ?array $metadata = null,
    ?string $expiresAt = null
  ): int {
    $sql = '
            INSERT INTO notification (
                user_id, title, message, type, priority, metadata, expires_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ';

    $metadataJson = $metadata ? json_encode($metadata) : null;

    self::$db->query($sql, [
      $userId,
      $title,
      $message,
      $type,
      $priority,
      $metadataJson,
      $expiresAt
    ]);

    return self::$db->lastInsertId();
  }

  /**
   * Get all notifications for a user
   *
   * @param int $userId The user ID
   * @param bool $unreadOnly Whether to only get unread notifications
   * @param int|null $limit Max number of notifications to return
   * @return array Notifications
   */
  public function getForUser(int $userId, bool $unreadOnly = false, ?int $limit = null): array
  {
    $sql = '
            SELECT
                id, title, message, type, priority, metadata, is_read,
                created_at, expires_at
            FROM notification
            WHERE user_id = ?
        ';

    $params = [$userId];

    if ($unreadOnly) {
      $sql .= ' AND is_read = 0';
    }

    // Only show notifications that haven't expired
    $sql .= ' AND (expires_at IS NULL OR expires_at > NOW())';

    $sql .= ' ORDER BY created_at DESC';

    if ($limit) {
      $sql .= ' LIMIT ?';
      $params[] = $limit;
    }

    $stmt = self::$db->query($sql, $params);
    $notifications = $stmt->fetchAll();

    // Parse metadata JSON
    foreach ($notifications as &$notification) {
      if (!empty($notification['metadata'])) {
        $notification['metadata'] = json_decode($notification['metadata'], true);
      }
    }

    return $notifications;
  }

  /**
   * Mark a notification as read
   *
   * @param int $id The notification ID
   * @param int $userId The user ID (for security)
   * @return bool Success
   */
  public function markAsRead(int $id, int $userId): bool
  {
    $sql = '
            UPDATE notification
            SET is_read = 1, read_at = NOW()
            WHERE id = ? AND user_id = ?
        ';

    $stmt = self::$db->query($sql, [$id, $userId]);
    return $stmt->rowCount() > 0;
  }

  /**
   * Mark all notifications as read for a user
   *
   * @param int $userId The user ID
   * @return int Number of notifications marked as read
   */
  public function markAllAsRead(int $userId): int
  {
    $sql = '
            UPDATE notification
            SET is_read = 1, read_at = NOW()
            WHERE user_id = ? AND is_read = 0
        ';

    $stmt = self::$db->query($sql, [$userId]);
    return $stmt->rowCount();
  }

  /**
   * Delete a notification
   *
   * @param int $id The notification ID
   * @param int $userId The user ID (for security)
   * @return bool Success
   */
  public function delete(int $id, int $userId): bool
  {
    $sql = 'DELETE FROM notification WHERE id = ? AND user_id = ?';

    $stmt = self::$db->query($sql, [$id, $userId]);
    return $stmt->rowCount() > 0;
  }

  /**
   * Delete all notifications for a user
   *
   * @param int $userId The user ID
   * @return int Number of deleted notifications
   */
  public function deleteAll(int $userId): int
  {
    $sql = 'DELETE FROM notification WHERE user_id = ?';

    $stmt = self::$db->query($sql, [$userId]);
    return $stmt->rowCount();
  }

  /**
   * Count user's unread notifications
   *
   * @param int $userId The user ID
   * @return int Count of unread notifications
   */
  public function countUnread(int $userId): int
  {
    $sql = '
            SELECT COUNT(*) as count
            FROM notification
            WHERE user_id = ?
            AND is_read = 0
            AND (expires_at IS NULL OR expires_at > NOW())
        ';

    $stmt = self::$db->query($sql, [$userId]);
    $result = $stmt->fetch();

    return (int)$result['count'];
  }
}
