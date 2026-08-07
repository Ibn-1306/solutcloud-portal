<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Disque par défaut
    |--------------------------------------------------------------------------
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Configuration des Disques
    |--------------------------------------------------------------------------
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | CONFIGURATION LWS (KILL SWITCH & MANAGEMENT)
        |--------------------------------------------------------------------------
        | Ce disque gère l'accès FTP à la racine de l'hébergement pour manipuler 
        | les fichiers .htaccess des instances Start, Business et Premium.
        */
        'lws' => [
            'driver'   => 'ftp',
            'host'     => env('FTP_HOST'),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),
            'port'     => (int) env('FTP_PORT', 21),
            'root'     => env('FTP_ROOT', '/'), // Utilise la racine définie dans le .env
            'passive'  => true,
            'ssl'      => false,
            'timeout'  => 30,
            'throw'    => true, // Senior Tip : Force Laravel à lever une exception en cas d'échec FTP
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Liens Symboliques
    |--------------------------------------------------------------------------
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];