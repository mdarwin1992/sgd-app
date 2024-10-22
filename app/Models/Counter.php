<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;

    protected $table = 'counters';

    protected $fillable = ['parent_count', 'child_count', 'series_entity_id'];

    public function parent()
    {
        return $this->belongsTo(Counter::class, 'parent_id');
    }

    public function child()
    {
        return $this->hasOne(Counter::class, 'parent_id');
    }
}
