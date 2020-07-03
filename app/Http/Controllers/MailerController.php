<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Image;
use Config;
use Helpers\Datasource;
use Mail;
use App\GeneralSettingSmtp;
use App\EndCustomerModel;
use Response;
use DateTime;
use DateTimeZone;

class MailerController extends Controller
{
     public function __construct()

  {
       
      $settings = GeneralSettingSmtp::first(); 
      //$this->toaddress = $settings['to_email'];
      $this->domain_name = $settings['domain_name'];
      $host = $settings['host']; 
      $port = $settings['port'];
      $username = $settings['user_name'];
      $password = $settings['password'];
//      $username = 'support@varskart.com';
//      $password = 'support@123#';
      $encryption = $settings['encryption'];
      $fromaddress = $settings['from_email'];
      $fromname = $settings['from_name'];
      $to_email = $settings['to_email'];
      \Config::set('mail.host', trim($host));
      \Config::set('mail.port', trim($port));
      \Config::set('mail.username', trim($username));
      \Config::set('mail.password', trim($password));
      \Config::set('mail.encryption', trim($encryption));
      \Config::set('mail.from.address', trim($fromaddress));
      \Config::set('mail.from.name', trim($fromname));
  }
      public function order_review_add($order_number,$star,$review,$dlvrystar,$dlvryreview)
    {
        $timezone = 'ASIA/KOLKATA';
        if($review=='null' || $review=='Null' || $review=='NULL'){
            $review ='';
        }
        $details = DB::SELECT("select customer_id,rest_details->>'$.name' as rest_name,delivery_assigned_details->>'$.name' as staff_name,customer_details->>'$.name' as cust_name from order_master where order_number='$order_number'");
        $rest_name = $details[0]->rest_name;
        $staff_name = $details[0]->staff_name;
        $cust_name = $details[0]->cust_name;
        $cust_id = $details[0]->customer_id;
        $mailto = GeneralSettingSmtp::select('to_email')->first();
        $to_email = $mailto->to_email;
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i: A');
        Mail::send('Mailer_templates.review_add',
           ['cust_name'=>$cust_name,'rest_name'=>$rest_name,'staff_name'=>$staff_name,'star'=>$star,'review'=>$review,'dlvrystar'=>$dlvrystar,'dlvryreview'=>$dlvryreview,'to_email'=>$to_email], function ($message) use ($cust_name,$rest_name,$staff_name,$star,$review,$dlvrystar,$dlvryreview,$to_email) {
           $message->to($to_email)	
           ->subject('New Review Entered  For "'.$rest_name.'" and "'.$staff_name.'"');
           });
           
        $exist_check =DB::SELECT("SELECT email FROM customer_list WHERE id='".$cust_id."' and email IS NOT NULL");
        if(count($exist_check)>0)
        {
        $email = $exist_check[0]->email;
//        Mail::send('Mailer_templates.thankyou',
//           ['email'=>$email,'cust_name'=>$cust_name], function ($message) use ($email,$cust_name) {
//           $message->to($email)
//           ->subject('Thank You '.$cust_name.'' );
//           }); 
        }
        DB::SELECT('UPDATE `order_master` SET `review_star`="'.$star.'",review_details= JSON_OBJECT("review","'.$review.'","date","'.$datetime.'","status","N"),delivery_assigned_details=JSON_SET(delivery_assigned_details,"$.star_rate","'.$dlvrystar.'","$.review","'.$dlvryreview.'") WHERE order_number = "'.$order_number.'"');
        $msg = 'Successful';
        return response::json(['msg' => $msg]);
    }
    public function order_review_add_new(Request$request)
    {
        $order_number = $request['order_number'];
        $star = $request['restaurantstar'];
        $review = $request['restaurantreview'];
        $dlvrystar = $request['deliverystar'];
        $dlvryreview = $request['deliveryreview'];
        $timezone = 'ASIA/KOLKATA';
        if($review=='null' || $review=='Null' || $review=='NULL')
        {
            $review ='';
        }
        $details = DB::SELECT("select customer_id,rest_id,rest_details->>'$.name' as rest_name,delivery_assigned_details->>'$.name' as staff_name,customer_details->>'$.name' as cust_name from order_master where order_number='$order_number'");
        $rest_id = $details[0]->rest_id;
        $rest_name = $details[0]->rest_name;
        $staff_name = $details[0]->staff_name;
        $cust_name = $details[0]->cust_name;
        $cust_id = $details[0]->customer_id;
        $mailto = GeneralSettingSmtp::select('to_email')->first();
        $to_email = $mailto->to_email;
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i: A');
        $arr = ['cust_name'=>$cust_name,'rest_name'=>$rest_name,'staff_name'=>$staff_name,'star'=>$star,'review'=>$review,'dlvrystar'=>$dlvrystar,'dlvryreview'=>$dlvryreview,'to_email'=>$to_email];
       // return $arr;
//        Mail::send('Mailer_templates.review_add',
//           ['cust_name'=>$cust_name,'rest_name'=>$rest_name,'staff_name'=>$staff_name,'star'=>$star,'review'=>$review,'dlvrystar'=>$dlvrystar,'dlvryreview'=>$dlvryreview,'to_email'=>$to_email], function ($message) use ($cust_name,$rest_name,$staff_name,$star,$review,$dlvrystar,$dlvryreview,$to_email) {
//           $message->to($to_email)
//           ->subject('New Review Entered  For "'.$rest_name.'" and "'.$staff_name.'"');
//           });

        $exist_check =DB::SELECT("SELECT email FROM customer_list WHERE id='".$cust_id."' and email IS NOT NULL");
        if(count($exist_check)>0)
        {
        $email = $exist_check[0]->email;
//        Mail::send('Mailer_templates.thankyou',
//           ['email'=>$email,'cust_name'=>$cust_name], function ($message) use ($email,$cust_name) {
//           $message->to($email)
//           ->subject('Thank You '.$cust_name.'' );
//           });
        }
        DB::SELECT('UPDATE `order_master` SET `review_star`="'.$star.'",review_details= JSON_OBJECT("review","'.$review.'","date","'.$datetime.'","status","Y"),delivery_assigned_details=JSON_SET(delivery_assigned_details,"$.star_rate","'.$dlvrystar.'","$.review","'.$dlvryreview.'") WHERE order_number = "'.$order_number.'"');
        $msg = 'Successful';
        $takeavg_rating =  DB::SELECT("SELECT CAST(AVG(`review_star`) AS UNSIGNED) AS avg_rating FROM order_master where `rest_id` = $rest_id and `current_status` = 'D' AND review_star>0");
        $avg_rating = $takeavg_rating[0]->avg_rating;
        if($avg_rating<3){
            $avg_rating=3;
        }
        DB::UPDATE("UPDATE restaurant_master SET star_rating=json_set(star_rating,'$.value',$avg_rating) WHERE id=$rest_id");
      
        $detail = DB::SELECT("SELECT count(order_number) AS count FROM order_master WHERE rest_id ='".$rest_id."' and review_details->>'$.status' = 'Y'");
        if(count($detail[0]->count)>0)
        {
            DB::SELECT("update `restaurant_master` set `star_rating` = JSON_SET(`star_rating`,'$.count','" . $detail[0]->count . "') where id='" . $rest_id . "'");
        }

        return response::json(['msg' => $msg]);
    }
}
  