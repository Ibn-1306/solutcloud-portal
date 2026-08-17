<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. CONFIANCE AUX PROXIES (Indispensable pour l'infrastructure LWS)
        // Cela permet à Laravel de comprendre qu'il est derrière un tunnel HTTPS
        // et résout l'erreur ERR_SSL_PROTOCOL_ERROR.
        $middleware->trustProxies(at: '*');

        // 2. EXEMPTION CSRF
        // On autorise les appels API externes et le futur Webhook de Moneroo.
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'moneroo-webhook' 
        ]);

        // 3. ÉTAT DE L'API
        // Permet la gestion des sessions entre les sous-domaines si nécessaire.
        $middleware->statefulApi();

        // 4. GESTION DU CORS (Custom)
        $middleware->append(\App\Http\Middleware\HandleCors::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        // Rendu JSON systématique pour les erreurs API
        // Évite de recevoir une page HTML d'erreur lors d'un fetch JavaScript.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

    })->create();