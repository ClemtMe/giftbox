<?php
use Psr\Http\Message\ResponseInterface;

class GetCategoriesAction extends AbstractAction{
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface
    {
        // Récupérer les catégories depuis le modèle
        $categories = \gift\appli\models\Categorie::all();

        $html = '';

        // Convertir les catégories en tableau associatif
        $categoriesArray = [];
        foreach ($categories as $categorie) {
            $categoriesArray[] = [
                'id' => $categorie->id,
                'libelle' => $categorie->libelle,
                'description' => $categorie->description,
            ];
        }

        // Retourner la réponse JSON
        return $this->json($response, $categoriesArray);
    }
}