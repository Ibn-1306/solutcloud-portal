<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // On autorise les routes API publiques utilisées par le site vitrine.
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // J'inscris ici vos domaines officiels pour une sécurité maximale
    'allowed_origins' => [
        'https://www.solutcloud.com',
        'https://solutcloud.com',
        'https://login.solutcloud.com',
        'https://admin.solutcloud.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
