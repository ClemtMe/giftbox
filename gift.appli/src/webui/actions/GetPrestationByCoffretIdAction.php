<?php
namespace gift\appli\webui\actions;

use gift\appli\core\application\exceptions\EntityNotFoundException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\application\usecases\CatalogueInterface;
use Slim\Routing\RouteContext;

class GetPrestationByCoffretIdAction
{

    private string $template;
    private CatalogueInterface $catalogue;
    public function __construct()
    {
        $this->template = 'pages/ViewCoffretPrestations.twig';
        $this->catalogue = new Catalogue();
    }

    public function __invoke($request, $response, array $args)
    {
        $id = $args['id'] ?? null;

        if ($id == null) {
            throw new \Slim\Exception\HttpBadRequestException($request, "Paramètre manquant");
        }

        try {
            $coffretType = $this->catalogue->getCoffretById($id);
            $prestations = $this->catalogue->getPrestationsByCoffret($id);
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        } catch (ExceptionDatabase $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        foreach ($prestations as $prestation) {
            $url = $routeParser->urlFor('prestation');
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, $this->template, [
            'prestations' => $prestations,
            'coffret' => $coffretType,
            'url' => $url,
        ]);

    }
}