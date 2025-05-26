<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentaryType extends Model
{
    use HasFactory;

    protected $table = 'documentary_type';

    protected $fillable = ['series_id', 'document_name'];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}
