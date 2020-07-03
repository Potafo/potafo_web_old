<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationGroup extends Model
{
    protected $table = 'notification_group';
    protected $primaryKey = 'g_id';
    public $timestamps =false;
}