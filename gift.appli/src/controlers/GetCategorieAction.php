<?php

namespace gift\appli\controlers;

class GetCategorieAction
{
    public function __invoke($request, $response, $args)
    {
        $id = $args['id'] ?? null;

        if ($id == null) {
            return $response->withStatus(400);
        }

        $categorie = \gift\appli\models\Categorie::find($id);

        if (!$categorie) {
            return $response->withStatus(404)->write('Catégorie non trouvée');
        }

        $html = <<<HTML
        <h1>Catégorie : {$categorie->libelle}</h1>
        <p>{$categorie->description}</p>    
        HTML;

        $response->getBody()->write($html);
        return $response;
    }
}