<?php

namespace gift\appli\webui\actions;

use gift\appli\core\application\exceptions\EntityNotFoundException;
use gift\appli\core\application\exceptions\ExceptionInterne;
use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\application\usecases\CatalogueInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class GetCategorieAction
{
    private string $template;
    private CatalogueInterface $catalogue;
    public function __construct()
    {
        $this->template = 'pages/ViewCategorie.twig';
        $this->catalogue = new Catalogue();
    }

    public function __invoke($request, $response, $args)
    {
        $id = $args['id'] ?? null;

        if ($id == null) {
            throw new HttpBadRequestException($request, "Paramètre manquant");
        }

        try {
            $categorie = $this->catalogue->getCategorieById($id);
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        } catch (ExceptionInterne $e) {
            throw new HttpInternalServerErrorException($request, $e->getMessage());
        }


        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, $this->template, $categorie);
    }
}