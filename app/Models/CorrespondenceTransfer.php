<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use mysql_xdevapi\Table;

/**
 * Modelo para las transferencias de correspondencia
 */
class CorrespondenceTransfer extends Model
{
    use HasFactory;

    protected $table = 'correspondence_transfer';

    protected $fillable = [
        'transfer_datetime',
        'office_id',
        'response_time',
        'response_deadline',
        'job_type',
        'document_id',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
