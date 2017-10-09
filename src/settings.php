<?php

//Boot dotenv
$dotenv = new Dotenv\Dotenv(realpath(__DIR__ . '/..'));
$dotenv->load();

//App config
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
            'default' => getenv('DB_CONNECTION'),

            'connections' => [
                'mysql' => [
                    'driver'    => 'mysql',
                    'host'      => getenv('DB_HOST'),
                    'database'  => getenv('DB_DATABASE'),
                    'username'  => getenv('DB_USERNAME'),
                    'password'  => getenv('DB_PASSWORD'),
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix'    => getenv('DB_PREFIX'),
                ]
            ]
        ],
    ],
];
