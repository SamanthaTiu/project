<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Notifications extends BaseController
{
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->session = session();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get notifications for the logged-in user.
     * Returns JSON with unread_count and notifications array.
     */
    public function index()
    {
        $userId = $this->session->get('user_id') ?? $this->session->get('id');

        if (empty($userId)) {
            return $this->response
                        ->setStatusCode(401)
                        ->setJSON(['status' => 'error', 'message' => 'User not logged in']);
        }

        // Get unread count
        $unreadCount = $this->db->table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        // Get all notifications (unread first, limit to recent ones)
        $notifications = $this->db->table('notifications')
            ->where('user_id', $userId)
            ->orderBy('is_read', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a notification as read.
     * Expects POST with id parameter.
     */
    public function mark_as_read($id)
    {
        $userId = $this->session->get('user_id') ?? $this->session->get('id');

        if (empty($userId)) {
            return $this->response
                        ->setStatusCode(401)
                        ->setJSON(['status' => 'error', 'message' => 'User not logged in']);
        }

        // Update the notification to mark as read
        $updated = $this->db->table('notifications')
            ->where('id', $id)
            ->where('user_id', $userId) // Ensure user can only mark their own notifications
            ->update(['is_read' => 1]);

        if ($updated) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response
                        ->setStatusCode(404)
                        ->setJSON(['status' => 'error', 'message' => 'Notification not found or already read']);
        }
    }

    /**
     * Mark a notification as unread.
     * Expects POST with id parameter.
     */
    public function mark_as_unread($id)
    {
        $userId = $this->session->get('user_id') ?? $this->session->get('id');

        if (empty($userId)) {
            return $this->response
                        ->setStatusCode(401)
                        ->setJSON(['status' => 'error', 'message' => 'User not logged in']);
        }

        // Update the notification to mark as unread
        $updated = $this->db->table('notifications')
            ->where('id', $id)
            ->where('user_id', $userId) // Ensure user can only mark their own notifications
            ->update(['is_read' => 0]);

        if ($updated) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response
                        ->setStatusCode(404)
                        ->setJSON(['status' => 'error', 'message' => 'Notification not found or already unread']);
        }
    }
}
