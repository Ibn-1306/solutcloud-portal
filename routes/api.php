<?php

use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\WebsiteLeadController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - SOLUTCLOUD
|--------------------------------------------------------------------------
| Ces routes sont utilisées par le site vitrine pour communiquer avec
| le portail de gestion (notamment pour le paiement Moneroo).
*/

// Route par défaut (Sanctum) - On peut la laisser
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * SP2 : Route pour la création du lien de paiement
 * Appelée par fetch('https://login.solutcloud.com/api/create-checkout')
 */
Route::post('/create-checkout', [OrderController::class, 'createCheckout']);
Route::post('/moneroo-webhook', [OrderController::class, 'handleWebhook']);

Route::middleware('throttle:30,1')->group(function () {
    Route::post('/leads', [WebsiteLeadController::class, 'store']);
    Route::post('/newsletter', [NewsletterController::class, 'store']);
});
