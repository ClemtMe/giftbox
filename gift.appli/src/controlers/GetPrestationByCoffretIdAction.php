<?php
namespace gift\appli\controlers;

class GetPrestationByCoffretIdAction
{
    public function __invoke($request, $response, array $args)
    {
        $id = $args['id'] ?? null;

        if ($id == null) {
            throw new \Slim\Exception\HttpBadRequestException($request, "Paramètre manquant");
        }

        try {
            $coffretType = \gift\appli\models\CoffretType::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Slim\Exception\HttpNotFoundException($request, $e->getMessage());
        } catch (\Exception $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $prestations = $coffretType->prestations;
        if ($prestations->isEmpty()) {
            throw new \Slim\Exception\HttpNotFoundException($request, "Aucune prestation trouvée pour la catégorie $coffretType->libelle.");
        }

        $text = "Prestations du coffret : {$coffretType->libelle}\n";
        foreach ($prestations as $prestation) {
            $text .= "- {$prestation->libelle}: {$prestation->description}\n";
        }

        $response->getBody()->write($text);

        return $response->withHeader('Content-Type', 'text/plain');
    }
}