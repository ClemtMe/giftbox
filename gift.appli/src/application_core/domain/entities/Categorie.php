<?php
namespace gift\appli\application_core\domain\entities;
use Illuminate\Database\Eloquent\Model;
class Categorie extends Model{
    protected $table = 'categorie';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function prestations(): \Illuminate\Database\Eloquent\Relations\HasMany{
        return $this->hasMany(Prestation::class, 'cat_id');
    }
}


