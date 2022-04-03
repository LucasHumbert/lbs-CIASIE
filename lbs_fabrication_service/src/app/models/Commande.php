<?php
namespace lbs\fabrication\app\models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $table = 'commande';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $hidden = ['updated_at', 'mail', 'montant', 'token'];
}