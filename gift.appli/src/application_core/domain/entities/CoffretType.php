<?php
namespace gift\appli\application_core\domain\entities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CoffretType extends Model {
    protected $table = 'coffret_type';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function prestations(): BelongsToMany {
        return $this->belongsToMany(Prestation::class, 'coffret2presta', 'coffret_id', 'presta_id');
    }

    public function theme(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Theme::class, 'theme_id');
    }
}