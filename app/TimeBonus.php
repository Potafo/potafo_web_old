<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeBonus extends Model
{
    protected $table = 'time_bonus';
    public function timeslot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
