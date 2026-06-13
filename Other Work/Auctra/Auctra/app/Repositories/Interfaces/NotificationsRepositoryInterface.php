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
    public function getUserNotifications($user);

    /**
     * todo Get unread notifications for a user
     */
    public function getUnreadNotifications($user);

    /**
     * todo Mark single notification as read
     */
    public function markAsRead($user, $notificationId);

    /**
     * todo Mark all notifications as read for a user
     */
    public function markAllAsRead($user);

    /**
     * todo Save FCM token for a user
     */
    public function saveFcmToken($user,string $token,$device_type=null);

    /**
     * todo Get all FCM tokens for a user
     */
    public function getUserFcmTokens($user);

    /**
     * !Delete FCM token (optional but important)
     */
    public function deleteFcmToken($user,string $token);
}