<?php
namespace gift\appli\webui\actions;

use gift\appli\core\application\exceptions\EntityNotFoundException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\application\usecases\CatalogueInterface;
use Slim\Exception\HttpNotFoundException;

class GetCoffretsTypeAction{

    private string $template;
    private CatalogueInterface $catalogue;
    public function __construct()
    {
        $this->template = 'pages/ViewCoffretsType.twig';
        $this->catalogue = new Catalogue();
    }

    public function __invoke($request, $response, $args)
    {
        try {
            $coffretsType = $this->catalogue->getCoffrets();
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        } catch (ExceptionDatabase $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, $this->template, [
            'coffrets_type' => $coffretsType
        ]);
    }
}