<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para las respuestas a las solicitudes
 */
class RequestResponse extends Model
{
    use HasFactory;

    protected $table = 'request_response';

    protected $fillable = [
        'correspondence_transfer_id', 'response_content', 'response_email',
        'response_document_path', 'response_status'
    ];

    /**
     * Obtiene la transferencia de correspondencia asociada a esta respuesta
     */
    public function correspondenceTransfer(): BelongsTo
    {
        return $this->belongsTo(CorrespondenceTransfer::class);
    }
}
