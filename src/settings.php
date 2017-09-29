<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/php',
            'twig_path' => __DIR__ . '/../templates/twig',
            'cache_path' => __DIR__ . '/../storage/app/cache/views',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        //Database settings
        'databases' => [
            'default' => 'mysql',

            'connections' => [
                'mysql' => [
                    'driver'    => 'mysql',
                    'host'      => 'localhost',
                    'database'  => 'database',
                    'username'  => 'root',
                    'password'  => 'root',
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix'    => '',
                ]
            ]
        ],
    ],
];
