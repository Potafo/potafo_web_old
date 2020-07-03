<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use DateTime;
use DateTimeZone;

class GeneralReportController extends Controller
{
    public function view_general_report()
    {
        $staffid = Session::get('staffid');
		 if(!$staffid){return redirect('');}
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('d-m-Y');
        $cat_test='';
         $order_cat = DB::SELECT("SELECT `order_list_cat` FROM `internal_staffs` WHERE `id`='".$staffid."'");
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
        $restraurents = DB::SELECT("SELECT `id`,`name_tagline`->>'$.name' as rest_name from restaurant_master r where r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and  a.staff_id = '$staffid') ".$cat_test." ORDER BY rest_name");
//      $restraurents = DB::SELECT("SELECT `id`,`name_tagline`->>'$.name' as rest_name FROM `restaurant_master` order by rest_name asc");
        $reports_all  = DB::SELECT("SELECT * FROM `report_master` WHERE `category`='general'");
        return view('reports.general.Salereport',compact('restraurents','reports_all','datetime'));
    }
    
    public function orderitem_search(Request $request)
    {
        $term    = $request['menu'];
        $rest_id = $request['rest_id'];
        $details = DB::SELECT("SELECT m_menu_id,JSON_UNQUOTE(m_name_type->'$.name') as name,JSON_UNQUOTE(`m_por_rate`) as portion,JSON_LENGTH(`m_por_rate`) as count,m_pack_rate FROM restaurant_menu where LOWER(m_name_type->>'$.name') LIKE '".strtolower($term)."%' and m_rest_id = '".trim($rest_id)."' and m_status = 'Y'");
        return $details;
    }
    
