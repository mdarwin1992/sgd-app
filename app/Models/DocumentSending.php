<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para el envío de documentos
 */
class DocumentSending extends Model
{
    use HasFactory;

    protected $table = 'document_sending';
    protected $fillable = [
        'send_date', 'subject', 'sender', 'recipient', 'page_count',
        'department_id', 'office_id', 'document_path'
    ];

    protected $casts = [
        'send_date' => 'date',
    ];

    /**
     * Obtiene el departamento asociado a este envío
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
