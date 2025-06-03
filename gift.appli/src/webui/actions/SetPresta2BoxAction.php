<?php

namespace gift\appli\webui\actions;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\application\usecases\BoxManagement;
use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\domain\exceptions\EntityNotFoundException;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class SetPresta2BoxAction
{
    private Catalogue $catalogue;
    private BoxManagement $bm;
    private string $template;

    public function __construct()
    {
        $this->catalogue = new Catalogue();
        $this->bm = new BoxManagement();
        $this->template = 'pages/ViewPrestation.twig';
    }
    public function __invoke($request, $response, $args)
    {
        $queryParams = $request->getParsedBody();

        $presta_id = $queryParams['prestation_id'] ?? null;
        $quantite = $queryParams['quantity'] ?? null;
        if ($presta_id == null || $quantite == null) {
            throw new HttpBadRequestException($request, "Paramètre manquant");
        }

        if (isset($_SESSION['box'])) {
            try {
                $this->bm->updateBoxPrestation('', $_SESSION['box'], $presta_id, $quantite);
                $qty = $this->bm->getQtyPrestation($presta_id, $_SESSION['box']);
                $box = $this->bm->getBoxByIdSessionFormat($_SESSION['box']);
            } catch (ExceptionDatabase $e) {
                throw new HttpInternalServiceException($request, "Erreur de base de données : " . $e->getMessage());
            } catch (EntityNotFoundException $e) {
                throw new HttpNotFoundException($request, "Entité non trouvée : " . $e->getMessage());
            }
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        try {
            $prestation = $this->catalogue->getPrestationById($presta_id);
        } catch (ExceptionDatabase $e) {
            throw new HttpInternalServiceException($request, "Erreur de base de données : " . $e->getMessage());
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, "Entité non trouvée : " . $e->getMessage());
        }

        return $response->withHeader('Location', $routeParser->urlFor('prestation')."?id={$prestation['id']}")->withStatus(302);
    }
}