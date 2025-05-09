<?php
namespace gift\appli\controlers;

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
            $rs->getBody()->write('Prestation not found');
            return $rs->withStatus(404);
        }
    }
}   

?>