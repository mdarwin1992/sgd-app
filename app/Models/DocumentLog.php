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
        'document_id',
        'action_type',
        'user_id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
