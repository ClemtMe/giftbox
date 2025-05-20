<?php
namespace gift\appli\webui\actions;

use gift\appli\core\domain\entities\Categorie;

class GetCategoriesAction {
    public function __invoke($request, $response, array $args)
    {
        // Récupérer les catégories depuis le modèle
        try {
            $categories = Categorie::all();
            if($categories->isEmpty()){
                throw new \Exception("Aucunes catégories trouvées");
            }
        } catch (\Exception $e){
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'pages/ViewCategories.twig', [
            'categories' => $categories
        ]);
    }
}