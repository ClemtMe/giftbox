<?php
namespace gift\appli\console;


use gift\appli\models\Categorie;
use gift\appli\utils\Eloquent;
require_once __DIR__.'/../vendor/autoload.php';
Eloquent::init(__DIR__.'/../conf/gift.db.conf.ini.dist');

$list_res = Categorie::get();

foreach ($list_res as $res) {
    printf($res->libelle . "\n");
}