<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole([
            User::ROLE_ADMIN,
            User::ROLE_GERENTE,
            User::ROLE_VENDEDOR,
        ]);
    }

    public function rules(): array
    {
        return [
            // REQUISITOS: Reglas string, min, max, required
            'nombre' => 'required|string|min:3|max:255',
            'descripcion' => 'required|string|min:10',

            // REQUISITOS: Reglas numeric
            'precio' => 'required|numeric|min:0.01|max:999999',

            // REQUISITOS: Regla exists (verificar que la categoría existe en BD)
            'categoria_id' => 'required|exists:categorias,id',

            // REQUISITOS: Validación de múltiples archivos y regla 'image'
            'fotos' => 'required|array|min:1|max:5', // Mínimo 1 foto, máximo 5
            'fotos.*' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Cada foto debe ser imagen
        ];
    }
}
