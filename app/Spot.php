<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model
{
    protected $table = 'area_spot';
    protected $primaryKey = 'id';
    public $timestamps =false;
}
