<?php
namespace gift\appli\webui\actions;

use gift\appli\core\domain\entities\CoffretType;
use Slim\Routing\RouteContext;

class GetPrestationByCoffretIdAction
{
    public function __invoke($request, $response, array $args)
    {
        $id = $args['id'] ?? null;

        if ($id == null) {
            throw new \Slim\Exception\HttpBadRequestException($request, "Paramètre manquant");
        }

        try {
            $coffretType = CoffretType::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Slim\Exception\HttpNotFoundException($request, $e->getMessage());
        } catch (\Exception $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $prestations = $coffretType->prestations;
        if ($prestations->isEmpty()) {
            throw new \Slim\Exception\HttpNotFoundException($request, "Aucune prestation trouvée pour le coffret $coffretType->libelle.");
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        foreach ($prestations as $prestation) {
            $prestation->url = $routeParser->urlFor('prestation', ['id' => $prestation->id]);
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'pages/ViewCoffretPrestations.twig', [
            'prestations' => $prestations,
            'coffret' => $coffretType,
        ]);

    }
}