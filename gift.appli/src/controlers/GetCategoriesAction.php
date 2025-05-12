<?php
namespace gift\appli\controlers;

class GetCategoriesAction {
    public function __invoke($request, $response, array $args)
    {
        // Récupérer les catégories depuis le modèle
        try {
            $categories = \gift\appli\models\Categorie::all();
            if($categories->isEmpty()){
                throw new \Exception("Aucune catégories");
            }
        } catch (\Exception $e){
            $html = <<<HTML
            <h1>Erreur avec les catégories</h1>
            <a href="/giftbox/">Page d'accueil</a>
            HTML;

            $response->getBody()->write($html);
            return $response->withStatus(500);
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