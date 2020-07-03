<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
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
       $payment_method = DB::SELECT("select name,image FROM payment_methods WHERE active='Y'");
       if(count($payment_method)>0)
       {
         $msg = 'Exist';
         return response::json(['msg' => $msg,'payment_mode' => $payment_method]);
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
                 $payment= $api->order->fetch('order_BnSkIa6wdAQVOD');
            //    $payment  = $api->payment->fetch('pay_BnSluOUG8xQssO');
                return Response::json( $payment->notes);
            }
            $msg = 'EXIST';
        //    $arr = array('msg' => $msg,'razorpay_order_id' => $payments);
        }
        else
        {
            $msg = 'NOT EXIST';
            $arr = array('msg' => $msg);
        }
//        return response::json($arr);
    }

    public function create_order_new(Request $request)
    {
        $id= $request['user_id'];
        $status = 'Not Paid';
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
            //  $payment  = $api->payment->fetch('pay_BnSluOUG8xQssO');
                return $payment->status;
            }
            $msg = 'EXIST';
        //  $arr = array('msg' => $msg,'razorpay_order_id' => $payments);
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
        $datetime = $date->format('Y-m-d G:i:s');
        $post = $request->all();
        $status = '';
        $userid = trim($post['user_id']);
        $line1 = trim($post['line1']);
          $line1 = str_replace("'", '', $line1); 
          $line1 = str_replace('"', '', $line1); 
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
            $lines = DB::SELECT("SELECT IFNULL(address->>'$.$address_id.LATITUDE',0) as lat,IFNULL(address->>'$.$address_id.LONGITUDE',0) as lon FROM `customer_list` WHERE id='" . $userid . "'");
            if(count($lines)>0)
            {
                $lat  = $lines[0]->lat;
                $long = $lines[0]->lon;
                $cordinates = ',"$.latitude","'.$lat.'","$.longitude","'.$long.'"';
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
                    $line2 = trim($post['line2']);
		          $line2 = str_replace("'", '', $line2);
		          $line2 = str_replace('"', '', $line2);
                    $pincode = trim($post['pincode']);
                    $landmark = urldecode(trim($post['landmark']));
                    $type = trim($post['default_type']);
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
                    if (strtoupper($paymode) == 'COD')
                    {
                        $status = 'Y';
                        $qry = 'UPDATE `order_master` SET `current_status`="P",mode_of_entry = "' . $mode . '",status_details= JSON_OBJECT("P","' . $time . '"),order_date ="' . $datetime . '",payment_method ="' . $paymode . '",customer_details= JSON_INSERT(customer_details,"$.addresstype","' . $type . '","$.addressline1","' . $line1 . '","$.addressline2","' . $line2 . '","$.pincode","' . $pincode . '","$.landmark","' . $landmark . '"'.$cordinates.')'.$version.'  WHERE order_number = "t_' . $userid . '"';
                    }
                    else if (strtoupper($paymode) == 'ONLINE')
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
            $lastdata = DB::SELECT("select order_number from order_master where customer_id = '$userid' ORDER BY order_date DESC limit 1");
            $msg = 'Confirmed';
            return response::json(['msg' => $msg,'order_number'=>$lastdata[0]->order_number]);
        }
    }


}
