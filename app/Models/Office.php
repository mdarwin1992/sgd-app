<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para las oficinas dentro de los departamentos
 */
class Office extends Model
{
    use HasFactory;

    protected $table = "office";

    protected $fillable = [
        'code', 'name', 'department_id', 'user_id', 'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Obtiene el departamento al que pertenece esta oficina
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Obtiene el usuario responsable de esta oficina
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
