<?php

namespace App\Repositories\Interfaces;

interface NotificationsRepositoryInterface
{
    /**
     * todo Send notification to user(s)
     */
    // public function sendNotification(array $data);

    /**
     * todo Save notification in DB
     */
    public function saveNotification(array $data);

    /**
     * todo Get all notifications for a user
     */
    public function getUserNotifications(int $userId);

    /**
     * todo Get unread notifications for a user
     */
    public function getUnreadNotifications(int $userId);

    /**
     * todo Mark single notification as read
     */
    public function markAsRead(int $notificationId);

    /**
     * todo Mark all notifications as read for a user
     */
    public function markAllAsRead(int $userId);

    /**
     * todo Save FCM token for a user
     */
    public function saveFcmToken(int $userId, string $token);

    /**
     * todo Get all FCM tokens for a user
     */
    public function getUserFcmTokens(int $userId);

    /**
     * !Delete FCM token (optional but important)
     */
    public function deleteFcmToken(string $token);
}