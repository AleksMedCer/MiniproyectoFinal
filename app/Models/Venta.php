<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = ['producto_id', 'comprador_id', 'ticket_path', 'validada'];

    protected $casts = [
        'validada' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function comprador()
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }
}
