<?php

namespace gift\appli\webui\actions;

use gift\appli\core\application\exceptions\AuthentificationException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\webui\providers\AuthProvider;
use gift\appli\webui\providers\AuthProviderInterface;
use Slim\Routing\RouteContext;

class LoginAction
{

    private string $template;
    private AuthProviderInterface $authProvider;
    public function __construct()
    {
        $this->template = 'pages/ViewLogin.twig';
        $this->authProvider = new AuthProvider();
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
            } catch (AuthentificationException $e) {
                throw new \Slim\Exception\HttpUnauthorizedException($request,"Authentification échouée : " . $e->getMessage());
            } catch (ExceptionDatabase $e) {
                throw new \Slim\Exception\HttpInternalServerErrorException($request,"Erreur de base de donnée : " . $e->getMessage());
            }

            if (isset($_SESSION['user'])) {
                // Authentification réussie, rediriger vers la page d'accueil
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                return $response->withHeader('Location', $routeParser->urlFor('home'))->withStatus(302);
            }

        } else {
            // Afficher le formulaire
            $view = \Slim\Views\Twig::fromRequest($request);
            return $view->render($response, $this->template);
        }
    }
}