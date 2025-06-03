<?php
namespace gift\appli\api;

use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\application\usecases\CatalogueInterface;
use gift\appli\core\domain\exceptions\EntityNotFoundException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class Categories {

    private CatalogueInterface $catalogue;
    public function __construct() {
        $this->catalogue = new Catalogue();
    }

    public function __invoke($request, $response, $args){
        //On tente de récupérer les catégories depuis le modèle
        try {
            $categories = $this->catalogue->getCategories();
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }

        //Transformation des données
        $data = [ 'type' => 'collection',
            'count' => count($categories),
            'categories' => $categories ];
        $response->getBody()->write(json_encode($data));

        //renvoie des données
        return
            $response->withHeader('Content-Type','application/json')
                ->withStatus(200);
    }
}