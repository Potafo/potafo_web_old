<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaxMaster extends Model
{
    protected $table = "tax_master";
    protected $primarykey = ["restaurant_id","t_slno"];
    public $timestamps = false;
}
