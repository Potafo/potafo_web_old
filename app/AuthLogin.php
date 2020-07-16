<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthLogin extends Model
{
    protected $table = 'auth_login';
    protected $primaryKey = 'id';
    public $timestamps =false;
}
