<?php

namespace gift\appli\webui\middleware;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\webui\providers\AuthProviderInterface;
use gift\appli\webui\providers\SessionAuthProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Views\Twig;

class TwigGlobalUserMiddleware
{
    private Twig $twig;
    private AuthProviderInterface $authProvider;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
        $this->authProvider = new SessionAuthProvider();
    }

    public function __invoke(Request $request, Handler $handler): ResponseInterface
    {
        try {
            $user = $this->authProvider->getSignedInUser();
            if($user !== []) {
                $this->twig->getEnvironment()->addGlobal('userSession', $user);
            }
        } catch (ExceptionDatabase $e) {}
        return $handler->handle($request);
    }
}