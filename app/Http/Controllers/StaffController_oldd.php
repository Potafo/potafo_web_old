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
use Helpers\Commonsource;
use App\OrderMaster;
use DateTime;
use DateTimeZone;
class StaffController extends Controller
{
   
     public function view_staff(Request $request)
    {
        $filterarr = array();
        $itemsarr = array();$i=0;
        $autharr = array();
        $rows =Staff::select('id','first_name','last_name','mobile','designation','active','emergency_number','authcode','confirm_permission','cancel_permission')
              ->orderBy('id','desc')
              ->get();
        $encr_method = Datasource::encr_method();

        foreach($rows as $data)
        {
            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $key1 = hash('sha256', $rowkey[0]->explore);
            $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
            $key = hash('sha256', $key1);
            $iv = substr(hash('sha256', $iv1), 0, 16);
            $authcode = openssl_decrypt(base64_decode($data->authcode), $encr_method, $key, 0, $iv);
            $id = $data->id;
            $first_name = $data->first_name;
            $last_name = $data->last_name;
            $mobile = $data->mobile;
            $designation = $data->designation;
            $active = $data->active;
            $confirm_permission = $data->confirm_permission;
            $cancel_permission = $data->cancel_permission;
            $emergency_number = $data->emergency_number;
            $itemsarr[$i] = ['id'=>$id,'first_name'=>$first_name,'last_name'=>$last_name,'mobile'=>$mobile,'designation'=>$designation,'active'=>$active,'emergency_number'=>$emergency_number,'authcode'=>$authcode,'confirm_permission'=>$confirm_permission,'cancel_permission'=>$cancel_permission];
            $i++;
       }
        return view('staff.manage_staff',compact('rows','filterarr','itemsarr','autharr'));
    }
    
