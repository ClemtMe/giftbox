<?php
namespace gift\appli\api\actions;

use gift\appli\core\application\usecases\Catalogue;
use gift\appli\core\application\usecases\CatalogueInterface;
use gift\appli\core\domain\exceptions\EntityNotFoundException;
use Slim\Exception\HttpNotFoundException;

class CategoriesAction {

    private CatalogueInterface $catalogue;
    public function __construct() {
        $this->catalogue = new Catalogue();
    }

    public function __invoke($request, $response, $args){
        $id = $args['id'] ?? null;

        if ($id == null) {
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

        } else{
            //On tente de récupérer les catégories depuis le modèle
            try {
                $categorie = $this->catalogue->getCategorieById($id);
            } catch (EntityNotFoundException $e) {
                throw new HttpNotFoundException($request, $e->getMessage());
            }

            //Transformation des données
            $data = [ 'type' => 'ressource',
                'categories' => $categorie ];
            $response->getBody()->write(json_encode($data));
        }

        //renvoie des données
        return
            $response->withHeader('Content-Type','application/json')
                ->withStatus(200);
    }
}