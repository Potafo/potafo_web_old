<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $table = 'designation_master';
    protected $primaryKey = 'id';
    public $timestamps =false;
}
