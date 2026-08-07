<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Client\PortalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| OUTILS DE MAINTENANCE (À utiliser sur LWS)
|--------------------------------------------------------------------------
*/

// Cette route permet de forcer le rafraîchissement de la configuration sans SSH
/*
Route::get('/force-clear', function() {
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return "✅ Le système SOLUTCLOUD a été réinitialisé avec succès sur LWS !";
});
*/

/*
|--------------------------------------------------------------------------
| CONFIGURATION GÉNÉRALE
|--------------------------------------------------------------------------
*/

// Redirection racine : renvoie vers le login par défaut
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| 1. ESPACE ADMINISTRATION (admin.solutcloud.com)
|--------------------------------------------------------------------------
*/
Route::domain('admin.solutcloud.com')->group(function () {

    Route::get('/', function () { return redirect()->route('login'); });

    Route::middleware(['auth', 'can:admin-only'])->group(function () {
        
        // Dashboard principal
        Route::get('/dashboard', [CompanyController::class, 'index'])->name('admin.dashboard');

        // GESTION DES INSTANCES (Moteur SaaS)
        Route::prefix('companies')->group(function () {
            // Création manuelle
            Route::post('/', [CompanyController::class, 'store'])->name('admin.companies.store');
            
            // LE BOUTON MAGIQUE : Activation finale et envoi des accès ERP
            Route::post('/{id}/finalize', [CompanyController::class, 'finalizeInstance'])->name('admin.companies.finalize');

            // ACTIONS FTP (Kill Switch .htaccess)
            Route::post('/{id}/suspend', [CompanyController::class, 'suspend'])->name('admin.suspend');
            Route::post('/{id}/activate', [CompanyController::class, 'activate'])->name('admin.activate');
            
            // Suppression
            Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
            Route::post('/truncate', [CompanyController::class, 'truncate'])->name('admin.companies.truncate');
        });

        // Profil Administrateur
        Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    });
});

/*
|--------------------------------------------------------------------------
| 2. ESPACE CLIENT (login.solutcloud.com)
|--------------------------------------------------------------------------
*/
Route::domain('login.solutcloud.com')->group(function () {

    Route::get('/', function () { return redirect()->route('login'); });

    Route::middleware(['auth', 'can:client-only'])->group(function () {
        
        // Dashboard Client (Statut, Date expiration, Lien ERP)
        Route::get('/dashboard', [PortalController::class, 'index'])->name('client.dashboard');
        
        // Renouvellement (Déclencheur Moneroo SP3)
        Route::post('/renew', [PortalController::class, 'renew'])->name('client.renew');

        // Profil Client
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| 3. COMPATIBILITÉ LOCALE ET REDIRECTION GÉNÉRALE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('client.dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| ROUTES D'AUTHENTIFICATION (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';