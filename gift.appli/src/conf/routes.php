<?php

use gift\appli\webui\actions\GetCategorieAction;
use gift\appli\webui\actions\GetCategoriesAction;
use gift\appli\webui\actions\GetCoffretsTypeAction;
use gift\appli\webui\actions\GetCoffretTypeAction;
use gift\appli\webui\actions\GetPrestationAction;
use gift\appli\webui\actions\GetPrestationByCateIdAction;
use gift\appli\webui\actions\GetPrestationByCoffretIdAction;
use gift\appli\webui\actions\AccesBoxAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \gift\appli\webui\actions\AddPresta2BoxAction;

return function (Slim\App $app) {
    // Page d'accueil
    $app->get('[/]', function (Request $request, Response $response) {
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'pages/ViewAccueil.twig');
    })->setName('home');

    // Toutes les catégories
    $app->get('/categories[/]', GetCategoriesAction::class)->setName('categories');

    // Une catégories selon un ID
    $app->get('/categorie[/[{id}[/]]]', GetCategorieAction::class)->setName('categorie');

    // Une préstation selon un ID passé dans la query string
    $app->get('/prestation[/]', GetPrestationAction::class)->setName('prestation');

    //ajout d'une prestation à la box courante
    $app->get('/prestation/add[/]', AddPresta2BoxAction::class)->setName('add_prestation_to_box');

    // Les préstations d'une categorie selon un ID
    $app->get('/categorie/{id}/prestations[/]', GetPrestationByCateIdAction::class)->setName('prestations_by_categorie');

    $app->get('/coffretsType[/]', GetCoffretsTypeAction::class)->setName('coffrets_type');

    $app->get('/coffretType[/[{id}[/]]]', GetCoffretTypeAction::class)->setName('coffret_type');

    $app->get('/coffretType/{id}/prestations[/]', GetPrestationByCoffretIdAction::class)->setName('prestations_by_coffret');

    $app->get('/box/access', AccesBoxAction::class)->setName('access_box');

    return $app;
};
