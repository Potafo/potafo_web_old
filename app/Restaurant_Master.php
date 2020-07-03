<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant_Master extends Model
{
    protected $table = 'restaurant_master';
    protected $primaryKey = 'id';
    public $timestamps =false;
}
