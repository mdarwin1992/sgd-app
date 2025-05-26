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
        'reference_code',
        'system_code',
        'received_date',
        'origin',
        'sender_name',
        'subject',
        'has_attachments',
        'page_count',
        'file_path',
    ];

    protected $casts = [
        'received_date' => 'datetime',
        'has_attachments' => 'boolean',
    ];

    public function reception()
    {
        return $this->hasOne(Reception::class);
    }

    public function documentStatus()
    {
        return $this->hasOne(DocumentStatus::class);
    }
}
