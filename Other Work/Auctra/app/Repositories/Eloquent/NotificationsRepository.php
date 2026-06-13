<?php

namespace App\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\NotificationsRepositoryInterface;

class NotificationsRepository implements NotificationsRepositoryInterface
{
    public function saveNotification(array $data)
    {
        return DB::table('notifications')->insert([
            'user_id' => $data['user_id'],
            'title'   => $data['title'],
            'body'    => $data['body'],
            'data'    => json_encode($data['data'] ?? null),
            'read_at' => null,
            'created_at' => now(),
        ]);
    }

    public function getUserNotifications(int $userId)
    {
        return DB::table('notifications')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();
    }

    public function getUnreadNotifications(int $userId)
    {
        return DB::table('notifications')
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->get();
    }

    public function markAsRead(int $notificationId)
    {
        return DB::table('notifications')
            ->where('id', $notificationId)
            ->update(['read_at' => now()]);
    }

    public function markAllAsRead(int $userId)
    {
        return DB::table('notifications')
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function saveFcmToken(int $userId, string $token)
    {
        return DB::table('user_fcm_tokens')->updateOrInsert(
            [
                'user_id' => $userId,
                'fcm_token' => $token,
            ],
            [
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function getUserFcmTokens(int $userId)
    {
        return DB::table('user_fcm_tokens')
            ->where('user_id', $userId)
            ->pluck('fcm_token')
            ->toArray();
    }

    public function deleteFcmToken(string $token)
    {
        return DB::table('user_fcm_tokens')
            ->where('fcm_token', $token)
            ->delete();
    }
}