<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use App\BannerList;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Session;

class NotificationController extends Controller
{
    public function notification_view(Request $request)
    {
		$staffid = Session::get('staffid');
        if(!$staffid){return redirect('');}
        return view('notifications.notification');
    }

    //Notification delete
    public function notification_delete($id)
    {
        $is_exist= DB::SELECT("SELECT a.id FROM `notifications` a WHERE a.id = '".$id."'  ORDER BY a.entry_date DESC");
        if(count($is_exist)!=0)
        {

          DB::DELETE("delete from notifications where id ='".$id."'");
            $msg = 'success';
        }
        else
        {
            $msg = 'error';

        }
        return response::json(['msg' => $msg]);
    }
   
}
