<?php
namespace gift\appli\webui\actions;
use gift\appli\webui\providers\SessionCsrfTokenProvider;
use webui\providers\CsrfTokenProviderInterface;

class FormCreationBoxAction{

    private string $template;
    private CsrfTokenProviderInterface $tokenProvider;
    public function __construct(){
        $this->template = 'pages/ViewFormCreationBox.twig';
        $this->tokenProvider = new SessionCsrfTokenProvider();
    }
    public function __invoke($request, $response, $args){
        $token = $this->tokenProvider->generateCsrf();
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, $this->template, [
            'csrf_token' => $token
        ]);
    }
}