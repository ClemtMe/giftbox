<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../src/vendor/autoload.php';
$app = require_once __DIR__ . '/../src/conf/bootstrap.php';
$app->run();