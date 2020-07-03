<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
use Helpers\Commonsource;
use App\PaymentMode;
use Razorpay\Api\Api;
use DateTime;
use DateTimeZone;
class PaymentController extends Controller
{
    public function __construct()
    {
        $this->key_id     = config('razor.key_id');
        $this->secret     = config('razor.key_secret');
        $this->api        = new Api($this->key_id , $this->secret);
    }
    public function payment_mode(Request $request)//API to List the Payment mode which are active
    {
        $user_id = $request['user_id'];
        $string = "";
        $codcallcnfrmlimit = Commonsource::codcallconfirmlimit();
        $orderslists = DB::SELECT("SELECT order_number from order_master where customer_id= '$user_id' and current_status ='D'");
        if((count($orderslists) < $codcallcnfrmlimit))
        {
            $show_call_confirm = 'Y';
        }
        else
        {
            $show_call_confirm = 'N';
        }
        $show_call_confirm = 'N';

        $restlist = DB::SELECT("SELECT category FROM restaurant_master LEFT JOIN order_master ON restaurant_master.id=order_master.rest_id WHERE order_number='t_$user_id'");
            
        if( $restlist[0]->category == 'Potafo Mart')
        {
            $string .= "";
            //$string .= " AND name !='ONLINE'";
        }    
        else
        {
                 $string .= "";
        }

                
       // return "select name,image FROM payment_methods WHERE active='Y' $string";
        $payment_method = DB::SELECT("select name,image FROM payment_methods WHERE active='Y' $string");
       
       if(count($payment_method)>0)
       {
         $msg = 'Exist';
         return response::json(['msg' => $msg,'payment_mode' => $payment_method, 'show_call_confirm' => $show_call_confirm]);
       }
       else
       {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
       }
    }

    public function payment_initialize($id)
    {
        $string = '';
        $exist=DB::SELECT("select order_number from order_master where order_number='t_$id'");
        if(count($exist)>0)
        {
            $string = '{"status":"initialize","referenceid":""}';
            DB::SELECT('UPDATE `order_master` SET payment_attempts =payment_attempts + 1,payment_details= \''.$string.'\'  WHERE order_number = "t_'.$id.'"');
            $msg = 'success';
        }
        else
        {
              $msg = 'Order Number Invalid';
        }
        return response::json(['msg' => $msg]);
    }

    public function payment_complete($id,$refid)
    {
        $exist=DB::SELECT("select order_number from order_details where order_number='t_$id'");
        if(count($exist)>0)
        {
            DB::SELECT('UPDATE `order_master` SET payment_details= json_set(payment_details,"$.status","completed","$.referenceid","'.$refid.'")  WHERE order_number = "t_'.$id.'"');
            $msg = 'success';
        }
        else
        {
            $msg = 'Order Number Invalid';
        }
        return response::json(['msg' => $msg]);
    }

    public function create_order(Request $request)
    {
        $id= $request['user_id'];
        $exist=DB::SELECT("select payment_details,final_total from order_master where order_number='t_$id'");
        if(count($exist)>0)
        {
            if(!isset($exist[0]->payment_details))
            {
                $api =  $this->api;
                $order  =  $api->order->create(array('amount' => $exist[0]->final_total*100, 'currency' => 'INR','payment_capture' => '1')); // Creates order
                $order_id = $order->id;
                $string = '{"razorpay_order_id":"'.$order_id.'"}';
                DB::SELECT('UPDATE order_master set payment_details = \''.$string.'\' where order_number="t_'.$id.'"');
            }
            else
            {
                $orderdetl = json_decode($exist[0]->payment_details,true);
                $order_id = $orderdetl['razorpay_order_id'];
				$api =  $this->api;
                $payment= $api->order->fetch($order_id);
				if( $payment->amount !=  $exist[0]->final_total*100)
				{
					$thisapi =  $this->api;
					$orders  =  $thisapi->order->create(array('amount' => $exist[0]->final_total*100, 'currency' => 'INR','payment_capture' => '1')); // Creates order
					$ordersid = $orders->id;
					$string = '{"razorpay_order_id":"'.$ordersid.'"}';
					$order_id = $ordersid;
					DB::UPDATE('UPDATE order_master set payment_details = \''.$string.'\' where order_number="t_'.$id.'"');
				}
				
            }
            $msg = 'EXIST';
            $arr = array('msg' => $msg,'razorpay_order_id' => $order_id);
        }
        else
        {
            $msg = 'NOT EXIST';
            $arr = array('msg' => $msg);
        }
        return response::json($arr);
    }

