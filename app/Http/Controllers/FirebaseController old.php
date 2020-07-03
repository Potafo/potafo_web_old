<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use DB;
use Response;
use Helpers\Commonsource;
use DateTime;
use DateTimeZone;
use Helpers\Datasource;

class FirebaseController extends Controller
{
    public function __construct()
    { // add this construct to firebase using pages and also 3 use Kreait\
        $this->jsonfile     = config('firebase.fb_jsonfile');
        $this->db_uri     = config('firebase.fb_dburi');
        $serviceAccount = ServiceAccount::fromJsonFile($this->jsonfile);
        $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri($this->db_uri)
        ->create();
        $this->firebase_db = $firebase->getDatabase();
    }
    public function fb_restmaster()
    {
         $database = $this->firebase_db;
      
        $database->getReference('restaurant_master')->remove();  //to delte a node
    /**************************************** bulk insert *******************************/
      $cityids=''; 
     $restlist_data='';
     $restlist_id='';
    $citylist = DB::SELECT("SELECT id FROM `city` WHERE `active`='Y' Order By id DESC");
     if(count($citylist)>0)
        {
             foreach($citylist as $key=>$item)
            {
                $cityids[] =$item->id;
                $restlist = DB::SELECT('SELECT `id`,`name_tagline`->>"$.name" as name,`geo_cordinates` FROM `restaurant_master` WHERE `city`="'.$item->id.'" AND status="Y"  ');//limit 0,5
                if(count($restlist)>0) 
                    {
                         foreach($restlist as $key=>$items)
                        {
                            $restlist_id['rest_id']=$items->id;
                            $restlist_data['rest_name']=$items->name;
                            $cord = explode(',',$items->geo_cordinates);
                            $restlist_data['rest_lat']=$cord[0];
                            $restlist_data['rest_long']=$cord[1];
                            $newUserKey = $database->getReference('restaurant_master')->getChild($item->id)->getChild($items->id)->update($restlist_data); 
                        }
                    }
            }
        }
   
        /**************************************** single insert*/
       /* $restlist_data['rest_name']="jes test123";             
        $restlist_data['rest_lat']="10.23";
        $restlist_data['rest_long']="20.3";
      $newUserKey = $database->getReference('restaurant_master')->getChild("3")->getChild("303")->update($restlist_data);          
        */ 
         
    $reference = $database->getReference('/restaurant_master');       
        $snapshot = $reference->getSnapshot();
        $k1 = $snapshot->getValue();
        //print_r($k1);
        if(!empty($k1)) 
         {
            echo "inserted successfully";
        } else {
            echo "error";
        }
        
            }
    public function index_set_old()
    {
       
        $database = $this->firebase_db;
        $mode_optn='';
        $order_number='20031000035';
        $delivery_staff='668';
        $staff_id='657';
         
        if($mode_optn=='changestaff')
        {
         // remove th specified order number from this delivery staff    
        $database->getReference('location')->getChild($delivery_staff)->getChild("orders")->getChild($order_number)->remove();         
        //decrease current order count of the delivery staff 
        $count_orderno = $database->getReference('location')->getChild($delivery_staff)->getChild("current_order")->getChild("order_count")->getValue();          
        $count_orderno_ct['order_count']= $count_orderno - 1;
        $database->getReference('location')->getChild($delivery_staff)->getChild("current_order")->update($count_orderno_ct);          
        
        // increase the count to the assigned staff
         $count_orderno = $database->getReference('location')->getChild($staff_id)->getChild("current_order")->getChild("order_count")->getValue();          
        $count_orderno_staf['order_count']= $count_orderno + 1;
        $database->getReference('location')->getChild($staff_id)->getChild("current_order")->update($count_orderno_staf);          
        
        // add the details
        $insrt_orders = DB::SELECT("select rest_details->>'$.name' as restname,customer_details->>'$.latitude' as latitude,customer_details->>'$.longitude' as longitude FROM order_master WHERE order_number = '$order_number'");
        $restlist_data['rest_name']=$insrt_orders[0]->restname;             
        $restlist_data['cut_lat']=$insrt_orders[0]->latitude;
        $restlist_data['cust_long']=$insrt_orders[0]->longitude;
         $newUserKey = $database->getReference('location')->getChild($staff_id)->getChild("orders")->getChild($order_number)->update($restlist_data);          
  
        }else
        {
            // increase th count of th delivery staff
           $count_orderno = $database->getReference('location')->getChild($delivery_staff)->getChild("current_order")->getChild("order_count")->getValue(); 
           $count_orderno_ct['order_count']= $count_orderno + 1;
        $database->getReference('location')->getChild($delivery_staff)->getChild("current_order")->update($count_orderno_ct); 
        //add the details
        $insrt_orders = DB::SELECT("select rest_details->>'$.name' as restname,customer_details->>'$.latitude' as latitude,customer_details->>'$.longitude' as longitude FROM order_master WHERE order_number = '$order_number'");
        $restlist_data['rest_name']=$insrt_orders[0]->restname;             
        $restlist_data['cut_lat']=$insrt_orders[0]->latitude;
        $restlist_data['cust_long']=$insrt_orders[0]->longitude;
         $newUserKey = $database->getReference('location')->getChild($delivery_staff)->getChild("orders")->getChild($order_number)->update($restlist_data);          
  
        }
    }
    public function insertrest_firebase(Request $request)
    {       
        $lat=$request['lat'];
        $long=$request['long'];      
        $rname=$request['rname'];      
        $resultid=$request['resultid'];
        $city=$request['city']; 
        $status=$request['status'];
        
     /*  $newval='';
        $restlist_data='';
        $lastinsertedid=DB::SELECT('SELECT id FROM `restaurant_master` WHERE name_tagline->>"$.name"="'.$rname.'" AND group_id="'.$group.'" AND category="'.$category.'" AND city="'.$city.'" AND country->>"$.country"="'.$country.'"');
        foreach($lastinsertedid as $valt){
           $newval=$valt->id;
        }*/
        
        $restlist_data['rest_name']=$rname;
        $restlist_data['rest_lat']=$lat;
        $restlist_data['rest_long']=$long;
        $database = $this->firebase_db;
        if($status=="insert")
        {
        $newUserKey = $database->getReference('restaurant_master')->getChild($city)->getChild($resultid)->update($restlist_data); 
        }else if($status=="update")
        {
            $edstatus=$request['edstatus'];
            if($edstatus=="Y")
            {
           $database->getReference('restaurant_master')->getChild($city)->getChild($resultid)->remove(); 
           $newUserKey = $database->getReference('restaurant_master')->getChild($city)->getChild($resultid)->update($restlist_data); 
            }else
            {
               $database->getReference('restaurant_master')->getChild($city)->getChild($resultid)->remove();  
            }
        }
          
        $msg ="success";
            return response::json(compact('msg'));    
    }
public function index_set()
{
    
}
public function fb_check_inprogress()
{
    $main_values = DB::SELECT("SELECT del_staff_auto_assign FROM `general_settings`");
    $check_auto_assign=$main_values[0]->del_staff_auto_assign;
    if($check_auto_assign=="Y")
    {
    $inprogress_check= DB::SELECT("SELECT * FROM order_master WHERE  assign_status = 'Inprogress' ");
        if(count($inprogress_check) > 0)
        {
           echo "Already Inprogress" ;
        }else
        {
        $this->fb_distance_staffid_check();
        }
    }
}
 public function fb_distance_staffid_check()
    {
     $chek_val_auto = DB::SELECT("SELECT del_staff_auto_assign FROM `general_settings`");
    $check_auto_assign=$chek_val_auto[0]->del_staff_auto_assign;
    if($check_auto_assign=="Y")
    {
        $inprogress_check= DB::SELECT("SELECT * FROM order_master WHERE  assign_status = 'Inprogress' ");
        if(count($inprogress_check) > 0)
        {
           echo "Already Inprogress" ;
        }else
        {
            //firebase code starts
            $database = $this->firebase_db; 
            $rowkey = DB::SELECT("SELECT `assign_min_dist_meter`,`assign_inc_dist_meter`,assign_inc_loop_count,`assign_max_order`,`assign_max_order_time_gap`,`assign_nxt_rest_max_dist`,`assign_nxt_cust_max_dist` FROM `general_settings`");
            $assign_min_dist_meter = $rowkey[0]->assign_min_dist_meter;
            $assign_inc_dist_meter = $rowkey[0]->assign_inc_dist_meter;
            $assign_max_order = $rowkey[0]->assign_max_order;
            $assign_max_order_time_gap = $rowkey[0]->assign_max_order_time_gap;
            $assign_nxt_rest_max_dist = $rowkey[0]->assign_nxt_rest_max_dist;
            $assign_nxt_cust_max_dist = $rowkey[0]->assign_nxt_cust_max_dist;
            $assign_inc_loop_count = $rowkey[0]->assign_inc_loop_count;

            $main_values = DB::SELECT("SELECT o.order_number,o.rest_id,o.rest_confirmed_time,r.geo_cordinates,o.customer_details->'$.latitude' as latitude,o.customer_details->'$.longitude' as longitude FROM order_master as o JOIN restaurant_master as r WHERE r.id=o.rest_id AND o.delivery_assigned_to is NULL AND (current_time() > o.assign_after_time) AND o.assign_status = 'Pending' order by o.assign_after_time asc LIMIT 1");
            if(count($main_values) > 0)
            {
                 $order_number=$main_values[0]->order_number;
                 DB::SELECT('UPDATE `order_master` SET  assign_status="Inprogress" WHERE order_number = "'.$order_number.'"');
                $rest_id=$main_values[0]->rest_id;
                $rest_confirmed_time=$main_values[0]->rest_confirmed_time;
                $rest_det=explode(",",$main_values[0]->geo_cordinates);
                $rest_lat=$rest_det[0];
                $rest_long=$rest_det[1];
                $cust_lat_array=explode("\"",$main_values[0]->latitude);
                $cust_lat=$cust_lat_array[1];
                $cust_long_array=explode("\"",$main_values[0]->longitude);
                $cust_long=$cust_long_array[1];
               $staf_det='';
               $staff_ordercount='';
               //$staff_ordercount='';
               $fb_stafflist = DB::SELECT("SELECT `staff_id` as id FROM `delivery_staff_attendence` WHERE `out_time` IS NULL ORDER BY `staff_id` ASC");
               if(count($fb_stafflist)>0)
                 {
                    foreach($fb_stafflist as $key=>$items)
                    {
                         $order_val=$database->getReference('location')->getChild($items->id)->getChild('current_order')->getChild('order_count')->getValue();
                         
                        if($order_val < $assign_max_order)
                        {//only check staff if current order less than 2
                            $distance_val=$database->getReference('location')->getChild($items->id)->getChild('restaurant_distance')->getChild($rest_id)->getChild('distance')->getValue();
                            if($distance_val != NULL)
                            {
                           $staff_ordercount[$order_val][$items->id]=$distance_val;
                            /*if($order_val=='0')
                            {
                                $staff_ordercount_0[$items->id]=$distance_val;
                            }else if($order_val=='1')
                            {
                                $staff_ordercount_1[$items->id]=$distance_val;
                            }*/
                           $staf_det[$items->id]=$distance_val;
                            }
                        }

                    }
                 }                 
                 $loopstatus='sorry';$finalstaff=0;
                for($j=0;$j<$assign_max_order;$j++)
                 {$finalstaff=0;
                    if($loopstatus=="sorry")
                    {
                        if($j==0)
                        {
                            if(isset($staff_ordercount[0]))
                            {
                               //if order count =0
                                $finalstaff=$this->check_distancemeter($staff_ordercount[0],$assign_min_dist_meter,0,0);
                                if($finalstaff==0)
                                {
                                    for($i=1;$i<=$assign_inc_loop_count;$i++)
                                    {
                                        if($loopstatus=="sorry")
                                        {
                                            $finalstaff=$this->check_distancemeter($staff_ordercount[0],$assign_min_dist_meter,$assign_inc_dist_meter,$i);
                                            if($finalstaff!=0)
                                            { 
                                                $loopstatus=$this->updatetofirebase($finalstaff, $order_number);                          
                                            }
                                        }
                                    }
                                }else
                                {
                                    $loopstatus=$this->updatetofirebase($finalstaff, $order_number);                    
                                }
                            }
                            if($loopstatus=="sorry") 
                               {
                                if($finalstaff!=0)
                                {
                                   unset($staff_ordercount[0][$finalstaff]);
                                   if(isset($staff_ordercount[0]))
                                   {
                                       $j--;
                                   }
                                }
                               }
                            
                        }else if($j==1)
                        {//echo "j first";print_r($staff_ordercount[1]);
                            if(isset($staff_ordercount[1]))
                            {
                                 $finalstaff=$this->check_distancemeter($staff_ordercount[1],$assign_min_dist_meter,0,0);
                                if($finalstaff==0)
                                {//echo "final0";
                                    for($i=1;$i<=$assign_inc_loop_count;$i++)
                                    {//echo $i."staff:".$finalstaff."loopstatus:".$loopstatus;
                                        if($loopstatus=="sorry")
                                        {
                                            $finalstaff=$this->check_distancemeter($staff_ordercount[1],$assign_min_dist_meter,$assign_inc_dist_meter,$i);
                                            if($finalstaff!=0)
                                            {//echo $finalstaff;
                                                $loopstatus=$this->check_count1_conditions($finalstaff,$order_number,$rest_confirmed_time,$rest_lat,$rest_long,$cust_lat,$cust_long);                    
                                            }
                                        }
                                    }

                                }else
                                {//echo "first".$finalstaff;
                                    $loopstatus=$this->check_count1_conditions($finalstaff,$order_number,$rest_confirmed_time,$rest_lat,$rest_long,$cust_lat,$cust_long);                    
                                   //echo $loopstatus;
                                }
                              
                            }
                             if($loopstatus=="sorry") 
                               {
                                 if($finalstaff!=0)
                                {
                                   unset($staff_ordercount[1][$finalstaff]);
                                   if(isset($staff_ordercount[1]))
                                   {
                                       $j--;
                                   }
                                }
                               }
                        }
                    }
                    //if order count 1
                   
                }
                 if($loopstatus=="sorry")
                            {
                             DB::UPDATE('update order_master set assign_after_time =DATE_ADD(NOW(), INTERVAL 2 MINUTE),assign_status = "Pending"  where order_number = "'.$order_number.'"');   
                            }
            }
        }
    }
        //firebase code ends
    }
    public function updatetofirebase($staffid,$order_number)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $time = strtoupper($date->format('h:i a'));
        $exist = DB::SELECT("SELECT ftoken,staff_max_credit,first_name,mobile from internal_staffs where id=$staffid");
        $staff_name=$exist[0]->first_name;
        $staff_number=$exist[0]->mobile;
        DB::SELECT('UPDATE `order_master` SET `current_status`="C",status_details= JSON_INSERT(status_details,"$.C","'.$time.'"),delivery_assigned_to="'.$staffid.'",delivery_assigned_details= JSON_OBJECT("name","'.$staff_name.'","phone","'.$staff_number.'","note",""),assign_status="Inprogress" WHERE order_number = "'.$order_number.'"');
        $contact = DB::SELECT("select customer_id,rest_details->>'$.name' as restname,delivery_assigned_details->>'$.phone' as mobile,customer_details->>'$.name' as cst_name,customer_details->>'$.mobile' as cst_mobile,customer_details->>'$.addressline2' as line2 FROM order_master WHERE order_number = '$order_number'");
        $mobile=$contact[0]->mobile;
        $cst_name=$contact[0]->cst_name;
        $cst_mobile=$contact[0]->cst_mobile;
        $line2=$contact[0]->line2;
        $userid = $contact[0]->customer_id;
        $hotel_name = $contact[0]->restname;
        $sendmsg = "You have New Order Assigned with --  Order Number- $order_number ,  Customer Name - $cst_name , Phone - $cst_mobile , Area - $line2 ";
        if(count($exist)>0)
        {
            $arr['to'] = $exist[0]->ftoken;
            $arr['title'] = 'Order Assigned';
            $arr['message'] = $sendmsg;
            $arr['image'] = 'null';
            $arr['action'] = 'orderhistory';
            $arr['action_destination'] = 'null';
            $arr['app_type'] = 'deliveryapp';
            $result = Commonsource::notification($arr);

        }

