<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BannerList extends Model
{
    protected $table = "banner_list";
    protected $primarykey = "id";
    public $timestamps = false;
}
