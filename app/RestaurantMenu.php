<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantMenu extends Model
{
    protected $table = "restaurant_menu";
    protected $primaryKey = ["m_rest_id","m_menu_id"];
    public $timestamps = false;
}
