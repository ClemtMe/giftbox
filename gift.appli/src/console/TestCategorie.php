<?php
namespace app\src\console;
use gift\appli\models\Categorie;
use Illuminate\Database\Eloquent\ModelNotFoundException;
try {
    $s = Categorie::all();
    print ($s);
}catch (ModelNotFoundException $e){
    print $e->getMessage();
}