        $is_exist= DB::SELECT("SELECT ftoken,customer_id,name FROM ftoken_master fm join customer_list cm on fm.customer_id = cm.id WHERE customer_id ='".trim($userid)."'");
        if(count($is_exist)>0)
        {
            foreach($is_exist as $item)
            {
                $arr['to'] = $item->ftoken;
                $arr['title'] = 'Order Confirmed';
                $arr['message'] = 'Hi '.title_case($item->name).', Thanks for using POTAFO. Your Order from '.$hotel_name.' ,Order Number- '.$order_number.' is confirmed and is been assigned to our Delivery staff - ' . $staff_name . ' for picking.';
                $arr['image'] = 'null';
                $arr['action'] = 'orderhistory';
                $arr['action_destination'] = 'null';
                $arr['app_type'] = 'customerapp';
                $result = Commonsource::notification($arr);
            }
        }



        $database = $this->firebase_db;
        // increase th count of th delivery staff
        $count_orderno = $database->getReference('location')->getChild($staffid)->getChild("current_order")->getChild("order_count")->getValue(); 
        $count_orderno_ct['order_count']= $count_orderno + 1;
        $database->getReference('location')->getChild($staffid)->getChild("current_order")->update($count_orderno_ct); 
        //add the details
        $restlist_data='';
        $insrt_orders = DB::SELECT("select rest_details->>'$.name' as restname,customer_details->>'$.latitude' as latitude,customer_details->>'$.longitude' as longitude FROM order_master WHERE order_number = '$order_number'");
        $restlist_data['rest_name']=$insrt_orders[0]->restname;             
        $restlist_data['cust_lat']=$insrt_orders[0]->latitude;
        $restlist_data['cust_long']=$insrt_orders[0]->longitude;
        $newUserKey = $database->getReference('location')->getChild($staffid)->getChild("orders")->getChild($order_number)->update($restlist_data);          
         
