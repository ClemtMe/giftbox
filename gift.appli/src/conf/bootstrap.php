<?php
declare(strict_types=1);
session_start();

use gift\appli\conf\middleware\TwigGlobalBoxMiddleware;
use gift\appli\infrastructure\Eloquent;
use Slim\Factory\AppFactory;

Eloquent::init(__DIR__ . '/gift.db.conf.ini.dist');

$app = AppFactory::create();
$app->addRoutingMiddleware(true, false, false);
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, false, false);
$app->setBasePath('/giftbox');

$twig = \Slim\Views\Twig::create(__DIR__ . '/../webui/views', ['cache' => false, 'auto_reload' => true , 'strict_variables' => true]);
$twig->getEnvironment()
    ->addGlobal('globals', [
        'css_dir'=> 'static/css',
        'img_dir'=> 'static/img',
    ]
);

$app->add(new TwigGlobalBoxMiddleware($twig));
$app->add(\Slim\Views\TwigMiddleware::create($app, $twig));

$app = (require_once __DIR__ . '/routes.php')($app);
return $app;