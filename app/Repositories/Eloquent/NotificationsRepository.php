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

    public function getUserNotifications($user)
    {
        return $user->notifications()->orderBy('created_at', 'desc')->get();
    }


    public function getUnreadNotifications($user)
    {

        return $user->unreadNotifications()->orderBy('created_at', 'desc')->get();
    }

    public function markAsRead($user , $notificationId)
    {
        return $user->notifications()->where('id', $notificationId)->update(['read_at' => now()]);
    }

    public function markAllAsRead($user)
    {
        return $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function saveFcmToken($user, string $token ,$device_type = null)
    {
        return DB::table('users_fcm_tokens')->updateOrInsert(
            [
                'user_id' => $userId,
                'fcm_token' => $token,
                'device_type' => $device_type,
            ],
            [
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function getUserFcmTokens($user)
    {
        return DB::table('users_fcm_tokens')
            ->where('user_id', $user->id)
            ->pluck('fcm_token')
            ->toArray();
    }

    public function deleteFcmToken($user,string $token)
    {
        return DB::table('users_fcm_tokens')
            ->where('fcm_token', $token)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->delete();
    }
}