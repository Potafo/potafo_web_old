<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    public function time_bonus()
    {
        return $this->hasOne(TimeBonus::class, 'time_slot_id');
    }
}
