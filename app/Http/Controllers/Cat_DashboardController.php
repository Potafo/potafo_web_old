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

class Cat_DashboardController extends Controller
{
    public function catering_dashboard(Request $request)
    {
       $staffid = Session::get('staffid');
       $assign_staff = DB::SELECT("SELECT a.staff_id,b.first_name,b.last_name,b.mobile FROM delivery_staff_attendence a LEFT JOIN internal_staffs b on a.staff_id=b.id  WHERE a.out_time is NULL and  a.staff_id in (select DISTINCT(id) from internal_staffs s,internal_staffs_area a WHERE s.`id` = a.staff_id and a.area_id in ( SELECT  a1.area_id from users u1, internal_staffs s1, internal_staffs_area a1 where u1.staffid =s1.id and s1.id = a1.staff_id and a1.staff_id = '$staffid'))");
       $i=0;
       $all = array();
       $time_zone   = 'Asia/Kolkata';
       date_default_timezone_set($time_zone);
       $current_date = date('Y-m-d');
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
          $pending_orders         = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and DATE(o.order_date)= current_date() and current_status IN('OP','C')");
          $completed_orders       = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and DATE(o.order_date)= current_date() and current_status = 'D'");
          $total_orders           = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and DATE(o.order_date)= current_date() and current_status != 'T'");
          $cancelled_orders       = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and DATE(o.order_date)= current_date() and current_status = 'CA'");
//          $pending_orders       = DB::SELECT("SELECT count(*) as total FROM order_master WHERE DATE(order_date)= current_date() and current_status IN('OP','C')");
        /*  $completed_orders     = DB::SELECT("SELECT count(*) as total FROM order_master WHERE DATE(order_date)= current_date() and current_status = 'D'");
          $total_orders           = DB::SELECT("SELECT count(*) as total FROM order_master WHERE DATE(order_date)= current_date() and current_status != 'T'");
          $cancelled_orders       = DB::SELECT("SELECT count(*) as total FROM order_master WHERE DATE(order_date)= current_date() and current_status = 'CA' ");
         */
          return view('catering/dashboard', compact('all','pending_orders','pending_orders','completed_orders','total_orders','cancelled_orders','staffid'));
   }
   
   function total_summary_orders(Request $request)
   {    
        $time_zone              = 'Asia/Kolkata';
        date_default_timezone_set($time_zone);
        $current_date = date('Y-m-d');
        $staffid     = $request['staffid'];
        $delivery_pending     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and  current_status IN('OP','C') and date(order_date)='".$current_date."'");
        $unassgined_orders     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and current_status  = 'P' and date(order_date)='".$current_date."'");
        $total_orders     = DB::SELECT("SELECT count(*) as total FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and current_status != 'T' and date(order_date)='".$current_date."'");
//      $delivery_pending     = DB::SELECT("SELECT count(*) as total FROM order_master WHERE current_status IN('OP','C') and date(order_date)='".$current_date."'");
       /* $unassgined_orders    = DB::SELECT("SELECT count(*) as total FROM order_master WHERE current_status = 'P' and date(order_date)='".$current_date."'");
        $total_orders         = DB::SELECT("SELECT count(*) as total FROM order_master WHERE current_status != 'T' and date(order_date)='".$current_date."'"); 
        */
        $total_del_pen      = $delivery_pending[0]->total;
        $total_unasgnd      = $unassgined_orders[0]->total;
        $total_orders_det   = $total_orders[0]->total;
        $all = ['total_orders_det'=>$total_orders_det,'total_unasgnd'=>$total_unasgnd,'total_del_pen'=>$total_del_pen];
        return $all;
       //return response::json(compact('total_orders_det','total_unasgnd','total_del_pen'));;
   }
}
