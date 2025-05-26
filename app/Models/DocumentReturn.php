<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentReturn extends Model
{
    use HasFactory;

    protected $table = 'document_returns';

    protected $fillable = [
        'document_loan_order_number',
        'return_date',
        'document_conditions',
        'comments',
        'created_at',
        'updated_at'
    ];

    public function documentLoan()
    {
        return $this->belongsTo(DocumentLoan::class, 'document_loan_order_number', 'order_number');
    }
}
