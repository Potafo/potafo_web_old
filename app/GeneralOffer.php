<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralOffer extends Model
{
    protected  $table      = "general_offers";
    protected $primaryKey  = 'id';
    public $timestamps     = false;
}
