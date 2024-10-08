<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para el registro de actividades de los documentos
 */
class DocumentLog extends Model
{
    use HasFactory;

    protected $table = 'document_log';
    protected $fillable = [
        'document_id', 'action_type', 'user_id'
    ];

    protected $casts = [
        'action_type' => 'string',
    ];

    /**
     * Obtiene el documento asociado a este registro
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Obtiene el usuario que realizó la acción
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
