<?php
namespace gift\appli\webui\actions;

use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\application\usecases\CatalogueInterface;
use gift\appli\core\domain\exceptions\EntityNotFoundException;
use Slim\Routing\RouteContext;

class GetPrestationByCateIdAction{

    private string $template;
    private CatalogueInterface $catalogue;
    public function __construct()
    {
        $this->template = 'pages/ViewCategoriePrestations.twig';
        $this->catalogue = new Catalogue();
    }

    public function __invoke($request, $response, array $args)
    {
        $id = $args['id'] ?? null;

        if ($id == null) {
            throw new \Slim\Exception\HttpBadRequestException($request, "Paramètre manquant");
        }

        try {
            $categorie = $this->catalogue->getCategorieById($id);
            $prestations = $this->catalogue->getPrestationsByCategorie($id);
        } catch (EntityNotFoundException $e) {
            throw new \Slim\Exception\HttpNotFoundException($request, $e->getMessage());
        } catch (ExceptionDatabase $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $url = $routeParser->urlFor('prestation');

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, $this->template, [
            'prestations' => $prestations,
            'categorie' => $categorie,
            'url' => $url,
        ]);
    }
}