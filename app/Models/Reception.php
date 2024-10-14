<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para la recepción de documentos
 */
class Reception extends Model
{
    use HasFactory;

    protected $table = 'reception';


    protected $fillable = [
        'document_id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
