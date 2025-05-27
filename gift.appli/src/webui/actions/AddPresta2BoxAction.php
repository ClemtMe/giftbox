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
    public function __invoke($request, $response, $args)
    {
        $this->catalogue = new Catalogue();
        $this->bm = new BoxManagement();
        $queryParams = $request->getQueryParams();

        $id = $queryParams['id'] ?? null;
        if ($id == null) {
            throw new HttpBadRequestException($request, "Paramètre manquant");
        }
        if (isset($_SESSION['box'])) {
            $presta_id = $request->getParam('id');
            $this->bm->updateBoxPrestation($_SESSION['user'], $_SESSION['box'], $presta_id, 1);
            $qty = $this->bm->getQtyPrestation($id, $_SESSION['box']->id);
        }else $qty=0;


        $prestation = $this->catalogue->getPrestationById($id);
        $template = 'pages/ViewPrestation.twig';
        $view = Twig::fromRequest($request);
        return $view->render($response, $template, ['prestation' => $prestation, 'quantity' => $qty]);
    }
}