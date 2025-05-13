<?php
declare(strict_types=1);
session_start();

use gift\appli\utils\Eloquent;
use Slim\Factory\AppFactory;

Eloquent::init(__DIR__ . '/gift.db.conf.ini.dist');

$app = AppFactory::create();
$app->addRoutingMiddleware(true, false, false);
$app->addErrorMiddleware(true, false, false);
$app->setBasePath('/giftbox');
$twig = \Slim\Views\Twig::create(__DIR__ . '/../views', ['cache' => false, 'auto_reload' => true , 'strict_variables' => true]);
$app->add(\Slim\Views\TwigMiddleware::create($app, $twig));

$app = (require_once __DIR__ . '/routes.php')($app);
return $app;