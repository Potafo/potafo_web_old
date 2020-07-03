<?php

namespace App\Http\Controllers;

use App\CustomerList;
use App\CategoryOrderStatus;
use App\FollowUps;
use App\CategoryExtraCharges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Session;
use Helpers\Datasource;
use Response;
use DateTime;
use DateTimeZone;
class CateringOrderController extends Controller
{
    public function manage_cateringorder(Request $request)
    {
        $staffid = Session::get('staffid');
        $all_orders = DB::SELECT("SELECT `rest_id`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`customer_details`->>'$.mobile' as mobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time,payment_method FROM order_master o, restaurant_master r where o.`rest_id` = r.id and date(o.order_date)=date(now()) and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '" . $staffid . "') order by o.order_date DESC");
//        $all_orders = DB::SELECT("SELECT `rest_id`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`customer_details`->>'$.mobile' as mobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time,payment_method FROM `order_master` WHERE date(order_date)=date(now()) order by order_date DESC");
        return view('catering.catering_orders.manage_cateringorder', compact('rows', 'filterarr', 'all_orders'));
    }

    public function cateringorder_history_filter_tables(Request $request)
    {
        $staffid                =   $request['staffid'];
        $order_rest_filter      =   strtoupper($request['order_rest_filter']);
        $order_name_filter      =   strtoupper($request['order_name_filter']);
        $order_status_filter    =   $request['order_status_filter'];
        $order_number_filter    =   $request['order_number_filter'];
        $flt_from               =   $request['flt_from'];
        $flt_to                 =   $request['flt_to'];
        $search = "";

        if($order_rest_filter != "")
        {
            $search.= "and UPPER(cr_name	) LIKE '".$order_rest_filter."%'";
        }

        if($order_status_filter != "")
        {
            $search.= " and `com_order_status` = '".$order_status_filter."'";
        }
        else{
            $search.= " and `com_order_status` IN ('P','C')";

        }

        if($order_number_filter != "")
        {
            $search.= " and `com_order_id` LIKE '".$order_number_filter."%'";
        }
        if($flt_from!='' && $flt_to !=''){
            $search.=" and  DATE(com_order_date)  BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
        }
        else if($flt_from!='' && $flt_to =='')
        {
            $search.=" and  DATE(com_order_date) = '".date('Y-m-d', strtotime(($flt_from)))."' ";
        }
        else if($flt_from=='' && $flt_to !='')
        {
            $search.=" and  DATE(com_order_date)='".date('Y-m-d', strtotime(($flt_to)))."'";
        }
        else
        {
            $search.=" and  DATE(com_order_date)=DATE(now()) ";
        }
        $all_orders = DB::SELECT("SELECT `com_order_rest_id`,`com_order_id`,`com_order_status`,`com_customer_id`,`com_menu_type_name`,`com_scheduled_date`,`com_final_rate`,cr_name,com_order_date FROM cat_order_master o, cat_restaurants r where o.`com_order_rest_id` = r.cr_id and r.cr_city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '".$staffid."') $search  order by com_order_date DESC");
        $append     = "";
        $append_2   = "";

        $append .=  '<table id="example2" class="table table-bordered">';
        $append .=    '<thead>';
        $append .=    '<tr>';
        $append .=        '<th style="min-width:30px">Sl No</th>';
        $append .=        '<th style="min-width:80px">Order No</th>';
        $append .=        '<th style="min-width:90px">Date</th>';
        $append .=        '<th style="min-width:90px">Rest Name</th>';
        $append .=        '<th style="min-width:140px">Customer Name </th>';
        $append .=        '<th style="min-width:140px">Customer No</th>';
        $append .=        '<th style="min-width:140px">Final Total</th>';
        $append .=        '<th style="min-width:140px">Menu Type Name</th>';
        $append .=        '<th style="min-width:70px">Schedule Date</th>';
        $append .=        '<th style="min-width:30px">View</th>';
        $append .=    '</tr>';
        $append .=    '</thead>';
        $append .=    '<tbody>';
        if(count($all_orders)>0){
            $i=0;
            foreach($all_orders as $orders){
                if($order_name_filter == "") {
                    $searchcondtion="";
                }
                else
                {
                    $searchcondtion=" and lower(CONCAT(name,' ',lname)) LIKE '%".strtolower($order_name_filter)."%'";
                }
                $customers = DB::SELECT("SELECT name,lname from customer_list where id ='".$orders->com_customer_id."' $searchcondtion ");
                if(count($customers)>0) {
                    $i++;
                    $append .= '<tr role="row" class="new_order_1">';
                    $append .= '<td style="min-width:30px;">' . $i . '</td>';
                    $append .= '<td style="min-width:80px;"><strong style="color: #227b73">' . $orders->com_order_id . '</strong></td>';
                    $append .= '<td style="min-width:90px;">' . date('d-m-Y', strtotime($orders->com_order_date)) . '</td>';
                    $append .= '<td style="min-width:90px;">' . $orders->cr_name . '</td>';
                    $append .= '<td style="min-width:140px;"><strong style="color: #77541f">' . $customers[0]->name . ' ' . $customers[0]->lname . '</strong></td>';
                    $append .= '<td style="min-width:140px;">' . $orders->com_customer_id . '</td>';
                    $append .= '<td style="min-width:140px;">' . $orders->com_final_rate . '</td>';
                    $append .= '<td style="min-width:140px;">' . $orders->com_menu_type_name . '</td>';
                    $append .= '<td style="min-width:70px;">' . $orders->com_scheduled_date . '</td>';
                    $append .= '<td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_details(\'' . $orders->com_order_id . '\');"><i class="fa fa-cog"></i></a></td>';
                    $append .= '</tr>';
                }
            }
        }
        $append .=    '</tbody>';
        $append .='</table>';

        return $append;

    }

