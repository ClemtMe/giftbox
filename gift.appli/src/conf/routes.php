<?php

use gift\appli\api\actions\CategoriesAction;
use gift\appli\api\actions\BoxesAction;
use gift\appli\api\actions\PrestationsAction;
use gift\appli\api\actions\PrestationsByCategorieAction;
use gift\appli\webui\actions\GetCategorieAction;
use gift\appli\webui\actions\GetCategoriesAction;
use gift\appli\webui\actions\GetCoffretsTypeAction;
use gift\appli\webui\actions\GetCoffretTypeAction;
use gift\appli\webui\actions\GetPrestationAction;
use gift\appli\webui\actions\GetPrestationByCateIdAction;
use gift\appli\webui\actions\GetPrestationByCoffretIdAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (Slim\App $app) {
    // Page d'accueil
    $app->get('[/]', function (Request $request, Response $response) {
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'pages/ViewAccueil.twig');
    })->setName('home');

    // Toutes les catégories
    $app->get('/categories[/]', GetCategoriesAction::class)->setName('categories');

    // Une catégories selon un ID
    $app->get('/categorie/{id}[/]', GetCategorieAction::class)->setName('categorie');

    // Une préstation selon un ID passé dans la query string
    $app->get('/prestation[/]', GetPrestationAction::class)->setName('prestation');

    // Les préstations d'une categorie selon un ID
    $app->get('/categorie/{id}/prestations[/]', GetPrestationByCateIdAction::class)->setName('prestations_by_categorie');

    $app->get('/coffretsType[/]', GetCoffretsTypeAction::class)->setName('coffrets_type');

    $app->get('/coffretType/{id}[/]', GetCoffretTypeAction::class)->setName('coffret_type');

    $app->get('/coffretType/{id}/prestations[/]', GetPrestationByCoffretIdAction::class)->setName('prestations_by_coffret');

    // API de toutes les catégories avec option d'en chercher une par id
    $app->get('/api/categories[/[{id}[/]]]', CategoriesAction::class)->setName('api_categories');

    // API de tous les croffrets-types avec option d'en chercher un par id
    $app->get('/api/boxes[/[{id}[/]]]', BoxesAction::class)->setName('api_boxes');

    // API de toutes les prestations avec option d'en chercher une par id
    $app->get('/api/prestations[/[{id}[/]]]', PrestationsAction::class)->setName('api_prestations');

    // API des préstations d'une categorie selon un ID
    $app->get('/api/categories/{id}/prestations[/]', PrestationsByCategorieAction::class)->setName('api_prestations_by_categorie');

    return $app;
};
