<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    protected $table = "payment_methods";
    protected $primaryKey = 'id';
    public $timestamps = false;
}