    public function create_order_new(Request $request)
    {
        $orderarr = array();
        $orderarrs = array();
        $id= $request['user_id'];
        $exist=DB::SELECT("select payment_details,final_total,razorpay_oldorders from order_master where order_number='t_$id'");
        if(count($exist)>0)
        {
            if(!isset($exist[0]->payment_details))
            {
                $api =  $this->api;
                $order  =  $api->order->create(array('amount' => $exist[0]->final_total*100, 'currency' => 'INR','payment_capture' => '1')); // Creates order
                $order_id = $order->id;
                $string = '{"razorpay_order_id":"'.$order_id.'"}';
                $status = 'NotPaid';
                DB::SELECT('UPDATE order_master set payment_details = \''.$string.'\' where order_number="t_'.$id.'"');
            }
            else
            {
                $orderdetl = json_decode($exist[0]->payment_details,true);
                $order_id = $orderdetl['razorpay_order_id'];
                $api =  $this->api;
                $payment= $api->order->fetch($order_id);
                if(strtolower($payment->status) == 'paid')
                {
                    if( $payment->amount ==  $exist[0]->final_total*100)
                    {
                        $status = 'Paid';
                    }
                    else
                    {
                        if(isset($exist[0]->razorpay_oldorders) && $exist[0]->razorpay_oldorders != '')
                        {
                            $orderarr= json_decode($exist[0]->razorpay_oldorders,true);
                            if(!in_array($order_id,$orderarr))
                            {
                                $orderarr[] = $order_id;
                            }
                        }
                        else
                        {
                            $orderarr[] = $order_id;
                        }
                        $userval = "'".implode ( "','",$orderarr)."'";
                        $thisapi =  $this->api;
                        $orders  =  $thisapi->order->create(array('amount' => $exist[0]->final_total*100, 'currency' => 'INR','payment_capture' => '1')); // Creates order
                        $ordersid = $orders->id;
                        $string = '{"razorpay_order_id":"'.$ordersid.'"}';
                        $order_id = $ordersid;
                        DB::UPDATE('UPDATE order_master set payment_details = \''.$string.'\',razorpay_oldorders = json_array('.$userval.') where order_number="t_'.$id.'"');
                        $status = 'NotPaid';
                    }
                }
                else
                {
                    if( $payment->amount !=  $exist[0]->final_total*100)
                    {
                        $thisapi =  $this->api;
                        $orders  =  $thisapi->order->create(array('amount' => $exist[0]->final_total*100, 'currency' => 'INR','payment_capture' => '1')); // Creates order
                        $ordersid = $orders->id;
                        $string = '{"razorpay_order_id":"'.$ordersid.'"}';
                        $order_id = $ordersid;
                        DB::UPDATE('UPDATE order_master set payment_details = \''.$string.'\' where order_number="t_'.$id.'"');
                    }
                    $status = 'NotPaid';
                }
            }
            $msg = 'EXIST';
            $arr = array('msg' => $msg,'razorpay_order_id' => $order_id,'status' =>$status);
            }
        else
        {
            $msg = 'NOT EXIST';
            $arr = array('msg' => $msg);
        }
        return response::json($arr);
    }



