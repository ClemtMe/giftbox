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

        $html = <<<HTML
        <h1>Catégorie : {$categorie->libelle}</h1>
        <p>{$categorie->description}</p>    
        HTML;

        $response->getBody()->write($html);
        return $response;
    }
}