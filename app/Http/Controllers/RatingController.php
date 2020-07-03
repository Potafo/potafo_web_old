<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function ratings()
    {
        return view('rating.rating_list');
    }
}
