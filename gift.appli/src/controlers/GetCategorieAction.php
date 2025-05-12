<?php

namespace gift\appli\controlers;

class GetCategorieAction
{
    public function __invoke($request, $response, $args)
    {
        $id = $args['id'] ?? null;

        if ($id == null) {
            throw new \Slim\Exception\HttpBadRequestException($request, "Paramètre manquant");
        }

        try {
            $categorie = \gift\appli\models\Categorie::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Slim\Exception\HttpNotFoundException($request, $e->getMessage());
        } catch (\Exception $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $html = <<<HTML
        <h1>Catégorie : {$categorie->libelle}</h1>
        <p>{$categorie->description}</p>    
        HTML;

        $response->getBody()->write($html);
        return $response;
    }
}