<?php

namespace gift\appli\webui\actions;

use gift\appli\webui\exceptions\CsrfException;
use gift\appli\webui\exceptions\ProviderAuthentificationException;
use gift\appli\webui\providers\AuthProviderInterface;
use gift\appli\webui\providers\CsrfTokenProviderInterface;
use gift\appli\webui\providers\SessionAuthProvider;
use gift\appli\webui\providers\SessionCsrfTokenProvider;
use Slim\Routing\RouteContext;

class LoginAction
{

    private string $template;
    private AuthProviderInterface $authProvider;
    private CsrfTokenProviderInterface $csrfTokenProvider;

    public function __construct()
    {
        $this->template = 'pages/ViewLogin.twig';
        $this->authProvider = new SessionAuthProvider();
        $this->csrfTokenProvider = new SessionCsrfTokenProvider();
    }

    public function __invoke($request, $response, $args)
    {
        if ($request->getMethod() === 'POST') {

            $params = $request->getParsedBody();
            $email = $params['email'] ?? '';
            $password = $params['password'] ?? '';
            $csrfToken = $params['csrf_token'] ?? '';

            // Vérifier le token CSRF
            try {
                $this->csrfTokenProvider->checkCsrf($csrfToken);
            } catch (CsrfException $e) {
                throw new \Slim\Exception\HttpBadRequestException($request, "Token CSRF invalide.");
            }

            // Authentifier l'utilisateur
            try {
                $this->authProvider->loginByCredential($email, $password);
            } catch (ProviderAuthentificationException $e) {
                throw new \Slim\Exception\HttpUnauthorizedException($request,"Authentification échouée : " . $e->getMessage());
            }

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            try {
                $this->authProvider->getSignedInUser();
                return $response->withHeader('Location', $routeParser->urlFor('home'))->withStatus(302);
            } catch (ProviderAuthentificationException $e) {
                return $response->withHeader('Location', $routeParser->urlFor('login'))->withStatus(302);
            }

        } else {
            $token = $this->csrfTokenProvider->generateCsrf();

            // Afficher le formulaire
            $view = \Slim\Views\Twig::fromRequest($request);
            return $view->render($response, $this->template , [
                'csrf_token' => $token
            ]);
        }
    }
}