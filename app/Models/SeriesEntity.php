<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function subseries(): HasMany
    {
        // Apunta al modelo Subseries y especifica la clave foránea 'series_id'.
        return $this->hasMany(Subseries::class, 'series_id', 'id');
    }
}
