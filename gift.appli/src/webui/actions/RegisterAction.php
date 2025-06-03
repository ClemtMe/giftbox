<?php

namespace gift\appli\webui\actions;

use gift\appli\core\application\exceptions\AuthentificationException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\webui\providers\AuthProvider;
use gift\appli\webui\providers\AuthProviderInterface;
use Slim\Routing\RouteContext;

class RegisterAction
{
    private string $template;
    private AuthProviderInterface $authProvider;
    public function __construct()
    {
        $this->template = 'pages/ViewRegister.twig';
        $this->authProvider = new AuthProvider();
    }

    public function __invoke($request, $response, array $args)
    {
        if ($request->getMethod() === 'POST') {
            // Traiter le formulaire
            $params = $request->getParsedBody();
            $email = $params['email'] ?? '';
            $password = $params['password'] ?? '';

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
            } catch (ExceptionDatabase $e) {
                throw new \Slim\Exception\HttpInternalServerErrorException($request,"Erreur lors de l'enregistrement : " . $e->getMessage());
            } catch (AuthentificationException $e) {
                throw new \Slim\Exception\HttpUnauthorizedException($request,"Authentification échouée : " . $e->getMessage());
            }

            if (isset($_SESSION['user'])) {
                // Authentification réussie, rediriger vers la page d'accueil
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                return $response->withHeader('Location', $routeParser->urlFor('home'))->withStatus(302);
            }
        } else {
            // Afficher le formulaire d'inscription
            $view = \Slim\Views\Twig::fromRequest($request);
            return $view->render($response, $this->template);
        }
    }
}