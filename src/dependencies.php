<?php
// DIC configuration

use Slim\Container;
use Illuminate\Container\Container as LaravelContainer;

use \Illuminate\Events\Dispatcher as LaravelEventDispatcher;
use \Illuminate\Database\Capsule\Manager as EloquentDatabaseManager;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function (Container $c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// twig view renderer
$container['twig'] = function (Container $c) {
    $settings = $c->get('settings')['renderer'];

    $loader = new Twig_Loader_Filesystem($settings['twig_path']);
    $twig = new Twig_Environment($loader, array(
        'debug' => true,
        'cache' => $settings['cache_path'],
    ));

    return $twig;
};

// monolog
$container['logger'] = function (Container $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

//Laravel container
$container['laravel_container'] = function(Container $c) {
    $container = new LaravelContainer;

    //Laravel event
    $container->singleton('events', function ($app) {
        return (new LaravelEventDispatcher($app))->setQueueResolver(function () use ($app) {
            throw new Exception('No queue system', 500);
        });
    });

    return $container;
};

//Laravel Eloquent
$container['db'] = function (Container $c) {
    $db = new EloquentDatabaseManager($c->get('laravel_container'));

    $settings = $c->get('settings')['databases'];

    if (!isset($settings['connections'][$settings['default']])) {
        throw new Exception('Not found default database config', 500);
    }

    foreach ($settings['connections'] as $name => $connection) {
        $db->addConnection($connection, $name === $settings['default'] ? 'default' : $name);
    }

    $db->setAsGlobal();

    $db->bootEloquent();

    $def_db = $db->getConnection('default');

    $def_db->listen(function (\Illuminate\Database\Events\QueryExecuted $event) use ($c) {
        $event->bindings = array_map(function($bind){
            return is_numeric($bind) ? $bind : "'{$bind}'";
        }, $event->bindings);

        array_unshift($event->bindings, str_replace(['%', '?'], ['%%', '%s'], $event->sql));

        $sql_str = count($event->bindings) == 1 ? current($event->bindings) : call_user_func_array("sprintf", $event->bindings);

        $c->get('logger')->info($sql_str . ( is_null($event->time) ? "" : " [{$event->time}ms]"));
    });

    return $db;
};

//Need to boot the dependencies
$container['boot'] = function (Container $c) {
    static $booted = [];
    if (count($booted) >= 1) {
        return $booted;
    }

    $boots = ['db'];

    foreach ($boots as $boot) {
        $c->get($boot);
        $booted[$boot] = true;
    }

    return $booted;
};

$container['boot'];

