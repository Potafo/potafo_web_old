<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use DatePeriod;
use DateTime;
use DateTimeZone;
use App\GeneralSetting;
use DateInterval;
use Response;
class StaffReportController extends Controller
{
    public function view_staff_report()
    {
        $staffid = Session::get('staffid');
		 if(!$staffid){return redirect('');}
        $reports_all  ='';
        $staffs       = DB::SELECT("select concat_WS(' ',`first_name`,`last_name`) as name,`id` from internal_staffs s,internal_staffs_area a WHERE s.`id` = a.staff_id and a.area_id in ( SELECT  a1.area_id from users u1, internal_staffs s1, internal_staffs_area a1 where u1.staffid =s1.id and s1.id = a1.staff_id and a1.staff_id = '$staffid') and `designation`='Delivery staff' group by s.id order by name");
         $order_cat = DB::SELECT("SELECT `order_list_cat` FROM `internal_staffs` WHERE `id`='".$staffid."'");
        foreach($order_cat as $valt){
           $newval=$valt->order_list_cat;
           if($newval == "potafo_mart")
                { 
               $reports_all  = DB::SELECT("SELECT * FROM `report_master` WHERE `category`='staff' and `report_name` NOT IN ('Staff_change_report','Staff_salary_report') ");
                }else 
                {
                $reports_all  = DB::SELECT("SELECT * FROM `report_master` WHERE `category`='staff'");
                }
           }
       // $reports_all  = DB::SELECT("SELECT * FROM `report_master` WHERE `category`='staff'"); 
        $g_settings   = DB::SELECT("SELECT `decimal_digit` FROM `general_settings`");
        $num_format   = $g_settings[0]->decimal_digit; 
        $filter_query = array();
        $i=0;
        $all_array = array();
        
        $asnd_stf = DB::SELECT("SELECT i.id,date(om.`order_date`) as date,om.`delivery_assigned_details`->>'$.name' as name,om.`delivery_assigned_to` FROM `order_master` as om LEFT JOIN `internal_staffs` as i ON om.`delivery_assigned_to`= i.id WHERE om.`current_status` NOT IN('T','CA','P') and date(`order_date`) = CURRENT_DATE() group by name,date,om.delivery_assigned_to");
        foreach ($asnd_stf as $value) {
                    $staff_id     = $value->id;
                    if($staff_id!='') {
                        $filter_query[$i] = DB::SELECT("SELECT `order_number`,date(`order_date`) as date,`delivery_assigned_details`->>'$.name' as stf_name,`delivery_assigned_details`->>'$.phone' as stf_phone,`sub_total` as amount,`total_details`->'$.packing_charge' as pck_chrg,`total_details`->'$.delivery_charge' as del_chrg,`final_total`,`status_details`->>'$.OP' as picked,`status_details`->>'$.C' as cnfrm,`status_details`->>'$.D' as delivery,`delivery_assigned_details`->'$.review' as review,`delivery_assigned_details`->'$.star_rate' as rating,`customer_details`->>'$.name' as cus_name,`customer_details`->>'$.mobile' as cus_mobile FROM `order_master` WHERE delivery_assigned_to = '$staff_id' and `current_status` NOT IN('T','CA','P') and date(`order_date`) = CURRENT_DATE()");
                        $i++;
                    }

                    
        }
        return view('reports.staff.deliver_staff_report',compact('staffs','reports_all','filter_query','staffs','asnd_stf','num_format'));
    }
    
    
    public function filter_staff_reports(Request $request)
    {
        return response('{
            "msg": "Exist",
            "report_data": []
        }');
        $timezone = 'ASIA/KOLKATA';
        $staffid   =   $request['staff_id'];
        $reports_name   =   $request['reports_name'];
        $replaced = str_replace(' ', '_', $reports_name);
        $string = "";
        $catload='';
        $append = '';
        $g_settings   = DB::SELECT("SELECT `decimal_digit` FROM `general_settings`");
        $num_format   = $g_settings[0]->decimal_digit; 
        $order_cat_filter   =   $request['order_cat_filter'];
        if($reports_name == 'Delivery_staff_report')
        {
            $staffs         =   $request['staff'];
            $paymode        = $request['payment_mode'];
            if($request['date_from'] != '' && $request['date_to'] != '')
            {
                $from_date      =   date('Y-m-d',  strtotime($request['date_from'])); 
                $to_date        =   date('Y-m-d',  strtotime($request['date_to'])); 

                $string .= " and date(`order_date`) between '$from_date' and '$to_date' ";
            }
            else
            {
                      $string .= " ";
            }
        
        if($staffs == 'all')
        {
            $string .= "";
        }
        else
        {
            $string .= " and delivery_assigned_to = '$staffs'";
        }
            if($paymode !='all')
            {
                $string .=  " and payment_method = '$paymode'";
            }
            if($order_cat_filter == 'Restaurant') 
                    {
                    $catload .= " and `category`  <> 'Potafo Mart'";
                    //$strings .= " and r.`category`  <>'Potafo Mart'";
                }
                else if ($order_cat_filter == 'Potafo Mart')
                {
                    $catload .= " and `category`  = 'Potafo Mart'";
                    //$strings .= " and r.`category`  = 'Potafo Mart'";
                }
            if($staffs == 'all')
            {
                $i=0;
                $k=0;
                $staffs = DB::SELECT("SELECT  date(`order_date`) as date FROM order_master o, restaurant_master r where o.`rest_id` = r.id  and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') $string $catload  group by date");
                $bb="SELECT  date(`order_date`) as date FROM order_master o, restaurant_master r where o.`rest_id` = r.id  and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') $string  group by date";
                //return $bb;
//              $staffs = DB::SELECT("SELECT date(`order_date`) as date FROM `order_master` as om LEFT JOIN `internal_staffs` as i ON om.`delivery_assigned_to`= i.id WHERE om.`current_status` NOT IN('T','CA','P') $string group by date");
                foreach ($staffs as $values)
                {
                    $date = $values->date;
                    $asnd_stf[$k] = DB::SELECT("SELECT i.id,om.`delivery_assigned_details`->>'$.name' as name,date(`order_date`) as date FROM order_master om, restaurant_master r, `internal_staffs`  i where om.`rest_id` = r.id and om.`delivery_assigned_to`= i.id and  r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and om.`current_status` NOT IN('T','CA','P') and  date(`order_date`) = '$date' $catload group by id,name,date");
//                    $asnd_stf[$k] = DB::SELECT("SELECT i.id,om.`delivery_assigned_details`->>'$.name' as name,date(`order_date`) as date FROM `order_master` as om LEFT JOIN `internal_staffs` as i ON om.`delivery_assigned_to`= i.id WHERE om.`current_status` NOT IN('T','CA','P') and date(`order_date`) = '$date' group by id,name,date");
                    foreach ($asnd_stf[$k] as $value)
                    {
                        $staff_id     = $value->id;
                        $date         = $value->date;
                        if($paymode !='all')
                        {
                              $filter_query[$i] = DB::SELECT("SELECT `order_number`,date(`order_date`) as date,`delivery_assigned_details`->>'$.name' as stf_name,`delivery_assigned_details`->>'$.phone' as stf_phone,`sub_total` as amount,JSON_UNQUOTE(`total_details`->'$.packing_charge') as pck_chrg,JSON_UNQUOTE(`total_details`->'$.delivery_charge') as del_chrg,`final_total`,`status_details`->>'$.OP' as picked,`status_details`->>'$.C' as cnfrm,`status_details`->>'$.D' as delivery,`delivery_assigned_details`->'$.review' as review,`delivery_assigned_details`->'$.star_rate' as rating,`customer_details`->>'$.name' as cus_name,`customer_details`->>'$.mobile' as cus_mobile,payment_method,IFNULL(`payment_details`->>'$.razorpay_payment_id',0) as paymentid FROM `order_master`, restaurant_master r WHERE order_master.`rest_id` = r.id and `current_status` NOT IN('T','CA','P') and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staffid') and delivery_assigned_to = '$staff_id' and date(`order_date`) = '$date' and payment_method = '$paymode' $catload");
//                            $filter_query[$i] = DB::SELECT("SELECT `order_number`,date(`order_date`) as date,`delivery_assigned_details`->>'$.name' as stf_name,`delivery_assigned_details`->>'$.phone' as stf_phone,`sub_total` as amount,JSON_UNQUOTE(`total_details`->'$.packing_charge') as pck_chrg,JSON_UNQUOTE(`total_details`->'$.delivery_charge') as del_chrg,`final_total`,`status_details`->>'$.OP' as picked,`status_details`->>'$.C' as cnfrm,`status_details`->>'$.D' as delivery,`delivery_assigned_details`->'$.review' as review,`delivery_assigned_details`->'$.star_rate' as rating,`customer_details`->>'$.name' as cus_name,`customer_details`->>'$.mobile' as cus_mobile,payment_method,IFNULL(`payment_details`->>'$.razorpay_payment_id',0) as paymentid FROM `order_master` WHERE `current_status` NOT IN('T','CA','P') and delivery_assigned_to = '$staff_id' and date(`order_date`) = '$date' and payment_method = '$paymode'");
                        }
                        else
                        {
                            $filter_query[$i] = DB::SELECT("SELECT `order_number`,date(`order_date`) as date,`delivery_assigned_details`->>'$.name' as stf_name,`delivery_assigned_details`->>'$.phone' as stf_phone,`sub_total` as amount,JSON_UNQUOTE(`total_details`->'$.packing_charge') as pck_chrg,JSON_UNQUOTE(`total_details`->'$.delivery_charge') as del_chrg,`final_total`,`status_details`->>'$.OP' as picked,`status_details`->>'$.C' as cnfrm,`status_details`->>'$.D' as delivery,`delivery_assigned_details`->'$.review' as review,`delivery_assigned_details`->'$.star_rate' as rating,`customer_details`->>'$.name' as cus_name,`customer_details`->>'$.mobile' as cus_mobile,payment_method,IFNULL(`payment_details`->>'$.razorpay_payment_id',0) as paymentid FROM `order_master`, restaurant_master r    WHERE  order_master.`rest_id` = r.id and `current_status` NOT IN('T','CA','P') and delivery_assigned_to = '$staff_id' and date(`order_date`) = '$date' and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id  = '$staffid') $catload");
//                          $filter_query[$i] = DB::SELECT("SELECT `order_number`,date(`order_date`) as date,`delivery_assigned_details`->>'$.name' as stf_name,`delivery_assigned_details`->>'$.phone' as stf_phone,`sub_total` as amount,JSON_UNQUOTE(`total_details`->'$.packing_charge') as pck_chrg,JSON_UNQUOTE(`total_details`->'$.delivery_charge') as del_chrg,`final_total`,`status_details`->>'$.OP' as picked,`status_details`->>'$.C' as cnfrm,`status_details`->>'$.D' as delivery,`delivery_assigned_details`->'$.review' as review,`delivery_assigned_details`->'$.star_rate' as rating,`customer_details`->>'$.name' as cus_name,`customer_details`->>'$.mobile' as cus_mobile,payment_method,IFNULL(`payment_details`->>'$.razorpay_payment_id',0) as paymentid FROM `order_master` WHERE `current_status` NOT IN('T','CA','P') and delivery_assigned_to = '$staff_id' and date(`order_date`) = '$date'");
                        }
                        $i++;
                    }
                    $k++;
                }
                    
                                $append .=  '<table id="'.$replaced.'" class="table table-striped table-bordered">';
                                $append .=    '<thead>';
                                $append .=    '<tr>';
                                $append .=        '<th style="min-width:40px">Sl No</th>';
                                $append .=        '<th style="min-width:70px">Order No</th>';
                                $append .=        '<th style="min-width:100px">Date</th>';
                                $append .=        '<th style="min-width:100px">Amount</th>';
                                $append .=        '<th style="min-width:100px">Pack Chrg</th>';
                                $append .=        '<th style="min-width:100px">Delv chrg</th>';
                                $append .=        '<th style="min-width:100px">Final Amount</th>';
                                $append .=        '<th style="min-width:100px">Pay Mode</th>';
                                $append .=        '<th style="min-width:100px">Payment Id</th>';
                                $append .=        '<th style="min-width:100px">Conf Time</th>';
                                $append .=        '<th style="min-width:100px">Pick Time</th>';
                                $append .=        '<th style="min-width:100px">Delv Time</th>';
                                $append .=        '<th style="min-width:100px">Staff Rating</th>';
                                $append .=        '<th style="min-width:100px">Staff Review</th>';
                                $append .=        '<th style="min-width:100px">Cust Name</th>';
                                $append .=        '<th style="min-width:100px">Phone</th>';
                                $append .=    '</tr>';
                                $append .=    '</thead>';

                                $append .=    '<tbody>';

                   
                                    $j=0;
                                    $k=0;

                    
                                            $tfoot_amount_total = 0;
                                            $tfoot_pck_chrg_total = 0;
                                            $tfoot_chrg_total = 0;
                                            $tfoot_total = 0;
											//return $staffs;
                        foreach ($staffs as $values)
                        {                 
                                 $append .=            '<tr role="row" class="odd"  style="background-color: beige !important;">';
                                 $append .=                '<td style="min-width:30px;"></td>';
                                 $append .=                '<td style="min-width:30px;"></td>';
                                 $append .=                '<td style="min-width:30px;"><strong style="color:#000">'.$values->date.'<strong></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=            '</tr>';

                 foreach($asnd_stf[$k] as $value)
                 {
                             $tot_cash_tot=0; 
                             $tot_amount_total = 0;
                             $tot_pck_chrg_total = 0;
                             $tot_del_chrg_total = 0;
                             $tot_final_total = 0;

if($from_date== $to_date)
{
    $id_staff = $value->id;
    $orddate  = $value->date;
    $time = DB::SELECT("select DATE_Format(in_time,'%H:%i') as in_time,DATE_Format(out_time,'%H:%i') as out_time from delivery_staff_attendence where staff_id = '$id_staff' and entry_date= '$orddate'");
       $tt ='';
       //return "select DATE_Format(in_time,'%H:%i') as in_time,DATE_Format(out_time,'%H:%i') as out_time from delivery_staff_attendence where staff_id = '$id_staff' and entry_date= '$orddate'" ;
    foreach($time as $timeout)
          {
            $intime = $timeout->in_time;
            $outtime = $timeout->out_time;
            if($outtime == '')
            {
                $outtime == '';
            }
            else
            {
                $outtime == $outtime;
            }
            $tt .= $intime.'-'.$outtime.', ';
                                
          }
          $tt = substr($tt,0,-2);
           $append .=            '<tr role="row" class="odd">';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td>'.$value->name.'</td>';
                                 $append .=                '<td >'.$tt.'</td>';
                                 $append .=                '<td ></td>';
                                 $append .=                '<td ></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=            '</tr>';
}
else
{
                                 $append .=            '<tr role="row" class="odd">';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td>'.$value->name.'</td>';
                                 $append .=                '<td ></td>';
                                 $append .=                '<td ></td>';
                                 $append .=                '<td ></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=            '</tr>';
}

                 $u=0;
                 foreach($filter_query[$j] as $val)
                     {

                                 $u++; 
                                 $tot_amount = $val->amount;
                                 $tot_pck_chrg = $val->pck_chrg;
                                 $tot_del_chrg = $val->del_chrg;
                                 $tot_final = $val->final_total;

                                 $tot_amount_total += $tot_amount;
                                 $tot_pck_chrg_total += $tot_pck_chrg;
                                 $tot_del_chrg_total += $tot_del_chrg;
                                 $tot_final_total += $tot_final;


                                 $append .=            '<tr role="row" class="odd">';
                                 $append .=                '<td style="min-width:30px;">'.$u.'</td>';
                                 $append .=                '<td>'.$val->order_number.'</td>';
                                 $append .=                '<td> </td>';
                                 $append .=                '<td>'.number_format((float)$val->amount, $num_format).'</td>';
                                                 $amt = $val->pck_chrg; 
                                 $append .=                '<td>'.number_format((float)$amt, $num_format).'</td>';
                                                 $fnl = $val->del_chrg;
                                 $append .=                '<td>'.  number_format((float)$fnl, $num_format).'</td>';
                                 $append .=                '<td>'.number_format((float)$val->final_total,$num_format).'</td>';
                                 $append .=                '<td>'.$val->payment_method.'</td>';
                                 $append .=                '<td>'.$val->paymentid.'</td>';
                                 $append .=                '<td>'.$val->cnfrm.'</td>';
                                 $append .=                '<td>'.$val->picked.'</td>';
                                 $append .=                '<td>'.$val->delivery.'</td>';
                                                 $rating = substr($val->rating,0,-1);
                                 $append .=                '<td>'.   substr($rating,1).'</td>';
                                                 $review = substr($val->review,0,-1);
                                 $append .=                '<td>'.  substr($review,1).'</td>';
                                 $append .=                '<td>'.$val->cus_name.'</td>';
                                 $append .=                '<td>'.$val->cus_mobile.'</td>';
                                 $append .=              '</tr>';
                                             }
                                     $j++; 
                                 $append .=              '<tr role="row" class="odd" style="    background-color: #e2e2e2 !important;">';
                                 $append .=                '<td style="min-width:40px;"></td>';
                                 $append .=                '<td style="min-width:70px;"><strong style="color:#000">Total</strong></td>';
                                 $append .=                '<td style="min-width:100px;"></td>';
                                 $append .=                '<td style="font-weight:bold;color:#000">'.number_format((float)$tot_amount_total, $num_format).'</td>';
                                 $append .=                '<td style="font-weight:bold;color:#000">'.number_format((float)$tot_pck_chrg_total, $num_format).'</td>';
                                 $append .=                '<td style="font-weight:bold;color:#000">'.number_format((float)$tot_del_chrg_total, $num_format).'</td>';
                                 $append .=                '<td style="font-weight:bold;color:#000">'.number_format((float)$tot_final_total, $num_format).'</td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=              '</tr>';

                                                 $tfoot_amount_total += $tot_amount_total;
                                                 $tfoot_pck_chrg_total += $tot_pck_chrg_total;
                                                 $tfoot_chrg_total += $tot_del_chrg_total;
                                                 $tfoot_total += $tot_final_total;                             

                                     }
                                 $k++;    
                         }            
                    

                                $append .=    '</tbody>';
                                $append .=    '<tfoot>';
                                $append .=               '<tr role="row" class="odd">';
                                $append .=                '<td style="min-width:30px;"></td>';
                                $append .=                '<td style="min-width:30px;"></td>';
                                $append .=                '<td style="min-width:30px;"></td>';
                                $append .=                '<td style="min-width:100px;font-weight:bold;color:#000">'.$tfoot_amount_total.'</td>';
                                $append .=                '<td style="min-width:100px;font-weight:bold;color:#000">'.$tfoot_pck_chrg_total.'</td>';
                                $append .=                '<td style="min-width:100px;font-weight:bold;color:#000">'.$tfoot_chrg_total.'</td>';
                                $append .=                '<td style="min-width:100px;font-weight:bold;color:#000">'.$tfoot_total.'</td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=              '</tr>';
                                $append .=  '</tfoot>';                
                                $append .=  '</table>';
                    
                   
                }
                else
                {
//                    $asnd_stf = DB::SELECT("SELECT i.id,date(`order_date`) as date FROM `order_master` as om LEFT JOIN `internal_staffs` as i ON om.`delivery_assigned_to`= i.id WHERE om.`current_status` NOT IN('T','CA','P')  $string group by id,date");
                    $i=0;
//                    foreach ($asnd_stf as $value) 
//                    {
//                        $id = $value->id;
                        $asnd = DB::SELECT("SELECT i.id,date(`order_date`) as date FROM `order_master` as om LEFT JOIN `restaurant_master` as rm ON  om.rest_id = rm.id LEFT JOIN `internal_staffs` as i ON om.`delivery_assigned_to`= i.id WHERE om.`current_status` NOT IN('T','CA','P')  $string $catload  group by id,date");
                        $filter_query = DB::SELECT("SELECT `order_number`,date(`order_date`) as date,`delivery_assigned_details`->>'$.name' as stf_name,`delivery_assigned_details`->>'$.phone' as stf_phone,`sub_total` as amount,JSON_UNQUOTE(`total_details`->'$.packing_charge') as pck_chrg,JSON_UNQUOTE(`total_details`->'$.delivery_charge') as del_chrg,`final_total`,`status_details`->>'$.OP' as picked,`status_details`->>'$.C' as cnfrm,`status_details`->>'$.D' as delivery,`delivery_assigned_details`->'$.review' as review,`delivery_assigned_details`->'$.star_rate' as rating,`customer_details`->>'$.name' as cus_name,`customer_details`->>'$.mobile' as cus_mobile,payment_method,IFNULL(`payment_details`->>'$.razorpay_payment_id',0) as paymentid FROM `order_master` LEFT JOIN `restaurant_master` as rm ON  order_master.rest_id = rm.id  WHERE `current_status` NOT IN('T','CA','P') $string $catload Order BY date");
                        $asnd_stf = DB::SELECT("SELECT i.id,date(`order_date`) as date FROM `order_master` as om LEFT JOIN `restaurant_master` as rm ON  om.rest_id = rm.id LEFT JOIN `internal_staffs` as i ON om.`delivery_assigned_to`= i.id WHERE om.`current_status` NOT IN('T','CA','P')  $string $catload group by id,date");
                  foreach ($asnd_stf as $value) 
                    {
                        if($from_date== $to_date)
                     {
                        $id_staff = $value->id;
                        $orddate  = $value->date;
                        $time = DB::SELECT("select DATE_Format(in_time,'%H:%i') as in_time,DATE_Format(out_time,'%H:%i') as out_time from delivery_staff_attendence where staff_id = '$id_staff' and entry_date= '$orddate'");
                        $tt ='';
                        foreach($time as $timeout)
                        {
                            $intime = $timeout->in_time;
                            $outtime = $timeout->out_time;
                            if($outtime == '')
                            {
                                $outtime == '';
                            }
                            else
                            {
                               $outtime == $outtime;
                            }
                            $tt .= $intime.'-'.$outtime.', ';
                                
                        }
                        $tt = substr($tt,0,-2);
                     }
                        $i++;
                    }
                  
                    $append .=  '<table id="'.$replaced.'" class="table table-striped table-bordered">';
                                $append .=    '<thead>';
                                $append .=    '<tr>';
                                $append .=        '<th style="min-width:40px">Sl No</th>';
                                $append .=        '<th style="min-width:70px">Order No</th>';
                                $append .=        '<th style="min-width:100px">Date</th>';
                                $append .=        '<th style="min-width:100px">Amount</th>';
                                $append .=        '<th style="min-width:100px">Pack Chrg</th>';
                                $append .=        '<th style="min-width:100px">Delv chrg</th>';
                                $append .=        '<th style="min-width:100px">Final Amount</th>';
                                $append .=        '<th style="min-width:100px">Pay Mode</th>';
                                $append .=        '<th style="min-width:100px">Payment Id</th>';
                                $append .=        '<th style="min-width:100px">Conf Time</th>';
                                $append .=        '<th style="min-width:100px">Pick Time</th>';
                                $append .=        '<th style="min-width:100px">Delv Time</th>';
                                $append .=        '<th style="min-width:100px">Staff Rating</th>';
                                $append .=        '<th style="min-width:100px">Staff Review</th>';
                                $append .=        '<th style="min-width:100px">Cust Name</th>';
                                $append .=        '<th style="min-width:100px">Phone</th>';
                                $append .=    '</tr>';
                                $append .=    '</thead>';

                                $append .=    '<tbody>';

                   
                                    $j=0;
                              

                    
                                            $tfoot_amount_total = 0;
                                            $tfoot_pck_chrg_total = 0;
                                            $tfoot_chrg_total = 0;
                                            $tfoot_total = 0;
                            

//                            foreach($asnd_stf as $value)
//                            {
//                             $tot_cash_tot=0; 
//                             $tot_amount_total = 0;
//                             $tot_pck_chrg_total = 0;
//                             $tot_del_chrg_total = 0;
//                             $tot_final_total = 0;


                 $u=0;
                        if(count($filter_query) != 0){
                            
                            if($from_date == $to_date)
                            {
                                 $append .=            '<tr role="row" class="odd" style="background-color: beige !important;">';
                                 $append .=                '<td style="min-width:40px;"></td>';
                                 $append .=                '<td style="min-width:70px;"></td>';
                                 $append .=                '<td><strong style="color:#000">'.$filter_query[0]->stf_name.'</strong></td>';
                                 $append .=                '<td style="min-width:30px;">'.$tt.'</td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=            '</tr>';
                            }
                            else{
                            
                                 $append .=            '<tr role="row" class="odd" style="background-color: beige !important;">';
                                 $append .=                '<td style="min-width:40px;"></td>';
                                 $append .=                '<td style="min-width:70px;"></td>';
                                 $append .=                '<td><strong style="color:#000">'.$filter_query[0]->stf_name.'</strong></td>';
                                 $append .=                '<td style="min-width:30px;"></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=                '<td></td>';
                                 $append .=            '</tr>';
                            }
                            
                            
                            
                             foreach($asnd_stf as $value)
                            {
                             $tot_cash_tot=0; 
                             $tot_amount_total = 0;
                             $tot_pck_chrg_total = 0;
                             $tot_del_chrg_total = 0;
                             $tot_final_total = 0;
                            
                            } 
                            
                 foreach($filter_query as $val)
                     {
                            
                                 $u++; 
                                 $tot_amount = $val->amount;
                                 $tot_pck_chrg = $val->pck_chrg;
                                 $tot_del_chrg = $val->del_chrg;
                                 $tot_final = $val->final_total;

                                 $tot_amount_total += $tot_amount;
                                 $tot_pck_chrg_total += $tot_pck_chrg;
                                 $tot_del_chrg_total += $tot_del_chrg;
                                 $tot_final_total += $tot_final;


                                 
                                 $append .=            '<tr role="row" class="odd">';
                                 $append .=                '<td style="min-width:30px;">'.$u.'</td>';
                                 $append .=                '<td>'.$val->order_number.'</td>';
                                 $append .=                '<td>'.$val->date.'</td>';
                                 $append .=                '<td>'.number_format((float)$val->amount, $num_format).'</td>';
                                                 $amt = $val->pck_chrg; 
                                 $append .=                '<td>'.number_format((float)$amt, $num_format).'</td>';
                                                 $fnl = $val->del_chrg;
                                 $append .=                '<td>'.  number_format((float)$fnl, $num_format).'</td>';
                                 $append .=                '<td>'.number_format((float)$val->final_total,$num_format).'</td>';
                                 $append .=                '<td>'.$val->payment_method.'</td>';
                                 $append .=                '<td>'.$val->paymentid.'</td>';
                                 $append .=                '<td>'.$val->cnfrm.'</td>';
                                 $append .=                '<td>'.$val->picked.'</td>';
                                 $append .=                '<td>'.$val->delivery.'</td>';
                                                 $rating = substr($val->rating,0,-1);
                                 $append .=                '<td>'.   substr($rating,1).'</td>';
                                                 $review = substr($val->review,0,-1);
                                 $append .=                '<td>'.  substr($review,1).'</td>';
                                 $append .=                '<td>'.$val->cus_name.'</td>';
                                 $append .=                '<td>'.$val->cus_mobile.'</td>';
                                 $append .=              '</tr>';
                                             }
                                     $j++; 
                               
                                $tfoot_amount_total += $tot_amount_total;
                                $tfoot_pck_chrg_total += $tot_pck_chrg_total;
                                $tfoot_chrg_total += $tot_del_chrg_total;
                                $tfoot_total += $tot_final_total;                             

                            }
                                 
                                    
                    

                                $append .=    '</tbody>';
                                $append .=    '<tfoot>';
                                $append .=               '<tr role="row" class="odd">';
                                $append .=                '<td style="min-width:30px;"></td>';
                                $append .=                '<td style="min-width:30px;"><strong style="color:#000">Total</strong></td>';
                                $append .=                '<td style="min-width:30px;"></td>';
                                $append .=                '<td style="min-width:100px;">'.$tfoot_amount_total.'</td>';
                                $append .=                '<td style="min-width:100px;">'.$tfoot_pck_chrg_total.'</td>';
                                $append .=                '<td style="min-width:100px;">'.$tfoot_chrg_total.'</td>';
                                $append .=                '<td style="min-width:100px;">'.$tfoot_total.'</td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=                '<td style="min-width:100px;"></td>';
                                $append .=              '</tr>';
                                $append .=  '</tfoot>';                
                                $append .=  '</table>';
                    
                }
                
                 return $append;    
                
            }
        else if($reports_name == 'Staff_change_report')
        {
            if($request['date_from'] != '' && $request['date_to'] != '')
            {
                $from_date      =   date('Y-m-d',  strtotime($request['date_from'])); 
                $to_date        =   date('Y-m-d',  strtotime($request['date_to'])); 

                $string .= " and date(`order_date`) between '$from_date' and '$to_date' ";
            }
            else
            {
                      $string .= " ";
            }
                $staffs = DB::SELECT("SELECT `order_number`,json_length(`staff_change_details`) as length, date(`order_date`) as date FROM `order_master`, restaurant_master r WHERE `delivery_staff_change`='Y' $string and order_master.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id= '$staffid') group by order_number,length,date order by date desc");
//              $staffs = DB::SELECT("SELECT `order_number`,json_length(`staff_change_details`) as length, date(`order_date`) as date FROM `order_master` WHERE `delivery_staff_change`='Y' $string group by order_number,length,date order by date desc");

                $append .=        '<table id="'.$replaced.'" class="table table-striped table-bordered">';
                $append .=        '<thead>';
                $append .=        '<tr style="font-size: 10px !important">';
                $append .=            '<th style="min-width:40px">Sl No</th>';
                $append .=            '<th style="min-width:200px">Order Number</th>';
                $append .=            '<th style="min-width:200px">Date</th>';
                $append .=            '<th style="min-width:140px">Area</th>';
                $append .=            '<th style="min-width:400px">Staff Change Details</th>';
                $append .=        '</tr>';
                $append .=        '</thead>';
                $append .=        '<tbody>';
                $t=0;
                $j=0;
                $p=0;
                foreach ($staffs as $value)
                {
                    $length[$t] = $value->length;
                    $order  = $value->order_number;
                    $k=0;
                    for($i=0;$i<$length[$t];$i++)
                    {
                        $k++;
                        $details[$j] = DB::SELECT("SELECT concat_ws(' ',sf1.`first_name`,sf1.`last_name`) as from_name,concat_ws(' ',sf2.`first_name`,sf2.`last_name`) as to_name,om.`order_number`,date(om.`order_date`)as date,om.`staff_change_details`->>'$.staff$k.time' as time,om.`customer_details`->>'$.addressline2' as addr FROM `order_master` as om LEFT JOIN `internal_staffs` as sf1 ON om.`staff_change_details`->>'$.staff$k.fromstaff' = sf1.id LEFT JOIN `internal_staffs` as sf2 ON om.`staff_change_details`->>'$.staff$k.tostaff' = sf2.id  WHERE `delivery_staff_change`='Y' AND `order_number`= '$order' order by date desc");
//                      $details[$j] = DB::SELECT("SELECT concat_ws(' ',sf1.`first_name`,sf1.`last_name`) as from_name,concat_ws(' ',sf2.`first_name`,sf2.`last_name`) as to_name,om.`order_number`,date(om.`order_date`)as date,om.`staff_change_details`->>'$.staff$k.time' as time,om.`customer_details`->>'$.addressline2' as addr FROM `order_master` as om LEFT JOIN `internal_staffs` as sf1 ON om.`staff_change_details`->>'$.staff$k.fromstaff' = sf1.id LEFT JOIN `internal_staffs` as sf2 ON om.`staff_change_details`->>'$.staff$k.tostaff' = sf2.id  WHERE `delivery_staff_change`='Y' AND `order_number`= '$order' order by date desc");
                        $p++;
                        foreach ($details[$j] as $val)
                        {
                            $append .=                '<tr role="row" class="odd" style="background-color: #fff !important;">';
                            $append .=                    '<td>'.$p.'</td>';
                            $append .=                    '<td>'.$val->order_number.'</td>';
                            $append .=                    '<td>'.$val->date.'</td>';
                            $append .=                    '<td>'.$val->addr.'</td>';
                            $append .=                    '<td>From   '.$val->from_name.'   Changed to   '.$val->to_name.'   at   '.$val->time.'</td>';
                            $append .=                '</tr>';   
                        }
                        $j++;   
                    }
                        $t++;
                }
                    $append .=        '</tbody>';
                    $append .=    '</table>';
                    return $append;
            }
        else if($reports_name == 'Staff_salary_report')
        {
            $staffs = $request['staff'];
            $from_mode = $request['from_mode'];
            $app_screen= $request['app_screen'];
            //return  $from_mode;
            //$arr = '';
            $append .=        '<table id="'.$replaced.'" class="table table-striped table-bordered">';
            $append .=        '<thead>';
            $append .=        '<tr style="font-size: 10px !important">';
            $append .=            '<th style="min-width:40px">Date</th>';
            $append .=            '<th style="min-width:40px">LogIn</th>';
            $append .=            '<th style="min-width:40px">LogOut</th>';
            $append .=            '<th style="min-width:120px">Time Split</th>';
            $append .=            '<th style="min-width:30px">Ttl. Working Time</th>';
            $append .=            '<th style="min-width:30px">5  Star Count </th>';
            $append .=            '<th style="min-width:30px">5 Star Amt </th>';
            $append .=            '<th style="min-width:30px">Nrml Order Count</th>';
            $append .=            '<th style="min-width:40px">Nrml Order Amt.</th>';
            $append .=            '<th style="min-width:30px">Spcl Order Count</th>';
            $append .=            '<th style="min-width:40px">Spcl Order Amt.</th>';
            $append .=            '<th style="min-width:30px">Bonus (Whole Hrs)</th>';
            $append .=            '<th style="min-width:40px">Bonus Time Amt.</th>';
            $append .=            '<th style="min-width:20px">Ttl Bns  Amt.</th>';
            $append .=            '<th style="min-width:20px">Ttl Shrt Amt.</th>';
            $append .=            '<th style="min-width:40px">Final Ttl Amt.</th>';
            $append .=        '</tr>';
            $append .=        '</thead>';
            $append .=        '<tbody>';
            $t=0;
            $j=0;
            $p=0;
         
            if($request['date_from'] != '' && $request['date_to'] != '')
            {
                $from_date      =   date('Y-m-d',  strtotime($request['date_from']));
                $to_date        =   date('Y-m-d',  strtotime($request['date_to']));

                $string .= " and date(`entry_date`) between '$from_date' and '$to_date' ";
            }
            else
            {
                $string .= " ";
            }
            if($staffs == 'all')
            {
               $string .= "";
            }
            else
            {
                $string .= " and staff_id = '$staffs'";
            }
            $totalbonusrate= 0;
            $totalnormalrate= 0;
            $totalspecialorderrate= 0;
            
            $general= GeneralSetting::where('id','1')->select('five_star_rate','restaurant_from_time','restaurant_to_time','normal_order_rate','spcl_order_rate','bonus_rate_weekdays','bonus_rate_weekend','five_star_rate')->first();
            //$arr[] = (object)array('Date' => '','Total_time' =>'','Total_Earnings' => '');
            $arr =array();         
            //RETURN "SELECT distinct(first_name),last_name,ist.id FROM `delivery_staff_attendence` as at left join  `internal_staffs` as ist on at.staff_id = ist.id where ist.id is not null $string group by id order by first_name";
            $staffs =  DB::SELECT("SELECT distinct(first_name),last_name,ist.id FROM `delivery_staff_attendence` as at left join  `internal_staffs` as ist on at.staff_id = ist.id where ist.id is not null $string group by id order by first_name");
            if((count($staffs)<=0) && ($from_mode == 'APP'))
            {
                
               if($app_screen == 'DASHBOARD')
                {
                    return response::json(['Total_time' =>'0','Total_Earnings' =>'0' ]);
                }
                else 
                {
                    $msg = 'Not Exist';
                    return response::json(['msg' => $msg,'report_data' =>$arr,'Total_Sum' => '']);
                }
                      
            }
            foreach($staffs as $item=>$value)
            {
                dd('this');
                $totalamount = 0;
                $name = $value->first_name.' '.$value->last_name;
                $append .=                '<tr role="row" class="odd" style="background-color: #fff !important;">';
                $append .=                    '<td><b>'.$name.'</b></td>';
                $append .=                    '<td ></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                '</tr>';

                $arr =[];
                $timesplit = '';
                $staffdetails =  DB::SELECT("SELECT entry_date,ist.id,MIN(time(in_time)) as in_time,MAX(time(out_time)) as out_time FROM `delivery_staff_attendence` as at left join  `internal_staffs` as ist on at.staff_id = ist.id where staff_id= '".$value->id."' and  ist.id is not null $string group by entry_date order by entry_date asc");
                foreach($staffdetails as $key=>$data)
                {
                    // return 'SELECT order_number,MAX(str_to_date(status_details->>"$.D", "%l:%i %p")) as delivered_time,(select count(*) from order_master where `delivery_assigned_details`->>"$.star_rate" IN("5","5.0") and date(order_date)="'.$data->entry_date.'" and delivery_assigned_to = "'.$value->id.'")  as starcount  from order_master where date(order_date)="'.$data->entry_date.'" and delivery_assigned_to = "'.$value->id.'" order by order_number desc limit 1';
                    $orderdetails = DB::SELECT('SELECT order_number,MAX(str_to_date(status_details->>"$.D", "%l:%i %p")) as delivered_time,(select count(*) from order_master where `delivery_assigned_details`->>"$.star_rate" IN("5","5.0") and date(order_date)="'.$data->entry_date.'" and delivery_assigned_to = "'.$value->id.'")  as starcount  from order_master where date(order_date)="'.$data->entry_date.'" and delivery_assigned_to = "'.$value->id.'" order by order_number desc limit 1');
                    $staffarr['id'] = $value->id;
                    $staffarr['date'] = $data->entry_date;
                    if(strtotime($data->in_time) >= strtotime($general->restaurant_from_time))
                    {
                        $logintime = $data->in_time;
                    }
                    else{
                        $logintime = $general->restaurant_from_time;
                    }
                    $staffarr['login_time'] =$logintime;
                    $logouttime =$logintime;
                   
                    $checklogoutime =  DB::SELECT("SELECT  slno  FROM  delivery_staff_attendence  WHERE  staff_id  = '".$value->id."' and out_time IS NULL and entry_date = '".$data->entry_date."'");
                    if(count($checklogoutime)>0)
                    {
                        $chlogoutime = '';
                    }
                    else
                    {
                        $chlogoutime = $data->out_time;
                    }

                    
                    if($chlogoutime=='')
                    {
                        $date =       new DateTime('now', new DateTimeZone($timezone));
                        $logouttime = strtoupper($date->format('H:i:s'));
                    }
                    else if(isset($chlogoutime) && strtotime($chlogoutime) <= strtotime($general->restaurant_to_time))
                    {
                        $logouttime = $chlogoutime;
                    }
                    else
                    {
                        if(isset($orderdetails[0]->delivered_time) && strtotime($orderdetails[0]->delivered_time) <= strtotime($general->restaurant_to_time)) 
                        {
                            $logouttime = $general->restaurant_to_time;
                        }
                        else
                        {
                            $logouttime = $orderdetails[0]->delivered_time;
                        }
                    }
                    
                    $staffarr['logout_time'] = $logouttime;
                    $totalhour= '0';
                    $totalsec= '0';
                    $bonus_calhour = 0 ;
                    $timearr= [];
                    $staffdetail = DB::SELECT("select time(out_time) as out_time,time(in_time) as in_time from delivery_staff_attendence where staff_id = '".$value->id."' and  date(entry_date) = '".$data->entry_date."'  order by slno asc");
                    foreach($staffdetail as $item=>$detail)
                    {
                        $timearr[] = $detail->in_time.'-'.$detail->out_time;
                        if(strtotime($detail->in_time)>strtotime($general->restaurant_from_time))
                        {
                            $fromtime =$detail->in_time;
                        }
                        else
                        {
                            $fromtime = $logintime;
                        }
                        $frmtime = new DateTime(date('H:i:s' ,strtotime($fromtime)), new DateTimeZone($timezone));
                        
                        if(isset($detail->out_time)&& strtotime($detail->out_time) <= strtotime($general->restaurant_to_time)) 
                        {
                            
                            $totime = new DateTime(date('H:i:s' ,strtotime($detail->out_time)), new DateTimeZone($timezone));
                        }
                        else
                        {

                            $totime = new DateTime(date('H:i:s' ,strtotime($logouttime)), new DateTimeZone($timezone));
                        }
                        
                        //return $totime->format('H:i:s');
                        $hours = $frmtime->diff($totime)->format('%H %i %s');
                        $actualhours = $frmtime->diff($totime);
                        $sec = ((($actualhours->format('%a')*24)+$actualhours->format('%H'))*60 +$actualhours->format('%i'))*60+($actualhours->format('%s'));
                        $totalhour = strtotime($totalhour) +strtotime($hours);
                        $sumhours = $totalhour;
                        $totalsec = $totalsec +$sec;

                        $calhours =0;
                        $bonus_work_timing = DB::SELECT('SELECT * FROM `bonus_work_timing` order by `bw_id` asc');
                         foreach($bonus_work_timing as $ky=>$timeval)
                        {
                           

                            if(strtotime($detail->in_time) <= strtotime($timeval->bw_from_time))
                            {
                                if(strtotime($totime->format('H:i:s')) <= strtotime($timeval->bw_from_time))
                                {
                                    $bonus_calhour = $bonus_calhour + 0 ;
                                }
                                else
                                {
                                    if(strtotime($detail->in_time) >= strtotime($timeval->bw_to_time))  
                                    {
                                        $bonus_calhour =  $bonus_calhour + 0 ;
                                    }
                                    else
                                    {
                                        $staff_from_time = new DateTime(date('H:i' ,strtotime($timeval->bw_from_time)), new DateTimeZone($timezone));
                                    }
                                    if(strtotime($totime->format('H:i:s')) >= strtotime($timeval->bw_to_time))  
                                    {
                                        $staff_to_time = new DateTime(date('H:i' ,strtotime($timeval->bw_to_time)), new DateTimeZone($timezone));
                                        $calhours = $staff_from_time->diff($staff_to_time)->format('%H');
                                        $bonus_calhour =  $bonus_calhour + $calhours ;
                                    }
                                    else
                                    {
                                        $staff_to_time = new DateTime(date('H:i' ,strtotime($totime->format('H:i:s'))), new DateTimeZone($timezone));
                                        $calhours = $staff_from_time->diff($staff_to_time)->format('%H');
                                        $bonus_calhour =  $bonus_calhour + $calhours ;
                                    }
                                    
                                }
                            }
                            else
                            {
                                if(strtotime($detail->in_time)>=strtotime($timeval->bw_to_time))
                                {
                                    $bonus_calhour = $bonus_calhour + 0 ;
                                }
                                else
                                {
                                    $staff_from_time = new DateTime(date('H:i' ,strtotime($detail->in_time)), new DateTimeZone($timezone));
                                        

                                    if(strtotime($totime->format('H:i:s'))>=strtotime($timeval->bw_to_time))
                                    {
                                       
                                        $staff_to_time = new DateTime(date('H:i' ,strtotime($timeval->bw_to_time)), new DateTimeZone($timezone));
                                        $calhours = $staff_from_time->diff($staff_to_time)->format('%H');

                                        $bonus_calhour =  $bonus_calhour + $calhours ;
                                       

                                    }
                                    else
                                    {
                                        $staff_to_time = new DateTime(date('H:i' ,strtotime($totime->format('H:i:s'))), new DateTimeZone($timezone));
                                        
                                        $calhours = $staff_from_time->diff($staff_to_time)->format('%H');
                                      
                                        $bonus_calhour =  $bonus_calhour + $calhours ;

                                    }
                                }
                            }
                            //$bonus[] = $bonus_calhour;
                          //  $bonustimearr[] = '('.$detail->in_time.'-'.$detail->out_time.' ! '.$timeval->bw_from_time.'-'.$timeval->bw_to_time.' ! '.$bonus_calhour.' )';

                        } 
                        
                       
                   }
                   // $bonusall= implode(' ,',$bonus);
                    //$bonustimeall= implode(' ,',$bonustimearr);
                  // return $bonustimeall;
                    $timesplit= implode(' ,',$timearr);
                    $staffarr['totaltime']= isset($sumhours)?$sumhours:0;
                    $staffarr['timesplit'] = $timesplit;
                   // $staffarr['fivestarcount']= isset($orderdetails[0]->starcount)?$orderdetails[0]->starcount:0;
                    $staffarr['fivestarrate']= number_format((isset($orderdetails[0]->starcount)?$orderdetails[0]->starcount:0)*($general->five_star_rate),$num_format);
                    $normaltiming = DB::SELECT("select from_time,to_time from normal_order_timing");
                    $totalorders =0;
                    foreach($normaltiming as $timings)
                    {
                        $orders = DB::select("select count(order_number) as ordercount from order_master where str_to_date(`status_details`->>'$.C', '%l:%i %p') BETWEEN '".$timings->from_time."' and '".$timings->to_time."' and current_status = 'D' and date(order_date)='".$data->entry_date."' and delivery_assigned_to = '".$value->id."'");
                        $totalorders = $totalorders + $orders[0]->ordercount;
                    }
                    $staffarr['normalorders'] = $totalorders;
                    $staffarr['normalorderrate'] = number_format($totalorders*$general->normal_order_rate,$num_format);
                    $totalnormalrate =$totalorders*$general->normal_order_rate;

                    $spcltiming = DB::SELECT("select from_time,to_time from special_order_timing");
                    $totalspclorders =0;
                    foreach($spcltiming as $times)
                    {
                        $spcltimingorders = DB::select("select count(order_number) as ordercount from order_master where str_to_date(`status_details`->>'$.C', '%l:%i %p') BETWEEN '".$times->from_time."' and '".$times->to_time."' and current_status = 'D' and date(order_date)='".$data->entry_date."' and  delivery_assigned_to = '".$value->id."'");
                        $totalspclorders = $totalspclorders + $spcltimingorders[0]->ordercount;
                    }
                    $staffarr['specialorders'] = $totalspclorders;
                    $staffarr['specialordersrate'] = number_format($totalspclorders*$general->spcl_order_rate,$num_format);
                    $totalspecialorderrate = $totalspclorders*$general->spcl_order_rate;
                    
                    if(date('N',strtotime($data->entry_date)) >= 6)
                    {
                        $bonusrate = $general->bonus_rate_weekend;
                    }
                    else{
                        $bonusrate = $general->bonus_rate_weekdays;
                    }
                    //$bonus_calhour = 0 ;
                    $total_sal_short = 0;
                    $sal_shortage = DB::select("select IFNULL(SUM(is_staff_amount),0) as total_amount  FROM internal_staffs_sal_adj WHERE is_mode = 'Shortage' AND is_staff_id = '".$value->id."'  AND is_staff_date = '".$data->entry_date."'");
                    $total_sal_short = $sal_shortage[0]->total_amount;
                    
                    
                    $total_sal_bonus = 0;
                    $sal_bonus = DB::select("select IFNULL(SUM(is_staff_amount),0)as total_amount FROM internal_staffs_sal_adj WHERE is_mode = 'Bonus' AND is_staff_id = '".$value->id."'  AND is_staff_date = '".$data->entry_date."'");
                    $total_sal_bonus = $sal_bonus[0]->total_amount;

                    

                    $staffarr['bonusrate'] =  $bonusrate;
                    $starcount = (isset($orderdetails[0]->starcount)?$orderdetails[0]->starcount:0);
                    $starrate = $starcount*$general->five_star_rate;
                    $totalbonusrate = $bonusrate*$bonus_calhour;
                    $date_final_total = ($starrate+$totalnormalrate+$totalspecialorderrate+$totalbonusrate+$total_sal_bonus)-$total_sal_short;
                    $append .=                '<tr role="row" class="odd" style="background-color: #fff !important;">';
                    $append .=                    '<td>'.date('d-m-y',  strtotime($data->entry_date)).'</td>';
                    $append .=                    '<td>'.$logintime.'</td>';
                    $append .=                    '<td>'.$logouttime.'</td>';
                    $append .=                    '<td>'.$timesplit.'</td>';
                    $append .=                    '<td>'.(isset($totalsec)?gmdate('H:i:s',$totalsec):0).'</td>';
                    $append .=                    '<td>'.$starcount.'</td>';
                    $append .=                    '<td>'.$starrate.'</td>';
                    $append .=                    '<td>'.$totalorders.'</td>';
                    $append .=                    '<td>'.$totalnormalrate.'</td>';
                    $append .=                    '<td>'.$totalspclorders.'</td>';
                    $append .=                    '<td>'.$totalspecialorderrate.'</td>';
                    $append .=                    '<td>'.(isset($bonus_calhour)?$bonus_calhour:0).'</td>';
                    $append .=                    '<td>'.$totalbonusrate.'</td>';
                    $append .=                    '<td ><span style="color:green;">'.$total_sal_bonus.'</span></td>';
                    $append .=                    '<td><span style="color:red;">'.$total_sal_short.'</span></td>';
                    $append .=                    '<td>'.$date_final_total.'</td>';
                    $append .=                    '</tr>';
                    $totalamount = $totalamount + $date_final_total;
                    if($from_mode == 'APP')
                    {

                        if($app_screen == 'DASHBOARD')
                        {
                            $msg = 'Exist';
                            //return response::json(['Total_time' =>'0','Total_Earnings' =>'0' ]);
                     
                            return response::json(['Total_time' =>(string) (isset($totalsec)?gmdate('H:i:s',$totalsec):0),'Total_Earnings' =>$date_final_total ]);
                        }
                        else 
                        {   $msg = 'Exist';
                            //$arr[] = (object)array('Date' => $data->entry_date,'Total_time' =>(string) (0),'Total_Earnings' =>(string) (0),'Deductions' => (string) (0),'Bonus' =>(string) (0),'Final_Total' => (string) (0));
                            $arr[] = (object)array('Date' => $data->entry_date,'Total_time' =>(string) (isset($totalsec)?gmdate('H:i',$totalsec):0),'Total_Earnings' =>(string)($date_final_total+$total_sal_short-$total_sal_bonus),'Deductions' => (string)$total_sal_short,'Bonus' =>(string)$total_sal_bonus,'Final_Total' => (string)$date_final_total);
                        }
                       
                    }
                    
                }
                
                $append .=                '<tr role="row" class="odd" style="background-color: #fff !important;">';
                $append .=                    '<td></td>';
                $append .=                    '<td><b>Total</b></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td><b>'.$totalamount.'</b></td>';
                $append .=                    '</tr>';
         }

          
            $append .=        '</tbody>';
            $append .=    '</table>';
            if($from_mode == 'REPORT')
            {
                return $append;
            }
            else if($from_mode == 'APP')
            {
                 //return response::json(['msg' => $msg,'report_data' =>$arr,'Total_Sum' => '']);
               return response::json(['msg' => $msg,'report_data' =>$arr,'Total_Sum' =>(string) $totalamount]);
            }
            
        }
    }
}
