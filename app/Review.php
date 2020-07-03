<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'restaurant_reviews';
    protected $primaryKey = 'id';
    public $timestamps =false;
}
