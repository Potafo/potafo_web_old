<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    protected $fillable = ['staff_id', 'checkin_serial', 'checkin_time', 'checkin_spot', 'checkout_time', 'checkout_by', 'checkout_done_by_user'];
}
