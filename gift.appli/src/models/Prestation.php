<?php
namespace gift\appli\models;
use Illuminate\Database\Eloquent\Model;

class Prestation extends Model{
    protected $table = 'prestation';
    protected $primaryKey = 'id';
    protected $fillable = ['nom', 'description', 'url', 'unit', 'tarif', 'img'];
    public $timestamps = false;

    public function Categorie(){
        return $this->belongsTo(Categorie::class, 'id_categorie');
    }
}