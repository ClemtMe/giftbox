<?php
namespace gift\appli\models;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';
    protected $guarded = ['role'];
    public $timestamps = false;

    public function boxes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Box::class, 'createur_id');
    }
}