<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['categoria_id', 'vendedor_id', 'nombre', 'descripcion', 'precio', 'fotos'];

    // Castear el JSON a un arreglo de PHP automáticamente
    protected $casts = [
        'fotos' => 'array',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
