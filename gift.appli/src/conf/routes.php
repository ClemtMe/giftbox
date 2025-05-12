<?php

use gift\appli\controlers\GetCategorieAction;
use gift\appli\controlers\GetCategoriesAction;
use gift\appli\controlers\GetPrestationAction;
use gift\appli\controlers\GetPrestationByCateIdAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (Slim\App $app) {
    // Page d'accueil
    $app->get('[/]', function (Request $request, Response $response) {
        $response->getBody()->write("Bienvenue sur la page d'accueil !");
        return $response;
    });

    // Toutes les catégories
    $app->get('/categories[/]', GetCategoriesAction::class);

    // Une catégories selon un ID
    $app->get('/categorie[/[{id}[/]]]', GetCategorieAction::class);

    // Une préstation selon un ID passé dans la query string
    $app->get('/prestation[/[{id}[/]]]', GetPrestationAction::class);

    // Les préstations d'une categorie selon un ID
    $app->get('/categorie/{id}/prestations[/]', GetPrestationByCateIdAction::class);

    return $app;
};
