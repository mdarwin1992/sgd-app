<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CentralArchiveLoans extends Model
{
    use HasFactory;

    protected $table = 'central_archive_loans';

    protected $fillable = [
        'document_loans_order_number',
        'central_archive_id',
    ];

    public function documentLoan()
    {
        return $this->belongsTo(DocumentLoan::class, 'document_loans_order_number', 'order_number');
    }

    public function centralArchive()
    {
        return $this->belongsTo(CentralArchive::class, 'central_archive_id');
    }
}
