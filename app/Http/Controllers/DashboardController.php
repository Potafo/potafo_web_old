<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
use Json;
use Session;

class DashboardController extends Controller
{
    public function index_function(Request $request)
    {
       $staffid = Session::get('staffid');
        $desgnationdetails = DB::SELECT("SELECT designation  FROM `users` left Join internal_staffs on  users.staffid =  internal_staffs.id where staffid ='".$staffid."'");
       /* $designation = $desgnationdetails[0]->designation;
        if(!in_array(strtoupper($designation),["MANAGER", "SUPER_ADMIN","ADMIN"]))
        {
                 return redirect('manage_credits');
        }*/
       if($staffid!="")
       {
       $assign_staff = DB::SELECT("SELECT a.staff_id,b.first_name,b.last_name,b.mobile FROM delivery_staff_attendence a LEFT JOIN internal_staffs b on a.staff_id=b.id  WHERE a.out_time is NULL and  a.staff_id in (select DISTINCT(id) from internal_staffs s,internal_staffs_area a WHERE s.`id` = a.staff_id and a.area_id in ( SELECT  a1.area_id from users u1, internal_staffs s1, internal_staffs_area a1 where u1.staffid =s1.id and s1.id = a1.staff_id and a1.staff_id = '$staffid'))");
       $i=0;
       $all = array();
       $time_zone   = 'Asia/Kolkata';
       date_default_timezone_set($time_zone);
       $current_date = date('Y-m-d');
       $pending_orders  ="";
        $completed_orders ="";
        $total_orders    ="";
        $cancelled_orders ="";
       foreach($assign_staff as $staff)
       {
           $name    = $staff->first_name;
           $lastname    = $staff->last_name;
           $mobile  = $staff->mobile;
           $pending_details = DB::SELECT("SELECT count(*) as total FROM order_master WHERE delivery_assigned_to='".$staff->staff_id."' AND date(order_date)='".$current_date."' AND current_status IN('OP','C')");
           foreach($pending_details as $val){
                $pending = $val->total;
           }
           $all_details = DB::SELECT("SELECT count(*) as total FROM order_master WHERE delivery_assigned_to='".$staff->staff_id."' AND date(order_date)='".$current_date."' AND current_status IN('D','OP','C')");
           foreach($all_details as $val){
                $all_order = $val->total;
           }
           $all[$i] = ['name'=>$name.' '.$lastname,'mobile'=>$mobile,'pending'=>$pending,'all_order'=>$all_order];
             $i++;
           }
           $order_cat = DB::SELECT("SELECT `order_list_cat` FROM `internal_staffs` WHERE `id`='".$staffid."'");
           
           foreach($order_cat as $valt){
               $newval=$valt->order_list_cat;
           if($newval == "potafo_mart")
           {
               $pending_orders         = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category = 'Potafo Mart'  and current_status IN('OP','C','P') ");//and DATE(o.order_date)= current_date()
                $completed_orders       = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category = 'Potafo Mart' and DATE(o.order_date)= current_date() and current_status = 'D' ");
                $total_orders           = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category = 'Potafo Mart' and DATE(o.order_date)= current_date() and current_status != 'T' ");
                $cancelled_orders       = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category = 'Potafo Mart' and DATE(o.order_date)= current_date() and current_status = 'CA' ");
               
           }else if($newval == "restaurant")
           {
               $pending_orders         = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category <> 'Potafo Mart'  and current_status IN('OP','C','P') ");//and DATE(o.order_date)= current_date()
                $completed_orders       = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category <> 'Potafo Mart' and DATE(o.order_date)= current_date() and current_status = 'D' ");
                $total_orders           = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category <> 'Potafo Mart' and DATE(o.order_date)= current_date() and current_status != 'T' ");
                $cancelled_orders       = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category <> 'Potafo Mart'  and DATE(o.order_date)= current_date() and current_status = 'CA' ");
               
           }else
           {
          $pending_orders         = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid')  and current_status IN('OP','C','P')");//and DATE(o.order_date)= current_date()
          $completed_orders       = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and DATE(o.order_date)= current_date() and current_status = 'D'");
          $total_orders           = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and DATE(o.order_date)= current_date() and current_status != 'T'");
          $cancelled_orders       = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and DATE(o.order_date)= current_date() and current_status = 'CA'");
//          $pending_orders       = DB::SELECT("SELECT count(*) as total FROM order_master WHERE DATE(order_date)= current_date() and current_status IN('OP','C')");
        /*  $completed_orders     = DB::SELECT("SELECT count(*) as total FROM order_master WHERE DATE(order_date)= current_date() and current_status = 'D'");
          $total_orders           = DB::SELECT("SELECT count(*) as total FROM order_master WHERE DATE(order_date)= current_date() and current_status != 'T'");
          $cancelled_orders       = DB::SELECT("SELECT count(*) as total FROM order_master WHERE DATE(order_date)= current_date() and current_status = 'CA' ");
         */
           }
           }
          return view('index', compact('all','pending_orders','pending_orders','completed_orders','total_orders','cancelled_orders','staffid'));
       }else
       {
           return view('userlogin.login');
       }
   }
   
