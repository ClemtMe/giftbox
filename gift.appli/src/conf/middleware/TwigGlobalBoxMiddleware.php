<?php

namespace gift\appli\conf\middleware;
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
            $this->twig->getEnvironment()->addGlobal('box', $_SESSION['box']);
        }
        return $handler->handle($request);
    }
}