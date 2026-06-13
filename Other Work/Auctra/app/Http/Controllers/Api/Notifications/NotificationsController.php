<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
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
        return successResponse('send notification successfully', [], 200);
    }

    public function sendToAll(Request $request, NotificationService $service)
    {
        $service->sendToAllUsers(
            $request->title,
            $request->body
        );
        return successResponse('send notification to all users successfully', [], 200);
    }
}
