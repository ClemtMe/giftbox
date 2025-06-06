<?php

namespace gift\appli\webui\actions;

use gift\appli\core\application\exceptions\ExceptionInterne;
use gift\appli\core\application\usecases\BoxManagement;
use gift\appli\core\application\usecases\BoxManagementInterface;
use gift\appli\webui\exceptions\ProviderAuthentificationException;
use gift\appli\webui\providers\AuthProviderInterface;
use gift\appli\webui\providers\CsrfTokenProviderInterface;
use gift\appli\webui\providers\SessionAuthProvider;
use gift\appli\webui\providers\SessionCsrfTokenProvider;

class GetUserBoxesAction
{
    private string $template;
    private BoxManagementInterface $boxManagement;
    private AuthProviderInterface $authProvider;
    private CsrfTokenProviderInterface $csrfTokenProvider;

    public function __construct()
    {
        $this->template = 'pages/ViewUserBoxes.twig';
        $this->boxManagement = new BoxManagement();
        $this->authProvider = new SessionAuthProvider();
        $this->csrfTokenProvider = new SessionCsrfTokenProvider();
    }

    public function __invoke($request, $response, $args)
    {
        try {
            $userId = $this->authProvider->getSignedInUser()['id'];
        } catch (ProviderAuthentificationException $e) {
            throw new \Slim\Exception\HttpUnauthorizedException($request, $e->getMessage());
        }

        try {
            $boxes = $this->boxManagement->getBoxesByUserId($userId);
        } catch (ExceptionInterne $e) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $e->getMessage());
        }

        $token = $this->csrfTokenProvider->generateCsrf();

        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, $this->template, [
            'boxes' => $boxes,
            'csrf_token' => $token,
        ]);
    }
}