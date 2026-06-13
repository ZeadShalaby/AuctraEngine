<?php

namespace App\Services;

use App\Repositories\Interfaces\NotificationsRepositoryInterface;

class NotificationService
{
    public function __construct(
        protected NotificationsRepositoryInterface $notificationsRepo,
        protected FirebaseService $firebase
    ) {
    }

    public function sendToAllUsers(string $title, string $body)
    {
        return $this->firebase->messaging()->send([
            'topic' => 'all_users',
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ]);
    }

    public function sendNotification(array $data)
    {
        // 1. save notification in DB
        $this->notificationsRepo->saveNotification($data);

        // 2. get tokens
        $tokens = $this->notificationsRepo->getUserFcmTokens($data['user_id']);

        // 3. send via Firebase
        foreach ($tokens as $token) {
            $this->firebase->messaging()->send([
                'token' => $token,
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['body'],
                ],
            ]);
        }

        return true;
    }

    public function getUserNotifications($user)
    {
        return $this->notificationsRepo->getUserNotifications($user);
    }

    public function getUnreadNotifications($user)
    {
        return $this->notificationsRepo->getUnreadNotifications($user);
    }

    public function markAsRead($user,$notificationId)
    {
        return $this->notificationsRepo->markAsRead($user,$notificationId);
    }

    public function markAllAsRead($user)
    {
        return $this->notificationsRepo->markAllAsRead($user);
    }

    public function saveFcmToken($user, string $token ,$device_type = null)
    {
        return $this->notificationsRepo->saveFcmToken($user, $token, $device_type);
    }

}