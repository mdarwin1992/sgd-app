<?php

namespace App\Observers;

use App\Models\CorrespondenceTransfer;
use App\Models\Notification;
use App\Models\Office;
use Illuminate\Support\Facades\Log;

class CorrespondenceTransferObserver
{
    /**
     * Handle the CorrespondenceTransfer "created" event.
     *
     * @param  \App\Models\CorrespondenceTransfer  $correspondenceTransfer
     * @return void
     */
    public function created(CorrespondenceTransfer $correspondenceTransfer)
    {
        try {
            // Buscamos la oficina de destino. Si no existe, findOrFail lanzará una excepción.
            $office = Office::findOrFail($correspondenceTransfer->office_id);

            // Obtenemos información adicional para construir el mensaje
            // Es buena práctica usar relaciones para evitar múltiples consultas si es posible.
            // Asumiendo que `correspondenceTransfer` tiene una relación con `correspondence`
            $correspondence = $correspondenceTransfer->correspondence;
            $subject = $correspondence ? $correspondence->subject : 'un documento';

            // --- ESTA ES LA CORRECCIÓN CLAVE ---
            // Creamos el array de datos que se guardará como JSON en la columna 'data'.
            // Estos campos (`title`, `message`, `url`, etc.) son los que tu frontend espera.
            $notificationData = [
                'title'     => 'Nueva Transferencia de Correspondencia',
                'message'   => "Has recibido una transferencia del documento '{$subject}'.",
                //'url'       => route('correspondence.show', ['id' => $correspondenceTransfer->id]), // Ejemplo de URL
               // 'icon'      => 'mdi mdi-file-document-outline', // Un icono relevante de Material Design Icons
                'transfer_id' => $correspondenceTransfer->id, // Puedes añadir IDs relevantes aquí
            ];

            // Creamos la notificación incluyendo el campo 'data'.
            // Laravel automáticamente codificará $notificationData a JSON.
            Notification::create([
                'user_id' => $office->user_id,
                'correspondence_transfer_id' => $correspondenceTransfer->id,
                'data' => $notificationData, // ¡Aquí está la magia!
            ]);

        } catch (\Exception $e) {
            // Es una buena práctica registrar cualquier error que pueda ocurrir en un observador
            // para que no falle silenciosamente.
            Log::error('Error al crear la notificación de transferencia de correspondencia: ' . $e->getMessage(), [
                'transfer_id' => $correspondenceTransfer->id,
                'exception' => $e
            ]);
        }
    }

    /**
     * Handle the CorrespondenceTransfer "updated" event.
     *
     * @param  \App\Models\CorrespondenceTransfer  $correspondenceTransfer
     * @return void
     */
    public function updated(CorrespondenceTransfer $correspondenceTransfer)
    {
        // Puedes añadir lógica aquí si también necesitas notificar sobre actualizaciones.
        // Por ejemplo, si se cambia el estado de la transferencia.
    }

    /**
     * Handle the CorrespondenceTransfer "deleted" event.
     *
     * @param  \App\Models\CorrespondenceTransfer  $correspondenceTransfer
     * @return void
     */
    public function deleted(CorrespondenceTransfer $correspondenceTransfer)
    {
        // Lógica si es necesario...
    }

    /**
     * Handle the CorrespondenceTransfer "restored" event.
     *
     * @param  \App\Models\CorrespondenceTransfer  $correspondenceTransfer
     * @return void
     */
    public function restored(CorrespondenceTransfer $correspondenceTransfer)
    {
        // Lógica si es necesario...
    }

    /**
     * Handle the CorrespondenceTransfer "force deleted" event.
     *
     * @param  \App\Models\CorrespondenceTransfer  $correspondenceTransfer
     * @return void
     */
    public function forceDeleted(CorrespondenceTransfer $correspondenceTransfer)
    {
        // Lógica si es necesario...
    }
}