   function total_summary_orders(Request $request)
   {    
        $time_zone              = 'Asia/Kolkata';
        date_default_timezone_set($time_zone);
        $current_date = date('Y-m-d');
        $staffid     = $request['staffid'];
         $order_cat = DB::SELECT("SELECT `order_list_cat` FROM `internal_staffs` WHERE `id`='".$staffid."'");
          $delivery_pending  ="";
        $unassgined_orders ="";
        $total_orders    =""; 
           foreach($order_cat as $valt){
               $newval=$valt->order_list_cat;
           if($newval == "potafo_mart")
           {
        $delivery_pending     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category = 'Potafo Mart' and  current_status IN('OP','C')  ");//and date(order_date)='".$current_date."'
        $unassgined_orders     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category = 'Potafo Mart' and current_status  = 'P'  ");//and date(order_date)='".$current_date."'
        $total_orders     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category = 'Potafo Mart' and current_status != 'T'  and date(order_date)='".$current_date."'");
//      $delivery_pending     = DB::SELECT("SELECT count(*) as total FROM order_master WHERE current_status IN('OP','C') and date(order_date)='".$current_date."'");
       /* $unassgined_orders    = DB::SELECT("SELECT count(*) as total FROM order_master WHERE current_status = 'P' and date(order_date)='".$current_date."'");
        $total_orders         = DB::SELECT("SELECT count(*) as total FROM order_master WHERE current_status != 'T' and date(order_date)='".$current_date."'"); 
        */
           }  elseif($newval == "restaurant")
           {
        $delivery_pending     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category <> 'Potafo Mart' and  current_status IN('OP','C')  ");//and date(order_date)='".$current_date."'
        $unassgined_orders     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category <> 'Potafo Mart' and current_status  = 'P'  ");//and date(order_date)='".$current_date."'
        $total_orders     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid' ) and category <> 'Potafo Mart' and current_status != 'T'  and date(order_date)='".$current_date."'");
//      $delivery_pending     = DB::SELECT("SELECT count(*) as total FROM order_master WHERE current_status IN('OP','C') and date(order_date)='".$current_date."'");
       /* $unassgined_orders    = DB::SELECT("SELECT count(*) as total FROM order_master WHERE current_status = 'P' and date(order_date)='".$current_date."'");
        $total_orders         = DB::SELECT("SELECT count(*) as total FROM order_master WHERE current_status != 'T' and date(order_date)='".$current_date."'"); 
        */
           }  else                
           {
                $delivery_pending     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and  current_status IN('OP','C') ");//and date(order_date)='".$current_date."'
        $unassgined_orders     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and current_status  = 'P'");// and date(order_date)='".$current_date."'
        $total_orders     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and current_status != 'T' and date(order_date)='".$current_date."'");
           }
           }
        $total_del_pen      = $delivery_pending[0]->total;
        $total_unasgnd      = $unassgined_orders[0]->total;
        $total_orders_det   = $total_orders[0]->total;
        $all = ['total_orders_det'=>$total_orders_det,'total_unasgnd'=>$total_unasgnd,'total_del_pen'=>$total_del_pen];
        return $all;
       //return response::json(compact('total_orders_det','total_unasgnd','total_del_pen'));;
   }
}
