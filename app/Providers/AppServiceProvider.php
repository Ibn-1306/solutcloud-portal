<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;

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
        // 1. COMPATIBILITÉ BDD (Indispensable pour LWS)
        Schema::defaultStringLength(191);

        // 2. SÉCURITÉ DES ACCÈS (GATES)
        // Vérifie si l'utilisateur est admin
        Gate::define('admin-only', function ($user) {
            return $user && trim(strtolower($user->role)) === 'admin';
        });

        // Vérifie si l'utilisateur est client
        Gate::define('client-only', function ($user) {
            return $user && trim(strtolower($user->role)) === 'client';
        });

        // 3. FORCE LE HTTPS EN PRODUCTION
        // Vital pour Moneroo et la sécurité des domaines admin. et login.
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}