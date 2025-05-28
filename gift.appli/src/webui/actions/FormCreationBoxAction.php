<?php
namespace gift\appli\webui\actions;
use gift\appli\webui\providers\CsrfTokenProvider;

class FormCreationBoxAction{

    private string $template;
    public function __construct(){
        $this->template = 'pages/ViewFormCreationBox.twig';
    }
    public function __invoke($request, $response, $args){
        $tokken = CsrfTokenProvider::generate();
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, $this->template, [
            'token' => $tokken
        ]);
    }
}