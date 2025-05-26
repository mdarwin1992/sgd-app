<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subseries extends Model
{
    use HasFactory;

    protected $table = 'subseries';


    protected $fillable = ['series_id', 'subseries_name', 'subseries_code'];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}
