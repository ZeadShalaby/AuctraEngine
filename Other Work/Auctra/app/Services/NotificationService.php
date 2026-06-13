<?php

namespace App\Services;

use App\Repositories\Interfaces\NotificationsRepositoryInterface;

class NotificationService
{
    public function __construct(
        private NotificationsRepositoryInterface $repo,
        private FirebaseService $firebase
    ) {
    }

    public function sendToAllUsers($title, $body)
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
        $this->repo->saveNotification($data);

        // 2. get tokens
        $tokens = $this->repo->getUserFcmTokens($data['user_id']);

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

    public function getUserNotifications(int $userId)
    {
        return $this->repo->getUserNotifications($userId);
    }

    public function getUnreadNotifications(int $userId)
    {
        return $this->repo->getUnreadNotifications($userId);
    }

    public function markAsRead(int $notificationId)
    {
        return $this->repo->markAsRead($notificationId);
    }

    public function markAllAsRead(int $userId)
    {
        return $this->repo->markAllAsRead($userId);
    }

    public function saveFcmToken(int $userId, string $token)
    {
        return $this->repo->saveFcmToken($userId, $token);
    }
}