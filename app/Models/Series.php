<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Series extends Model
{
    use HasFactory;

    protected $table = 'series';


    protected $fillable = ['office_id', 'series_entity_id', 'series_code'];

    public function subseries(): HasMany
    {
        return $this->hasMany(Subseries::class);
    }

    public function retention(): HasOne
    {
        return $this->hasOne(Retention::class);
    }

    public function finalDisposition(): HasOne
    {
        return $this->hasOne(FinalDisposition::class);
    }

    public function documentaryTypes(): HasMany
    {
        return $this->hasMany(DocumentaryType::class);
    }

    public function seriesEntity()
    {
        return $this->belongsTo(SeriesEntity::class, 'series_entity_id');
    }

    public function centralArchives()
    {
        return $this->hasMany(CentralArchive::class);
    }

}
