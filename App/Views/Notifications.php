<?php

use App\Services\NotificationService;

// Get current filters from request
$currentFilter = $_GET['filter'] ?? 'all';
$searchQuery = $_GET['q'] ?? '';

// Parse other query parameters
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
$itemsPerPage = isset($_GET['ipp']) ? intval($_GET['ipp']) : 10;

// Helper function to get badge class based on notification type
function getNotificationBadgeClass($type, $priority)
{
  if ($priority === 'high') {
    return 'danger';
  }

  switch ($type) {
    case 'success':
      return 'success';
    case 'warning':
      return 'warning';
    case 'error':
      return 'danger';
    case 'info':
    default:
      return 'info';
  }
}

// Get notifications with pagination
$notifications = NotificationService::getNotifications(false, null);

// Get unread count for filter badge
$unreadCount = NotificationService::getUnreadCount();
?>

<link rel="stylesheet" href="/css/pages/notifications.css">

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <div class="card glass page-header">
      <div class="header-content">
        <h1>Notifications</h1>
        <p class="subtitle">View and manage your notifications</p>
      </div>

      <div class="header-actions">
        <?php if (!empty($notifications) && $unreadCount > 0): ?>
          <button class="btn btn-primary" id="markAllReadBtn">
            <span class="icon">done_all</span>
            Mark All As Read
          </button>
        <?php endif; ?>
      </div>
    </div>

    <!-- Filters and search controls -->
    <div class="card glass controls">
      <div class="search-bar-with-btn">
        <span class="icon">search</span>
        <input type="text" id="notificationSearch" placeholder="Search notifications..."
          value="<?= htmlspecialchars($searchQuery) ?>">
        <button class="icon-btn" onclick="applyFilters()">
          <span class="icon">search</span>
        </button>
      </div>

      <div class="filters">
        <button class="filter-chip <?= $currentFilter === 'all' ? 'active' : '' ?>" data-filter="all" onclick="setFilter('all')">
          All
        </button>
        <button class="filter-chip <?= $currentFilter === 'unread' ? 'active' : '' ?>" data-filter="unread" onclick="setFilter('unread')">
          Unread
          <?php if ($unreadCount > 0): ?>
            <span class="badge danger"><?= $unreadCount ?></span>
          <?php endif; ?>
        </button>
        <button class="filter-chip <?= $currentFilter === 'high' ? 'active' : '' ?>" data-filter="high" onclick="setFilter('high')">
          High Priority
        </button>
      </div>
    </div>

    <!-- Notification cards container -->
    <div class="notification-cards-container">
      <?php if (empty($notifications)): ?>
        <div class="card glass empty-state">
          <span class="icon large">notifications_none</span>
          <h3>No notifications</h3>
          <p>You don't have any notifications at the moment.</p>
        </div>
      <?php else: ?>
        <?php foreach ($notifications as $notification):
          $isUnread = !$notification['is_read'];
          $notificationType = $notification['type']; // info, warning, error, success
          $notificationPriority = $notification['priority']; // low, normal, high
          $timeAgo = getTimeAgo($notification['created_at']);

          // Determine icon based on notification type and metadata
          $icon = 'notifications';
          if (isset($notification['metadata']['action'])) {
            switch ($notification['metadata']['action']) {
              case 'view_product':
                $icon = 'inventory_2';
                break;
              case 'view_order':
                $icon = 'local_shipping';
                break;
              default:
                $icon = 'notifications';
            }
          } else {
            // Fallback icons based on notification type
            switch ($notificationType) {
              case 'success':
                $icon = 'check_circle';
                break;
              case 'warning':
                $icon = 'warning';
                break;
              case 'error':
                $icon = 'error';
                break;
              default:
                $icon = 'info';
            }
          }

          // Additional classes based on notification properties
          $itemClasses = ['card', 'glass', 'notification-card'];
          if ($isUnread) $itemClasses[] = 'unread';
          if ($notificationType) $itemClasses[] = $notificationType;
          if ($notificationPriority === 'high') $itemClasses[] = 'high-priority';

          $classes = implode(' ', $itemClasses);
        ?>
          <div class="<?= $classes ?>" data-id="<?= $notification['id'] ?>">
            <div class="notification-header">
              <div class="notification-meta">
                <span class="notification-icon <?= $notificationType ?>">
                  <span class="icon"><?= $icon ?></span>
                </span>
                <div class="notification-info">
                  <h3 class="notification-title"><?= htmlspecialchars($notification['title']) ?></h3>
                  <span class="notification-time"><?= $timeAgo ?></span>
                </div>
              </div>
              <div class="notification-indicators">
                <?php if ($notificationPriority === 'high'): ?>
                  <span class="badge danger">High Priority</span>
                <?php endif; ?>
                <?php if ($isUnread): ?>
                  <span class="badge primary">New</span>
                <?php endif; ?>
              </div>
            </div>

            <div class="notification-body">
              <p class="notification-message"><?= htmlspecialchars($notification['message']) ?></p>
            </div>

            <div class="notification-footer">
              <?php if (!empty($notification['metadata'])): ?>
                <?php if (isset($notification['metadata']['action'])): ?>
                  <?php if ($notification['metadata']['action'] === 'view_product' && isset($notification['metadata']['product_id'])): ?>
                    <button class="btn btn-sm btn-primary view-action-btn"
                      data-url="/products/<?= $notification['metadata']['product_id'] ?>">
                      <span class="icon">visibility</span>
                      View Product
                    </button>
                  <?php elseif ($notification['metadata']['action'] === 'view_order' && isset($notification['metadata']['order_id'])): ?>
                    <button class="btn btn-sm btn-primary view-action-btn"
                      data-url="/orders/<?= $notification['metadata']['order_id'] ?>">
                      <span class="icon">visibility</span>
                      View Order
                    </button>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>

              <?php if ($isUnread): ?>
                <button class="btn btn-sm btn-outline mark-read-btn" data-id="<?= $notification['id'] ?>">
                  <span class="icon">check_circle</span>
                  Mark as read
                </button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>

        <!-- Pagination controls -->
        <div class="pagination-controls card mt-lg">
          <div class="items-per-page">
            <span>Show:</span>
            <select class="items-select" onchange="changeItemsPerPage(this.value)">
              <option value="10" <?= $itemsPerPage === 10 ? 'selected' : '' ?>>10</option>
              <option value="25" <?= $itemsPerPage === 25 ? 'selected' : '' ?>>25</option>
              <option value="50" <?= $itemsPerPage === 50 ? 'selected' : '' ?>>50</option>
              <option value="100" <?= $itemsPerPage === 100 ? 'selected' : '' ?>>100</option>
            </select>
            <span>entries</span>
          </div>

          <div class="pagination" data-page="<?= $page ?>" data-total-pages="<?= $totalPages ?? 1 ?>">
            <!-- Pagination will be generated here by JavaScript -->
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Reference the centralized notifications.js file -->
<script src="/js/notifications.js"></script>