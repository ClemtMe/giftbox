<?php

namespace gift\appli\webui\actions;

use gift\appli\webui\exceptions\ProviderAuthentificationException;
use gift\appli\webui\providers\AuthProviderInterface;
use gift\appli\webui\providers\SessionAuthProvider;
use Slim\Routing\RouteContext;

class LogoutAction
{
    private AuthProviderInterface $authProvider;

    public function __construct()
    {
        $this->authProvider = new SessionAuthProvider();
    }

    public function __invoke($request, $response, $args)
    {
        try {
            $this->authProvider->logout();
        } catch (ProviderAuthentificationException $e) {
            throw new \Slim\Exception\HttpUnauthorizedException($request, "Authentification échouée : " . $e->getMessage());
        }

        // Rediriger vers la page de connexion
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response->withHeader('Location', $routeParser->urlFor('login'))->withStatus(302);
    }
}