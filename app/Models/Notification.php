<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model // o extends DatabaseNotification
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications'; // Asegura que el nombre de la tabla sea correcto si es un modelo personalizado.

    /**
     * The attributes that are mass assignable.
     *
     * --- ESTA ES LA CORRECCIÓN CLAVE ---
     * Añade 'data' a este array para permitir que se guarde
     * usando métodos como create() o update().
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'correspondence_transfer_id',
        'data', // <-- ¡AÑADE ESTA LÍNEA!
        // No incluyas 'id', 'read_at', 'created_at', 'updated_at' aquí.
        // Laravel los gestiona automáticamente.
        // Si tienes otros campos que deban poderse llenar masivamente, añádelos también.
    ];

    /**
     * The attributes that should be cast.
     *
     * Laravel ya suele hacer esto por defecto con DatabaseNotification, pero
     * si es un modelo personalizado, es una excelente práctica decirle que
     * el campo 'data' debe ser tratado como un array/JSON.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array', // Esto asegura que al leer, sea un array PHP, y al guardar, sea JSON.
        'read_at' => 'datetime', // Si no lo heredas de DatabaseNotification
    ];

    /**
     * Relación con el usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la transferencia de correspondencia.
     */
    public function correspondenceTransfer()
    {
        return $this->belongsTo(CorrespondenceTransfer::class);
    }
}
