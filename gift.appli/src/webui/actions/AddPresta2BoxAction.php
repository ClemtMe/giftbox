<?php

namespace gift\appli\webui\actions;
use gift\appli\core\application\usecases\BoxManagement;
use gift\appli\core\application\usecases\Catalogue;
use Slim\Views\Twig;

class AddPresta2BoxAction
{
    private Catalogue $catalogue;
    private BoxManagement $bm;
    public function __invoke($request, $response, $args)
    {
        $this->catalogue = new Catalogue();
        $this->bm = new BoxManagement();

        $presta_id = $request->getParam('id');
        $this->bm->updateBoxPrestation($_SESSION['user'], $_SESSION['box'], $presta_id,1);
        $qty = $this->bm->getQtyPrestation($request->getParam('id'), $_SESSION['box']->id);
        $prestation = $this->catalogue->getPrestationById($request->getParam('id'));
        $template = 'pages/ViewPrestation.twig';
        $view = Twig::fromRequest($request);
        return $view->render($request, $template, ['prestation' => $prestation, 'quantity' => $qty]);
    }
}