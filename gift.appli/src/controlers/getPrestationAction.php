<?php
namespace gift\appli\controlers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use gift\appli\models\Prestation;

class getPrestationAction
{
    public function __invoke(Request $rq, Response $rs, array $args) : Response {
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