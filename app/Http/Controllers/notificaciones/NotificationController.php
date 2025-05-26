<?php

namespace App\Http\Controllers\notificaciones;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getUnreadNotifications($userId)
    {
        $notifications = Notification::where('user_id', $userId)
            ->where('read', false)
            ->with('correspondenceTransfer.document')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }
}
