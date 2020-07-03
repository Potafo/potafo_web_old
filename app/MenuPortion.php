<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuPortion extends Model
{
    protected $table = 'menu_portion';
    protected $primaryKey = 'mp_id';
    public $timestamps =false;
}
