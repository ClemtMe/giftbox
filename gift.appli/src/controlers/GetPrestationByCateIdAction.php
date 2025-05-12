<?php
namespace gift\appli\controlers;

class GetPrestationByCateIdAction{
    public function __invoke($request, $response, array $args)
    {
        // Retrieve the category ID from the request arguments
        $categoryId = $args['id'];

        // Fetch prestations associated with the category from the model
        $prestations = \gift\appli\models\Prestation::where('categorie_id', $categoryId)->get();

        if ($prestations->isNotEmpty()) {
            // Create a plain text representation of the prestations
            $text = "Prestations for Category ID: {$categoryId}\n";
            foreach ($prestations as $prestation) {
                $text .= "- {$prestation->libelle}: {$prestation->description}\n";
            }

            // Write the plain text to the response body
            $response->getBody()->write($text);
        } else {
            // If no prestations are found, return a 404 error with a plain text message
            $response->getBody()->write("No prestations found for Category ID: {$categoryId}");
            return $response->withStatus(404);
        }

        return $response->withHeader('Content-Type', 'text/plain');
    }
}