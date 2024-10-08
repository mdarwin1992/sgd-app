<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use mysql_xdevapi\Table;

/**
 * Modelo para la entidad (organización pública o privada)
 */
class Entity extends Model
{
    use HasFactory;

    protected $table = "entity";

    protected $fillable = [
        'nit', 'verification_digit', 'name', 'type', 'address', 'phone', 'email', 'creation_date',
        'legal_representative', 'employee_count', 'website', 'logo', 'status'
    ];

    protected $casts = [
        'creation_date' => 'date',
        'type' => 'string',
        'status' => 'string',
    ];

    /**
     * Obtiene los departamentos asociados a esta entidad
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}
