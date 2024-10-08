<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para la recepción de documentos
 */
class Reception extends Model
{
    use HasFactory;

    protected $table = 'reception';

    protected $fillable = [
        'document_id', 'response_status'
    ];

    /**
     * Obtiene el documento asociado a esta recepción
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Obtiene las transferencias de correspondencia asociadas a esta recepción
     */
    public function correspondenceTransfers(): HasMany
    {
        return $this->hasMany(CorrespondenceTransfer::class);
    }
}
