<?php

namespace App\Http\Controllers;

use App\GeneralSetting;
use App\RestaurantOffer;
use Helpers\Commonsource;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Image;
use Session;
use App\UserMaster;
use Response;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
use App\Group;
use App\Restaurant_Master;
class MartController extends Controller
{
    

    public function mart_login(Request $request)
    {
        $username      = $request['username'];
        $pswd       = trim($request['password']);
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($pswd, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        $list = DB::SELECT('select u.id,u.token,u.restaurant_id,u.ftoken,u.role from users u left join restaurant_master r on r.id = u.restaurant_id
         where name = "'.trim($username).'" and password =  "'.trim($password).'" and active = "Y" and r.category = "Potafo Mart"');
        if(count($list) >0)
        {  
            $msg =  'Login Success';
            $rest_details =DB::SELECT('SELECT JSON_UNQUOTE(name_tagline->"$.name") as restaurant_name,address FROM restaurant_master where id ="'.$list[0]->restaurant_id.'"');
            return response::json(['msg' => $msg,'user_id'=>$list[0]->id,'ftoken'=>(string)$list[0]->ftoken,'role'=>$list[0]->role,'name'=>$rest_details[0]->restaurant_name,'address' => $rest_details[0]->address,'token'=>$list[0]->token,'restaurant_id'=>$list[0]->restaurant_id]);
        }
        else
        {
            $msg =  'Invalid Login';
            return response::json(['msg' => $msg]);
        }
    }

    
    public function mart_force_update(Request $request)
    {
   
		$setting = GeneralSetting::where('id','1')->select('mart_app_currnt_version','mart_app_custom_msg','mart_force_update')->first();
        if(count($setting)>0)
        {
            if(strtoupper($setting['mart_force_update']) == 'Y')
            {
                $msg = 'Exist';
                
                return response::json(['msg' => $msg,'version' => $setting['mart_app_currnt_version'],'message' => $setting['mart_app_custom_msg'],'clear_data' => 'N']);
            }
            else if(strtoupper($setting['mart_force_update']) == 'N')
            {
                $msg = 'no_force_update';
                return response::json(['msg' => $msg,'version' => $setting['mart_app_currnt_version'],'message' => $setting['mart_app_custom_msg'],'clear_data' => 'N']);
            }
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }
    

}

