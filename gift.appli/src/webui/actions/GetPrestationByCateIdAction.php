<?php
namespace gift\appli\webui\actions;

use gift\appli\core\domain\entities\Categorie;
use Slim\Routing\RouteContext;

class GetPrestationByCateIdAction{
    public function __invoke($request, $response, array $args)
    {
        $id = $args['id'] ?? null;

        if ($id == null) {
            throw new \Slim\Exception\HttpBadRequestException($request, "Paramètre manquant");
        }

        try {
            $categorie = Categorie::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Slim\Exception\HttpNotFoundException($request, $e->getMessage());
        } catch (\Exception $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $prestations = $categorie->prestations;
        if ($prestations->isEmpty()) {
            throw new \Slim\Exception\HttpNotFoundException($request, "Aucune prestation trouvée pour la catégorie $categorie->libelle.");
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        foreach ($prestations as $prestation) {
            $prestation->url = $routeParser->urlFor('prestation', ['id' => $prestation->id]);
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'pages/ViewCategoriePrestations.twig', [
            'prestations' => $prestations,
            'categorie' => $categorie,
        ]);
    }
}