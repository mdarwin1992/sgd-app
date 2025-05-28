<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'correspondence_transfer_id',
        'read',
        'data',
    ];

    // Dentro del modelo Notification
    public function correspondenceTransfer()
    {
        return $this->belongsTo(CorrespondenceTransfer::class, 'correspondence_transfer_id'); // Asegúrate de usar el nombre correcto del campo
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Asegúrate de usar el nombre correcto del campo
    }
}
