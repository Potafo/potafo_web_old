<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InternalStaffArea extends Model
{
    protected $table = 'internal_staffs_area';
    protected $primaryKey = 'staff_id';
    public $timestamps =false;
}
