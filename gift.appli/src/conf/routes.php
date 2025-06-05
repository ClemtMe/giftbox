<?php

use gift\appli\webui\actions\AccesBoxAction;
use gift\appli\webui\actions\CreationBoxAction;
use gift\appli\webui\actions\CreationBoxCoffretAction;
use gift\appli\webui\actions\DeleteBoxAction;
use gift\appli\webui\actions\GetCategorieAction;
use gift\appli\webui\actions\GetCategoriesAction;
use gift\appli\webui\actions\GetCoffretsTypeAction;
use gift\appli\webui\actions\GetCoffretTypeAction;
use gift\appli\webui\actions\GetPrestationAction;
use gift\appli\webui\actions\GetPrestationByCateIdAction;
use gift\appli\webui\actions\GetPrestationByCoffretIdAction;
use gift\appli\webui\actions\GetUserBoxesAction;
use gift\appli\webui\actions\LoginAction;
use gift\appli\webui\actions\LogoutAction;
use gift\appli\webui\actions\ModifBoxAction;
use gift\appli\webui\actions\RegisterAction;
use gift\appli\webui\actions\SetPresta2BoxAction;
use gift\appli\webui\actions\ValidateBoxAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (Slim\App $app) {
    // Page d'accueil
    $app->get('[/]', function (Request $request, Response $response) {
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'pages/ViewAccueil.twig');
    })->setName('home');

    $app->map(['GET', 'POST'], '/login[/]', LoginAction::class)->setName('login');
    $app->map(['GET', 'POST'], '/register[/]', RegisterAction::class)->setName('register');
    $app->post('/logout[/]',  LogoutAction::class)->setName('logout');

    // Toutes les catégories
    $app->get('/categories[/]', GetCategoriesAction::class)->setName('categories');

    // Une catégories selon un ID
    $app->get('/categorie[/[{id}[/]]]', GetCategorieAction::class)->setName('categorie');

    // Une préstation selon un ID passé dans la query string
    $app->get('/prestation[/]', GetPrestationAction::class)->setName('prestation');

    //met la quantite d'une prestation à la box courante
    $app->post('/prestation[/]', SetPresta2BoxAction::class)->setName('set_prestation_to_box');

    // Les préstations d'une categorie selon un ID
    $app->get('/categorie/{id}/prestations[/]', GetPrestationByCateIdAction::class)->setName('prestations_by_categorie');

    $app->get('/coffretsType[/]', GetCoffretsTypeAction::class)->setName('coffrets_type');

    $app->get('/coffretType[/[{id}[/]]]', GetCoffretTypeAction::class)->setName('coffret_type');

    $app->get('/coffretType/{id}/prestations[/]', GetPrestationByCoffretIdAction::class)->setName('prestations_by_coffret');

    $app->get('/box/access', AccesBoxAction::class)->setName('access_box');

    $app->map(['GET', 'POST'], '/box/create', CreationBoxAction::class)->setName('create_box');

    $app->map(['GET', 'POST'], '/box/createfromcoffret', CreationBoxCoffretAction::class)->setName('create_box_coffret');

    $app->get('/mesBoxes[/]', GetUserBoxesAction::class)->setName('mes_boxes');

    $app->post('/box/modif[/]', ModifBoxAction::class)->setName('box_modif');

    $app->post('/box/validate[/]', ValidateBoxAction::class)->setName('box_validate');

    $app->post('/box/delete[/]', DeleteBoxAction::class)->setName('box_delete');


    return $app;
};
