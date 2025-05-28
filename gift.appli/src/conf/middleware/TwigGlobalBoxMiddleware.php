<?php

namespace gift\appli\conf\middleware;
use gift\appli\core\application\usecases\BoxManagement;
use gift\appli\core\application\usecases\BoxManagementInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Views\Twig;

class TwigGlobalBoxMiddleware
{
    private Twig $twig;
    private BoxManagementInterface $boxManagement;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
        $this->boxManagement = new BoxManagement();
    }

    public function __invoke(Request $request, Handler $handler): ResponseInterface
    {
        if(ISSET($_SESSION['box'])){
            $box = $this->boxManagement->getBoxByIdSessionFormat($_SESSION['box']);
            $this->twig->getEnvironment()->addGlobal('box', $box);
        }
        return $handler->handle($request);
    }
}