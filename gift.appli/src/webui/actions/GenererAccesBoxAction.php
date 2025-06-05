<?php

namespace gift\appli\webui\actions;

use gift\appli\core\domain\exceptions\InvalidTokenException;
use gift\appli\core\domain\exceptions\TokenMissingException;
use gift\appli\webui\providers\BoxServiceProvider;
use gift\appli\webui\providers\BoxServiceProviderInterface;
use gift\appli\webui\providers\CsrfTokenProviderInterface;
use gift\appli\webui\providers\SessionCsrfTokenProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use webui\exceptions\BoxAccesException;

class GenererAccesBoxAction
{
    private BoxServiceProviderInterface $boxService;
    private CsrfTokenProviderInterface $csrfTokenProvider;

    public function __construct()
    {
        $this->boxService = new BoxServiceProvider();
        $this->csrfTokenProvider = new SessionCsrfTokenProvider();
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $queryParams = $request->getParsedBody();

        $boxId = $queryParams['box_id'] ?? null;
        $csrfToken = $queryParams['csrf_token'] ?? '';

        // Vérifier le token CSRF
        try {
            $this->csrfTokenProvider->checkCsrf($csrfToken);
        } catch (\gift\appli\webui\exceptions\CsrfException $e) {
            throw new \Slim\Exception\HttpBadRequestException($request, "Token CSRF invalide.");
        }

        try {
            $boxToken = $this->boxService->generateBoxAccesLink($boxId);
        } catch (\gift\appli\webui\exceptions\BoxAccesException $e) {
            throw new \Slim\Exception\HttpBadRequestException($request, "Erreur lors de la génération du lien d'accès à la box : " . $e->getMessage());
        }

        $routeParser = \Slim\Routing\RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->fullUrlFor(
            $request->getUri(),
            'access_box',
            [],
            ['token' => $boxToken]
        );

        $urlHome = $routeParser->urlFor('mes_boxes');

        // Ouvrir une alerte avec le lien généré
        $response->getBody()->write(
            "<script>
                alert('Lien d\'accès généré : ' + '{$url}');
                window.location.href = '{$urlHome}';
            </script>"
        );
        return $response;
    }
}
