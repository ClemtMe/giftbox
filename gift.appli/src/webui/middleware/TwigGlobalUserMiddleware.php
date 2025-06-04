<?php

namespace gift\appli\webui\middleware;
use gift\appli\webui\exceptions\ProviderAuthentificationException;
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
        } catch (ProviderAuthentificationException $e) {
            return $handler->handle($request);
        }
        $this->twig->getEnvironment()->addGlobal('userSession', $user);
        return $handler->handle($request);
    }
}