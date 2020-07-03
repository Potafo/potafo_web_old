<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    protected $table = "order_details";
    protected $primaryKey = ["order_number","sl_no"];
    public $timestamps = false;
}
