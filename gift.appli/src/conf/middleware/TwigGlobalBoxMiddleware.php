<?php

namespace gift\appli\conf\middleware;
use gift\appli\core\domain\entities\Box;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Views\Twig;

class TwigGlobalBoxMiddleware
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Handler $handler): ResponseInterface
    {
        if(ISSET($_SESSION['box'])){
            $box = Box::find($_SESSION['box']);
            $box = [
                'id' => $box->id,
                'libelle' => $box->libelle,
                'description' => $box->description,
                'montant' => $box->montant,
                'prestations' => $box->prestations->map(function ($presta) {
                    return [
                        'libelle' => $presta->libelle,
                        'description' => $presta->description,
                        'tarif' => $presta->tarif,
                        'unite' => $presta->unite,
                        'quantite' => $presta->pivot->quantite,
                    ];
                })->toArray(),
            ];
            $this->twig->getEnvironment()->addGlobal('box', $box);
        }
        return $handler->handle($request);
    }
}