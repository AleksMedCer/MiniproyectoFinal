<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    // Relación: Una categoría tiene muchos productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    // REQUISITO: hasManyThrough
    // Una categoría tiene muchas ventas a través de sus productos
    public function ventas()
    {
        return $this->hasManyThrough(Venta::class, Producto::class);
    }
}
