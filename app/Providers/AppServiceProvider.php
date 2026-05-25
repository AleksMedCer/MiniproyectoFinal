<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Venta;
use App\Policies\DashboardPolicy;
use App\Policies\VentaPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, DashboardPolicy::class);
        Gate::policy(Venta::class, VentaPolicy::class);
    }
}