 public function orderconfirmation_new(Request $request)
    {
        $qry = "";
        $version = "";
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $time = strtoupper($date->format('h:i a'));
        $operationtime = strtoupper($date->format('H:i:s'));
        $datetime = $date->format('Y-m-d G:i:s');
        $day = strtoupper($date->format('l'));
        $post = $request->all();
        $status = '';
        $userid = trim($post['user_id']);
        $cust_token = trim($post['cust_token']);
        $line1 = trim($post['line1']);
        $line1 = str_replace("'", '', $line1);
        $line1 = str_replace('"', '', $line1);
        $line2 = trim($post['line2']);
        $line2 = str_replace("'", '', $line2);
        $line2 = str_replace('"', '', $line2);
        $pincode = trim($post['pincode']);
        $landmark = urldecode(trim($post['landmark']));
        $type = trim($post['default_type']);
		$is_valid = DB::SELECT("SELECT id FROM `customer_list` WHERE id='".$userid."' AND cust_token ='".$cust_token."' ");
		if(count($is_valid)!=0){
         //Restaurent open/active check

			
            //Restaurent open/active check
          //menu open Check 
			$checkitemstock = 'Y';
			$payeexist = DB::SELECT("select payment_details from order_master where order_number='t_$userid'");
			if(isset($payeexist[0]->payment_details))
			{
				$orderdetlpay = json_decode($payeexist[0]->payment_details,true);
				$order_idpay = $orderdetlpay['razorpay_order_id'];
				$api =  $this->api;
				$paymentpay= $api->order->fetch($order_idpay);
				if(strtolower($paymentpay->status) == 'paid')
				{
					$checkitemstock = 'N';
				}
            }
            
            $restlist = DB::SELECT("select rt_rest_id as rest_id,rt_from_time as from_time,rt_to_time as to_time from restaurant_timings left join order_master on restaurant_timings.rt_rest_id=order_master.rest_id join restaurant_master on restaurant_master.id = restaurant_timings.rt_rest_id where order_number='t_$userid' AND busy='N'  AND status='Y' and force_close ='N' and rt_day = '$day' and time(rt_from_time) <= '$operationtime' and time(rt_to_time) >='$operationtime'");
            if($checkitemstock == 'Y')
            {
                if(count($restlist)!=0) 
                {  $c =0; } 
                else 
                {
                  return response::json(['msg' => 'Sorry this restaurant is Currently Not Available.']);
                }
            }

            $order_details = DB::SELECT("SELECT a.rest_id,a.menu_id,b.m_name_type->>'$.name' as menuname,a.order_number FROM order_details a left join restaurant_menu b on a.menu_id=b.m_menu_id AND a.rest_id=b.m_rest_id WHERE a.order_number='t_$userid' and a.final_rate >0 ");
                         $inactive_menlist = "";
                        $i=0;
            foreach($order_details as $menu)
            {
                $dayname = ucwords($date->format('D'));
                $dayname = '"'.$dayname.'"';
                $menuname = $menu->menuname;
                $menuid= $menu->menu_id;
                $rest_id= $menu->rest_id;
                //return "SELECT m_menu_id,m_time,m_name_type->>'$.name' as menuname FROM restaurant_menu WHERE m_rest_id='$rest_id' and m_menu_id='$menuid' and json_contains(m_days,'[$dayname]') AND DATE_FORMAT(NOW(),'%H:%i') between m_time->>'$.from' AND m_time->>'$.to' AND m_status='Y' ";
                $menu_info = DB::SELECT("SELECT m_menu_id,m_time,m_name_type->>'$.name' as menuname FROM restaurant_menu WHERE m_rest_id='$rest_id' and m_menu_id='$menuid' and json_contains(m_days,'[$dayname]') AND DATE_FORMAT(NOW(),'%H:%i') between m_time->>'$.from' AND m_time->>'$.to' AND m_status='Y' ");
                if(count($menu_info)==0 &&  $i==0){
                    $inactive_menlist .= "$menuname";
                }
                if(count($menu_info)==0 &&  $i!=0){
                    $inactive_menlist .= ", $menuname";
                }
                $i++;
                
            }
            if($inactive_menlist!='' && $checkitemstock != 'N' ) 
            {
                return response::json(['msg' =>"$inactive_menlist - Not Available Now"]);
            }
                        //menu open Check 
            if(trim($post['address_id']) ==' ' ||trim($post['address_id']) =='' ||trim($post['address_id']) =='0'  ||trim($post['address_id']) =='null')
            {
            $address_id = '';
            $lines ='';
            $lat = '';
            $long = '';
            $cordinates = '';
        }
        else
        {
            $address_id = trim($post['address_id']);
            $lines = DB::SELECT("SELECT address->>'$.$address_id' as addressdetail,IFNULL(address->>'$.$address_id.LATITUDE',0) as lat,IFNULL(address->>'$.$address_id.LONGITUDE',0) as lon FROM `customer_list` WHERE id='" . $userid . "'");
            if(count($lines)>0)
            {
                $lat  = $lines[0]->lat;
                $long = $lines[0]->lon;
                $cordinates = ',"$.latitude","'.$lat.'","$.longitude","'.$long.'"';
                $l1 =   json_decode($lines[0]->addressdetail,true);
                $line1 =   str_replace("'", '', $l1['LINE1']);
                $line2 =  str_replace("'", '',$l1['LINE2']);
                $type =    trim($l1['TYPE']);
                $pincode =    trim($l1['PINCODE']);
                $landmark =    trim($l1['LANDMARK']);
            }
            else
            {
                $lat = '';
                $long = '';
                $cordinates = '';
            }
        }
        if(isset($userid))
        {
            $exist = DB::SELECT("select order_number from order_details where order_number='t_$userid'");
            if (count($exist) > 0)
            {
                if($line1 == '' || $line1 == ' ' || $line1 == null)
                {
                    return response::json(['msg' => 'Invalid Address']);
                }
                else
                {
                    $paymode = trim($post['paymode']);
                    $mode = trim($post['mode']);
                    $app_version = isset($post['app_version'])?$post['app_version']:null;
                    if(!isset($app_version) && $app_version == 'null' || $app_version == null || $app_version == ' ' || $app_version == '')
                    {
                        $version = '';
                    }
                    else
                    {
                        $version = ',app_version="'.$app_version.'"';
                    }
					
					$customer_cod_block = DB::SELECT("SELECT TRIM(cod_enable) AS cod_enable FROM customer_list WHERE id ='$userid'");
					if(count($customer_cod_block)>0)
					{
						$customer_cod_enabled = $customer_cod_block[0]->cod_enable;
					}
					else
					{
						$customer_cod_enabled = 'Y';
                    }
                    

                    $no_contact_status = DB::SELECT("select no_contact_del from order_master  where order_number='t_$userid'"); 
   
                    if (strtoupper($paymode) == 'COD')
                    {
						if($customer_cod_enabled == 'N')
						{
							return response::json(['msg' => 'COD Restricted. Please Proceed with Online Payment Mode']);
                        }
                        else if($no_contact_status[0]->no_contact_del == 'Y') //nocontact delivery cod restrict
						{
							return response::json(['msg' => 'COD Payment Option Not available for No-Contact Delivery.']);
						} 
						else
						{
							$exist = DB::SELECT("select payment_details,final_total,razorpay_oldorders from order_master where order_number='t_$userid'");
							if (count($exist) > 0)
							{
								if (isset($exist[0]->payment_details))
								{
									$orderdetl = json_decode($exist[0]->payment_details,true);
									$order_id = $orderdetl['razorpay_order_id'];
									$api =  $this->api;
									$payment= $api->order->fetch($order_id);
									if(strtolower($payment->status) == 'paid')
									{
										if ($payment->amount == $exist[0]->final_total * 100)
										{
											$razorpay_payment_id ='No_data';
											$razorpay_signature = 'No_data';
											$paymentmethod = 'No_data';
											$status = 'Y';
											$qry = 'UPDATE `order_master` SET `current_status`="P",mode_of_entry = "' . $mode . '",payment_details = JSON_SET(payment_details,\'$.razorpay_payment_id\', "' . $razorpay_payment_id . '",\'$.razorpay_signature\', "' . $razorpay_signature . '",\'$.method\', "' . $paymentmethod . '"),status_details= JSON_OBJECT("P","' . $time . '"),order_date ="' . $datetime . '",payment_method ="ONLINE",customer_details= JSON_INSERT(customer_details,"$.addresstype","' . $type . '","$.addressline1","' . $line1 . '","$.addressline2","' . $line2 . '","$.pincode","' . $pincode . '","$.landmark","' . $landmark . '"'.$cordinates.')'.$version.'  WHERE order_number = "t_'.$userid.'"';
										}
										else
										{
											$status = 'Y';
											$qry = 'UPDATE `order_master` SET `current_status`="P",mode_of_entry = "' . $mode . '",status_details= JSON_OBJECT("P","' . $time . '"),order_date ="' . $datetime . '",payment_method ="' . $paymode . '",customer_details= JSON_INSERT(customer_details,"$.addresstype","' . $type . '","$.addressline1","' . $line1 . '","$.addressline2","' . $line2 . '","$.pincode","' . $pincode . '","$.landmark","' . $landmark . '"'.$cordinates.')'.$version.'  WHERE order_number = "t_' . $userid . '"';
										}
									}
									else
									{
										$status = 'Y';
										$qry = 'UPDATE `order_master` SET `current_status`="P",mode_of_entry = "' . $mode . '",status_details= JSON_OBJECT("P","' . $time . '"),order_date ="' . $datetime . '",payment_method ="' . $paymode . '",customer_details= JSON_INSERT(customer_details,"$.addresstype","' . $type . '","$.addressline1","' . $line1 . '","$.addressline2","' . $line2 . '","$.pincode","' . $pincode . '","$.landmark","' . $landmark . '"'.$cordinates.')'.$version.'  WHERE order_number = "t_' . $userid . '"';
									}
								 }
								else
								{
								$status = 'Y';
								$qry = 'UPDATE `order_master` SET `current_status`="P",mode_of_entry = "' . $mode . '",status_details= JSON_OBJECT("P","' . $time . '"),order_date ="' . $datetime . '",payment_method ="' . $paymode . '",customer_details= JSON_INSERT(customer_details,"$.addresstype","' . $type . '","$.addressline1","' . $line1 . '","$.addressline2","' . $line2 . '","$.pincode","' . $pincode . '","$.landmark","' . $landmark . '"'.$cordinates.')'.$version.'  WHERE order_number = "t_' . $userid . '"';
								}
							}
							else
							{
								$status = 'Y';
								$qry = 'UPDATE `order_master` SET `current_status`="P",mode_of_entry = "' . $mode . '",status_details= JSON_OBJECT("P","' . $time . '"),order_date ="' . $datetime . '",payment_method ="' . $paymode . '",customer_details= JSON_INSERT(customer_details,"$.addresstype","' . $type . '","$.addressline1","' . $line1 . '","$.addressline2","' . $line2 . '","$.pincode","' . $pincode . '","$.landmark","' . $landmark . '"'.$cordinates.')'.$version.'  WHERE order_number = "t_' . $userid . '"';
							}
						}
                    }
                    else if (strtoupper($paymode) == 'ONLINE')
                    {
                        if(trim($post['razorpay_payment_id']) == 'Noreply')
                        {
                            $razorpay_payment_id ='No_data';
                            $razorpay_signature = 'No_data';
                            $paymentmethod = 'No_data';
                            $status = 'Y';
                            $qry = 'UPDATE `order_master` SET `current_status`="P",mode_of_entry = "' . $mode . '",payment_details = JSON_SET(payment_details,\'$.razorpay_payment_id\', "' . $razorpay_payment_id . '",\'$.razorpay_signature\', "' . $razorpay_signature . '",\'$.method\', "' . $paymentmethod . '"),status_details= JSON_OBJECT("P","' . $time . '"),order_date ="' . $datetime . '",payment_method ="' . $paymode . '",customer_details= JSON_INSERT(customer_details,"$.addresstype","' . $type . '","$.addressline1","' . $line1 . '","$.addressline2","' . $line2 . '","$.pincode","' . $pincode . '","$.landmark","' . $landmark . '"'.$cordinates.')'.$version.'  WHERE order_number = "t_' . $userid . '"';
                        }
                        else
                        {
                            $razorpay_payment_id = trim($post['razorpay_payment_id']);
                            $razorpay_order_id = trim($post['razorpay_order_id']);
                            $razorpay_signature = trim($post['razorpay_signature']);
                            $secret = $this->secret;    //razorpay key scret;
                            $generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, $secret);
                            if ($generated_signature == $razorpay_signature)   //payment is successful
                            {
                                $api           = $this->api;
                                $payment       = $api->payment->fetch($razorpay_payment_id);
                                $paymentmethod = $payment->method;
                                $status = 'Y';
                                $qry = 'UPDATE `order_master` SET `current_status`="P",mode_of_entry = "' . $mode . '",payment_details = JSON_SET(payment_details,\'$.razorpay_payment_id\', "' . $razorpay_payment_id . '",\'$.razorpay_signature\', "' . $razorpay_signature . '",\'$.method\', "' . $paymentmethod . '"),mode_of_entry = "' . $mode . '",status_details= JSON_OBJECT("P","' . $time . '"),order_date ="' . $datetime . '",payment_method ="' . $paymode . '",customer_details= JSON_INSERT(customer_details,"$.addresstype","' . $type . '","$.addressline1","' . $line1 . '","$.addressline2","' . $line2 . '","$.pincode","' . $pincode . '","$.landmark","' . $landmark . '"'.$cordinates.')'.$version.'  WHERE order_number = "t_' . $userid . '"';
                            }
                            else
                            {
                                return response::json(['msg' => 'Amount Error']);
                            }
                        }

                    }
                    else
                    {
                        return response::json(['msg' => 'Invalid Payment Mode']);
                    }
                }
            }
            else
            {
                $msg = 'Not Exist';
            }
        }
        if($status == 'Y')
        {
            $length = DB::SELECT("SELECT json_length(`address`) as count FROM `customer_list` WHERE id='".$userid."'");
            $address_count =$length[0]->count;
           /* for($l=1;$l<=25;$l++)
            {
                $addresslimit = 'ADDRESS'.$l;
                DB::SELECT("UPDATE customer_list SET address = JSON_SET(address,'$.$addresslimit.DEFAULT','N') WHERE id ='$userid'");
            }*/
            $dtl=    DB::select("SELECT substring_index(substring_index(JSON_UNQUOTE(JSON_SEARCH(address, 'ONE', '".$line1."')),'.', -2),'.', 1) AS result FROM customer_list  where id='$userid'");
            $aupdateadd = TRIM($dtl[0]->result);
            $address = DB::SELECT("UPDATE customer_list SET address=JSON_SET(address,'$.$aupdateadd.DEFAULT','Y'),`default_address`='$address_id' where address->>'$.$aupdateadd.TYPE' = '$type' and address->>'$.$aupdateadd.LINE1' = '$line1' and id = '$userid'");
            DB::SELECT($qry);
            $lastdata = DB::SELECT("select order_number,rest_id from order_master where customer_id = '$userid' ORDER BY order_date DESC limit 1");
            $msg = 'Confirmed';
			$neworderno= TRIM($lastdata[0]->order_number);
			$menudetails = DB::SELECT("SELECT single_rate_details->'$.exc_rate'*qty as total_amount_exc ,single_rate_details->'$.inc_rate'*qty as total_amount_inc ,single_rate_details->'$.pack_rate'*qty as pack_rate FROM order_details where order_number  ='$neworderno'");
            
			$arr = array();
            $inc_rate =0;
            $excl_rate=0;
            $tax_rate =0;
            $pack_rate =0;
			
            foreach($menudetails as $key=>$value)
            {
                $inc_rate = $inc_rate + $value->total_amount_inc;
                $excl_rate = $excl_rate +$value->total_amount_exc;
                $pack_rate= $pack_rate+$value->pack_rate;
                $menuarr[] = $arr;
            }   
            $tax_rate = $inc_rate - $excl_rate;           
			DB::SELECT('UPDATE order_master SET rest_item_total_details = JSON_OBJECT("inc_rate","'.$inc_rate.'","excl_rate","'.$excl_rate.'","pack_rate","'.$pack_rate.'","tax_rate","'.$tax_rate.'") WHERE order_number="'.$neworderno.'"');
																			
			
	
			
			//$codcallcnfrmlimit = Commonsource::codcallconfirmlimit();
            //$orderslists = DB::SELECT("SELECT order_number from order_master where customer_id= '$userid' and current_status ='D'");
			/* $onhold = DB::SELECT("SELECT on_hold from order_master where order_number  ='$neworderno'");
            if(TRIM($onhold[0]->on_hold)=='N')
            {
                $is_exist= DB::SELECT("SELECT ftoken FROM users WHERE restaurant_id ='".$lastdata[0]->rest_id."' and active = 'Y' and login_group ='H' and ftoken is not null");
				if(count($is_exist)>0)
				{
					foreach($is_exist as $item)
					{
							$arr['to'] = $item->ftoken;
							$arr['title'] = 'New Potafo Order!';
							$arr['message'] = "Please accept the order to proceed.Order Number - ".$lastdata[0]->order_number."";
							$arr['image'] = 'null';
							$arr['action'] = 'orders';
							$arr['action_destination'] = 'null';
							$arr['app_type'] = 'partnerapp';
						    $result = Commonsource::notification($arr);
					}
				}
            } */

            $codcallcnfrmlimit = Commonsource::codcallconfirmlimit();
            $orderslists = DB::SELECT("SELECT order_number from order_master where customer_id= '$userid' and current_status ='D'");
			$orderpaymode = DB::SELECT("SELECT payment_method from order_master where order_number  ='$neworderno'");
			if((count($orderslists) < $codcallcnfrmlimit) and (strtoupper($orderpaymode[0]->payment_method)== 'COD'))
            {
                DB::update('update order_master set on_hold = "Y" where order_number ="'.$lastdata[0]->order_number.'"');
            }
			else
            {
                $order_restcat = DB::SELECT("SELECT r.category as category  FROM order_master o left join restaurant_master r on r.id = o.rest_id WHERE o.order_number  ='$neworderno'");
                if($order_restcat[0]->category== 'Potafo Mart')
                {
                    $not_app_type = 'potafo_mart';
                }
                else
                {
                    $not_app_type = 'partnerapp';
                }
     			$is_exist= DB::SELECT("SELECT ftoken FROM users WHERE restaurant_id ='".$lastdata[0]->rest_id."' and active = 'Y' and login_group ='H' and ftoken is not null");
				if(count($is_exist)>0)
				{
					foreach($is_exist as $item)
					{
							$arr['to'] = $item->ftoken;
							$arr['title'] = 'New Potafo Order!';
							$arr['message'] = "Please accept the order to proceed.Order Number - ".$lastdata[0]->order_number."";
							$arr['image'] = 'null';
							$arr['action'] = 'orders';
							$arr['action_destination'] = 'null';
							$arr['app_type'] = $not_app_type;
						    $result = Commonsource::notification($arr);
					}
				}
            }
            

	
            return response::json(['msg' => $msg,'order_number'=>$lastdata[0]->order_number]);
        }
     }
     else{
          return response::json(['msg' => 'Invalid Info']);
     }
        
    }

    public function call_customer(Request $request)
    {
        $userid = trim($request['user_id']);
        $on_hold = trim($request['on_hold']);
        $on_hold= 'N';
        DB::update('update order_master set on_hold = "'.$on_hold.'" where order_number = "t_' . $userid . '"');
        $msg = 'Success';
        return response::json(['msg' => $msg]);

    }

    public function no_contact_del(Request $request)
    {
        $userid = trim($request['user_id']);
        $no_contact_del = trim($request['no_contact_del']);
        DB::update('update order_master set no_contact_del = "'.$no_contact_del.'" where order_number = "t_' . $userid . '"');
        $msg = 'Success';
        return response::json(['msg' => $msg]);

    }

}
