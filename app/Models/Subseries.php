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


    public function seriesEntity(): BelongsTo
    {
        // Apunta al modelo SeriesEntity usando la clave foránea 'series_id'.
        return $this->belongsTo(SeriesEntity::class, 'series_id', 'id');
    }
}
