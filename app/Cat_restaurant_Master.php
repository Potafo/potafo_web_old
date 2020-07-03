<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cat_restaurant_Master extends Model
{
    protected $table = 'cat_restaurants';
    protected $primaryKey = 'cr_id';
    public $timestamps =false;
}