    public function filter_general_reports(Request $request)
    {
        $staff_id       =   $request['staff_id'];
        $restraurent    =   $request['restraurent'];
        $reports_name   =   $request['reports_name'];
        $order_cat_filter   =   $request['order_cat_filter'];
        $string = "";
        $strings = "";
        $append = '';
       
        $g_settings   = DB::SELECT("SELECT `decimal_digit` FROM `general_settings`");
        $num_format   = $g_settings[0]->decimal_digit; 
        $replaced = str_replace(' ', '_', $reports_name);
        
        if($reports_name == 'Sales_reports')
        {
            $from_date      =   date('Y-m-d',  strtotime($request['date_from'])); 
            $to_date        =   date('Y-m-d',  strtotime($request['date_to']));
            $paymode        =   $request['payment_mode'];
            $menu_name      =   $request['menu_name'];
            if($restraurent == 'select')
            {
                 $string .= "";
                 $strings .= "";
            }
            else
            {
                 $string .= "AND `rest_id`='$restraurent'";
                 $strings .= "AND od.`rest_id`='$restraurent'";
            }
           // if($order_cat_filter != 'All')
           // {
                if($order_cat_filter == 'Restaurant') 
                    {
                    $string .= " and r.`category`  <> 'Potafo Mart'";
                    //$strings .= " and r.`category`  <>'Potafo Mart'";
                }
                else if ($order_cat_filter == 'Potafo Mart')
                {
                    $string .= " and r.`category`  = 'Potafo Mart'";
                    //$strings .= " and r.`category`  = 'Potafo Mart'";
                }
            //}
            if($menu_name != '')
            {
                $strings .= "and `menu_details`->>'$.menu_name' = '$menu_name'";
            }
            if($paymode !='all')
            {
                $string .=  " and payment_method = '$paymode'";
                $strings .=  " and payment_method = '$paymode'";
            }
            $get_order_nums =   DB::SELECT("SELECT date(`order_date`) as date,payment_method,`final_total`,`total_details`->>'$.packing_charge' as rest_pack,`total_details`->>'$.delivery_charge' as rest_del,`total_details`->>'$.discount_amount' as rest_dis,`order_number`,`rest_details`->>'$.name' as htl_name,`customer_details`->>'$.name' as cs_name,`customer_details`->>'$.mobile' as mobile,`customer_details`->>'$.addressline2' as addressline2,`delivery_assigned_details`->>'$.name' as stf_name,`status_details`->>'$.D' as del_time,IFNULL(`payment_details`->>'$.razorpay_payment_id',0) as payment_id FROM `order_master`, restaurant_master r WHERE date(`order_date`) between '$from_date' and '$to_date' $string AND `current_status`='D' and order_master.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id  = '$staff_id')");
            $i=0;
            //$append.=$get_order_nums;
            //$ap1 ="SELECT date(`order_date`) as date,payment_method,`final_total`,`total_details`->>'$.packing_charge' as rest_pack,`total_details`->>'$.delivery_charge' as rest_del,`total_details`->>'$.discount_amount' as rest_dis,`order_number`,`rest_details`->>'$.name' as htl_name,`customer_details`->>'$.name' as cs_name,`customer_details`->>'$.mobile' as mobile,`customer_details`->>'$.addressline2' as addressline2,`delivery_assigned_details`->>'$.name' as stf_name,`status_details`->>'$.D' as del_time,IFNULL(`payment_details`->>'$.razorpay_payment_id',0) as payment_id FROM `order_master`, restaurant_master r WHERE date(`order_date`) between '$from_date' and '$to_date' $string AND `current_status`='D' and order_master.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id  = '$staff_id')";
            //$ap2="SELECT `menu_details`->>'$.menu_name'as menu,sum(((od.`single_rate_details`->>'$.exc_rate') * `qty`)) as exc_rate,sum(((od.`single_rate_details`->>'$.inc_rate') * `qty`)) as inc_rate,sum(((od.`single_rate_details`->>'$.extra_val') * `qty`)) as extra_val,sum(((od.`single_rate_details`->>'$.pack_rate') * `qty`)) as pack_rate,od.`menu_details`->>'$.single_rate' as single_rate,sum(od.`qty`) as qty,sum(od.`final_rate`) as total_rate,sum(od.`final_rate`)  as final_rate FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number='$order_number' $strings group by menu,single_rate order by menu desc";
           // return $ap1;
            foreach ($get_order_nums as $value)
            {
                $order_number = $value->order_number;
                $delivery= $value->rest_del;
                $all_details[$i] = DB::SELECT("SELECT `menu_details`->>'$.menu_name'as menu,sum(((od.`single_rate_details`->>'$.exc_rate') * `qty`)) as exc_rate,sum(((od.`single_rate_details`->>'$.inc_rate') * `qty`)) as inc_rate,sum(((od.`single_rate_details`->>'$.extra_val') * `qty`)) as extra_val,sum(((od.`single_rate_details`->>'$.pack_rate') * `qty`)) as pack_rate,od.`menu_details`->>'$.single_rate' as single_rate,sum(od.`qty`) as qty,sum(od.`final_rate`) as total_rate,sum(od.`final_rate`)  as final_rate FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number='$order_number' $strings group by menu,single_rate order by menu desc");
                $i++;
            }
                    $foottotal_excl = 0;
                    $foottotal_inc = 0;
                    $foottotal_extra_per = 0;
                    $foottotal_total_rates = 0;
                    $foottotal_final_rate = 0;
                    $Total_order = 0;
                    $footdlvcharge =0;
                   // $append.="SELECT `menu_details`->>'$.menu_name'as menu,sum(((od.`single_rate_details`->>'$.exc_rate') * `qty`)) as exc_rate,sum(((od.`single_rate_details`->>'$.inc_rate') * `qty`)) as inc_rate,sum(((od.`single_rate_details`->>'$.extra_val') * `qty`)) as extra_val,sum(((od.`single_rate_details`->>'$.pack_rate') * `qty`)) as pack_rate,od.`menu_details`->>'$.single_rate' as single_rate,sum(od.`qty`) as qty,sum(od.`final_rate`) as total_rate,sum(od.`final_rate`)  as final_rate FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number='$order_number' $strings group by menu,single_rate order by menu desc";
                    $append .=    '<table id="'.$replaced.'" class="table table-striped table-bordered">';
                    $append .=        '<thead>';
                    $append .=        '<tr style="font-size: 10px !important">';
                    $append .=            '<th style="min-width:40px">Sl No</th>';
                    $append .=            '<th style="min-width:200px">Item Name</th>';
                    $append .=            '<th style="min-width:200px">Excl Rate</th>';
                    $append .=            '<th style="min-width:140px">Inc Rate</th>';
                    $append .=            '<th style="min-width:140px">Extra Value</th>';
                    $append .=            '<th style="min-width:200px">Single Rate</th>';
                    $append .=            '<th style="min-width:200px">Quantity</th>';
                    $append .=            '<th style="min-width:100px">Item Pk chg</th>';
                    $append .=            '<th style="min-width:100px">Item Total</th>';
                    $append .=            '<th style="min-width:140px">Total Rate</th>';
                    $append .=            '<th style="min-width:80px">Res Pk chg</th>';
                    $append .=            '<th style="min-width:80px">Res Dlv chg</th>';
                    $append .=            '<th style="min-width:80px">Res Dis chg</th>';
                    $append .=            '<th style="min-width:80px">Order Total</th>';
                    $append .=        '</tr>';
                    $append .=        '</thead>';
                    $append .=        '<tbody>';
                    if(count($get_order_nums) != 0)
                    {
                        $j=0;
                             foreach ($get_order_nums as $value) {
                                 $k = 0;
                                 $total_excl = 0;
                                 $total_inc = 0;
                                 $total_extra_per = 0;
                                 $total_total_rates = 0;
                                 $total_final_rate = 0;
                                 $dlvcharge = 0;
                                 $order_total = $value->final_total;
                                 $total_charges = ($value->rest_pack + $value->rest_del) + $value->rest_dis;

                                 if (count($all_details[$j]) > 0) {
                                     $append .= '<tr role="row" class="odd" style="background-color: beige !important;">';
                                     $append .= '<td>' . count($all_details[$j]) . '</td>';
                                     $append .= '<td><strong>' . $value->date . '</strong></td>';
                                     $append .= '<td><strong>Name - ' . $value->cs_name . '</strong></td>';
                                     $append .= '<td><strong>' . $value->htl_name . '</strong></td>';
                                     $append .= '<td><strong>Order - ' . $value->order_number . '</strong></td>';
                                     $append .= '<td><strong>Mobile - ' . $value->mobile . '</strong></td>';
                                     $append .= '<td><strong>Address - ' . $value->addressline2 . '</strong></td>';
                                     $append .= '<td><strong>Staff - ' . $value->stf_name . '</strong></td>';
                                     $append .= '<td><strong>Del Time - ' . $value->del_time . '</strong></td>';
                                     $append .= '<td><strong>Pay Mode - ' . $value->payment_method . '</strong></td>';
                                     if ($value->payment_id != '0') {
                                         $append .= '<td style="border-right: none;"><strong>Payment Id</strong></td>';
                                         $append .= '<td><strong>' . $value->payment_id . '</strong></td>';
                                     } else {
                                         $append .= '<td style="border-right: none;"><strong></strong></td>';
                                         $append .= '<td><strong></strong></td>';
                                     }
                                     $append .= '<td><strong></strong></td>';
                                     $append .= '<td><strong></strong></td>';
                                     $append .= '</tr>';
                                 }
                                 foreach ($all_details[$j] as $val) {
                                     $k++;
                                     $tot_exc_rate = $val->exc_rate;
                                     $tot_inc_rate = $val->inc_rate;
                                     $tot_extra_val = $val->extra_val;
                                     $tot_toal = $val->total_rate;
                                     $tot_final = $val->final_rate;
                                     $dlv_charge = $delivery;


                                     $total_excl += $tot_exc_rate;
                                     $total_inc += $tot_inc_rate;
                                     $total_extra_per += $tot_extra_val;
                                     $total_total_rates += $tot_toal;
                                     $total_final_rate += $tot_final;
                                     $dlvcharge += $dlv_charge;

                                     $append .= '<tr role="row" class="odd" style="background-color: #e9f5ff !important;">';
                                     $append .= '<td>' . $k . '</td>';
                                     $append .= '<td>' . $val->menu . '</td>';
                                     $append .= '<td>' . number_format($val->exc_rate, $num_format) . '</td>';
                                     $append .= '<td>' . number_format($val->inc_rate, $num_format) . '</td>';
                                     $append .= '<td>' . number_format($val->extra_val, $num_format) . '</td>';
                                     $append .= '<td>' . number_format(($val->single_rate - $val->pack_rate), $num_format) . '</td>';
                                     $append .= '<td>' . $val->qty . '</td>';
                                     $append .= '<td>' . number_format($val->pack_rate, $num_format) . '</td>';
                                     $append .= '<td>' . number_format($val->final_rate, $num_format) . '</td>';
                                     $append .= '<td>' . number_format((float)$val->total_rate, $num_format) . '</td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '</tr>';

                                     $foottotal_excl += $tot_exc_rate;
                                     $foottotal_inc += $tot_inc_rate;
                                     $foottotal_extra_per += $tot_extra_val;
                                     $foottotal_total_rates += $tot_toal;
                                     $foottotal_final_rate += $tot_final;
                                     $footdlvcharge += $dlv_charge;
                                 }
                                 $j++;
                                 $Total_order += $order_total;
                                 if ($total_final_rate != '0') {
                                     $append .= '<tr role="row" class="odd" style="background-color: #e2e2e2 !important;">';
                                     $append .= '<td></td>';
                                     $append .= '<td><strong>Total</strong></td>';
                                     $append .= '<td><strong>' . $total_excl . '</strong></td>';
                                     $append .= '<td><strong>' . $total_inc . '</strong></td>';
                                     $append .= '<td><strong>' . $total_extra_per . '</strong></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td><strong>' . $total_final_rate . '</strong></td>';
                                     $append .= '<td>' . $total_total_rates . '</td>';
                                     $append .= '<td>' . $value->rest_pack . '</td>';
                                     $append .= '<td>' . $value->rest_del . '</td>';
                                     $append .= '<td>' . $value->rest_dis . '</td>';
                                     $append .= '<td><strong>' . number_format($order_total, $num_format) . '</strong></td>';
                                     $append .= '</tr>';
                                     $append .= '<tr role="row" class="odd" style="background-color: #fff !important;">';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '<td></td>';
                                     $append .= '</tr>';
                                 }
                             }
                    }

                    $append .=                '<tr role="row" class="odd" style="background-color: #fff !important;display: none">';
                    $append .=                    '<td></td>';
                    $append .=                    '<td><strong>Total</strong></td>';
                    $append .=                    '<td>'.$foottotal_excl.'</td>';
                    $append .=                    '<td>'.$foottotal_inc.'</td>';
                    $append .=                    '<td>'.$foottotal_extra_per.'</td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td>'.$foottotal_final_rate.'</td>';
                    $append .=                    '<td>'.$foottotal_total_rates.'</td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td>'.number_format($Total_order, $num_format).'</td>';
                    $append .=                '</tr>';
                    $append .=        '</tbody>';
                    $append .=        '<tfoot>';
                    $append .=                '<tr role="row" class="odd" style="background-color: #fff !important;">';
                    $append .=                    '<td></td>';
                    $append .=                    '<td><strong>Total</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_excl.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_inc.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_extra_per.'</strong></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    
                    $append .=                    '<td></td>';
                    $append .=                    '<td><strong>'.$foottotal_final_rate.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_total_rates.'</strong></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td><strong>'.$footdlvcharge.'</strong></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td><strong>'.number_format($Total_order, $num_format).'</strong></td>';
                    $append .=                '</tr>';
                    $append .=    '</tfoot>';
                    $append .=    '</table>';

                    return $append;
            
            }
                elseif($reports_name == 'Item_sale_report'){
                    $strings='';
                $restraurent    =   $request['restraurent'];        
                $reports_name   =   $request['reports_name'];  
                $menu_name           =   $request['menu_name'];
                if($menu_name == '')
                {
                    $string .= " ";
                }else{
                    $string .= "and `menu_details`->>'$.menu_name' = '$menu_name'";
                }
                
                if($request['date_from'] != '' && $request['date_to'] != '')
                {
                    $from_date      =   date('Y-m-d',  strtotime($request['date_from'])); 
                    $to_date        =   date('Y-m-d',  strtotime($request['date_to'])); 

                    $string .= " and date(om.`order_date`) between '$from_date' and '$to_date' ";
                }else{
                          $string .= " ";
                }
                   // if(isset($order_cat_filter))
                    //{
                        if($order_cat_filter == 'Restaurant') {
                            $strings .= " and r.`category`  <> 'Potafo Mart'";
                        }
                        else if ($order_cat_filter == 'Potafo Mart')
                        {
                            $strings .= " and r.`category`  = 'Potafo Mart'";
                        }
                    //}
                if($restraurent == 'select')
                {
                    $string .= "";
                }else{
                    $string .= " and od.`rest_id` = '$restraurent'";
                }
                    $append .= '';
                    $menu_names = DB::SELECT("SELECT trim(`menu_details`->>'$.menu_name') as mname FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number  left join restaurant_master r on om.`rest_id` = r.id  WHERE od.rest_id = '$restraurent' $string $strings and om.current_status = 'D' and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id  = '$staff_id') group by mname");
                    $i=0;
                    //$pp="SELECT trim(`menu_details`->>'$.menu_name') as mname FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number  left join restaurant_master r on om.`rest_id` = r.id  WHERE od.rest_id = '$restraurent' $string and om.current_status = 'D' and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id  = '$staff_id') group by mname";
                    
                    foreach ($menu_names as $value)
                    {
                        $menu = $value->mname;
                        $all_details[$i] = DB::SELECT("SELECT date(om.`order_date`) as date,sum(((od.`single_rate_details`->>'$.exc_rate') * `qty`)) as exc_rate,sum(((od.`single_rate_details`->>'$.inc_rate') * `qty`)) as inc_rate,sum(((od.`single_rate_details`->>'$.extra_val') * `qty`)) as extra_val,sum(((od.`single_rate_details`->>'$.pack_rate') * `qty`)) as pack_rate,od.`menu_details`->>'$.single_rate' as single_rate,sum(od.`qty`) as qty,sum(od.`final_rate`) as total_rate, sum(od.`final_rate`) as final_rate FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE om.current_status = 'D' AND od.`menu_details`->>'$.menu_name'='$menu' $string group by date,single_rate order by date desc");
                        
                        $i++;
                        
                        
                   }
                $foottotal_excl = 0;
                $foottotal_inc = 0;
                $foottotal_extra_per = 0;
                $foottotal_total_rates = 0;
                $foottotal_final_rate = 0;

                $append .=    '<table id="'.$replaced.'" class="table table-striped table-bordered">';
                $append .=        '<thead>';
                $append .=        '<tr>';
                $append .=            '<th style="min-width:40px">Sl No</th>';
                $append .=            '<th style="min-width:200px">Date</th>';
                $append .=            '<th style="min-width:100px">Excl Rate</th>';
                $append .=            '<th style="min-width:100px">Inc Rate</th>';
                $append .=            '<th style="min-width:100px">Extra Value</th>';
                $append .=            '<th style="min-width:100px">Single Rate</th>';
                $append .=            '<th style="min-width:100px">Quantity</th>';
                $append .=            '<th style="min-width:100px">Item discount</th>';
                $append .=            '<th style="min-width:100px">Pack Chrg</th>';
                $append .=            '<th style="min-width:100px">Total Rate</th>';
                $append .=            '<th style="min-width:100px">Final Total</th>';
                $append .=        '</tr>';
                $append .=        '</thead>';

                $append .=        '<tbody>';
                        if(count($menu_names) != 0)
                        {

                            $j=0;

                            foreach ($menu_names as $value) 
                            {
                                $k=0;
                                $total_excl = 0;
                                $total_inc = 0;
                                $total_extra_per = 0;
                                $total_total_rates = 0;
                                $total_final_rate = 0;

                $append .=                '<tr role="row" class="odd" style="background-color: beige !important;">';
                $append .=                    '<td></td>';
                $append .=                    '<td><strong>'.$value->mname.'</strong></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td><strong></strong></td>';
                $append .=                '</tr>';


                                foreach ($all_details[$j] as $val) 
                                {
                                    $k++;

                                            $tot_exc_rate = $val->exc_rate;
                                            $tot_inc_rate = $val->inc_rate;
                                            $tot_extra_val = $val->extra_val;
                                            $tot_toal = $val->total_rate;
                                            $tot_final = $val->final_rate;


                                            $total_excl += $tot_exc_rate;
                                            $total_inc += $tot_inc_rate;
                                            $total_extra_per += $tot_extra_val;
                                            $total_total_rates += $tot_toal;
                                            $total_final_rate += $tot_final;

                $append .=                     '<tr role="row" class="odd" style="background-color: #e9f5ff !important;">';
                $append .=                        '<td>'.$k.'</td>';
                $append .=                        '<td>'.$val->date.'</td>';
                $append .=                        '<td>'.number_format($val->exc_rate,$num_format).'</td>';
                $append .=                        '<td>'.number_format($val->inc_rate,$num_format).'</td>';
                $append .=                        '<td>'.number_format($val->extra_val,$num_format).'</td>';
                $append .=                        '<td>'.number_format(($val->single_rate - $val->pack_rate),$num_format).'</td>';
                $append .=                        '<td>'.$val->qty.'</td>';
                if($val->total_rate < $val->exc_rate)
                {
                    $append .=                        '<td>Yes</td>';
                }
                else
                {
                    $append .=                        '<td>No</td>';
                }
                $append .=                        '<td>'.number_format($val->pack_rate,$num_format).'</td>';
                $append .=                        '<td>'.number_format((float)$val->total_rate, $num_format).'</td>';
                $append .=                        '<td><strong>'.number_format($val->final_rate,$num_format).'</strong></td>';
                $append .=                    '</tr>';

                                $foottotal_excl += $tot_exc_rate;
                                $foottotal_inc += $tot_inc_rate;
                                $foottotal_extra_per += $tot_extra_val;
                                $foottotal_total_rates += $tot_toal;
                                $foottotal_final_rate += $tot_final; 
                                }
                                $j++;

                $append .=                     '<tr role="row" class="odd" style="background-color: #e2e2e2 !important;">';
                $append .=                        '<td></td>';
                $append .=                        '<td><strong>Total</strong></td>';
                $append .=                        '<td>'.number_format($total_excl,$num_format).'</td>';
                $append .=                        '<td>'.number_format($total_inc,$num_format).'</td>';
                $append .=                        '<td>'.number_format($total_extra_per,$num_format).'</td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td>'.number_format($total_total_rates,$num_format).'</td>';
                $append .=                        '<td>'.number_format($total_final_rate,$num_format).'</td>';
                $append .=                    '</tr>';    

                $append .=                '<tr role="row" class="odd" style="background-color: white !important;">';
                $append .=                    '<td></td>';
                $append .=                    '<td></strong></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td></td>';
                $append .=                    '<td><strong></strong></td>';
                $append .=                '</tr>';
                            }

                        }

                $append .=                     '<tr role="row" class="odd" style="display: none">';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                    '</tr>';      

                 $append .=                     '<tr role="row" class="odd" style="display: none">';
                $append .=                        '<td></td>';
                $append .=                        '<td><strong>Total</strong></td>';
                $append .=                        '<td>'.number_format($foottotal_excl,$num_format).'</td>';
                $append .=                        '<td>'.number_format($foottotal_inc,$num_format).'</td>';
                $append .=                        '<td>'.number_format($foottotal_extra_per,$num_format).'</td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td>'.number_format($foottotal_total_rates,$num_format).'</td>';
                $append .=                        '<td>'.number_format($foottotal_final_rate,$num_format).'</td>';
                $append .=                    '</tr>';

                $append .=        '</tbody>';

                $append .=        '<tfoot>';
                $append .=                     '<tr role="row" class="odd">';
                $append .=                        '<td></td>';
                $append .=                        '<td><strong>Total</strong></td>';
                $append .=                        '<td>'.number_format($foottotal_excl,$num_format).'</td>';
                $append .=                        '<td>'.number_format($foottotal_inc,$num_format).'</td>';
                $append .=                        '<td>'.number_format($foottotal_extra_per,$num_format).'</td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td></td>';
                $append .=                        '<td>'.number_format($foottotal_total_rates,$num_format).'</td>';
                $append .=                        '<td>'.number_format($foottotal_final_rate,$num_format).'</td>';
                $append .=                    '</tr>';
                $append .=    '</tfoot>';
                $append .=    '</table>';

                return $append;
                }
  
            else if($reports_name == 'Order_summary'){
            $from_date      =   date('Y-m-d',  strtotime($request['date_from'])); 
            $to_date        =   date('Y-m-d',  strtotime($request['date_to'])); 
            $restraurent    =   $request['restraurent'];
            $paymode    =   $request['payment_mode'];
            if($restraurent == 'select')
            {
                 $string .= "";
                 $strings .= "";
            }
            else
            {
                 $string .= "AND `rest_id`='$restraurent'";
                 $strings .= "AND od.`rest_id`='$restraurent'";
            }
                if(isset($order_cat_filter))
                {
                    if($order_cat_filter == 'Restaurant') {
                        $string .= "and r.`category`  <> 'Potafo Mart'";
                    }
                    else if ($order_cat_filter == 'Potafo Mart')
                    {
                        $string .= "and r.`category`  = 'Potafo Mart'";
                    }
                }
            if($paymode !='all')
            {
                    $string .=  " and payment_method = '$paymode'";
            }
            $get_order_nums =   DB::SELECT("SELECT date(`order_date`) as date,order_number,`final_total`,`total_details`->>'$.packing_charge' as rest_pack,ifnull(`coupon_details`->>'$.coupon_amount',0) as coupon_amount,`total_details`->>'$.delivery_charge' as rest_del,`total_details`->>'$.discount_amount' as rest_dis,`order_number`,`rest_details`->>'$.name' as htl_name,`customer_details`->>'$.name' as cs_name,`customer_details`->>'$.mobile' as mobile,`delivery_assigned_details`->>'$.name' as stf_name,`delivery_assigned_details`->>'$.phone' as stf_phone,`status_details`->>'$.D' as del_time FROM `order_master` ,restaurant_master r WHERE date(`order_date`) between '$from_date' and '$to_date' $string  AND `current_status`='D' and order_master.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id  = '$staff_id') order by order_date desc");
            $i=0;
            $foottotal_taxrate = 0;
            $footcoupon_charge = 0;
            $foottotal_excl = 0;
            $foottotal_inc = 0;
            $foottotal_extra_per = 0;
            $foottotal_total_rates = 0;
            $footpack_charge = 0;
            $footpack_itemrate =0;
            $footdel_charge = 0;
            $footdis_charge = 0;
            $foottotal_order = 0;
                    $append .=    '<table id="'.$replaced.'" class="table table-striped table-bordered">';
                    $append .=        '<thead>';
                    $append .=        '<tr style="font-size: 10px !important">';
                    $append .=            '<th style="min-width:40px">Sl No</th>';
                    $append .=            '<th style="min-width:100px">Order Number</th>';
                    $append .=            '<th style="min-width:100px">Date</th>';
                    $append .=            '<th style="min-width:150px">Customer</th>';
                    $append .=            '<th style="min-width:150px">Contact</th>';
                    $append .=            '<th style="min-width:150px">Restaurant</th>';
                    $append .=            '<th style="min-width:100px">Excl Rate</th>';
                    $append .=            '<th style="min-width:100px">Tax</th>';
                    $append .=            '<th style="min-width:100px">Inc Rate</th>';
                    $append .=            '<th style="min-width:100px">Extra Value</th>';
                    $append .=            '<th style="min-width:100px">Total Rate</th>';
                    $append .=            '<th style="min-width:100px">Item Pk chg</th>';
                    $append .=            '<th style="min-width:80px">Res Pk chg</th>';
                    $append .=            '<th style="min-width:80px">Res Dlv chg</th>';
                    $append .=            '<th style="min-width:80px">Res Disc</th>';
                    $append .=            '<th style="min-width:100px">Coupon Amt</th>';
                    $append .=            '<th style="min-width:80px">Order Total</th>';
                    $append .=            '<th style="min-width:80px">Pay Mode</th>';
                    $append .=            '<th style="min-width:80px">Payment Id</th>';
                    $append .=            '<th style="min-width:80px">Order Id</th>';
                    $append .=            '<th style="min-width:150px">Delivery Boy</th>';
                    $append .=            '<th style="min-width:150px">Delivery Boy Num</th>';
                    $append .=        '</tr>';
                    $append .=        '</thead>';

                    $append .=        '<tbody>';
                foreach ($get_order_nums as $value) {
                $order_number = $value->order_number;
                $pkcharge = $value->rest_pack;
                $dlcharge = $value->rest_del;
                $discharge = $value->rest_dis;
                $totalorder = $value->final_total;
                //return "SELECT od.order_number,ifnull(`coupon_details`->>'$.coupon_amount',0) as coupon_amount,sum(((od.`single_rate_details`->>'$.exc_rate') * `qty`)) as exc_rate,sum(((od.`single_rate_details`->>'$.inc_rate') * `qty`)) as inc_rate,sum(((od.`single_rate_details`->>'$.extra_val') * `qty`)) as extra_val,sum(od.`qty`*od.`single_rate_details`->>'$.pack_rate') as pack_rate,sum(od.`qty`) as qty,sum(od.`final_rate`) as total_rate, sum(od.`single_rate_details`->>'$.pack_rate') + sum(od.`final_rate`) as final_rate,IFNULL(`payment_details`->>'$.razorpay_payment_id',0) as paymode,IFNULL(`payment_details`->>'$.razorpay_order_id',0) as orderid,payment_method FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number='$order_number' GROUP BY od.order_number";
                $all_details= DB::SELECT("SELECT od.order_number,ifnull(`coupon_details`->>'$.coupon_amount',0) as coupon_amount,sum(((od.`single_rate_details`->>'$.exc_rate') * `qty`)) as exc_rate,sum(((od.`single_rate_details`->>'$.inc_rate') * `qty`)) as inc_rate,sum(((od.`single_rate_details`->>'$.extra_val') * `qty`)) as extra_val,sum(od.`qty`*od.`single_rate_details`->>'$.pack_rate') as pack_rate,sum(od.`qty`) as qty,sum(od.`final_rate`) as total_rate, sum(od.`single_rate_details`->>'$.pack_rate') + sum(od.`final_rate`) as final_rate,IFNULL(`payment_details`->>'$.razorpay_payment_id',0) as paymode,IFNULL(`payment_details`->>'$.razorpay_order_id',0) as orderid,payment_method FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number='$order_number' GROUP BY od.order_number");
               if(count($all_details)!=0) {
                   $total_excl= $all_details[0]->exc_rate;
                    $total_inc= $all_details[0]->inc_rate;
                    $total_extra_per= $all_details[0]->extra_val;
                    $total_total_rates= $all_details[0]->total_rate;
                    $packrate= $all_details[0]->pack_rate;
                    $paymode= $all_details[0]->paymode;
                    $orderid= $all_details[0]->orderid;
                    $taxrate = ($total_inc - $total_excl);
                    $coupon_amount= $all_details[0]->coupon_amount;
                    $payment_mode= $all_details[0]->payment_method; $check='T';
               } else{
                     $total_excl= 0;
                    $total_inc= 0;
                    $total_extra_per= 0;
                    $total_total_rates= 0;
                    $packrate= 0;
                    $paymode= 0;
                    $orderid= 0;
                    $taxrate = ($total_inc - $total_excl);
                    $coupon_amount= 0;
                    $payment_mode= 0;
                    $check='F';
               }
                
                    if($paymode == '0')
                    {
                        $paymode = '';
                    }
                    else
                    {
                        $paymode=$paymode;
                    }
                    if(strtoupper($payment_mode) == 'COD')
                    {
                        $orderid = '';
                    }
                    else{
                        $orderid=$orderid;
                    }

                                    $i++;
                                    $append .=                '<tr role="row" class="odd" style="background-color: beige !important;">';
                                    $append .=                    '<td>'.$i.'</td>';
                                    $append .=                    '<td><strong>'.$value->order_number.'</strong></td>';
                                    $append .=                    '<td><strong>'.$value->date.'</strong></td>';
                                    $append .=                    '<td><strong>'.$value->cs_name.'</strong></td>';
                                    $append .=                    '<td><strong>'.$value->mobile.'</strong></td>';
                                    $append .=                    '<td><strong>'.$value->htl_name.'</strong></td>';
                                    $append .=                    '<td><strong>'.$total_excl.'</strong></td>';
                                    $append .=                    '<td><strong>'.$taxrate.'</strong></td>';
                                    $append .=                    '<td><strong>'.$total_inc.'</strong></td>';
                                    $append .=                    '<td><strong>'.$total_extra_per.'</strong></td>';
                                    $append .=                    '<td><strong>'.$total_total_rates.'</strong></td>';
                                    $append .=                    '<td><strong>'.$packrate.'</strong></td>';
                                    $append .=                    '<td><strong>'.$value->rest_pack.'</strong></td>';
                                    $append .=                    '<td><strong>'.$value->rest_del.'</strong></td>';
                                    $append .=                    '<td><strong>'.$value->rest_dis.'</strong></td>';
                                    $append .=                    '<td><strong>'.$value->coupon_amount.'</strong></td>';
                                    $append .=                    '<td><strong>'.$value->final_total.'</strong></td>';
                                    $append .=                    '<td><strong>'.$payment_mode.'</strong></td>';
                                    $append .=                    '<td><strong>'.$paymode.'</strong></td>';
                                    $append .=                    '<td><strong>'.$orderid.'</strong></td>';
                                    $append .=                    '<td><strong>'.$value->stf_name.'</strong></td>';
                                    $append .=                    '<td><strong>'.$value->stf_phone.'</strong></td>';
                                    $append .=                '</tr>';
                                    $foottotal_excl += $total_excl;
                                    $foottotal_inc += $total_inc;
                                    $foottotal_extra_per += $total_extra_per;
                                    $foottotal_total_rates += $total_total_rates;
                                    $footpack_charge += $pkcharge;
                                    $footpack_itemrate += $packrate;
                                    $footdel_charge += $dlcharge;
                                    $footdis_charge += $discharge;
                                    $footcoupon_charge += $coupon_amount;
                                    $foottotal_order += $totalorder;
                                    $foottotal_taxrate += $taxrate;
                }
                    $append .=                '<tr role="row" class="odd" style="background-color: #fff !important;display: none">';
                    $append .=                    '<td></td>';
                    $append .=                    '<td><strong>Total</strong></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td><strong>'.$foottotal_excl.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_taxrate.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_inc.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_extra_per.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_total_rates.'</strong></td>';
                    $append .=                    '<td><strong>'.$footpack_itemrate.'</strong></td>';
                    $append .=                    '<td><strong>'.$footpack_charge.'</strong></td>';
                    $append .=                    '<td><strong>'.$footdel_charge.'</strong></td>';
                    $append .=                    '<td><strong>'.$footdis_charge.'</strong></td>';
                    $append .=                    '<td><strong>'.$footcoupon_charge.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_order.'</strong></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                '</tr>';
                    $append .=        '</tbody>';
                    $append .=        '<tfoot>';
                    $append .=                '<tr role="row" class="odd" style="background-color: #fff !important;">';
                    $append .=                    '<td></td>';
                    $append .=                    '<td><strong>Total</strong></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td><strong>'.$foottotal_excl.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_taxrate.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_inc.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_extra_per.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_total_rates.'</strong></td>';
                    $append .=                    '<td><strong>'.$footpack_itemrate.'</strong></td>';
                    $append .=                    '<td><strong>'.$footpack_charge.'</strong></td>';
                    $append .=                    '<td><strong>'.$footdel_charge.'</strong></td>';
                    $append .=                    '<td><strong>'.$footdis_charge.'</strong></td>';
                    $append .=                    '<td><strong>'.$footcoupon_charge.'</strong></td>';
                    $append .=                    '<td><strong>'.$foottotal_order.'</strong></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                    '<td></td>';
                    $append .=                '</tr>';
                    $append .=    '</tfoot>';
                    $append .=    '</table>';
                    return $append;
                }
                else if($reports_name == 'Cancellation_report')
                {
                    if(isset($order_cat_filter))
                    {
                        if($order_cat_filter == 'Restaurant') {
                            $string .= "and r.`category`  != 'Potafo Mart'";
                        }
                        else if ($order_cat_filter == 'Potafo Mart')
                        {
                            $string .= "and r.`category`  = 'Potafo Mart'";
                        }
                    }
            $from_date      =   date('Y-m-d',  strtotime($request['date_from'])); 
            $to_date        =   date('Y-m-d',  strtotime($request['date_to']));
            $get_order_nums =   DB::SELECT("SELECT date(`order_date`) as date,order_number,`cancel_reason`,`status_details`->>'$.CA' as cancel_time,payment_method,`payment_details`->>'$.refundid' as refund_id FROM `order_master`,restaurant_master r WHERE date(`order_date`) between '$from_date' and '$to_date' AND `current_status`='CA' $string and order_master.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staff_id') order by order_date desc");
//          $get_order_nums =   DB::SELECT("SELECT date(`order_date`) as date,order_number,`cancel_reason`,`status_details`->>'$.CA' as cancel_time,payment_method,`payment_details`->>'$.refundid' as refund_id FROM `order_master` WHERE date(`order_date`) between '$from_date' and '$to_date' AND `current_status`='CA' order by order_date desc");
            $i=0;
                    $append .=    '<table id="'.$replaced.'" class="table table-striped table-bordered">';
                    $append .=        '<thead>';
                    $append .=        '<tr style="font-size: 10px !important">';
                    $append .=            '<th style="min-width:50px">Sl No</th>';
                    $append .=            '<th style="min-width:100px">Date</th>';
                    $append .=            '<th style="min-width:150px">Order Number</th>';
                    $append .=            '<th style="min-width:250px">Cancellation Reason</th>';
                    $append .=            '<th style="min-width:100px">Time</th>';
                    $append .=            '<th style="min-width:150px">Payment Mode</th>';
                    $append .=            '<th style="min-width:150px">Refund ID</th>';
                    $append .=        '</tr>';
                    $append .=        '</thead>';
                    $append .=        '<tbody>';
                foreach ($get_order_nums as $value) {
                                    $i++;
                                    $append .=                '<tr role="row" class="odd" style="background-color: beige !important;">';
                                    $append .=                    '<td>'.$i.'</td>';
                                    $append .=                    '<td>'.date('d-m-Y',  strtotime($value->date)).'</strong></td>';
                                    $append .=                    '<td>'.$value->order_number.'</td>';
                                    $append .=                    '<td>'.$value->cancel_reason.'</td>';
                                    $append .=                    '<td>'.$value->cancel_time.'</td>';
                                    $append .=                    '<td>'.$value->payment_method.'</td>';
                                    $append .=                    '<td>'.$value->refund_id.'</td>';
                                    $append .=                '</tr>';
                 
                }
                    $append .=        '</tbody>';
                    $append .=    '</table>';
                    return $append;
                }

    }

    public function get_restaurants_category($type)
    {
        $string='';
        $append ='';
        $staffid = Session::get('staffid');
        if(isset($type))
        {
            if($type == 'Restaurant') {
                $string .= " and r.`category`  != 'Potafo Mart'";
            }
            else if ($type == 'Potafo Mart')
            {
                $string .= " and r.`category`  = 'Potafo Mart'";
            }
        }
        $restraurents = DB::SELECT("SELECT `id`,`name_tagline`->>'$.name' as rest_name from restaurant_master r where r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id  and s.id = a.staff_id and  a.staff_id = '$staffid') $string  ORDER BY rest_name");
       
        $append .= "<option value='select'>Select</option>";
        if(count($restraurents)>0) {
            foreach ($restraurents as $item => $val) {
                
                $append .= "<option value='" . $val->id . "'>" . $val->rest_name . "</option>";
            }
        }
          return $append;
    }
}
