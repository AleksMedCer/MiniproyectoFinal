<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo los compradores pueden registrar ventas
        return auth()->check() && auth()->user()->role === 'comprador';
    }

    public function rules(): array
    {
        return [
            // REQUISITOS: exists, required, image
            'producto_id' => 'required|exists:productos,id',
            'ticket' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ];
    }
}
