<?php

namespace gift\appli\webui\actions;

use gift\appli\core\domain\entities\CoffretType;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class GetCoffretTypeAction
{
    public function __invoke($request, $response, $args)
    {
        $id = $args['id'] ?? null;

        if ($id == null) {
            throw new HttpBadRequestException($request, "Paramètre manquant");
        }

        $coffretType = CoffretType::find($id);

        if (!$coffretType) {
            throw new HttpNotFoundException($request, "Coffret introuvable");
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'pages/ViewCoffretType.twig', $coffretType->toArray());
    }
}