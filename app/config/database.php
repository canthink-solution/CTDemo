<?php

/*
|--------------------------------------------------------------------------
| Database
|--------------------------------------------------------------------------
*/
$config['db'] = [
    'default' => [
        'development' => [
            'dsn' => '',
            'driver' => 'mysql',
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'ct_demo_db',
            'port' => '3306',
            'charset' => 'utf8mb4',
        ],
        'staging' => [
            'dsn' => '',
            'driver' => 'mysql',
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => '',
            'port' => '3306',
            'charset' => 'utf8mb4',
        ],
        'production' => [
            'dsn' => '',
            'driver' => 'mysql',
            'hostname' => '',
            'username' => 'root',
            'password' => '',
            'database' => '',
            'port' => '3306',
            'charset' => 'utf8mb4',
        ]
    ],

    'slave' => [
        'development' => [
            'dsn' => '',
            'driver' => 'mysql',
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'schoolscan',
            'port' => '3306',
            'charset' => 'utf8mb4',
        ],
        'staging' => [
            'dsn' => '',
            'driver' => 'mysql',
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => '',
            'port' => '3306',
            'charset' => 'utf8mb4',
        ],
        'production' => [
            'dsn' => '',
            'driver' => 'mysql',
            'hostname' => '',
            'username' => 'root',
            'password' => '',
            'database' => '',
            'port' => '3306',
            'charset' => 'utf8mb4',
        ]
    ]
];
