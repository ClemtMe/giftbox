<?php

namespace gift\appli\controlers;

use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class GetCategorieAction
{
    public function __invoke($request, $response, $args)
    {
        $id = $args['id'] ?? null;

        if ($id == null) {
            throw new HttpBadRequestException($request, "Paramètre manquant");
        }

        $categorie = \gift\appli\models\Categorie::find($id);

        if (!$categorie) {
            throw new HttpNotFoundException($request, "Catégorie introuvable");
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'pages/ViewCategorie.twig', $categorie->toArray());
    }
}