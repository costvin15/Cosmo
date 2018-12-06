<?php

/*
|--------------------------------------------------------------------------
| Example structure
|--------------------------------------------------------------------------
|
| doctrine-odm  ->  The driver to be used by the facilitator
| facilitator   ->  Class name facilitator. The facilitator is responsible
|                   for opening the connection to the database
|
| Note: The other settings are the responsibility of the driver are
| requirements , not being compulsory for the facilitator .
|
*/

return [
    'driver' => 'doctrine-odm-test',

    'doctrine-odm' => [
        'facilitator' => \App\Facilitator\Database\Drivers\DoctrineODM::class,
        'connection' => [
            'user' => 'uder',
            'password' => 'pass',
            'server' => 'server',
            'dbname' => 'BD_COSMO',
            'port' => '27017',
        ],
        'configuration' => [
            'ProxyDir' =>  __DIR__ . '/../storage/cache/DoctrineMongoDB/Proxy/',
            'HydratorsDir' => __DIR__ . '/../storage/cache/DoctrineMongoDB/Hydrators/',
            'DirectoryMapping' => __DIR__ . '/src/Mapper/',
        ]
    ],

    'doctrine-odm-test' => [
        'facilitator' => \App\Facilitator\Database\Drivers\DoctrineODM::class,
        'connection' => [
            'user' => '',
            'password' => '',
            'server' => 'localhost',
            'dbname' => 'BD_COSMO',
            'port' => '27017',
        ],
        'configuration' => [
            'ProxyDir' =>  __DIR__ . '/../storage/cache/DoctrineMongoDB/Proxy/',
            'HydratorsDir' => __DIR__ . '/../storage/cache/DoctrineMongoDB/Hydrators/',
            'DirectoryMapping' => __DIR__ . '/src/Mapper/',
        ]
    ],

    'doctrine-orm' => [
        'facilitator' => \App\Facilitator\Database\Drivers\DoctrineORM::class,
        'connection' => [
            'user' => '',
            'password' => '',
            'server' => '',
            'dbname' => '',
            'port' => '3306',
            'driver' => '',
            'charset' => '',
        ],
        'configuration' => [
            'ProxyDir' =>  __DIR__ . '/../storage/cache/DoctrineORM/Proxy/',
            'DirectoryMapping' => __DIR__ . '/src/Mapper/',
        ]
    ],

    'pdo' => []
];
