<?php

namespace App\Http\Controllers;

use App\AreaSpot;
use Illuminate\Http\Request;

class SpotController extends Controller
{
    public function spots($area_id)
    {
        $spots = AreaSpot::all()->where('city_id', $area_id);

        echo 'fgbfd';
    }
}
