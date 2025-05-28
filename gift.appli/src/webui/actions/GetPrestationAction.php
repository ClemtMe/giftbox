<?php
namespace gift\appli\webui\actions;
use gift\appli\core\application\exceptions\EntityNotFoundException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\application\usecases\CatalogueInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

class GetPrestationAction
{

    private string $template;
    private CatalogueInterface $catalogue;
    public function __construct()
    {
        $this->template = 'pages/ViewPrestation.twig';
        $this->catalogue = new Catalogue();
    }
    public function __invoke($rq, $rs, $args) {
        $queryParams = $rq->getQueryParams();

        $id = $queryParams['id'] ?? null;
        if ($id == null) {
            throw new HttpBadRequestException($rq, "Paramètre manquant");
        }

        try {
            $prestation = $this->catalogue->getPrestationById($id);
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        } catch (ExceptionDatabase $e) {
            throw new HttpInternalServiceException($rq, $e->getMessage());
        }

        $view = Twig::fromRequest($rq);
        return $view->render($rs, $this->template, $prestation);
    }
}   

?>