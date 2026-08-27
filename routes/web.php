<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DemoController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Client\PortalController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Client\SubscriptionController;
use App\Http\Controllers\PaymentReturnController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionExpiredController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RACINE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return redirect()->route('login');

});

Route::get('/payments/return', PaymentReturnController::class)
    ->name('payments.return');

Route::get('/abonnement-expire/statut', [SubscriptionExpiredController::class, 'status'])
    ->middleware('throttle:30,1')
    ->name('subscription.expired.status');

Route::get('/abonnement-expire/renouveler', [SubscriptionExpiredController::class, 'renew'])
    ->middleware('throttle:30,1')
    ->name('subscription.expired.renew');

Route::get('/abonnement-expire', [SubscriptionExpiredController::class, 'show'])
    ->name('subscription.expired');

/*
|--------------------------------------------------------------------------
| REDIRECTION DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    $user = Auth::user();

    if ($user->role === 'admin') {

        return redirect()->route('admin.dashboard');

    }

    return redirect()->route('client.dashboard');

})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:admin-only'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard',
            [CompanyController::class, 'index']
        )->name('admin.dashboard');

        Route::prefix('companies')->group(function () {

            Route::post('/',
                [CompanyController::class, 'store']
            )->name('admin.companies.store');

            Route::post('/{id}/finalize',
                [CompanyController::class, 'finalizeInstance']
            )->name('admin.companies.finalize');

            Route::post('/{id}/suspend',
                [CompanyController::class, 'suspend']
            )->name('admin.suspend');

            Route::post('/{id}/activate',
                [CompanyController::class, 'activate']
            )->name('admin.activate');

            Route::delete('/{company}',
                [CompanyController::class, 'destroy']
            )->name('companies.destroy');

        });

        Route::get('/profile',
            [ProfileController::class, 'edit']
        )->name('admin.profile.edit');

        Route::get('/demos',
            [DemoController::class, 'index']
        )->name('admin.demos.index');

        Route::post('/demos',
            [DemoController::class, 'store']
        )->name('admin.demos.store');

        Route::get('/orders',
            [OrderController::class, 'index']
        )->name('admin.orders.index');

        Route::get('/payments',
            [PaymentController::class, 'index']
        )->name('admin.payments.index');

        Route::post('/payments',
            [PaymentController::class, 'store']
        )->name('admin.payments.store');

        Route::post('/payments/{payment}/initialize',
            [PaymentController::class, 'initialize']
        )->name('admin.payments.initialize');

        Route::post('/payments/{payment}/send-link',
            [PaymentController::class, 'sendLink']
        )->name('admin.payments.send-link');

        Route::post('/payments/{payment}/refresh',
            [PaymentController::class, 'refresh']
        )->name('admin.payments.refresh');

        Route::delete('/payments/{payment}',
            [PaymentController::class, 'destroy']
        )->name('admin.payments.destroy');

    });

/*
|--------------------------------------------------------------------------
| PROFIL GENERAL LARAVEL
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch('/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete('/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| CLIENT
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:client-only'])
    ->prefix('client')
    ->group(function () {

        Route::get('/dashboard', [PortalController::class, 'index'])
            ->name('client.dashboard');

        Route::get('/profile', [PortalController::class, 'profile'])
            ->name('client.profile');

        Route::put('/profile/password',
            [ClientProfileController::class, 'updatePassword']
        )->name('client.password.update');

        Route::get('/renew', [SubscriptionController::class, 'index'])
            ->name('client.renew');

        Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout'])
            ->name('client.subscription.checkout');

    });

require __DIR__.'/auth.php';