    public function catering_details($id)
    {
        $FollowUpsdetail = FollowUps::where('order_number',$id)->select('datetime','status','comment')->get();
        $orders = DB::SELECT("SELECT `com_order_rest_id`,`com_order_id`,`com_final_rate`,`com_single_rate`,com_rate_details->>'$.tax_rate' as tax_rate,`com_pax`,`com_scheduled_date`,`com_scheduled_time`,`cr_address`,`com_delivery_location`,`com_pincode`,`com_order_status`,`com_customer_id`,`com_menu_type_name`,`com_scheduled_date`,`com_final_rate`,cr_name,com_order_date,(select city from  cat_city where id =o.com_city_id)  as cityname,`com_reg_number`,(select name from  customer_list where id =o.com_customer_id)  as customername,(select mobile_contact from  customer_list where id =o.com_customer_id)  as customermobile FROM cat_order_master o left join cat_restaurants r on o.`com_order_rest_id` = r.cr_id   where o.`com_order_id` = '".$id."'  order by com_order_date DESC");
        $details = DB::SELECT('SELECT cod_menu_name,cod_menu_cat_name,cod_menu_details,cod_diet FROM cat_order_details WHERE cod_order_id = "'.$id.'"');
        $statusid = DB::SELECT('select id from cat_order_status  where code = "'.$orders[0]->com_order_status.'"');
        if(count($statusid)>0)
        {
           $start = $statusid[0]->id -1;
        }
        else{
            $start = 0;
        }
        $CategoryOrderStatus = DB::SELECT('SELECT status,code FROM cat_order_status order by id asc limit '.$start.',4');
        $extradetail = CategoryExtraCharges::where('order_no',$id)->select('attribute_name','order_no','final_rate','slno')->get();
        $extrasum = CategoryExtraCharges::where('order_no',$id)->selectRaw('sum(final_rate)as total')->get();
        return view('catering.catering_details',compact('orders','details','CategoryOrderStatus','FollowUpsdetail','extradetail','extrasum'));
    }

