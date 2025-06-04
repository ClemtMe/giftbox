<?php

namespace gift\appli\webui\actions;
use gift\appli\core\application\exceptions\AuthorizationException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\application\usecases\BoxManagement;
use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\domain\exceptions\EntityNotFoundException;
use gift\appli\webui\exceptions\ProviderAuthentificationException;
use gift\appli\webui\providers\AuthProviderInterface;
use gift\appli\webui\providers\SessionAuthProvider;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class SetPresta2BoxAction
{
    private Catalogue $catalogue;
    private BoxManagement $bm;
    private AuthProviderInterface $auth;

    public function __construct()
    {
        $this->catalogue = new Catalogue();
        $this->bm = new BoxManagement();
        $this->auth = new SessionAuthProvider();
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
                $user = $this->auth->getSignedInUser();
                $this->bm->updateBoxPrestation($this->auth->getSignedInUser()['id'], $_SESSION['box'], $presta_id, $quantite);
            } catch (ExceptionDatabase $e) {
                throw new \Slim\Exception\HttpInternalServerErrorException($request, "Erreur de base de données : " . $e->getMessage());
            } catch (EntityNotFoundException $e) {
                throw new HttpNotFoundException($request, "Entité non trouvée : " . $e->getMessage());
            } catch (AuthorizationException $e) {
                throw new HttpForbiddenException($request, "Vous n'êtes pas autorisé à effectuer cette action : " . $e->getMessage());
            } catch (ProviderAuthentificationException $e) {
                throw new \Slim\Exception\HttpUnauthorizedException($request, "Erreur d'authentification : " . $e->getMessage());
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