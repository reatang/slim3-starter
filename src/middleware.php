<?php

use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

// e.g: middleware define
function _define_middleware_function(RequestInterface $request, ResponseInterface $response, callable $next)
{
    global $app;

    $app->getContainer()->get('logger')->info('middleware in');
    $response = $next($request, $response);
    $app->getContainer()->get('logger')->info('middleware out');

    return $response;
}

/**
 * App Middleware
 * @var array
 */
$app_middleware = [

];

/**
 * Route Middleware
 * @var array
 */
$route_middleware = [
    'example' => '_define_middleware_function'
];

/**
 * get route middleware
 *
 * @param $name
 * @return null|callable|Object
 */
function route_middleware($name) {
    global $route_middleware;

    if (isset($route_middleware[$name])) {
        if ($route_middleware[$name] instanceof \Closure || function_exists($route_middleware[$name])) {
            return $route_middleware[$name];
        } else if (class_exists($route_middleware[$name])) {
            return new $route_middleware[$name];
        }
    }

    throw new Exception("Not found route middleware [{$name}]");
}

//app middleware register
foreach ($app_middleware as $middleware) {
    if ($middleware instanceof \Closure || function_exists($middleware)) {
        $app->add($middleware);
        continue;
    } else if (class_exists($middleware)) {
        $app->add(new $middleware);
        continue;
    }

    throw new Exception("Not found app middleware [{$middleware}]");
}
