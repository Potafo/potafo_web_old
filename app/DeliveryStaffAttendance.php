<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryStaffAttendance extends Model
{
    protected  $table      = "delivery_staff_attendence";
    protected $primaryKey  = ['staff_id','entry_date','slno'];
    public $timestamps     = false;
}
