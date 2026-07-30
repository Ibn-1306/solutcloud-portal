<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CompanyController;
use Illuminate\Support\Facades\Route;

// --- RACINE : Redirection vers Login ---
Route::get('/', function () { return view('auth.login'); });

// --- ZONE CONNECTÉE (AUTH) ---
Route::middleware(['auth', 'can:admin-only'])->group(function () {

    // ROUTES MANQUANTES DU PROFIL (AJOUTE CE BLOC ICI)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- ZONE ADMIN-ONLY ---
    Route::middleware('can:admin-only')->group(function () {
        
        Route::get('/dashboard', [CompanyController::class, 'index'])->name('dashboard');

        Route::prefix('admin/companies')->group(function () {
            Route::post('/', [CompanyController::class, 'store'])->name('admin.companies.store');
            Route::post('/{id}/suspend', [CompanyController::class, 'suspend'])->name('admin.suspend');
            Route::post('/{id}/activate', [CompanyController::class, 'activate'])->name('admin.activate');
            Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
            
            Route::post('/truncate', function () {
                \App\Models\Company::query()->delete();
                return back()->with('status', 'Toutes les instances ont été supprimées.');
            })->name('admin.companies.truncate');
        });
    });
});

require __DIR__.'/auth.php';