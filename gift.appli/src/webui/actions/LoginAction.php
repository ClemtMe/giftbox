<?php

namespace gift\appli\webui\actions;

use gift\appli\webui\exceptions\ProviderAuthentificationException;
use gift\appli\webui\providers\AuthProviderInterface;
use gift\appli\webui\providers\SessionAuthProvider;
use Slim\Routing\RouteContext;

class LoginAction
{

    private string $template;
    private AuthProviderInterface $authProvider;
    public function __construct()
    {
        $this->template = 'pages/ViewLogin.twig';
        $this->authProvider = new SessionAuthProvider();
    }

    public function __invoke($request, $response, $args)
    {
        if ($request->getMethod() === 'POST') {

            $params = $request->getParsedBody();
            $email = $params['email'] ?? '';
            $password = $params['password'] ?? '';

            // Authentifier l'utilisateur
            try {
                $this->authProvider->loginByCredential($email, $password);
            } catch (ProviderAuthentificationException $e) {
                throw new \Slim\Exception\HttpUnauthorizedException($request,"Authentification échouée : " . $e->getMessage());
            }

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            if ($this->authProvider->getSignedInUser() !== []) {
                // Authentification réussie, rediriger vers la page d'accueil
                return $response->withHeader('Location', $routeParser->urlFor('home'))->withStatus(302);
            }else{
                // Authentification échouée, rediriger vers la page de connexion
                return $response->withHeader('Location', $routeParser->urlFor('login'))->withStatus(302);
            }

        } else {
            // Afficher le formulaire
            $view = \Slim\Views\Twig::fromRequest($request);
            return $view->render($response, $this->template);
        }
    }
}