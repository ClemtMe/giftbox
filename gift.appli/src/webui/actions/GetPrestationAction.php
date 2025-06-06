<?php
namespace gift\appli\webui\actions;
use gift\appli\core\application\exceptions\EntityNotFoundException;
use gift\appli\core\application\exceptions\ExceptionInterne;
use gift\appli\core\application\usecases\BoxManagement;
use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\application\usecases\CatalogueInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class GetPrestationAction
{

    private string $template;
    private CatalogueInterface $catalogue;
    private BoxManagement $bm;
    public function __construct()
    {
        $this->template = 'pages/ViewPrestation.twig';
        $this->catalogue = new Catalogue();
        $this->bm = new BoxManagement();
    }
    public function __invoke($rq, $rs, $args) {
        $queryParams = $rq->getQueryParams();

        $id = $queryParams['id'] ?? null;
        if ($id == null) {
            throw new HttpBadRequestException($rq, "Paramètre manquant");
        }

        try {
            $prestation = $this->catalogue->getPrestationById($id);
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        } catch (ExceptionInterne $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($rq, $e->getMessage());
        }

        if (isset($_SESSION['box'])) {
            try {
                $qty = $this->bm->getQtyPrestation($id, $_SESSION['box']);
            } catch (ExceptionInterne $e) {
                throw new \Slim\Exception\HttpInternalServerErrorException($rq, "Erreur de base de données : " . $e->getMessage());
            } catch (EntityNotFoundException $e) {
                throw new HttpNotFoundException($rq, "Entité non trouvée : " . $e->getMessage());
            }

            $routeParser = RouteContext::fromRequest($rq)->getRouteParser();
            $url = $routeParser->urlFor('set_prestation_to_box');

            $view = Twig::fromRequest($rq);
            return $view->render($rs, $this->template, ['prestation' => $prestation, 'quantity' => $qty, 'url' => $url]);
        }

        $view = Twig::fromRequest($rq);
        return $view->render($rs, $this->template, ['prestation' => $prestation]);
    }
}   

