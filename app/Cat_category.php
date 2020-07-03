<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cat_category extends Model
{
    protected $table = 'cat_category';
    protected $primaryKey = 'cc_id';
    public $timestamps =false;
}