        // Notification of warning message On about to reaching of CREDIT AMOUNT.

        $total_amount=0;
        $orderdetails = DB::SELECT("SELECT ROUND(om.final_total,2) as omfinal_total,order_date,upper(payment_method) as paymethod From order_master as om WHERE om.order_number = '$order_number'");
        if(count($orderdetails)>0 && $orderdetails[0]->paymethod == 'COD')
        {
            DB::select("delete from `internal_staffs_credits` where order_number = '" . $order_number . "'");
            DB::select("INSERT INTO `internal_staffs_credits`(`staff_id`, `order_number`, `staff_number`, `order_date`, `final_total`) VALUES ('" . $staffid . "','" . $order_number . "','" . $staff_number . "','". $orderdetails[0]->order_date. "','" . $orderdetails[0]->omfinal_total . "')");
            $staffamount = DB::SELECT("select a.staff_credit - b.total  as pending_amount from ( select staff_max_credit as staff_credit,id from  `internal_staffs` where id = '".$staffid."') as a Join (select sum(final_total) as total,staff_id from `internal_staffs_credits` where status in ('Reserve','Credit') and staff_id = '".$staffid."') as b on a.id = b.staff_id");
            if(count($staffamount) != 0)
            {
                if($staffamount[0]->pending_amount) {
                    $total_amount = $staffamount[0]->pending_amount;
                }
                else
                {
                    $staffcreditmax = DB::SELECT("select staff_max_credit as staff_credit from  `internal_staffs` where id =  '".$staffid."'");
                    $total_amount =$staffcreditmax[0]->staff_credit;
                }
            }
            else
            {
                $staffcreditmax = DB::SELECT("select staff_max_credit as staff_credit from  `internal_staffs` where id =  '".$staffid."'");
                $total_amount =$staffcreditmax[0]->staff_credit;
            }
            $limit = Commonsource::notifylimit();
                if(count($exist)>0 && $total_amount <= $limit) {
                $arr['to'] = $exist[0]->ftoken;
                $arr['title'] = 'Warning!!';
                $arr['message'] = 'Warning!!You are about to reach your maximum cash on hand limit of Rs '.$exist[0]->staff_max_credit.'.Please remit cash to nearest given outlet for continuing getting Orders.Please contact Potafo team for any concerns';
                $arr['image'] = 'null';
                $arr['action'] = 'orderhistory';
                $arr['action_destination'] = 'null';
                $arr['app_type'] = 'deliveryapp';
                $result = Commonsource::notification($arr);
            }
        }






