<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowUps extends Model
{
    protected $table = "cat_order_followups";
    protected $primaryKey = 'id';
    public $timestamps = false;
}
