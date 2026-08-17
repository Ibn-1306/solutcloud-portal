<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware CORS maison.
 *
 * Autorise le site vitrine (solutcloud-web) à appeler les endpoints API
 * publiés de login.solutcloud.com.
 *
 * En production, restreindre CORS_ALLOWED_ORIGINS à la liste blanche
 * des domaines autorisés (ex: https://www.solutcloud.com).
 */
class HandleCors
{
    public function handle(Request $request, Closure $next)
    {
        // Liste blanche des origines autorisées (via config/services.php)
        $allowedOrigins = config('services.cors.allowed_origins', 'https://www.solutcloud.com,https://solutcloud.com');
        $allowedOrigins = array_map('trim', explode(',', (string) $allowedOrigins));

        $origin = $request->headers->get('Origin');

        // Gestion du préflight OPTIONS (déclenché par Content-Type: x-www-form-urlencoded)
        if ($request->getMethod() === 'OPTIONS') {
            return $this->addCorsHeaders(
                response('', 204),
                $origin,
                $allowedOrigins
            );
        }

        $response = $next($request);

        return $this->addCorsHeaders($response, $origin, $allowedOrigins);
    }

    /**
     * Ajoute les en-têtes CORS à la réponse si l'origine est autorisée.
     */
    private function addCorsHeaders($response, ?string $origin, array $allowedOrigins)
    {
        if ($origin && in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, X-CSRF-TOKEN, Authorization');
            $response->headers->set('Access-Control-Max-Age', '3600');
        }

        return $response;
    }
}