        DB::SELECT('UPDATE `order_master` SET  assign_status="Completed" WHERE order_number = "'.$order_number.'"');
        return "finished";
    }


    public function check_distancemeter($stafflist,$meterval,$inc_count,$count)
    {
        $distance_calc=0;
        if($count==0)
        {
            $distance_calc=$meterval;
        }else
        {
            $distance_calc = $meterval + ($inc_count * $count);
        }
        //echo $count.":".$distance_calc."\n";
        $min_dist_value = min($stafflist);//returns min distance of the array
        if($min_dist_value <= $distance_calc)
        {
            $staffid_min_dist = array_search($min_dist_value, $stafflist);
            return $staffid_min_dist;
        }else
        {
            return 0;
        }
        
                    //return 0 if fails
    }

    public function check_count1_conditions($finalstaff,$order_number,$rest_confirmed_time,$rest_lat,$rest_long,$cust_lat,$cust_long)
    {
         $rowkey = DB::SELECT("SELECT `assign_min_dist_meter`,`assign_inc_dist_meter`,assign_inc_loop_count,`assign_max_order`,`assign_max_order_time_gap`,`assign_nxt_rest_max_dist`,`assign_nxt_cust_max_dist` FROM `general_settings`");
         $assign_max_order_time_gap = $rowkey[0]->assign_max_order_time_gap;
         $assign_nxt_rest_max_dist = $rowkey[0]->assign_nxt_rest_max_dist;
         $assign_nxt_cust_max_dist = $rowkey[0]->assign_nxt_cust_max_dist;
        $database = $this->firebase_db; 
        $orderdet= $database->getReference('location')->getChild($finalstaff)->getChild("orders")->getValue();
        //print_r($orderdet);
        $orderid_array=(array_keys($orderdet));
        $orderid= $orderid_array[0];
        $cust_lat_old= $orderdet[$orderid]['cust_lat'];
        $cust_long_old= $orderdet[$orderid]['cust_long'];
        $old_order_number = DB::SELECT("SELECT o.rest_confirmed_time,r.geo_cordinates FROM order_master as o JOIN restaurant_master as r WHERE r.id=o.rest_id AND o.order_number='".$orderid."'");
        $rest_confirmed_time_old_order=$old_order_number[0]->rest_confirmed_time;
        $rest_det=explode(",",$old_order_number[0]->geo_cordinates);
        $rest_lat_old=$rest_det[0];
        $rest_long_old=$rest_det[1]; 
        //echo "time1:".$rest_confirmed_time_old_order;
        //echo "     time2:".$rest_confirmed_time;
       $time_diff=time_difference($rest_confirmed_time_old_order,$rest_confirmed_time);
       //echo $assign_max_order_time_gap;
        if(strtotime($time_diff) <= strtotime($assign_max_order_time_gap))
        {//echo $rest_lat_old." ".$rest_lat." ".$rest_long_old." ".$rest_long;
            $rest_distance = Commonsource::distance_calculate($rest_lat_old,$rest_lat,$rest_long_old,$rest_long);
            if($rest_distance <= $assign_nxt_rest_max_dist)
            {
                 $cust_distance = Commonsource::distance_calculate($cust_lat_old,$cust_lat,$cust_long_old,$cust_long);
                if($cust_distance <= $assign_nxt_cust_max_dist)
                {
                    $this->updatetofirebase($finalstaff, $order_number); //break; 
                    return "finished";
                }else
                {
                    return "sorry";
                    //break;
                }
            }else
            {
                return "sorry";
                //break;
            }
        }else
        {
            return "sorry";
            //break;
        }
    }
 

}