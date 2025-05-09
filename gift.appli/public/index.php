<?php
declare(strict_types=1);

session_start();

use gift\appli\utils\Eloquent;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../src/vendor/autoload.php';
Eloquent::init(__DIR__.'/../src/conf/gift.db.conf.ini.dist');

$app = AppFactory::create();
$app->addRoutingMiddleware(true, false, false);
$app->setBasePath('/giftbox');

$app = (require_once __DIR__ . '/../src/conf/routes.php')($app);

$app->run();