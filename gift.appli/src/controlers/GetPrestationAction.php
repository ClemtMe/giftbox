<?php
namespace gift\appli\controlers;
use Slim\Exception\HttpBadRequestException;
use gift\appli\models\Prestation;
use Slim\Exception\HttpNotFoundException;

class GetPrestationAction
{
    public function __invoke($rq, $rs, $args) {
        $id = $args['id'] ?? null;
        if ($id == null) {
            throw new HttpBadRequestException($rq, "Paramètre manquant");
        }
        $prestation = Prestation::find($id);
        if ($prestation) {
            $rs->getBody()->write(json_encode($prestation));
            return $rs->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpNotFoundException($rq, "Prestation introuvable");
        }
    }
}   

?>