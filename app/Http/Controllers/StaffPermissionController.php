<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
use App\Staff;
use App\DeliveryStaffAttendance;
use App\OrderDetails;
use App\OrderMaster;
use App\UserMaster;
use DateTime;
use DateTimeZone;
use Session;
class StaffPermissionController extends Controller
{
   
    public function view_staffpermission($id)
    {
        $filterarr = array();
        $encr_method = Datasource::encr_method();
        $detail = DB::SELECT("select name,password FROM users where staffid = '$id'");
       foreach($detail as $data)
        {
            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $key1 = hash('sha256', $rowkey[0]->explore);
            $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
            $key = hash('sha256', $key1);
            $iv = substr(hash('sha256', $iv1), 0, 16);
            $password = openssl_decrypt(base64_decode($data->password), $encr_method, $key, 0, $iv);
       }
       $rows = DB::SELECT("select id,name FROM users where staffid = '$id'");
//       return $rows;
       if(isset($rows[0]->id) && $rows != '')
            {
                $userid = $rows[0]->id;
            }
            else{
                $userid = 0;
            }
            
           $main_module = DB::SELECT("SELECT DISTINCT(mm.module_name),(select count(`sub_module`) from module_master ms where ms.module_name = mm.module_name) as count from module_master mm LEFT JOIN users_modules um ON mm.m_id=um.module_id where mm.module_for='C' and um.user_id = '$userid'"); 
           $module_list = DB::SELECT("SELECT mm.module_name,mm.m_id,mm.sub_module as sub_module,mm.page_link,um.active from module_master mm LEFT JOIN users_modules um ON mm.m_id=um.module_id where mm.module_for='C' and um.user_id = '$userid'");

           return view('staff.staff_permission',compact('filterarr','id','rows','password','module_list','main_module','userid'));
    }
    public function savepassword(Request $request)
    {
        $name =$request['name'];
        $psw =$request['password'];
        $s_id =$request['s_id'];
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($psw, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i:s');
         $exist = DB::SELECT("select staffid from users where staffid = '$s_id'");
         if(count($exist)>0)
         {
             DB::SELECT("UPDATE users SET name = '$name',password='$password',updated_at='$datetime' WHERE staffid='$s_id' ");
             $msg = 'update';
         }
         else
         {
             DB::INSERT("INSERT INTO `users`(`login_group`, `name`, `password`, `staffid`,`created_at`) VALUES ('A','" . trim($name) . "','".$password."','".$s_id."','".$datetime."')");
             $msg = 'insert';
         }
         return response::json(compact('msg'));
    }
    
    public function savepermission(Request $request)
    {   
        
        $userid =$request['userid'];
        $s_id =$request['id'];
        $m_id =$request['m_id'];
//        return $m_id;
        $staffexist = DB::SELECT("select staffid from users where staffid ='$s_id'");
//        $userid = $staffexist[0]->id;
        if(count($staffexist)>0)
        {
            
         $exist = DB::SELECT("select active from users_modules where user_id = '$userid' and module_id = '$m_id' and active = 'Y'");
            if(count($exist)>0)
            {
               DB::SELECT("update users_modules SET active = 'N' where user_id = '$userid' and module_id = '$m_id'");
            }
            else
            {
               DB::SELECT("update users_modules SET active = 'Y' where user_id = '$userid' and module_id = '$m_id'");
            }

        }
        else 
        {
            $msg = 'No Staff Login';
        }
        return response::json(compact('msg'));
    }
    
}