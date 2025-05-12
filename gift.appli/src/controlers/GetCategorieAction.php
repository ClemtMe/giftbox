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

        try {
            $categorie = \gift\appli\models\Categorie::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $html = <<<HTML
            <h1>Catégorie non trouvée</h1>
            <p>La catégorie que vous recherchez n'existe pas.</p>
            <a href="/giftbox/categories">Toutes les catégories</a>
            HTML;

            $response->getBody()->write($html);
            return $response->withStatus(400);
        } catch (\Exception $e) {
            return $response->withStatus(500);
        }

        $html = <<<HTML
        <h1>Catégorie : {$categorie->libelle}</h1>
        <p>{$categorie->description}</p>    
        HTML;

        $response->getBody()->write($html);
        return $response;
    }
}