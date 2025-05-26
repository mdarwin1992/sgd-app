<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricalArchiveLoan extends Model
{
    use HasFactory;

    protected $table = 'historical_archive_loans';

    protected $fillable = [
        'document_loans_order_number',
        'historic_file_id'
    ];

    public function documentLoan()
    {
        return $this->belongsTo(DocumentLoan::class, 'document_loans_order_number', 'order_number');
    }

    public function historicFile()
    {
        return $this->belongsTo(HistoricFile::class, 'historic_file_id');
    }
}
