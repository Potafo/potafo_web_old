<?php

namespace App\Http\Controllers;

use DB;
use App\City;
use App\Spot;
use Response;
use Session;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;

class SpotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($latitude = Null, $longitude = Null)
    {
        if($latitude != Null && $longitude != Null){
            // $spots = Spot::all()->where('status', '1')->where('deleted_on', NULL);
            $dist = 1500;
            $spots = DB::select("select name, maplink, address, latitude, longitude, 3956 * 2 * 
            ASIN(SQRT( POWER(SIN(($latitude - latitude)*pi()/180/2),2)
            +COS($latitude*pi()/180 )*COS(latitude*pi()/180)
            *POWER(SIN(($longitude-longitude)*pi()/180/2),2))) 
            as distance FROM area_spot WHERE 
            longitude between ($longitude-$dist/cos(radians($latitude))*69) 
            and ($longitude+$dist/cos(radians($latitude))*69)
            and latitude between ($latitude-($dist/69)) 
            and ($latitude+($dist/69))
            and status = 1
            and deleted_on is NULL
            having distance < $dist ORDER BY distance limit 5
            ");
        } else {
            $spots = $spots = Spot::all()->where('status', '1')->where('deleted_on', NULL);
        }                

        return response(
                [
                    'status' => 'success',
                    'response_code' => 200,
                    'spots' => $spots
                ]
            );
    }

    /**
     * Check if the delivery boy is inside the boundry
     *
     * @return \Illuminate\Http\Response
     */
    public function check_starting_spot($latitude, $longitude)
    {
        if($latitude != NULL && $longitude != NULL) {
            $dist = 500;
            $within_radius = DB::select("select id as spot_id, name, maplink, radius, latitude, longitude, 3956 * 2 * 
            ASIN(SQRT( POWER(SIN(($latitude - latitude)*pi()/180/2),2)
            +COS($latitude*pi()/180 )*COS(latitude*pi()/180)
            *POWER(SIN(($longitude-longitude)*pi()/180/2),2))) 
            as distance FROM area_spot WHERE 
            longitude between ($longitude-radius/cos(radians($latitude))*69) 
            and ($longitude+radius/cos(radians($latitude))*69) 
            and latitude between ($latitude-(radius/69)) 
            and ($latitude+(radius/69)) 
            having distance < radius ORDER BY distance limit 100");
            
            if($within_radius){
                return response(
                    [
                        'status'=> 'success',
                        'response_code' => 200,
                        'spot_id' => $within_radius[0]->spot_id,
                        'message' => 'Start granted..!'
                    ]
                );
            } else {
                return response(
                    [
                        'status'=> 'fail',
                        'response_code' => 200,
                        'message' => 'You\'re not in the circle.'
                    ]
                );
            }
        } else {
            return response(
                [
                    'status' => 'fail',
                    'response_code' => 200,
                    'errors' => [
                        'Latitude or longitude is missing..!'
                    ]
                ]
            );
        }
    }
    public function view_spot(Request $request)
    {
        $staffid = Session::get('staffid');
        if(!$staffid){return redirect('');}
        $cityid=$request['id'];
        $rows= Spot::all()->where('city_id', $request['id'])->where('deleted_on', NULL);
        return view('city.spot',compact('rows','cityid'));//,compact('rows')
    }
    public function add_spot(Request $request)
    {
        $type =$request['type'];
        $spot_map_link =$request['spot_map_link'];
        $spot_address =$request['spot_address'];
        $spot_longitude =$request['spot_longitude'];
        $spot_latitude =$request['spot_latitude'];
        $spot_name =$request['spot_name'];
        $spot_status =$request['spot_status'];
        $cityid =$request['city_id'];
        
        if($type == 'insert')
        {
            $arealist = Spot::where('name',$request['spot_name'])
                        ->get();
            if(count($arealist)>0)
            {

                $msg = 'already exist';
               // $rows=DB::SELECT("Select * from area_spot where city_id='".$request['city_id']."' and deleted_on IS NULL");
               $rows= Spot::all()->where('city_id', $request['city_id'])->where('deleted_on', NULL);
                return response::json(compact('msg','rows','cityid'));
            }
            else
            {//spot_map_link spot_address spot_longitude spot_latitude spot_name spot_status
                //`id`, `city_id`, `name`, `latitude`, `longitude`, `address`, `maplink`, `status`
                $cty= new Spot();
                $cty->maplink = (($request['spot_map_link']));
                $cty->address = (($request['spot_address']));
                $cty->longitude = (($request['spot_longitude']));
                $cty->latitude = (($request['spot_latitude']));
                $cty->name = (strtolower($request['spot_name']));
                //$cty->status = (strtolower($request['spot_status']));
                $cty->city_id = (($request['city_id']));
                $cty->save();

                $msg = 'success';
                //$rows=DB::SELECT("Select * from area_spot where city_id='".$request['city_id']."' and deleted_on IS NULL");
                $rows= Spot::all()->where('city_id', $request['city_id'])->where('deleted_on', NULL);
                return response::json(compact('msg','rows','cityid'));
            }

            return redirect('spot');
        }

        else if($type == 'update')
        { $spotid =$request['spot_id'];
            $spotradius =$request['spot_radius'];

            $ctys = Spot::where('id','!=',$spotid)
            ->where('latitude',$request['spot_latitude'])
            ->where('longitude',$request['spot_longitude'])
                ->first();
            if(count($ctys)>0)
            {

               $msg = 'exist';
               //$rows=DB::SELECT("Select * from area_spot where city_id='".$request['city_id']."' and deleted_on IS NULL");
               $rows= Spot::all()->where('city_id', $request['city_id'])->where('deleted_on', NULL);
               return response::json(compact('msg','rows','cityid'));
            }
            else {
                //spot_map_link spot_address spot_longitude spot_latitude spot_name spot_status
                //`id`, `city_id`, `name`, `latitude`, `longitude`, `address`, `maplink`, `status`
                Spot::where('city_id', $request['city_id'])
                ->where('id', $request['spot_id'])
                ->update(
                    ['name' => (strtolower($request['spot_name'])),
                        'latitude' => $request['spot_latitude'],
                        'longitude' => $request['spot_longitude'],
                        'address' => $request['spot_address'],
                        'status' => ($request['spot_status']),
                        'maplink' => $request['spot_map_link'],
                        'radius' => $spotradius
                    ]);

                $msg = 'done';
                //$rows=DB::SELECT("Select * from area_spot where city_id='".$request['city_id']."' and deleted_on IS NULL");
                $rows= Spot::all()->where('city_id', $request['city_id'])->where('deleted_on', NULL);
                return response::json(compact('msg','rows','cityid'));
            }
            return redirect('area');
        }
    }
    public function spot_status_change(Request $request)
    {
        $updatestatus=1;
        $ctys = Spot::where('id',$request['spot'])
                ->where('city_id',$request['city'])
                ->first();
        if($ctys->status==1)
       {
           $updatestatus= 0;
       }else  {
            $updatestatus= 1;
        }
       Spot::where('city_id', $request['city'])
                ->where('id', $request['spot'])
                ->update(
                    ['status' => ($updatestatus)
                    ]);
        $msg="ok";
        $cityid=$request['city'];
        $rows= Spot::all()->where('city_id', $request['city'])->where('deleted_on', NULL);
        // $rows=DB::SELECT("Select * from area_spot where city_id='".$request['city']."' and deleted_on IS NULL");
        return response::json(compact('msg','rows','cityid'));
    }
    public function spot_delete($spotid,$cityid)
    {
        DB::SELECT("UPDATE area_spot SET deleted_on=now() where city_id='".$cityid."' and id='".$spotid."'");
        //$rows=DB::SELECT("Select * from area_spot where city_id='".$cityid."' and deleted_on IS NULL");
        $rows= Spot::all()->where('city_id', $cityid)->where('deleted_on', NULL);
        $msg="deleted";
        return response::json(compact('msg','rows','cityid'));
    }
    public function spotlist()
    {
        $rows=Spot::select(`id`, `city_id`, `name`, `latitude`, `longitude`, `address`, `maplink`, `status`)
            ->orderBy('id','desc')
            ->get();
        return $rows;
    }

}
