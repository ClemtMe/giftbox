<?php
namespace gift\appli\controlers;

class GetCategoriesAction {
    public function __invoke($request, $response, array $args)
    {
        // Récupérer les catégories depuis le modèle
        try {
            $categories = \gift\appli\models\Categorie::all();
            if($categories->isEmpty()){
                throw new \Exception("Aucunes catégories trouvées");
            }
        } catch (\Exception $e){
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'ViewCategories.twig', [
            'categories' => $categories
        ]);
    }
}