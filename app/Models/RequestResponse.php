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
        'response_content',
        'response_email',
        'response_document_path',
        'document_id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
