<?php

namespace App\Providers;

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
    \Illuminate\Support\Facades\Gate::define('admin-only', function ($user) {
        // On utilise trim() pour enlever les espaces et on force en minuscules
        return trim(strtolower($user->role)) === 'admin';
    });
    }
}
