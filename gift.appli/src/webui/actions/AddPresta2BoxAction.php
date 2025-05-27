<?php

namespace gift\appli\webui\actions;
use gift\appli\core\application\usecases\BoxManagement;
use gift\appli\core\application\usecases\Catalogue;
use Slim\Exception\HttpBadRequestException;
use Slim\Views\Twig;

class AddPresta2BoxAction
{
    private Catalogue $catalogue;
    private BoxManagement $bm;
    private string $template;

    public function __construct()
    {
        $this->template = 'pages/ViewPrestation.twig';
        $this->catalogue = new Catalogue();
        $this->bm = new BoxManagement();
    }
    public function __invoke($request, $response, $args)
    {
        $queryParams = $request->getQueryParams();

        $presta_id = $queryParams['id'] ?? null;
        if ($presta_id == null) {
            throw new HttpBadRequestException($request, "Paramètre manquant");
        }
        if (isset($_SESSION['box'])) {
            $this->bm->updateBoxPrestation('', $_SESSION['box']['id'], $presta_id, 1);
            $qty = $this->bm->getQtyPrestation($presta_id, $_SESSION['box']['id']);
        }else $qty=0;


        $prestation = $this->catalogue->getPrestationById($presta_id);
        $view = Twig::fromRequest($request);
        return $view->render($response, $this->template, ['prestation' => $prestation, 'quantity' => $qty]);
    }
}