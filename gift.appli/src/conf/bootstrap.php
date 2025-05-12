<?php
declare(strict_types=1);
session_start();

use gift\appli\utils\Eloquent;
use Slim\Factory\AppFactory;

Eloquent::init(__DIR__ . '/gift.db.conf.ini.dist');

$app = AppFactory::create();
$app->addRoutingMiddleware(true, false, false);
$app->setBasePath('/giftbox');

$app = (require_once __DIR__ . '/routes.php')($app);
return $app;