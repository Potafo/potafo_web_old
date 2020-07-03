<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = "sub_category";
    protected $primarykey = ["restaurant_id","slno"];
    public $timestamps = false;
}
