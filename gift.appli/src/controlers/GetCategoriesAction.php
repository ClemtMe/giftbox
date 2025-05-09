<?php
namespace gift\appli\controlers;
use Psr\Http\Message\ResponseInterface;


class GetCategoriesAction{
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface
    {
        // Récupérer les catégories depuis le modèle
        $categories = \gift\appli\models\Categorie::all();

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Categories</title>
        </head>
        <body>
            <h1>Categories</h1>
            <ul>';

        foreach ($categories as $categorie) {
            $html .= '<li><strong>' . htmlspecialchars($categorie->libelle) . '</strong>: '
                . htmlspecialchars($categorie->description) . '</li>';
        }

        $html .= '</ul>
        </body>
        </html>';

        // Write the HTML to the response body
        $response->getBody()->write($html);

        return $response->withHeader('Content-Type', 'text/html');
    }
}