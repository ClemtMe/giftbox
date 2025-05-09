<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__.'/../vendor/autoload.php';

return function (Slim\App $app) {
    // Route 1 : Page d'accueil
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write("Bienvenue sur la page d'accueil !");
        return $response;
    });

    // Route 2 : Hello
    $app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
        $name = $args['name'];
        $response->getBody()->write("Hello, $name");
        return $response;
    });

    // Route 3 : Allo
    $app->get('/allo/{name}', function (Request $request, Response $response, array $args) {
        $name = $args['name'];
        $response->getBody()->write("Allo, $name");
        return $response;
    });

    $app->get('/categories', \gift\appli\controlers\GetCategoriesAction::class);

    return $app;
};
