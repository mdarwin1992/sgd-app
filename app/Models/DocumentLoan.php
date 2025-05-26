<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentLoan extends Model
{
    use HasFactory;


    use HasFactory;

    protected $table = "document_loans";

    protected $fillable = [
        'registration_date',
        'order_number',
        'identification',
        'names',
        'office_id',
        'return_date',
        'type_of_document_borrowed'
    ];


    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function centralArchiveLoans()
    {
        return $this->hasOne(CentralArchiveLoans::class, 'document_loans_order_number', 'order_number');
    }

    public function historicalArchiveLoans()
    {
        return $this->hasOne(HistoricalArchiveLoan::class, 'document_loans_order_number', 'order_number');
    }

    public function documentReturn()
    {
        return $this->hasOne(DocumentReturn::class, 'document_loan_order_number', 'order_number');
    }
}
