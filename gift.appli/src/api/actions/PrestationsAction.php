<?php
namespace gift\appli\api\actions;

use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\application\usecases\CatalogueInterface;
use gift\appli\core\domain\exceptions\EntityNotFoundException;
use Slim\Exception\HttpNotFoundException;

class PrestationsAction {

    private CatalogueInterface $catalogue;
    public function __construct() {
        $this->catalogue = new Catalogue();
    }

    public function __invoke($request, $response, $args){
        
        $id = $args['id'] ?? null;

        if ($id == null) {
            try {
                $prestations = $this->catalogue->getPrestations();
            } catch (EntityNotFoundException $e) {
                throw new HttpNotFoundException($request, $e->getMessage());
            }

            //Transformation des données
            $data = [ 'type' => 'collection',
                'count' => count($prestations),
                'prestations' => $prestations ];
            $response->getBody()->write(json_encode($data));

        } else{
            //On tente de récupérer les catégories depuis le modèle
            try {
                $prestation = $this->catalogue->getPrestationById($id);
            } catch (EntityNotFoundException $e) {
                throw new HttpNotFoundException($request, $e->getMessage());
            }

            //Transformation des données
            $data = [ 'type' => 'ressource',
                'prestation' => $prestation ];
            $response->getBody()->write(json_encode($data));
        }

        //renvoie des données
        return
            $response->withHeader('Content-Type','application/json')
                ->withStatus(200);
    }
}