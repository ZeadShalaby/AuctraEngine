<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\FcmTokenRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    //
    public function send(Request $request, NotificationService $service)
    {
        $service->sendNotification([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'body' => $request->body,
        ]);
        return successResponse(__('messages.success'), [], 200);
    }

    public function sendToAll(Request $request, NotificationService $service)
    {
        $service->sendToAllUsers(
            $request->title,
            $request->body
        );
        return successResponse(__('messages.success'), [], 200);
    }


    public function getNotifications(Request $request, NotificationService $service)
    {
        $notifications = $service->getUserNotifications(auth()->user());
        return successResponse(__('messages.success'), $notifications, 200);
    }

    public function getUnreadNotifications(Request $request, NotificationService $service)
    {
        $notifications = $service->getUnreadNotifications(auth()->user());
        return successResponse(__('messages.success'), $notifications, 200);
    }

    public function markAsRead($notification_id, NotificationService $service)
    {
        dd($notification_id);
        $service->markAsRead(auth()->user(),$notification_id);
        return successResponse(__('messages.success'), [], 200);
    }

    public function markAllAsRead(Request $request, NotificationService $service)
    {
        $service->markAllAsRead(auth()->user());
        return successResponse(__('messages.success'), [], 200);
    }

    public function saveFcmToken(FcmTokenRequest $request, NotificationService $service)
    {
        $data = $request->validated();
        $service->saveFcmToken(auth()->user(), $data['fcm_token'], $data['device_type']);
        return successResponse(__('messages.success'), [], 200);
    }
}