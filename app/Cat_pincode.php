<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cat_pincode extends Model
{
    protected $table = 'cat_city_pincodes';
    protected $primaryKey =['city_id','sl_no'];
    public $timestamps =false;
}
