<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cat_tax extends Model
{
    protected $table = "cat_rest_tax_master";
    protected $primarykey = ["crt_rest_id","crt_slno"];
    public $timestamps = false;
}
