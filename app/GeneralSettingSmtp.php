<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralSettingSmtp extends Model
{
    protected $table = "tbl_generalsettings_smtp";
    protected $primaryKey = 'id';
    public $timestamps = false;
}