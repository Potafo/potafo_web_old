<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantOffer extends Model
{
    protected  $table      = "restaurant_offers";
    protected $primaryKey  = ['rest_id','sl_no'];
    public $timestamps     = false;
}
