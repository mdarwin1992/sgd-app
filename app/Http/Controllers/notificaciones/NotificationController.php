<?php

namespace App\Http\Controllers\notificaciones;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getUnreadNotifications($id)
    {
        $user_id = $id;
        $notifications = Notification::where('user_id', $user_id)
            ->where('read', false)
            ->with('correspondenceTransfer.document')
            ->orderBy('created_at', 'desc')
            ->get();

       // dd($notifications);

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
