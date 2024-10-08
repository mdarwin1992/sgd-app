<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para los documentos en el sistema
 */
class Document extends Model
{
    use HasFactory;

    protected $table = 'document';

    protected $fillable = [
        'reference_code', 'system_code', 'received_date', 'origin', 'sender_name',
        'subject', 'has_attachments', 'page_count', 'file_path', 'transfer_status'
    ];

    protected $casts = [
        'received_date' => 'date',
        'has_attachments' => 'string',
    ];

    /**
     * Obtiene los registros de actividad asociados a este documento
     */
    public function documentLogs(): HasMany
    {
        return $this->hasMany(DocumentLog::class);
    }

    /**
     * Obtiene las recepciones asociadas a este documento
     */
    public function reception(): HasMany
    {
        return $this->hasMany(Reception::class);
    }
}
