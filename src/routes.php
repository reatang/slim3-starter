<?php

use Slim\Http\Request;
use Slim\Http\Response;

// DB test
$app->get('/db', function (Request $request, Response $response, array $args) {

    $users = \App\Models\User::all();

    // Render index view
    return $this->renderer->render($response, 'db.phtml', ['users' => $users->toArray()]);
})->add(route_middleware('example'));

//twig test
$app->get('/twig', function (Request $request, Response $response, array $args) {

    $users = \App\Models\User::all();

    // Render index view
    return $response->getBody()->write($this->twig->render('index.twig', ['users' => $users->toArray()]));
});

// slim
$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
