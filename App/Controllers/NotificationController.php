<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    /**
     * Display the notifications page
     */
    public function index()
    {
        // Get all notifications for the current user
        $notifications = NotificationService::getNotifications();

        View::renderTemplate('Notifications', [
            'title' => 'Notifications',
            'notifications' => $notifications
        ]);
    }

    /**
     * API endpoint to mark a notification as read
     */
    public function markAsRead()
    {
        // Get JSON data from request body
        $data = self::recvJSON();

        if (!isset($data['id']) || !is_numeric($data['id'])) {
            self::sendJSON(['success' => false, 'message' => 'Invalid notification ID']);
            return;
        }

        $success = NotificationService::markAsRead((int)$data['id']);

        if ($success) {
            self::sendJSON(['success' => true]);
        } else {
            self::sendJSON(['success' => false, 'message' => 'Failed to mark notification as read']);
        }
    }

    /**
     * API endpoint to mark all notifications as read
     */
    public function markAllAsRead()
    {
        $count = NotificationService::markAllAsRead();

        self::sendJSON([
            'success' => true,
            'count' => $count
        ]);
    }
}