    public function add_staff(Request $request)
    {
        
        $types =$request['types'];
        $user = $request['fname'];
        $psw = mt_rand(1000,9999);//generate 4 digit random authcode
//      $psw = $request['auth_code'];
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($psw, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        $staff = Staff::where('authcode',$password)
                 ->first();
         if(count($staff)>0)
         {
            $psw = mt_rand(1000,9999);//generate 4 digit random authcode
            $encr_method = Datasource::encr_method();
            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $key1 = hash('sha256', $rowkey[0]->explore);
            $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
            $key = hash('sha256', $key1);
            $iv = substr(hash('sha256', $iv1), 0, 16);
            $password = openssl_encrypt($psw, $encr_method, $key, 0, $iv);
            $password = base64_encode($password);
             
         }
         else
         {
             $password = $password;
         }

        if($types == 'insert')
        {
        $staff = Staff::where('mobile',$request['mobile_number'])
                ->get();
        $code = Staff::where('authcode',$password)
                ->get();
        
        if(count($staff)>0 || count($code)>0)
        {
           
            $msg = 'already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $staff= new Staff();
            $staff->first_name = $request['fname'];
            $staff->last_name = $request['lastname'];
            $staff->mobile = $request['mobile_number'];
            $staff->emergency_number = $request['alternate_number'];
            $staff->designation = $request['designation'];
            $staff->authcode = $password;
            $staff->save();
            $msg = 'success';
            return response::json(compact('msg'));
        }
       
        return redirect('manage_staff');
        }
        
        else if($types == 'update')
        {
            $pswd = $request['auth_code'];
            $encr_method = Datasource::encr_method();
            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $key1 = hash('sha256', $rowkey[0]->explore);
            $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
            $key = hash('sha256', $key1);
            $iv = substr(hash('sha256', $iv1), 0, 16);
            $passwrd = openssl_encrypt($pswd, $encr_method, $key, 0, $iv);
            $passwrd = base64_encode($passwrd);
            $staff = Staff::where('mobile',$request['mobile_number'])
            ->where('id','!=',$request['userid'])
            ->first();
            $code = Staff::where('authcode',$passwrd)
                ->where('id','!=',$request['userid'])
                ->get();
        if(count($staff)>0 || count($code)>0)
        {
            $msg = 'exist';
            return response::json(compact('msg'));
        }
        else {
            Staff::where('id', $request['userid'])->update(
                ['first_name' => $request['fname'],
                    'last_name' => $request['lastname'],
                    'mobile' => $request['mobile_number'],
                    'emergency_number' => $request['alternate_number'],
                    'designation' => $request['designation'],
                    'authcode' => $passwrd,
                    'active' => $request['status'],
                    'confirm_permission' => $request['permission'],
                    'cancel_permission' => $request['can_permission']
                ]);
            $msg = 'done';
            return response::json(compact('msg'));
        }
        return redirect('manage_staff');
        }
    }
     public function deliverystaff_login(Request $request)//API to lists the staff name and number if code matches
    {
         $code = $request['code'];
         $ftoken = $request['ftoken'];
         $id = substr($code, 0, 2);
         $authcode = substr($code, 2, 6);
         $encr_method = Datasource::encr_method();
         $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
         $key1 = hash('sha256', $rowkey[0]->explore);
         $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
         $key = hash('sha256', $key1);
         $iv = substr(hash('sha256', $iv1), 0, 16);
         $password = openssl_encrypt($authcode, $encr_method, $key, 0, $iv);
         $password = base64_encode($password);
         $array = array();
        $list = DB::SELECT("SELECT id,first_name,mobile FROM `internal_staffs` WHERE RIGHT(`id`,2) =$id AND  authcode='".$password."' ");
         if(count($list)>0)
         {
            $id=$list[0]->id;
            $name=strtoupper($list[0]->first_name);
            $mobile=$list[0]->mobile;
            $array['id'] = $id;
            $array['name'] = $name;
            $array['mobile'] = $mobile;
            $msg = 'Exist';
            DB::UPDATE("UPDATE internal_staffs SET ftoken='".trim($ftoken)."' WHERE RIGHT(`id`,2) =$id AND  authcode='".$password."'  ");
            return response::json(['msg' => $msg,'staff' => $array]);
         }
         else
         {
             $msg = 'Not Exist';
             return response::json(['msg' => $msg]);
         }

    }
    
    public function deliverystaff_details($id)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $date1 = $date->format('Y-m-d');
        $details = DB::SELECT('select staff_id,entry_date,in_time,out_time FROM delivery_staff_attendence WHERE out_time IS NULL and staff_id="'.$id.'"');
        $todaydelivered = DB::SELECT('select count(order_number) as todaycount from order_master where delivery_assigned_to = "'.$id.'" and date(order_date) = "'.$date1.'" and current_status ="D"');
        foreach($todaydelivered as $key=>$list)
            {
               $counttoady = $list->todaycount;
            }
        $totaldelivered = DB::SELECT('select count(order_number) as totalcount from order_master where delivery_assigned_to = "'.$id.'" and current_status ="D"');
        foreach($totaldelivered as $key=>$list)
            {
               $counttotal = $list->totalcount;
            }
        if(count($details)>0)
        {
            $list = DB::SELECT('select entry_date,cast(in_time as time(0)) as in_time,"'.$counttoady.'" as today_delivered,"'.$counttotal.'" as total_delivered,"Open" as status FROM delivery_staff_attendence WHERE out_time IS NULL and staff_id="'.$id.'"');
        }
        else
        {
            $list = DB::SELECT('select entry_date,cast(in_time as time(0)) as in_time,cast(out_time as time(0)) as out_time,"'.$counttoady.'" as today_delivered,"'.$counttotal.'" as total_delivered,"Closed" as status FROM delivery_staff_attendence WHERE out_time IS NOT NULL and staff_id="'.$id.'"');
        }
        if(count($list)>0){
          $msg = 'Exist';
          return response::json(['msg' => $msg,'staffdetails' => $list[0]]);
        }
        else
        {
            $list = DB::SELECT('select "Closed" as status FROM delivery_staff_attendence');
            $msg = 'Exist';
          return response::json(['msg' => $msg,'staffdetails' => $list[0]]);
        }
    }
    
