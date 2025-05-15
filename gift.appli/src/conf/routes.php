<?php

use gift\appli\controlers\GetCategorieAction;
use gift\appli\controlers\GetCategoriesAction;
use gift\appli\controlers\GetCoffretsTypeAction;
use gift\appli\controlers\GetCoffretTypeAction;
use gift\appli\controlers\GetPrestationAction;
use gift\appli\controlers\GetPrestationByCateIdAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (Slim\App $app) {
    // Page d'accueil
    $app->get('[/]', function (Request $request, Response $response) {
        $response->getBody()->write("Bienvenue sur la page d'accueil !");
        return $response;
    })->setName('home');

    // Toutes les catégories
    $app->get('/categories[/]', GetCategoriesAction::class)->setName('categories');

    // Une catégories selon un ID
    $app->get('/categorie[/[{id}[/]]]', GetCategorieAction::class)->setName('categorie');

    // Une préstation selon un ID passé dans la query string
    $app->get('/prestation[/[{id}[/]]]', GetPrestationAction::class)->setName('prestation');

    // Les préstations d'une categorie selon un ID
    $app->get('/categorie/{id}/prestations[/]', GetPrestationByCateIdAction::class)->setName('prestations_by_categorie');

    $app->get('/coffretsType[/]', GetCoffretsTypeAction::class)->setName('coffrets_type');

    $app->get('/coffretType[/[{id}[/]]]', GetCoffretTypeAction::class)->setName('coffret_type');

    return $app;
};
