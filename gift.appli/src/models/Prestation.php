<?php
namespace gift\appli\models;
use Illuminate\Database\Eloquent\Model;

class Prestation extends Model{
    protected $table = 'prestation';
    protected $primaryKey = 'id';
    public $incrementing =false;
    public $keyType = 'string';
    protected $fillable = ['id', 'libelle', 'description', 'url', 'unite', 'tarif'];
    public $timestamps = false;

    public function Categorie(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Categorie::class, 'id_categorie');
    }
}