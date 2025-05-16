<?php
namespace gift\appli\controlers;

use Slim\Routing\RouteContext;

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
        $basePath = RouteContext::fromRequest($request)->getBasePath();
        $url = $basePath . '/prestation/';
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'pages/ViewCategoriePrestations.twig', [
            'prestations' => $prestations,
            'categorie' => $categorie,
            'url' => $url,
        ]);
    }
}