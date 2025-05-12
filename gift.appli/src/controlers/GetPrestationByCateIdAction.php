<?php
namespace gift\appli\controlers;

class GetPrestationByCateIdAction{
    public function __invoke($request, $response, array $args)
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

        $prestations = $categorie->prestations;
        if ($prestations->isEmpty()) {
            throw new \Slim\Exception\HttpNotFoundException($request, "Aucune prestation trouvée pour la catégorie $categorie->libelle.");
        }

        $text = "Prestations de la catégorie : {$categorie->libelle}\n";
        foreach ($prestations as $prestation) {
            $text .= "- {$prestation->libelle}: {$prestation->description}\n";
        }

        $response->getBody()->write($text);

        return $response->withHeader('Content-Type', 'text/plain');
    }
}