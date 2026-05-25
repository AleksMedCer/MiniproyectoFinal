<?php

namespace App\Policies;

use App\Models\User;

class DashboardPolicy
{
    public function viewDashboard(User $user): bool
    {
        return $user->hasRole([User::ROLE_ADMIN, User::ROLE_GERENTE]);
    }
}
