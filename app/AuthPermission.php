<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthPermission extends Model
{
    protected $table = 'auth_permission';
    protected $primaryKey = 'id';
    public $timestamps =false;
}
