<?php
namespace gift\appli\controlers;
use Slim\Exception\HttpBadRequestException;
use gift\appli\models\Prestation;

class GetPrestationAction
{
    public function __invoke($rq, $rs, $args) {
        $id = $args['id'];
        $prestation = Prestation::find($id);
        if ($prestation) {
            $rs->getBody()->write(json_encode($prestation));
            return $rs->withHeader('Content-Type', 'application/json');
        } else {
            throw new HttpBadRequestException($rq, "Prestation not found");
        }
    }
}   

?>