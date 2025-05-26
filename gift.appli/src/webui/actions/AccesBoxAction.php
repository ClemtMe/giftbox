<?php

namespace gift\appli\webui\actions;

use gift\appli\core\application\usecases\BoxInterface;
use gift\appli\core\domain\exceptions\TokenMissingException;
use gift\appli\core\domain\exceptions\InvalidTokenException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AccesBoxAction
{
    private BoxInterface $boxService;

    public function __construct(BoxInterface $boxService)
    {
        $this->boxService = $boxService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $params = $request->getQueryParams();
        $token = $params['token'] ?? null;

        if (!$token) {
            throw new TokenMissingException("Aucun token fourni.");
        }

        $decodedToken = urldecode($token);
        $box = $this->boxService->getBoxByToken($decodedToken);

        if (!$box) {
            throw new InvalidTokenException("Token invalide ou box introuvable.");
        }

        $response->getBody()->write("Accès autorisé à la box : " . htmlspecialchars($box->name));
        return $response;
    }
}
