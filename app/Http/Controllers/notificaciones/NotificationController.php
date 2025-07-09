<?php

namespace App\Http\Controllers\notificaciones;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Obtiene las notificaciones no leídas de un usuario.
     */
    public function getUnread($userId)
    {
        // CORRECCIÓN: Usamos 'user_id' y 'read' = false
        $notifications = Notification::where('user_id', $userId)
            ->where('read', false) // Busca donde read es 0
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Marca una notificación específica como leída.
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);

        // CORRECCIÓN: Asignamos 'read' a true (esto lo guardará como 1)
        $notification->read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }

    /**
     * Marca todas las notificaciones de un usuario como leídas.
     */
    public function clearAll($userId)
    {
        // CORRECCIÓN: Actualizamos en base a 'user_id' y 'read'
        Notification::where('user_id', $userId)
            ->where('read', false)
            ->update(['read' => true]); // Cambia read a 1 para todas las no leídas

        return response()->json(['success' => true]);
    }
}
