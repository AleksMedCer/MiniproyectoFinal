<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venta;

class VentaPolicy
{
    // REQUISITO: Reglas de acceso específicas
    public function viewTicket(User $user, Venta $venta): bool
    {
        // Retorna TRUE si es el gerente o si el usuario logueado es el comprador de esta venta
        return $user->hasRole([User::ROLE_ADMIN, User::ROLE_GERENTE])
            || $user->id === $venta->comprador_id;
    }

    // Ya que estamos aquí, preparamos la regla para validar ventas (Siguiente requisito)
    public function validarVenta(User $user): bool
    {
        return $user->hasRole([User::ROLE_ADMIN, User::ROLE_GERENTE]);
    }
}
