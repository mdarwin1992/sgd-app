<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricFile extends Model
{
    use HasFactory;

    protected $table = 'historic_file';

    protected $fillable = [
        'entity_id',
        'system_code',
        'filed',
        'office_id',
        'series_id',
        'subseries_id',
        'shelf_number',
        'tray',
        'box_number',
        'main_conservation_medium',
        'preserved_in',
        'ord_number',
        'folio_number',
        'folder_year',
        'support',
        'start_date',
        'end_date',
        'ac_end_date',
        'document_reference',
        'third_parties',
        'object_observations',
        'file_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'ac_end_date' => 'date',
        'folder_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function subseries()
    {
        return $this->belongsTo(Subseries::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function historicalArchiveLoan()
    {
        return $this->hasOne(HistoricalArchiveLoan::class);
    }
}
