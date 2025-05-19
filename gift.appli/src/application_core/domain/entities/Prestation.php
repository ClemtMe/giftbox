<?php
namespace gift\appli\application_core\domain\entities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Prestation extends Model{
    protected $table = 'prestation';
    protected $primaryKey = 'id';
    public $incrementing =false;
    public $keyType = 'string';
    protected $fillable = ['id', 'libelle', 'description', 'url', 'unite', 'tarif'];
    public $timestamps = false;

    public function categorie(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Categorie::class, 'cat_id');
    }

    public function coffrets(): BelongsToMany{
        return $this->belongsToMany(CoffretType::class, 'coffret2presta', 'presta_id', 'coffret_id');
    }

    public function boxes(): BelongsToMany{
        return $this->belongsToMany(Box::class, 'box2presta', 'presta_id', 'box_id')
                    ->withPivot('quantite');
    }
}