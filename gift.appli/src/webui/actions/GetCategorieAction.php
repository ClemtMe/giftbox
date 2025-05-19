<?php

namespace gift\appli\webui\actions;

use gift\appli\application_core\domain\entities\Categorie;
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

        $categorie = Categorie::find($id);

        if (!$categorie) {
            throw new HttpNotFoundException($request, "Catégorie introuvable");
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'pages/ViewCategorie.twig', $categorie->toArray());
    }
}