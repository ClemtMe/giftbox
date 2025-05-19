<?php
namespace gift\appli\webui\actions;

use gift\appli\application_core\domain\entities\CoffretType;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class GetCoffretsTypeAction{
    public function __invoke($request, $response, $args)
    {
        try {
            $coffretsType = CoffretType::all();
            if($coffretsType->isEmpty()){
                throw new \Exception("Aucunes catégories trouvées");
            }
        } catch (\Exception $e){
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'pages/ViewCoffretsType.twig', [
            'coffrets_type' => $coffretsType
        ]);
    }
}