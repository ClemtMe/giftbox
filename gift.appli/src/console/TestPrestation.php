<?php
namespace gift\appli\console;


use gift\appli\models\Prestation;
use gift\appli\utils\Eloquent;
use Illuminate\Database\Eloquent\ModelNotFoundException;

require_once __DIR__.'/../vendor/autoload.php';
Eloquent::init(__DIR__.'/../conf/gift.db.conf.ini.dist');
try {
    $res = Prestation::where('id', '=', $argv[1])->firstOrFail();
    print ($res);
}catch (ModelNotFoundException $e){
    print $e->getMessage();
}