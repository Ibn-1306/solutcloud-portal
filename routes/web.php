<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Client\PortalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| MAINTENANCE & CACHE
|--------------------------------------------------------------------------
*/
Route::get('/force-clear', function() {
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return "Système SOLUTCLOUD synchronisé !";
});

/*
|--------------------------------------------------------------------------
| RACINE & AUTHENTIFICATION
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

// Le trieur intelligent : redirige vers le bon dashboard après la connexion
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('client.dashboard');
})->middleware(['auth'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| SECTION ADMIN (Accessible via login.solutcloud.com/admin/...)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'can:admin-only'])->prefix('admin')->group(function () {
    
    Route::get('/dashboard', [CompanyController::class, 'index'])->name('admin.dashboard');

    Route::prefix('companies')->group(function () {
        Route::post('/', [CompanyController::class, 'store'])->name('admin.companies.store');
        Route::post('/{id}/finalize', [CompanyController::class, 'finalizeInstance'])->name('admin.companies.finalize');
        Route::post('/{id}/suspend', [CompanyController::class, 'suspend'])->name('admin.suspend');
        Route::post('/{id}/activate', [CompanyController::class, 'activate'])->name('admin.activate');
        Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
});

/*
|--------------------------------------------------------------------------
| SECTION CLIENT (Accessible via login.solutcloud.com/portal/...)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| PROFIL (Accessible par admin et client)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| SECTION CLIENT (Accessible via login.solutcloud.com/portal/...)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'can:client-only'])->prefix('portal')->group(function () {
    
    Route::get('/dashboard', [PortalController::class, 'index'])->name('client.dashboard');
    Route::post('/renew', [PortalController::class, 'renew'])->name('client.renew');
});

require __DIR__.'/auth.php';