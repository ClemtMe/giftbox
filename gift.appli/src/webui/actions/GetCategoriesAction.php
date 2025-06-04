<?php
namespace gift\appli\webui\actions;

use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\application\usecases\CatalogueInterface;
use gift\appli\core\domain\exceptions\EntityNotFoundException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class GetCategoriesAction {

    private string $template;
    private CatalogueInterface $catalogue;
    public function __construct()
    {
        $this->template = 'pages/ViewCategories.twig';
        $this->catalogue = new Catalogue();
    }

    public function __invoke($request, $response, array $args)
    {
        // Récupérer les catégories depuis le modèle
        try {
            $categories = $this->catalogue->getCategories();
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        } catch (ExceptionDatabase $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, $this->template, [
            'categories' => $categories
        ]);
    }
}