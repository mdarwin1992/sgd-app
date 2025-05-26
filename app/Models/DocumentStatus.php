<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentStatus extends Model
{
    use HasFactory;

    protected $table = 'document_status';

    protected $fillable = [
        'document_id',
        'status',
    ];

    public function documentStatus()
    {
        return $this->hasOne(DocumentStatus::class, 'documento_id', 'id');
    }

}
