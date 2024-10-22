<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalDisposition extends Model
{
    use HasFactory;

    protected $table = 'final_disposition';

    protected $fillable = ['series_id', 'disposition_type', 'disposal_procedure'];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}
