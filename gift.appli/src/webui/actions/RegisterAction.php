<?php

namespace gift\appli\webui\actions;

use gift\appli\webui\exceptions\CsrfException;
use gift\appli\webui\exceptions\ProviderAuthentificationException;
use gift\appli\webui\providers\AuthProviderInterface;
use gift\appli\webui\providers\CsrfTokenProviderInterface;
use gift\appli\webui\providers\SessionAuthProvider;
use gift\appli\webui\providers\SessionCsrfTokenProvider;
use Slim\Routing\RouteContext;

class RegisterAction
{
    private string $template;
    private AuthProviderInterface $authProvider;
    private CsrfTokenProviderInterface $tokenProvider;

    public function __construct()
    {
        $this->template = 'pages/ViewRegister.twig';
        $this->authProvider = new SessionAuthProvider();
        $this->tokenProvider = new SessionCsrfTokenProvider();
    }

    public function __invoke($request, $response, array $args)
    {
        if ($request->getMethod() === 'POST') {
            // Traiter le formulaire
            $params = $request->getParsedBody();
            $email = $params['email'] ?? '';
            $password = $params['password'] ?? '';
            $csrfToken = $params['csrf_token'] ?? '';

            // Vérifier le token CSRF
            try {
                $this->tokenProvider->checkCsrf($csrfToken);
            } catch (CsrfException $e) {
                throw new \Slim\Exception\HttpBadRequestException($request, "Token CSRF invalide.");
            }

            // Valider les données
            if(FILTER_VAR($email, FILTER_VALIDATE_EMAIL) === false || empty($email)) {
                throw new \Slim\Exception\HttpBadRequestException($request, "Adresse e-mail invalide.");
            }

            if (empty($password) || strlen($password) < 6 || !preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
                throw new \Slim\Exception\HttpBadRequestException($request, "Le mot de passe doit contenir au moins 6 caractères et inclure des lettres et des chiffres.");
            }

            // Authentifier l'utilisateur
            try {
                $this->authProvider->register($email, $password);
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
            $token = $this->tokenProvider->generateCsrf();

            // Afficher le formulaire d'inscription
            $view = \Slim\Views\Twig::fromRequest($request);
            return $view->render($response, $this->template , [
                'csrf_token' => $token
            ]);
        }
    }
}