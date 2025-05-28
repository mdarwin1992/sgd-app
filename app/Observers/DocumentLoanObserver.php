<?php

namespace App\Observers;

use App\Models\DocumentLoan;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\User; // Asegúrate de importar tu modelo de User

class DocumentLoanObserver
{
    /**
     * Handle the DocumentLoan "created" event.
     *
     * @param  \App\Models\DocumentLoan  $documentLoan
     * @return void
     */
    public function created(DocumentLoan $documentLoan)
    {
        //
        if ($documentLoan->return_date < now()->toDateString()) {
            // Ahora podemos usar directamente el user_id del modelo DocumentLoan
            if ($documentLoan->user_id) {
                Notification::create([
                    'user_id' => $documentLoan->user_id,
                    'correspondence_transfer_id' => 1, // No es una transferencia de correspondencia
                    'read' => false,
                    'data' => json_encode([
                        'document_loan_id' => $documentLoan->id,
                        'order_number' => $documentLoan->order_number,
                        'return_date' => $documentLoan->return_date,
                        'message' => 'El préstamo de documento con número de orden ' . $documentLoan->order_number . ' ha vencido.',
                    ]),
                ]);
            } else {
                // Manejar el caso en que no se pueda identificar al usuario (aunque ahora debería haber un user_id)
                Log::warning('No se pudo determinar el usuario para notificar sobre el préstamo vencido: ' . $documentLoan->user_id);
            }
        }
    }

    /**
     * Handle the DocumentLoan "updated" event.
     *
     * @param  \App\Models\DocumentLoan  $documentLoan
     * @return void
     */
    public function updated(DocumentLoan $documentLoan)
    {
        //
    }

    /**
     * Handle the DocumentLoan "deleted" event.
     *
     * @param  \App\Models\DocumentLoan  $documentLoan
     * @return void
     */
    public function deleted(DocumentLoan $documentLoan)
    {
        //
    }

    /**
     * Handle the DocumentLoan "forceDeleted" event.
     *
     * @param  \App\Models\DocumentLoan  $documentLoan
     * @return void
     */
    public function forceDeleted(DocumentLoan $documentLoan)
    {
        //
    }

    /**
     * Handle the DocumentLoan "retrieved" event.
     *
     * @param  \App\Models\DocumentLoan  $documentLoan
     * @return void
     */
    public function retrieved(DocumentLoan $documentLoan)
    {
        if ($documentLoan->return_date < now()->toDateString()) {
            // Ahora podemos usar directamente el user_id del modelo DocumentLoan
            if ($documentLoan->user_id) {
                // Verificar si ya existe una notificación pendiente para este préstamo
                $notificationExists = Notification::where('user_id', $documentLoan->user_id)
                    ->where('data->document_loan_id', $documentLoan->id)
                    ->where('read', false)
                    ->exists();

                if (!$notificationExists) {
                    Notification::create([
                        'user_id' => $documentLoan->user_id,
                        'correspondence_transfer_id' => null,
                        'read' => false,
                        'data' => json_encode([
                            'title' => 'El préstamo de documento ha vencido',
                            'document_loan_id' => $documentLoan->id,
                            'order_number' => $documentLoan->order_number,
                            'return_date' => $documentLoan->return_date,
                            'message' => 'El préstamo de documento con número de orden ' . $documentLoan->order_number . ' ha vencido.',
                        ]),
                    ]);
                }
            } else {
                // Manejar el caso en que no se pueda identificar al usuario
                Log::warning('No se pudo determinar el usuario para notificar sobre el préstamo vencido: ' . $documentLoan->order_number);
            }
        }
    }
}
