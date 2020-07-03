<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\City;
use Response;
use Session;

class AreaController extends Controller
{
    //view area page
    public function view_area(Request $request)
    {
		$staffid = Session::get('staffid');
		if(!$staffid){return redirect('');}
        $filterarr = array();
        $rows=$this->citylist();
        return view('city.city',compact('rows','filterarr'));
    }

     // add/ edit area
    public function add_area(Request $request)
    {
        $type =$request['type'];
        if($type == 'insert')
        {
            $arealist = City::where('name',$request['area'])
                        ->get();
            if(count($arealist)>0)
            {

                $msg = 'already exist';
                $rows=$this->citylist();
                return response::json(compact('msg','rows'));
            }
            else
            {
                $cty= new City();
                $cty->name = ucwords(strtolower($request['area']));
                $cty->save();

                $msg = 'success';
                $rows=$this->citylist();
                return response::json(compact('msg','rows'));
            }

            return redirect('area');
        }

        else if($type == 'update')
        {

            $ctys = City::where('name',$request['area'])
                ->where('id','!=',$request['userid'])
                ->first();
            if(count($ctys)>0)
            {

                $msg = 'exist';
                $rows=$this->citylist();
                return response::json(compact('msg','rows'));
            }
            else {
                City::where('id', $request['userid'])->update(
                    ['name' => ucwords(strtolower($request['area'])),
                        'active' => $request['status']
                    ]);

                $msg = 'done';
                $rows=$this->citylist();
                return response::json(compact('msg','rows'));
            }
            return redirect('area');
        }
    }

    public function citylist()
    {
        $rows=City::select('id','name','active')
            ->orderBy('id','desc')
            ->get();
        return $rows;
    }
}
