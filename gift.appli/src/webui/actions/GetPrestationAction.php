<?php
namespace gift\appli\webui\actions;
use Slim\Exception\HttpBadRequestException;
use gift\appli\core\domain\entities\Prestation;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

class GetPrestationAction
{
    public function __invoke($rq, $rs, $args) {
        $id = $args['id'] ?? null;
        if ($id == null) {
            throw new HttpBadRequestException($rq, "Paramètre manquant");
        }
        $prestation = Prestation::find($id);
        if ($prestation) {
            $view = Twig::fromRequest($rq);
            return $view->render($rs, 'pages/ViewPrestation.twig', ['nom' => $prestation->libelle, 'description' => $prestation->description,
                'unite' => $prestation->unite, 'tarif' => $prestation->tarif, 'img' => $prestation->img, 'url' => $prestation->url]);
        } else {
            throw new HttpNotFoundException($rq, "Prestation introuvable");
        }
    }
}   

?>