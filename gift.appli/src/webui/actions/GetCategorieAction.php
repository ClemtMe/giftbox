<?php

namespace gift\appli\webui\actions;

use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\application\usecases\CatalogueInterface;
use gift\appli\core\domain\exceptions\EntityNotFoundException;
use Slim\Exception\HttpBadRequestException;
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
        } catch (ExceptionDatabase $e) {
            throw new HttpInternalServiceException($request, $e->getMessage());
        }


        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, $this->template, $categorie);
    }
}