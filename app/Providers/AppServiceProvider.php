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
     * Enregistrement des services.
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
        // 1. COMPATIBILITÉ INDEX MYSQL (Indispensable pour LWS)
        // Évite les erreurs de longueur de clé lors des migrations sur d'anciennes versions de MySQL
        Schema::defaultStringLength(191);

        /**
         * 2. SÉCURITÉ DES ACCÈS (GATES)
         * Je renforce la détection des rôles pour éviter les erreurs de casse (majuscules/minuscules)
         */
        
        // Gate pour l'Administrateur principal
        Gate::define('admin-only', function (User $user) {
            return $user && trim(strtolower($user->role)) === 'admin';
        });

        // Gate pour l'accès au Portail Client
        Gate::define('client-only', function (User $user) {
            return $user && trim(strtolower($user->role)) === 'client';
        });

        /**
         * 3. LOGIQUE RÉSEAU & HTTPS
         * Je force le HTTPS uniquement en production pour éviter les erreurs de Mixed Content
         * et sécuriser les cookies de session partagés.
         */
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}