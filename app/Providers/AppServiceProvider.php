<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Enregistrement des services de l'application.
     */
    public function register(): void
    {
        //
    }

    /**
     * Initialisation des services (Bootstrapping).
     */
    public function boot(): void
    {
        // 1. COMPATIBILITÉ INDEX MYSQL (Indispensable pour l'infrastructure LWS)
        Schema::defaultStringLength(191);

        /**
         * 2. SÉCURITÉ DES ACCÈS (GATES)
         * Utilisation des constantes du modèle User pour une maintenance évolutive.
         */
        Gate::define('admin-only', function (User $user) {
            return $user && trim(strtolower($user->role)) === User::ROLE_ADMIN;
        });

        Gate::define('client-only', function (User $user) {
            return $user && trim(strtolower($user->role)) === User::ROLE_CLIENT;
        });

        /**
         * 3. LOGIQUE RÉSEAU & FORÇAGE HTTPS (Production)
         * Cette instruction est vitale pour corriger l'erreur "Mixed Content".
         * Elle force Laravel à générer tous les liens (CSS, JS, Images) en https://
         */
        if ($this->app->environment('production') || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}