    public function submitcomment(Request $request)
    {
           $timezone = 'ASIA/KOLKATA';
           $date = new DateTime('now', new DateTimeZone($timezone));
           $datetime = $date->format('Y-m-d H:i:s');
           $post = $request->all();
           $FollowUps = new FollowUps();
           $FollowUps->datetime = $datetime;
           $FollowUps->status = $post['status'];
           $FollowUps->comment = $post['comment'];
           $FollowUps->notify_customer = $post['checkvalue'];
           $FollowUps->order_number = $post['order_no'];
           $FollowUps->save();
           DB::SELECT('update cat_order_master set com_order_status="'.strtoupper($post['status']).'" where com_order_id="'.$post['order_no'].'"');
           $append     = "";
           $FollowUpsdetail = FollowUps::where('order_number',$post['order_no'])->select('datetime','status','comment')->get();
           if(count($FollowUpsdetail)>0) {
           foreach($FollowUpsdetail as $item=>$value) {
               if($value->status == 'P')
               {
                 $statuses = 'Order Placed';
               }
               else if($value->status == 'C')
               {
                   $statuses = 'Confirmed';
               }
               else if($value->status == 'D')
               {
                   $statuses = 'Delivered';
               }
               else if($value->status == 'CA')
               {
                   $statuses = 'Cancelled';
               }
               $append .= '<tr>';
               $append .= '<td>'.$value->datetime.'</td>';
               $append .= '<td>'.$value->comment.'</td>';
               $append .= '<td>'.$statuses.'</td>';
               $append .= '</tr>';
           }
        }
        return $append;
    }

    public function submitextracharge(Request $request)
    {
           $post = $request->all();
           $chargedetail = CategoryExtraCharges::where('order_no',$post['order_no'])->where('attribute_name',$post['name'])
                           ->get();
           if(count($chargedetail)<=0) {
            $CategoryExtraCharges = new CategoryExtraCharges();
            $CategoryExtraCharges->order_no = $post['order_no'];
            $CategoryExtraCharges->attribute_name = $post['name'];
            $CategoryExtraCharges->final_rate = $post['fnalrate'];
            $CategoryExtraCharges->save();
            }
           $append     = "";
           $detail = CategoryExtraCharges::where('order_no',$post['order_no'])->select('attribute_name','order_no','final_rate','slno')->get();
           $extrasum = CategoryExtraCharges::where('order_no',$post['order_no'])->selectRaw('sum(final_rate) as total')->get();
           if(count($detail)>0) {
           foreach($detail as $item=>$value) {
               $append .= '<tr>';
               $append .= '<td>'.$value->attribute_name.'</td>';
               $append .= '<td>'.number_format($value->final_rate,2).'</td>';
               $append .= '<td><a onclick="deleteextracharge(\''.$value->order_no.'\',\''.$value->slno.'\')" class="btn button_table"><i class="fa fa-trash"></i></a></td>';
               $append .= '</tr>';
           }
        }
        return ['result'=>$append,'sum'=>$extrasum[0]['total']];
    }

    public function deleteextracharge(Request $request)
    {
        DB::select('delete from cat_order_extra_charges where order_no="'.$request['order_no'].'" and slno ="'.$request['slno'].'"');
        $append    = "";
        $detail   = CategoryExtraCharges::where('order_no',$request['order_no'])->select('attribute_name','order_no','final_rate','slno')->get();
        $extrasum = CategoryExtraCharges::where('order_no',$request['order_no'])->selectRaw('sum(final_rate) as total')->get();
        if(count($detail)>0)
        {
            foreach($detail as $item=>$value) {
                $append .= '<tr>';
                $append .= '<td>'.title_case($value->attribute_name).'</td>';
                $append .= '<td>'.number_format($value->final_rate,2).'</td>';
                $append .= '<td><a onclick="deleteextracharge(\''.$value->order_no.'\',\''.$value->slno.'\')" class="btn button_table"><i class="fa fa-trash"></i></a></td>';
                $append .= '</tr>';
            }
        }
        return ['result'=>$append,'sum'=>$extrasum[0]['total']];
    }

    //ALTER TABLE `cat_order_followups` ADD `notify_customer` CHAR(1) NOT NULL DEFAULT 'N' AFTER `comment`;

}
