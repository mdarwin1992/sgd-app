<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para las transferencias de correspondencia
 */
class CorrespondenceTransfer extends Model
{
    use HasFactory;

    protected $table = 'correspondence_transfer';

    protected $fillable = [
        'transfer_datetime', 'office_id', 'response_time', 'response_deadline',
        'job_type', 'reception_id', 'response_status'
    ];

    protected $casts = [
        'transfer_datetime' => 'datetime',
        'response_deadline' => 'date',
    ];

    /**
     * Obtiene la oficina destino de esta transferencia
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Obtiene la recepción asociada a esta transferencia
     */
    public function reception(): BelongsTo
    {
        return $this->belongsTo(Reception::class);
    }

    /**
     * Obtiene las respuestas asociadas a esta transferencia
     */
    public function requestResponse(): HasMany
    {
        return $this->hasMany(RequestResponse::class);
    }
}
