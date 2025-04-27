<?php

use App\Services\NotificationService;

function getTimeAgo($timestamp)
{
    $time = strtotime($timestamp);
    $time_difference = time() - $time;

    if ($time_difference < 60) {
        return 'Just now';
    } elseif ($time_difference < 3600) {
        $minutes = round($time_difference / 60);
        return $minutes . ' min' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($time_difference < 86400) {
        $hours = round($time_difference / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($time_difference < 604800) { // 7 days
        $days = round($time_difference / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $time);
    }
}

$unreadCount = NotificationService::getUnreadCount();
$notifications = NotificationService::getNotifications(false, 10);

?>

<nav class="navbar">
    <div class="navbar-content">
        <div class="navbar-left">
            <button class="menu-toggle" id="menuToggle">
                <span class="icon">menu</span>
            </button>
            <img class="navbar-logo" src="/images/logo-light.png" alt="logo">
        </div>

        <div class="navbar-right">
            <div id="notifications" class="dropdown">
                <button class="dropdown-trigger notification-btn">
                    <span class="icon">notifications</span>
                    <?php if ($unreadCount > 0): ?>
                        <span class="notification-badge"><?= $unreadCount > 99 ? '99+' : $unreadCount ?></span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu">
                    <div class="dropdown-header">
                        <h4>Notifications</h4>
                        <?php if ($unreadCount > 0): ?>
                            <button class="mark-all-read" id="markAllRead">Mark all read</button>
                        <?php endif; ?>
                    </div>
                    <div class="dropdown-items">
                        <?php if (empty($notifications)): ?>
                            <div class="no-notifications">
                                <span class="icon">notifications_none</span>
                                <p>No notifications</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notification): ?>
                                <?php
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
                                }

                                // Additional classes based on notification properties
                                $itemClasses = [];
                                if ($isUnread) $itemClasses[] = 'unread';
                                if ($notificationType) $itemClasses[] = $notificationType;
                                if ($notificationPriority === 'high') $itemClasses[] = 'high-priority';

                                $classes = !empty($itemClasses) ? ' ' . implode(' ', $itemClasses) : '';
                                ?>
                                <div class="notification-item<?= $classes ?>" data-id="<?= $notification['id'] ?>">
                                    <span class="icon"><?= $icon ?></span>
                                    <div class="notification-content">
                                        <p class="notification-title"><?= htmlspecialchars($notification['title']) ?></p>
                                        <p class="notification-message"><?= htmlspecialchars($notification['message']) ?></p>
                                        <span class="notification-time"><?= $timeAgo ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (count($notifications) > 0): ?>
                                <div class="notification-footer">
                                    <button class="view-all-btn">View all notifications</button>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="navbar-divider"></div>

            <div id="profile" class="dropdown">
                <button class="dropdown-trigger profile-btn">
                    <div class="profile-info">
                        <p class="profile-name"><?= $_SESSION['user']["display_name"] ?></p>
                        <p class="profile-role"><?= $_SESSION['user']["role_name"] ?></p>
                    </div>
                    <span class="icon">expand_more</span>
                </button>
                <div class="dropdown-menu">
                    <a href="/profile" class="dropdown-item">
                        <span class="icon">account_circle</span>
                        <span>My Profile</span>
                    </a>
                    <a href="/logout" class="dropdown-item">
                        <span class="icon">logout</span>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('menuToggle');
        const sidebar = document.querySelector('.sidebar');

        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
        });
    });
</script>

<!-- Include the centralized notifications.js file -->
<script src="/js/notifications.js"></script>