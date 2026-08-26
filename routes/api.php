<?php

use App\Http\Controllers\Api\MonerooWebhookController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\WebsiteLeadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - SOLUTCLOUD
|--------------------------------------------------------------------------
| Ces routes sont utilisées par le site vitrine pour transmettre les
| demandes commerciales et les inscriptions à la newsletter.
*/

// Route par défaut (Sanctum) - On peut la laisser
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('throttle:30,1')->group(function () {
    Route::post('/leads', [WebsiteLeadController::class, 'store']);
    Route::post('/newsletter', [NewsletterController::class, 'store']);
});

Route::post('/webhooks/moneroo', MonerooWebhookController::class)
    ->middleware('throttle:120,1')
    ->name('webhooks.moneroo');
