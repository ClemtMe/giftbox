<?php

namespace gift\appli\webui\actions;

use gift\appli\core\domain\exceptions\InvalidTokenException;
use gift\appli\core\domain\exceptions\TokenMissingException;
use gift\appli\webui\exceptions\BoxAccesException;
use gift\appli\webui\providers\BoxServiceProvider;
use gift\appli\webui\providers\BoxServiceProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AccesBoxAction
{
    private BoxServiceProviderInterface $boxService;
    private $template;

    public function __construct()
    {
        $this->boxService = new BoxServiceProvider();
        $this->template = 'pages/ViewBox.twig';
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $params = $request->getQueryParams();
        $token = $params['token'] ?? null;

        if (!$token) {
            throw new \Slim\Exception\HttpBadRequestException($request, "Token manquant dans les paramètres de la requête.");
        }

        try {
            $box = $this->boxService->getBoxByToken($token);
        } catch (BoxAccesException $e) {
            throw new \Slim\Exception\HttpBadRequestException($request, "Erreur lors de l'accès à la box: " . $e->getMessage());
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, $this->template, [
            'box' => $box
        ]);
    }
}
