<?php

namespace gift\appli\webui\actions;
use gift\appli\core\application\exceptions\AuthorizationException;
use gift\appli\core\application\exceptions\ExceptionInterne;
use gift\appli\core\application\usecases\BoxManagement;
use gift\appli\core\application\usecases\BoxManagementInterface;
use gift\appli\core\domain\exceptions\EntityNotFoundException;
use gift\appli\webui\exceptions\CsrfException;
use gift\appli\webui\exceptions\ProviderAuthentificationException;
use gift\appli\webui\providers\AuthProviderInterface;
use gift\appli\webui\providers\CsrfTokenProviderInterface;
use gift\appli\webui\providers\SessionAuthProvider;
use gift\appli\webui\providers\SessionCsrfTokenProvider;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class ValidateBoxAction
{
    private BoxManagementInterface $bm;
    private CsrfTokenProviderInterface $csrfTokenProvider;
    private AuthProviderInterface $auth;

    public function __construct()
    {
        $this->csrfTokenProvider = new SessionCsrfTokenProvider();
        $this->auth = new SessionAuthProvider();
        $this->bm = new BoxManagement();
    }

    public function __invoke($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $boxId = $params['box_id'] ?? '';
        $csrfToken = $params['csrf_token'] ?? '';

        if (empty($boxId)) {
            throw new HttpBadRequestException($request, "ID de la box manquant.");
        }

        // Vérifier le token CSRF
        try {
            $this->csrfTokenProvider->checkCsrf($csrfToken);
        } catch (CsrfException $e) {
            throw new \Slim\Exception\HttpBadRequestException($request, "Token CSRF invalide.");
        }

        try {
            $userId = $this->auth->getSignedInUser()['id'];
        } catch (ProviderAuthentificationException $e) {
            throw new \Slim\Exception\HttpUnauthorizedException($request, "Authentification échouée : " . $e->getMessage());
        }

        try {
            if($this->bm->validateBox($userId, $boxId)) {
                unset($_SESSION['box']);
            }
        } catch (AuthorizationException $e) {
            throw new HttpForbiddenException($request, "Vous n'êtes pas autorisé à valider cette box : " . $e->getMessage());
        } catch (ExceptionInterne | \gift\appli\core\application\exceptions\EntityNotFoundException $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, "Erreur de base de données : " . $e->getMessage());
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response->withHeader('Location', $routeParser->urlFor('mes_boxes'))->withStatus(302);
    }
}