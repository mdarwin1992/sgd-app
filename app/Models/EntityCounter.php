<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityCounter extends Model
{
    use HasFactory;

    protected $table = 'entity_counters';

    protected $fillable = [
        'entity_id',
        'current_count',
        'current_code'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
