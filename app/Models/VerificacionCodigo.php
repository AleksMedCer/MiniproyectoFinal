<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificacionCodigo extends Model
{
    protected $fillable = ['user_id', 'codigo', 'expiracion'];

    // Para poder usar funciones de tiempo como isPast() o isFuture()
    protected $casts = [
        'expiracion' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
