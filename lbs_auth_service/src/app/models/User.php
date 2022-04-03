<?php

namespace lbs\auth\app\models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'user';
    public $incrementing = false;
    protected $keyType = 'string';
}
