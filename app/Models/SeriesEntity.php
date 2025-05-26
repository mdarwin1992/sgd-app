<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeriesEntity extends Model
{
    use HasFactory;

    protected $table = 'series_entity';

    protected $fillable = [
        'entity_id',
        'series_name',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function series()
    {
        return $this->hasMany(Series::class);
    }
}
