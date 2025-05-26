<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Retention extends Model
{
    use HasFactory;

    protected $table = 'retention';

    protected $fillable = ['series_id', 'administrative_retention', 'central_retention'];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}
