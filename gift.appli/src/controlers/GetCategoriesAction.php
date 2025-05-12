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

        $html = '
            <h1>Categories</h1>
            <ul>';

        foreach ($categories as $categorie) {
            $html .= '<li><a href="/giftbox/categorie/'.$categorie->id.'">' . htmlspecialchars($categorie->libelle) . '</a></li>';
        }

        $html .= '</ul>';

        // Write the HTML to the response body
        $response->getBody()->write($html);

        return $response;
    }
}