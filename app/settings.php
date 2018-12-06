<?php
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => true,

        // monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../storage/log/app.log',
        ],

        //session
        'session' => [
            'name' => 'session',
            'lifetime' => 7200,
            'path' => null,
            'domain' => null,
            'secure' => false,
            'httponly' => true,
            'cache_limiter' => 'nocache',
        ],

        // View settings
        'view' => [
            'template_path' => __DIR__ . '/src',
            'twig' => [
                'cache' => __DIR__ . '/../storage/cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        'apikey.sendgrid' => 'CHAVE',
        'storage.photo' => __DIR__ . '/../storage/photo-save/',
    ],
];
