<?php
declare(strict_types=1);

session_start();

use Slim\Factory\AppFactory;

require_once __DIR__ . '/../src/vendor/autoload.php';

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->setBasePath('/giftbox');

$app = (require_once __DIR__ . '/../src/conf/routes.php')($app);

$app->run();