    public function deliverystaff_addtime($id)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i:s');
        $date1 = $date->format('Y-m-d');
        $details = DB::SELECT('select id FROM internal_staffs WHERE id="'.$id.'" and active ="Y"');
        if(count($details)>0)
        {
          $detail = DB::SELECT('select staff_id,entry_date,in_time,out_time FROM delivery_staff_attendence WHERE out_time IS NULL and staff_id="'.$id.'"');  
            if(count($detail)>0)
            {
                $checkstatus = DB::SELECT('select order_number FROM order_master WHERE delivery_assigned_to = "'.$id.'" and current_status != "D" and current_status !="T" and current_status !="CA"');
                if(count($checkstatus)>0)
                {
                    $msg = 'Order Pending To Be Delivered.';
                }
                else
                {
                   DB::SELECT("UPDATE delivery_staff_attendence SET out_time = '$datetime' WHERE staff_id='$id' ");
                   $msg = 'Successful';
                }
                return response::json(['msg' => $msg]);
            }
            else
            {
                DB::INSERT("INSERT INTO `delivery_staff_attendence`(`staff_id`, `entry_date`, `slno`, `in_time`) VALUES ('" . trim($id) . "','".$date1."','0','" .$datetime. "')");
              $msg = 'Successful';
              return response::json(['msg' => $msg]);
               
            }
        }
        else
        {
            $msg = 'No Match Found';
            return response::json(['msg' => $msg]);
        }
        
    }
    public function deliverycount_list($id,$frmdate,$todate)
    {
        $list = DB::SELECT('select date(order_date) as date,count(order_number) as count FROM order_master WHERE delivery_assigned_to = "'.$id.'" and current_status ="D" and date(order_date) >= "'.$frmdate.'" and date(order_date) <= "'.$todate.'" GROUP BY date(order_date)');    
        if(count($list)>0)
        {
          $msg = 'Exist';
          return response::json(['msg' => $msg,'deliverycount' => $list]);
        }
        else
        {
          $msg = 'Not Exist';
          return response::json(['msg' => $msg]);
        }
    }
    public function delivery_orders($staffid)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d');
        $orders = DB::SELECT("select order_number FROM order_master WHERE delivery_assigned_to='$staffid' and current_status IN ('C','OP','D') and date(order_date)='$datetime' order by status_details->>'$.C' desc");
        $detailsarr=array();
        foreach($orders as $key=>$list)
        {       
            $total =   OrderDetails::where('order_number',$list->order_number)->select("sl_no")->count();
            $details = DB::SELECT("select order_number,rs.name_tagline->>'$.name' as rest_name,customer_details->>'$.name' as cust_name,customer_details->>'$.mobile' as phone,'$total' as totalmenu,final_total as final_total,status_details->>'$.C' as time,customer_details->>'$.addresstype' as addresstype,customer_details->>'$.addressline1' as addressline1,customer_details->>'$.addressline2' as addressline2,customer_details->>'$.landmark' as landmark,customer_details->>'$.pincode' as pincode,current_status,IFNULL(rs.address,0) as restaurant_address,IFNULL(delivery_assigned_details->>'$.note',0) as note,IFNULL(customer_details->>'$.longitude',0) as longitude,IFNULL(customer_details->>'$.latitude',0) as latitude FROM order_master LEFT JOIN restaurant_master rs ON order_master.rest_id=rs.id WHERE delivery_assigned_to = '$staffid' and current_status IN ('C','OP','D') and order_number= '$list->order_number' order by date(order_date) DESC, status_details->>'$.C' desc");
            $detailsarr[]=$details[0];
        }
        if(count($detailsarr)>0)
        {
            $msg = 'Exist';
            return response::json(['msg' => $msg,'deliveryorders' => $detailsarr]);
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }
    
     public function delivery_order_details($order_number)
    {
         $decimal_points = commonSource::generalsettings();
         $array=array();
         $rest = DB::SELECT("select rest_id from order_master where order_number = '$order_number'");
        if(count($rest)>0) {
            foreach ($rest as $key => $item) {
                $rest_id = $item->rest_id;
            }
            $total = OrderDetails::where('order_number', $order_number)->select("sl_no")->count();
            $order_details = DB::SELECT("select om.order_number,om.customer_details->>'$.name' as cust_name,om.customer_details->>'$.mobile' as cust_phone,om.customer_details->>'$.addresstype' as cust_addresstype,om.customer_details->>'$.addressline1' as cust_addressline1,om.customer_details->>'$.addressline2' as cust_addressline2,om.customer_details->>'$.landmark' as cust_landmark,om.customer_details->>'$.pincode' as cust_pincode,om.customer_details->>'$.latitude' as cust_latitude,om.customer_details->>'$.longitude' as cust_longitude,rs.name_tagline->>'$.name' as rest_name,rs.address as rest_address,rs.phone as rest_phone,rs.mobile->>'$.ind' as rest_code,rs.mobile->>'$.mobile' as rest_mobile,od.menu_details->>'$.menu_name' as menu,od.qty as qty,od.menu_details->>'$.single_rate' as single_rate,od.final_rate as final_rate,'$total' as totalmenu,om.final_total as final_total,om.sub_total as sub_total,om.current_status,om.total_details as totals,om.payment_method as paymode,IFNULL(JSON_UNQUOTE(coupon_details->>'$.coupon_amount'),0) AS coupon_amount FROM order_master as om LEFT JOIN restaurant_master rs ON om.rest_id=rs.id LEFT JOIN order_details od ON om.order_number=od.order_number WHERE om.order_number= '$order_number'");
            $details = DB::SELECT("select menu_details->>'$.menu_name' as menu,rm.m_diet as diet,menu_details->>'$.single_rate' as single_rate,qty,final_rate FROM order_details as od LEFT JOIN restaurant_menu rm ON od.menu_id=rm.m_menu_id WHERE order_number='$order_number' and m_rest_id='$rest_id'");
            $menuarr = array();
            foreach ($order_details as $key => $items) {
                $array['cust_name'] = $items->cust_name;
                $array['cust_phone'] = $items->cust_phone;
                $array['cust_addresstype'] = $items->cust_addresstype;
                $array['cust_addressline1'] = $items->cust_addressline1;
                $array['cust_addressline2'] = $items->cust_addressline2;
                $array['cust_landmark'] = $items->cust_landmark;
                $array['cust_pincode'] = $items->cust_pincode;
                $array['cust_latitude'] = $items->cust_latitude;
                $array['cust_longitude'] = $items->cust_longitude;
                $array['rest_name'] = $items->rest_name;
                $array['rest_address'] = $items->rest_address;
                $array['rest_phone'] = $items->rest_phone;
                $array['rest_code'] = $items->rest_code;
                $array['rest_mobile'] = $items->rest_mobile;
                $array['paymode'] = $items->paymode;
                $array['coupon_amount'] = (string)round($items->coupon_amount, $decimal_points);
                $array['totalmenu'] = $total;
                $array['final_total'] = (string)round($items->final_total, $decimal_points);
                $array['sub_total'] = (string)round($items->sub_total, $decimal_points);
                $array['packing_charge'] = (string)round(json_decode($items->totals, true)['packing_charge'], $decimal_points);
                $array['delivery_charge'] = (string)round(json_decode($items->totals, true)['delivery_charge'], $decimal_points);
                $array['discount_amount'] = (string)round(json_decode($items->totals, true)['discount_amount'], $decimal_points);
                $statusname = $items->current_status;
                if ($statusname == 'P') {
                    $array['status'] = 'Placed';
                } else if ($statusname == 'C') {
                    $array['status'] = 'Confirmed';
                } else if ($statusname == 'OP') {
                    $array['status'] = 'Picked';
                } else if ($statusname == 'D') {
                    $array['status'] = 'Delivered';
                } else if ($statusname == 'CA') {
                    $array['status'] = 'Cancelled';
                }
            }
            $msg = 'Exist';
            return response::json(['msg' => $msg, 'deliveryorder_details' => $array, 'menu' => $details]);
        }
        else{
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }
    
    public function order_status($order_number,$status)
    {
        $arr = array();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $time = strtoupper($date->format('h:i a'));
        
        $statusfix = strtoupper($status);
        DB::SELECT("UPDATE order_master SET current_status = '$statusfix' WHERE order_number='$order_number'");
        if($statusfix == 'OP')
        {
            DB::SELECT("UPDATE order_master SET status_details=JSON_INSERT(status_details,'$.OP','$time') WHERE order_number ='$order_number'");
            $is_exist= DB::SELECT("SELECT ftoken,om.customer_id,name,rest_details->>'$.name' as restname FROM order_master om join customer_list cm on om.customer_id = cm.id  join ftoken_master fm on fm.customer_id = cm.id WHERE order_number =$order_number");
            if(count($is_exist)>0)
            {
                foreach($is_exist as $item)
                {
                   /* if(isset($item->ftoken) && ($item->ftoken != '' ||$item->ftoken != ' ' ||$item->ftoken!= 'null'))
                    {*/
                        $arr['to'] = $item->ftoken;
                        $arr['title'] = 'Order Picked';
                        $arr['message'] = "Your Order,Order Number - $order_number  has been collected from $item->restname and is on the way to your location.";
                        $arr['image'] = 'null';
                        $arr['action'] = 'orderhistory';
                        $arr['action_destination'] = 'null';
                        $arr['app_type'] = 'customerapp';
                        $result = Commonsource::notification($arr);
//                    }
                }
            }
        }
        else if($statusfix == 'D')
        {
            DB::SELECT("UPDATE order_master SET status_details=JSON_INSERT(status_details,'$.D','$time') WHERE order_number ='$order_number'");
            $is_exist= DB::SELECT("SELECT ftoken,om.customer_id,name,rest_details->>'$.name' as restname FROM order_master om join customer_list cm on om.customer_id = cm.id  join ftoken_master fm on fm.customer_id = cm.id WHERE order_number =$order_number");
            if(count($is_exist)>0)
            {
                foreach($is_exist as $item)
                {
                   /* if(isset($item->ftoken) && ($item->ftoken != '' ||$item->ftoken != ' ' ||$item->ftoken!= 'null'))
                    {*/
                        $arr['to'] = $item->ftoken;
                        $arr['title'] = 'Order Delivered';
                        $arr['message'] = "Your Order,Order Number - $order_number from $item->restname has been successfully delivered by our  staff. Request to please rate us for bettering our services.";
                        $arr['image'] = 'null';
                        $arr['action'] = 'orderhistory';
                        $arr['action_destination'] = 'null';
                        $arr['app_type'] = 'customerapp';
                        $result = Commonsource::notification($arr);
                  //  }
                }
            }
        }
        $msg = 'Successful';
        return response::json(['msg' => $msg]);
    }
    public function new_order_check($staffid)
    {
       
        $detail = DB::SELECT("SELECT count(order_number) as count from order_master where staff_notified = 'N' and delivery_assigned_to = '$staffid' and current_status='C'");
        $count = $detail[0]->count;
        if(count($detail)>0)
        {
            $msg = 'New order';
            return response::json(['msg' => $msg,'count' => $count]);
        }
        else
        {
            $msg = 'No order';
            return response::json(['msg' => $msg]);
        }
    }
     public function new_order_status($staffid)
    {
         $timezone = 'ASIA/KOLKATA';
         $date = new DateTime('now', new DateTimeZone($timezone));
         $time = strtoupper($date->format('h:i a'));
         DB::SELECT("UPDATE `order_master` SET `staff_notified` ='Y'  WHERE staff_notified = 'N' and delivery_assigned_to = '$staffid' and status_details->>'$.C' <='$time'");
         $msg = 'Successful';
         return response::json(['msg' => $msg]);
    }

    public function delivery_range_check(Request $request)
    {
        $dest_distance = '0';
        $ardius = '0';
        $id        = $request['user_id'];
        $line1     = $request['line1'];
        if(!isset($request['latitude']) && $request['latitude'] == 'null' || $request['latitude'] == '')
        {
            $latitude  = '0';
        }
        else
        {
            $latitude  = $request['latitude'];
        }
        if(!isset($request['latitude']) && $request['longitude'] == 'null' || $request['longitude'] == '')
        {
            $longitude  = '0';
        }
        else
        {
            $longitude  = $request['longitude'];
        }
        $rest = DB::SELECT("select rest_id,rs.google_location as rest_location,rs.geo_cordinates as cordinates,rs.delivery_range_unit->>'$.range' as range_unit from order_master om LEFT JOIN restaurant_master rs ON om.rest_id=rs.id where order_number = 't_$id'");
       if (count($rest) > 0)
       {
           $radius = $rest[0]->range_unit;
           if (!isset($rest[0]->cordinates) && $rest[0]->cordinates == '' || $rest[0]->cordinates == 'null' || $rest[0]->cordinates == ' ') {
               if (!isset($rest[0]->rest_location) && ($rest[0]->rest_location == 'null' || $rest[0]->rest_location == '' || $rest[0]->rest_location == ' ')) {
                   return response::json(['msg' => 'Service Unavailable At This Area']);
               }
               else
               {
                   $cordnt = Commonsource::latitude_longitude($rest[0]->rest_location);
                   if ($cordnt[2] == '0')
                   {
                       return response::json(['msg' => 'Service Unavailable At This Area']);
                   }
                   else
                   {
                       $cordinates = $cordnt;
                   }
               }
           }
           else
           {
               $crdarr = explode(',', $rest[0]->cordinates);
               $cordinates = $crdarr;
           }

           if ($latitude != '0' || $longitude != '0')
           {
               $dest_distance = Commonsource::distance_calculate($latitude, $cordinates[0], $longitude, $cordinates[1]);
               if ($dest_distance <= $radius)
               {
                   $msg = 'success';
               }
               else
               {
                   $msg = 'Service Unavailable At This Area';
               }

           }
           else if ($latitude == '0' || $longitude == '0')
           {
               if ($line1 == '' || $line1 == ' ' || $line1 == 'null') {
                   return response::json(['msg' => 'Service Unavailable At This Area']);
               }
               else
               {
                   $cordnt = Commonsource::latitude_longitude($line1);
                   if ($cordnt[2] == '0')
                   {
                       return response::json(['msg' => 'Service Unavailable At This Area']);
                   }
                   else
                   {
                       $cordt = $cordnt;
                   }
                   if(count($cordt)>0 || ($cordnt[0] != '' || $cordnt[0] != 'NULL' || $cordnt[0] != '0' ||$cordnt[0] != ' ' || $cordnt[1] != ''|| $cordnt[1] != 'NULL'|| $cordnt[1] != '0'|| $cordnt[1] != ' '))
                   {
                       $dest_distance = Commonsource::distance_calculate($cordnt[0], $cordinates[0], $cordnt[1], $cordinates[1]);
                       if ($dest_distance <= $radius)
                       {
                           $msg = 'success';
                       }
                       else
                       {
                           $msg = 'Service Unavailable At This Areas';
                       }
                   }
                   else
                   {
                       $msg = 'Service Unavailable At This Area';
                   }
               }
           }
       }
       else
       {
           return response::json(['msg' => 'Service Unavailable At This Area']);
       }
        return response::json(['msg' => $msg]);
      /*  if($line1 != '' || $line1 != ' ' || $line1 != 'null')
        {
            $googlekey = Commonsource::googleapikey();
            $arr = array();
            $rest = DB::SELECT("select rest_id,rs.google_location as rest_location,rs.delivery_range_unit->>'$.range' as range_unit from order_master om LEFT JOIN restaurant_master rs ON om.rest_id=rs.id where order_number = 't_$id'");
            if (count($rest) > 0)
            {
                if ($rest[0]->rest_location != '')
                {
                    $origin = $rest[0]->rest_location;
                    $radius = $rest[0]->range_unit;
                    $snw = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=' . urlencode($origin) . '&destinations=' . urlencode($line1) . '&key=' . $googlekey;
                    $menus = file_get_contents($snw);
                    $menulist = json_decode($menus, true);
                    $distance = $menulist['rows'][0]['elements'][0]['distance']['text'];
                    $distance_unit = explode(' ', $distance)[1];
                    $dest_distance = explode(' ', $distance)[0];
                    if (strtoupper($distance_unit) == 'KM') {
                        if ($radius > 0) {
                            if ($dest_distance <= $radius) {
                                $msg = 'success';

                            } else {
                                $msg = 'not success';
                            }
                        }
                        else
                        {
                            $msg = 'Restaurant Distance Radius Not Set';
                        }
                    }
                    else if (strtoupper($distance_unit) == 'M')
                    {
                        $msg = 'success';
                    }
                    return response::json(['msg' => $msg]);
                }
                else
                {
                    $msg = 'Restuarant Location not Set';
                    return response::json(['msg' => $msg]);
                }
            } else {
                return response::json(['msg' => 'Error']);
            }
        }
        else {
            return response::json(['msg' => 'Error']);
        }*/
    }
    public function minimumcartcheck($userid)
    {
        $decimal_points = commonSource::generalsettings();
        $order = DB::SELECT("select rest_id,IFNULL(sub_total,0) as sub_total,IFNULL(min_cart_value,0) as min_cart FROM order_master LEFT JOIN restaurant_master ON restaurant_master.id=order_master.rest_id where order_number = 't_$userid' ORDER BY order_date desc limit 1");
        if(count($order) >0) {
            $subtotal = $order[0]->sub_total;
            $min_cart = $order[0]->min_cart;
            if ($subtotal >= $min_cart)
            {
                $msg = 'success';
            }
            else
            {
                $msg = 'Sorry! Minimum cart amount should be Rs '.(string)round($min_cart, $decimal_points).' for this restaurant.';
            }
        }
        else
        {
            $msg = 'Error';
        }
        return response::json(['msg' => $msg]);

    }

    public function testing()
    {
        $tokenarrs = ["1","2","3","4","5","6"];
        $chunked_arr = array_chunk($tokenarrs,3);
        return $chunked_arr[1];
       /*$lon2  = "75.7842173";//11.246987,75.7842173,17
         $lon1  = "75.8744252";//11.3054724,75.8744252
         $lat2  = "11.246987";
         $lat1  = "11.3054724";
         $theta = $lon1 - $lon2;
         $dist  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
         $dist  = acos($dist);
         $dist  = rad2deg($dist);
         $miles = $dist * 60 * 1.1515;
         return ($miles * 1.609344);*/

//     return (SQRT((SIN(($lat2*(3.14159/180)-$lat1*(3.14159/180))/2))^2+COS($lat2*(3.14159/180))*COS($lat1*(3.14159/180))*SIN((($lon2*(3.14159/180)-$lon1*(3.14159/180))/2))^2));
       /* $restaurant = DB::SELECT('SELECT rt.id as res_id,rt.google_location as location FROM `restaurant_master` rt');
        foreach($restaurant as $item)
        {
            $cordinates = Commonsource::latitude_longitude($item->location);
            $cord = $cordinates[0].",".$cordinates[1];
            DB::UPDATE('UPDATE `restaurant_master` set geo_cordinates= "'.$cord.'" where id  = "'.$item->res_id.'"');


        }*/
    }
    public function ftoken_staff_check(Request $request) {
        $staffid= $request['code'];
        $ftoken = $request['ftoken'];
        $check = DB::SELECT("SELECT ftoken FROM internal_staffs WHERE id='".$staffid."' AND trim(ftoken)='".trim($ftoken)."' ");
        if(count($check)==0){
            DB::UPDATE("UPDATE internal_staffs SET ftoken='".trim($ftoken)."' WHERE id='".$staffid."' ");
             $msg = 'updated';
        }
        else{
            $msg = 'nochanges';
        }
         return response::json(['msg' => $msg]);
    }

    public function notificationsubmit(Request $request)
    {
        $userarr = array();
        $tokenarr = array();
        $arr =   array();
        $post = $request->all();
        $title = $post['title'];
        $group = $post['group'];
        $message = $post['message'];
        $expiry_date = date('Y-m-d',strtotime($post['expiry_date']));
        if(strtolower($group) == 'all')
        {
            DB::select("INSERT INTO `notifications`(`is_all`, `title`, `message`, `entry_date`, `expiry`) VALUES ('Y','".$title."','".$message."',now(),'".$expiry_date."')");
            $arr['to'] = '/topics/potafo';
            $arr['title'] = trim($title);
            $arr['message'] = trim($message);
            $arr['image'] = 'null';
            $arr['action'] = 'notification';
            $arr['action_destination'] = 'null';
            $arr['app_type'] = 'customerapp';
            $result = Commonsource::notification($arr);
        }
        else
        {
           $result = DB::SELECT('SELECT g_query FROM  notification_group WHERE g_id = "'.trim($group).'" ');
           $detail = DB::SELECT($result[0]->g_query);
           foreach($detail as $item)
           {
               if(!in_array($item->id,$userarr))
               {
                   $userarr[] = $item->id;
                   $data    = DB::SELECT("SELECT * FROM ftoken_master WHERE customer_id ='".trim($item->id)."'");
                   foreach($data as $key)
                   {
                       if(!in_array($key->ftoken,$tokenarr))
                       {
                           $tokenarr[] = $key->ftoken;
                       }
                   }
               }
           }
            $userval = "'".implode ( "','", $userarr )."'";
            DB::INSERT("INSERT INTO `notifications`(`is_all`,`groupid`,`title`, `message`, `entry_date`, `expiry`,`user_list`) VALUES ('Y','".$group."','".$title."','".$message."',now(),'".$expiry_date."',JSON_ARRAY($userval))");
            $chunked_arr = array_chunk($tokenarr,1000);
            foreach($chunked_arr as $index=>$item)
            {
                 $arr['tokens'] =$item;
                 $arr['title'] = trim($title);
                 $arr['message'] = trim($message);
                 $arr['image'] = 'null';
                 $arr['action'] = 'notification';
                 $arr['action_destination'] = 'null';
                 $arr['app_type'] = 'customerapp';
                 $result = Commonsource::group_notification($arr);
            }
        }
    }
}

