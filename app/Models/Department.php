<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para los departamentos dentro de una entidad
 */
class Department extends Model
{
    use HasFactory;

    protected $table = 'department';

    protected $fillable = [
        'code', 'name', 'entity_id', 'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Obtiene la entidad a la que pertenece este departamento
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Obtiene las oficinas asociadas a este departamento
     */
    public function offices(): HasMany
    {
        return $this->hasMany(Office::class);
    }
}
