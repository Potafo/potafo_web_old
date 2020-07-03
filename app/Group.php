<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'restaurant_group';
    protected $primaryKey = 'id';
    public $timestamps =false;
}
