<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Helpers\Datasource;
use Helpers\Commonsource;
use DateTime;

class OrderHistoryController extends Controller
{
    public function order_history(Request $request) {
          $staffid = Session::get('staffid');
		  if(!$staffid){return redirect('');}
           $order_cat = DB::SELECT("SELECT `order_list_cat` FROM `internal_staffs` WHERE `id`='".$staffid."'");
           $cat_test='';
        foreach($order_cat as $valt){
           $newval=$valt->order_list_cat;
           if($newval == "potafo_mart")
                {
               $cat_test=" and category = 'Potafo Mart'";
                }else if($newval == "restaurant")
                {
                    $cat_test=" and category <> 'Potafo Mart'";
                }
           }
          $all_orders = DB::SELECT("SELECT `rest_id`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`customer_details`->>'$.mobile' as mobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time,payment_method FROM order_master o, restaurant_master r where o.`rest_id` = r.id and date(o.order_date)=date(now()) and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '".$staffid."') ".$cat_test."  order by o.order_date DESC");
//        $all_orders = DB::SELECT("SELECT `rest_id`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`customer_details`->>'$.mobile' as mobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time,payment_method FROM `order_master` WHERE date(order_date)=date(now()) order by order_date DESC");
          return view('order.order_history',compact('rows','filterarr','all_orders','order_cat'));
    }
     public function order_history_filter_tables(Request $request)
    {
             $staffid                =   $request['staffid'];
             $order_rest_filter      =   strtoupper($request['order_rest_filter']);
             $order_name_filter      =   strtoupper($request['order_name_filter']);
             $order_phone_filter     =   $request['order_phone_filter'];
             $order_status_filter    =   $request['order_status_filter'];
             $order_number_filter    =   $request['order_number_filter'];
             $flt_from               =   $request['flt_from'];
             $flt_to                 =   $request['flt_to'];
             $order_cat_filter    = $request['order_cat_filter'];
             $search = "";
             
             if($order_rest_filter != "")
             {
                 $search.= "and UPPER(rest_details->>'$.name') LIKE '".$order_rest_filter."%'";
             }
             
             if($order_name_filter != "")
             {
                 $search.= " and UPPER(customer_details->>'$.name') LIKE '".$order_name_filter."%'";
             }
             
             if($order_phone_filter != "")
             {
                 $search.= " and `customer_details`->>'$.mobile' LIKE '".$order_phone_filter."%'";
             }
             
             if($order_status_filter != "")
             {
                 $search.= " and `current_status` = '".$order_status_filter."'";
             }
             
             if($order_number_filter != "")
             {
                 $search.= " and `order_number` LIKE '".$order_number_filter."%'";
             }
            if($flt_from!='' && $flt_to !=''){
                    $search.=" and  DATE(order_date)  BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
            } 
            else if($flt_from!='' && $flt_to =='')
            {
                $search.=" and  DATE(order_date) = '".date('Y-m-d', strtotime(($flt_from)))."' ";
            }            
            else if($flt_from=='' && $flt_to !='')
            {
                $search.=" and  DATE(order_date)='".date('Y-m-d', strtotime(($flt_to)))."'";
            }
            else
            {
                 $search.=" and  DATE(order_date)=DATE(now()) ";
            } 
            if($order_cat_filter != "")
        {
            if($search != "")
            {
                if($order_cat_filter === "Potafo Mart")
                {
                    $search.= " and `category` = '".$order_cat_filter."'";
                }else
                {
                     $search.= " and `category` <> 'Potafo Mart'";
                }
            }else{
                if($order_cat_filter === "Potafo Mart")
                {
                    $search.= "`category` =  '".$order_cat_filter."'";
                }else
                {
                     $search.= "`category` <> 'Potafo Mart'";   
                }
            }
        }
           $all_orders = DB::SELECT("SELECT `rest_id`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,`customer_details`->>'$.mobile' as mobile,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`rest_details`->>'$.name' as rest_name,`current_status`,IFNULL(`status_details`->>'$.P',0) as time,IFNULL(`status_details`->>'$.D',0) as dlvrd_time,order_date FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '".$staffid."') $search  order by order_date DESC");
//         $all_orders = DB::SELECT("SELECT `rest_id`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,`customer_details`->>'$.mobile' as mobile,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time FROM `order_master` WHERE 1 $search  order by order_date DESC");
           $append     = "";
           $append_2   = "";
           
            $append .=  '<table id="example2" class="table table-bordered">';
            $append .=    '<thead>';
            $append .=    '<tr>';
            $append .=        '<th style="min-width:30px">Sl No</th>';
            $append .=        '<th style="min-width:80px">Order No</th>';
            $append .=        '<th style="min-width:90px">Date</th>';
            $append .=        '<th style="min-width:90px">Time</th>';
            $append .=        '<th style="min-width:140px">Resataurant/Shop</th>';
            $append .=        '<th style="min-width:140px">Customer Name</th>';
            $append .=        '<th style="min-width:140px">Staff Name</th>';
            $append .=        '<th style="min-width:140px">Staff Mobile</th>';
            $append .=        '<th style="min-width:70px">Ttl Time</th>';
            $append .=        '<th style="min-width:30px">View</th>';
            $append .=    '</tr>';
            $append .=    '</thead>';
            $append .=    '<tbody>';
                    if(count($all_orders)>0){
                    $i=0; 
                    foreach($all_orders as $orders){
                     $i++;
                        if(timediff($orders->time) == 'Y')
                        {
                           $stats = " delayed_order";
                        }
                        else
                        {
                            $stats = ' ';
                        }
                        if($orders->dlvrd_time) {
                            $time1 = new DateTime($orders->time);
                            $time2 = new DateTime($orders->dlvrd_time);
                            $t = $time2->diff($time1);
                            $minutes = ($t->format('%h') * 60) + ($t->format('%i'));
                            $interval = $t->format("%hh %im");
                            if ($interval[0] > 0) {
                                $interval = $interval;
                            } else {
                                $interval = substr($interval, 3, 3);
                            }
                            if ($minutes >= 40) {
                                $color = "style='color:red;'";
                            } else {
                                $color = "";
                            }
                        }
                    if($orders->current_status == 'P'){
            $append .=        '<tr role="row" class="new_order_1">';
            $append .=            '<td style="min-width:30px;">'.$i.'</td>';
            $append .=            '<td style="min-width:80px;"><strong style="color: #227b73">'.$orders->order_number.'</strong></td>';
            $append .=            '<td style="min-width:90px;">'.date('d-m-Y',strtotime($orders->order_date)).'</td>';
            $append .=            '<td style="min-width:90px;">'.$orders->time.'</td>';
            $append .=            '<td style="min-width:140px;"><strong style="color: #77541f">'.$orders->rest_name.'</strong></td>';
            $append .=            '<td style="min-width:140px;">'.$orders->name.'</td>';
            $append .=            '<td style="min-width:140px;"></td>';
            $append .=            '<td style="min-width:140px;"></td>';
            $append .=            '<td style="min-width:70px;">0</td>';
            $append .=            '<td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');"><i class="fa fa-cog"></i></a></td>';
            $append .=        '</tr>';
                    }else if($orders->current_status == 'C'){
            $append .=        '<tr role="row" class="near_delivery_1'.$stats.'">';
            $append .=            '<td style="min-width:30px;">'.$i.'</td>';
            $append .=            '<td style="min-width:80px;"><strong style="color: #227b73">'.$orders->order_number.'</strong></td>';
            $append .=            '<td style="min-width:90px;">'.date('d-m-Y',strtotime($orders->order_date)).'</td>';
            $append .=            '<td style="min-width:90px;">'.$orders->time.'</td>';
            $append .=            '<td style="min-width:140px;"><strong style="color: #77541f">'.$orders->rest_name.'</strong></td>';
            $append .=            '<td style="min-width:140px;">'.$orders->name.'</td>';
            $append .=            '<td style="min-width:140px;">'.$orders->staffname.'</td>';
            $append .=            '<td style="min-width:140px;">'.$orders->staffmobile.'</td>';
            $append .=            '<td style="min-width:70px;">0</td>';
            $append .=            '<td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');"><i class="fa fa-cog"></i></a></td>';
            $append .=        '</tr>';
                    }else if($orders->current_status == 'OP'){
            $append .=        '<tr role="row" class="new_pick_up'.$stats.'">';
            $append .=            '<td style="min-width:30px;">'.$i.'</td>';
            $append .=            '<td style="min-width:80px;"><strong style="color: #227b73">'.$orders->order_number.'</strong></td>';
            $append .=            '<td style="min-width:90px;">'.date('d-m-Y',strtotime($orders->order_date)).'</td>';
            $append .=            '<td style="min-width:90px;">'.$orders->time.'</td>';
            $append .=            '<td style="min-width:140px;"><strong style="color: #77541f">'.$orders->rest_name.'</strong></td>';
            $append .=            '<td style="min-width:140px;">'.$orders->name.'</td>';
            $append .=            '<td style="min-width:140px;">'.$orders->staffname.'</td>';
            $append .=            '<td style="min-width:140px;">'.$orders->staffmobile.'</td>';
            $append .=            '<td style="min-width:70px;">0</td>';
            $append .=            '<td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');"><i class="fa fa-cog"></i></a></td>';
            $append .=        '</tr>';
                    }else if($orders->current_status == 'D'){
            $append .=        '<tr role="row" class="new_deliverd">';
            $append .=            '<td style="min-width:30px;">'.$i.'</td>';
            $append .=            '<td style="min-width:80px;"><strong style="color: #227b73">'.$orders->order_number.'</strong></td>';
            $append .= '<td style="min-width:90px;">' . date('d-m-Y', strtotime($orders->order_date)) . '</td>';
            $append .=            '<td style="min-width:90px;" '.$color.'>'.$orders->time.'</td>';
            $append .= '<td style="min-width:140px;"><strong style="color: #77541f">' . $orders->rest_name . '</strong></td>';
            $append .=            '<td style="min-width:140px;">'.$orders->name.'</td>';
            $append .= '<td style="min-width:140px;">' . $orders->staffname . '</td>';
            $append .=            '<td style="min-width:140px;">'.$orders->staffmobile.'</td>';
             if($color == '') {
                $append .=            '<td style="min-width:70px;">'.$interval.'</td>';
            }
            else{
                $append .=            '<td style="min-width:70px;"><strong style="color:red;">'.$interval.'</strong></td>';
            }
            $append .=            '<td style="min-width:30px;" ><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');"><i class="fa fa-cog"></i></a></td>';
            $append .=        '</tr>';
                    }else if($orders->current_status == 'CA'){
            $append .=        '<tr role="row" class="new_cancelled">';
            $append .=            '<td style="min-width:30px;">'.$i.'</td>';
            $append .=            '<td style="min-width:80px;"><strong style="color: #227b73">'.$orders->order_number.'</strong></td>';
            $append .=            '<td style="min-width:90px;">'.date('d-m-Y',strtotime($orders->order_date)).'</td>';
            $append .=            '<td style="min-width:90px;">'.$orders->time.'</td>';
            $append .=            '<td style="min-width:140px;"><strong style="color: #77541f">'.$orders->rest_name.'</strong></td>';
            $append .=            '<td style="min-width:140px;">'.$orders->name.'</td>';
            $append .=            '<td style="min-width:140px;"></td>';
            $append .=            '<td style="min-width:140px;"></td>';
          $append .=            '<td style="min-width:70px;">0</td>';
            $append .=            '<td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');"><i class="fa fa-cog"></i></a></td>';
            $append .=        '</tr>';
                    }
                  }
                }
            $append .=    '</tbody>';
            $append .='</table>';
              
              return $append;
    }
  public function view_order_history_details(Request $request)
 {
     $decimal_point = Commonsource::generalsettings();
     $order_number = $request['order_number'];
     $rest_id     = $request['rest_id'];
     $details = DB::SELECT("SELECT IFNULL(om.coupon_details->>'$.coupon_amount',0) as coupon_amount,om.mode_of_entry,om.app_version,om.current_status,od.order_number,od.sl_no,od.rest_id,od.menu_id,od.menu_details->>'$.portion' as odportion,od.menu_details->>'$.menu_name' as odmenu,od.menu_details->>'$.category' as odcategory,od.menu_details->>'$.preference' as odpreference,od.menu_details->>'$.single_rate' as odsinglerate,od.qty as odqty,od.final_rate as odfinalrate,om.order_number,om.sub_total as omsubtotal,ROUND(om.final_total,$decimal_point) as omfinal_total,om.total_details as omtotal FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number = '$order_number' and od.rest_id ='$rest_id' ");
     $masterdetails = DB::SELECT("SELECT payment_method FROM order_master WHERE order_number='".$order_number."' ");
     $count = count($details);
     $total =  json_decode($details[0]->omtotal,true);
     $append     = "";
           
            $append .=  '<table id="example" class="timing_sel_popop_tbl">';
            $append .=    '<thead>';
            $append .=    '<tr>';
            $append .=        '<th style="width:30px"></th>';
            $append .=        '<th style="width:130px;">Items</th>';
            $append .=        '<th style="width:50px">Qty</th>';
            $append .=        '<th style="width:70px">Rate</th>';
            $append .=        '<th style="width:70px">Amount</th>';
            $append .=    '</tr>';
            $append .=    '</thead>';
            $append .=    '<tbody>';
                    if(count($details)>0){
                    $i=0; 
                   
                    foreach($details as $orders){
                     $i++; 
                     if(round($orders->odfinalrate,$decimal_point) ==0 || count($details)==1){
                         $class='not-active';
                     }
                     else{
                         $class='';
                     }

            $append .=        '<tr>';
            $append .=            '<td style="width:30px;">'.$i.'</td>';
            $append .=            '<td style="width:130px;">'.$orders->odmenu.','.$orders->odportion.'</td>';
            $append .=            '<td style="width:50px";">'.$orders->odqty.'</td>';
            $append .=            '<td style="width:70px";">'.round($orders->odsinglerate,$decimal_point).'</td>';
            $append .=            '<td style="width:70px";">'.round($orders->odfinalrate,$decimal_point).'</td>';
            
            $append .=        '</tr>';
            $append .=        '<tr>';
            $append .=            '<div class="restaurant_more_detail_text">';
                              if(($orders->odpreference== 'null') || ($orders->odpreference== ''))
                              {
            $append .=              '<td style="text-align:left;width:10px;">Category: '.$orders->odcategory.'</td>';
            $append .=              '<td style="text-align:left;width:10px;">Preference: </td>';
            
                              }
                              else 
                              {
            $append .=              '<td style="text-align:left;width:10px;">Category: '.$orders->odcategory.'</td>';                      
            $append .=              '<td style="text-align:left;width:10px;">Preference: '.$orders->odpreference.'</td>';
            
                              }
            $append .=            '</div>';
            $append .=        '</tr>';
                  }
                }
            
            $append .=    '</tbody>';
            $append .='</table>';
            $append .=  '<table class="timing_sel_popop_tbl tfooter_ttl_order">';
            $append .=    '<tfoot>';
            $append .=    '<tr>';
            $append .=        '<td style="width:40px"></td>';
            $append .=        '<td style="width:160px">Items '.$count.'</td>';
            $append .=        '<td style="width:50px"></td>';
            $append .=        '<td style="width:70px;text-align:right">Total</td>';
            $append .=        '<td style="width:70px">'.round($orders->omsubtotal,$decimal_point).'</td>';
            $append .=        '<td style="width:60px"></td>';
            $append .=    '</tr>';
            $append .=    '</tfoot>';
            $append .=    '</table>';
            $append .=  '<table class="timing_sel_popop_tbl tbl_totalcharge">';
            $append .=    ' <tr>';
            $append .=    '<tr>';
            $append .=    '<td>Pck Charge: <strong>'.round($total['packing_charge'],$decimal_point).'</strong></td>';
            $append .=    '<td>Dlv Charge: <strong>'.round($total['delivery_charge'],$decimal_point).'</strong></td>';
            $append .=    '<td>Discount: <strong>'.round($total['discount_amount'],$decimal_point).'</strong></td>';
            if($orders->coupon_amount!=0){
                  $append .=    '<td>Coupon: <strong>'.round($orders->coupon_amount,$decimal_point).'</strong></td>';
            }
           $append .=    '<td><strong>Final: </strong><strong style="font-size:15px;">'.round($details[0]->omfinal_total,0).'</strong></td>';
            $append .=    '</tr>';
            $append .=    '</table>';
            $append .=    '</tr>';
            $append .=    '</tfoot>';
            $append .=    '</table>';
            return $append;
    
     
 }
    //ALTER TABLE `general_settings` ADD `rest_conf_alert_sec` INT(11) NULL DEFAULT NULL AFTER `cod_call_confirm_limit`;

}
