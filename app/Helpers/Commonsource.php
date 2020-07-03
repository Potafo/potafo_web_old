<?php

namespace Helpers;
use App\TaxMaster;
use Request;
use Config;
use App\Category;
use App\GeneralSetting;
use App\SubCategory;
use Helpers\Datasource;
use DB;
use DateTime;
use DateTimeZone;
use Razorpay\Api\Api;

class Commonsource
{
     //Update the subtotal each time when a user id given
    public static function subtotal($userid,$type)
    {
        $exist = DB::SELECT('select count(order_number) as count from order_details where order_number ="t_'.$userid.'"');
        foreach($exist as $key=>$listtt)
        {
           $all=$listtt->count;
        } 
        if($all=='0' && $type == 'Delete')
        {
            DB::SELECT('UPDATE `order_master` SET `sub_total`="0",final_total="0",total_details= JSON_SET(total_details,"$.delivery_charge","0","$.packing_charge","0") WHERE order_number = "t_'.$userid.'"');
        }
        else
        {
          $sub_totallist = DB::SELECT('SELECT (SUM(final_rate)) as subtotal FROM order_details WHERE order_number = "t_'.$userid.'"');
          $rest = DB::SELECT('SELECT rest_id FROM order_master WHERE order_number = "t_'.$userid.'"');
          foreach($sub_totallist as $key=>$list)
          {
             $subtotal=$list->subtotal;
          }
          foreach($rest as $key=>$lists)
          {
             $rest_id=$lists->rest_id;
          }
          $final = DB::SELECT('SELECT delivery_charge,packing_charge,bill_offer_exist,bill_offer_slno FROM restaurant_master WHERE id="'.$rest_id.'"');
          foreach($final as $key=>$listing)
          {
              $discount_amount =0;
              $offer_percent='';
              if($listing->bill_offer_exist=='Y'){
                     //$offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND offer_details->>'$.valid_to' >= '$datetime' AND offer_details->>'$.valid_from' <= '$datetime'");
                     $offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$listing->bill_offer_slno."' AND rest_id='".$rest_id."' AND DATE_FORMAT(offer_details->>'$.valid_to','%Y-%m-%d %T') >= now() AND DATE_FORMAT(offer_details->>'$.valid_from','%Y-%m-%d %T') <= now() ");
                     if(count($offer_info)!=0){
                              $max_amount    = $offer_info[0]->max_amount;
                              $amount_above  = $offer_info[0]->amount_above;
                              $offer_percent = $offer_info[0]->offer_percent;
                              if($subtotal>$amount_above){
                                  $discount_amount = ($subtotal*$offer_percent)/100;
                                  if($discount_amount>$max_amount){
                                      $discount_amount = $max_amount;
                                  }
                              }
                          }
                    }
            $delivery=$listing->delivery_charge;
            $packing=$listing->packing_charge;
          }
//          $finaltotal = $subtotal + $delivery + $packing;
//          $final_total = floatval(round($finaltotal));
          //DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_SET(total_details,"$.delivery_charge","'.$delivery.'","$.packing_charge","'.$packing.'") WHERE order_number = "t_'.$userid.'"');
      $finaltotal = ($subtotal + $delivery + $packing)-$discount_amount;
                $final_total = floatval(round($finaltotal));
            $checknull = DB::SELECT('SELECT total_details from order_master WHERE total_details is NULL and order_number ="t_'.$userid.'" and rest_id="'.$rest_id.'"');
                if(count($checknull)>0)
                {
                    DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_OBJECT("delivery_charge","'.$delivery.'","packing_charge","'.$packing.'","discount_amount","'.$discount_amount.'","discount_label","'.$offer_percent.'") WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
                }
                else
               {
                    DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_SET(total_details,"$.delivery_charge","'.$delivery.'","$.packing_charge","'.$packing.'","$.discount_amount","'.$discount_amount.'","$.discount_label","'.$offer_percent.'") WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
               }
          }
    
    }

    public static function generalsettings()
    {
        $general = GeneralSetting::where('id','!=','')->select('decimal_digit')->first();
        return $general['decimal_digit'];
    }
    public static function notifylimit()
    {
        $general = GeneralSetting::where('id','!=','')->select('notify_limit')->first();
        return $general['notify_limit'];
    }
    public static function credits_pay_reference()
    {
        $general = GeneralSetting::where('id','!=','')->select('credits_pay_reference')->first();
        return $general['credits_pay_reference'];
    }

    public static function item_pack_offer($orderid,$restid,$menuid,$order_qty) {
        $offer_exist = DB::SELECT("SELECT m_present_offers FROM restaurant_menu WHERE m_present_offers IS NOT NULL AND m_menu_id = '".$menuid."' AND m_rest_id='".$restid."' ");
        if(count($offer_exist)!=0){
            $portion_data = DB::SELECT("SELECT d.menu_details->>'$.portion' as portion_fld FROM order_details d WHERE d.menu_id='".$menuid."' AND d.rest_id='".$restid."' AND order_number='".$orderid."' ");
            $portion_newfld= $portion_data[0]->portion_fld;
            $offer_details = DB::SELECT("SELECT a.offer_details->>'$.qty' as of_menu_qty,a.offer_details->>'$.off_qty' as off_qty,SUBSTRING_INDEX(a.offer_details->>'$.offer_item',',',1) as offeritem,SUBSTRING_INDEX(a.offer_details->>'$.offer_item',',',-1) as offermenuportion,c.m_menu_id as offeritemid,JSON_UNQUOTE(b.m_category) as category FROM restaurant_offers a LEFT JOIN restaurant_menu b on a.rest_id=b.m_rest_id AND a.sl_no=b.m_present_offers->>'$.$portion_newfld.offer_slno' LEFT JOIN restaurant_menu c on a.rest_id=c.m_rest_id AND SUBSTRING_INDEX(a.offer_details->>'$.offer_item',',',1)=c.m_name_type->>'$.name' WHERE b.m_rest_id='".$restid."' AND b.m_menu_id='".$menuid."' AND a.active='Y' AND now() BETWEEN a.offer_details->>'$.valid_from' AND a.offer_details->>'$.valid_to' ");
              if(count($offer_details)!=0){
                   $order_menu_qty    = $offer_details[0]->of_menu_qty;
            $offer_menu_qty    = $offer_details[0]->off_qty;
            $offer_menu_name   = $offer_details[0]->offeritem;
            $offer_menu_potion = $offer_details[0]->offermenuportion;
            $offer_menu_id     = $offer_details[0]->offeritemid;
            $offer_menu_cat = implode(",",json_decode($offer_details[0]->category));
                $reslt_qty = ($order_qty/$order_menu_qty);
                $reslt_qty_int = floor($reslt_qty);
                $result_value = $reslt_qty_int*$offer_menu_qty;
                 $offer_menu_check = DB::SELECT("SELECT * FROM order_details WHERE order_number='".$orderid."' AND menu_id='".$offer_menu_id."' AND offer_of_id='".$menuid."' ");
                if($result_value>=1 && count($offer_menu_check)==0){
                   
                         DB::INSERT('INSERT INTO order_details(order_number, rest_id, menu_id, menu_details, single_rate_details, qty, final_rate,offer_of_id)'
                            . ' VALUES("'.$orderid.'","'.$restid.'","'.$offer_menu_id.'",json_object("portion","'.$offer_menu_potion.'","category","OfferItem","menu_name","'.$offer_menu_name.'","category","'.$offer_menu_cat.'","preference","null","single_rate","0"),json_object("exc_rate","0","inc_rate","0","extra_val","0","pack_rate","0.000"),"'.$result_value.'","0","'.$menuid.'")');
                
                }  
                else if($result_value>=1 && count($offer_menu_check)!=0){
                        DB::UPDATE('UPDATE `order_details` SET `qty`="'.$result_value.'",`final_rate`="0" WHERE order_number = "'.$orderid.'" and rest_id="'.$restid.'" and menu_id="'.$offer_menu_id.'" and offer_of_id="'.$menuid.'" ');
                }
                else if($result_value<1 && count($offer_menu_check)!=0){
                        DB::DELETE('DELETE FROM order_details WHERE order_number = "'.$orderid.'" and rest_id="'.$restid.'" and menu_id="'.$offer_menu_id.'" and offer_of_id="'.$menuid.'"');
                }
             }
             
           
        }
    }
    public static function apply_coupon_offer($userid,$couponcode,$optn) {
          $coupon_offer = 0;
         $result = 'Invalid Coupon Code';
        $offer_details = DB::SELECT("SELECT usage_limit,offer_details->>'$.name' as name,offer_details->>'$.amt_abv' as amt_abv,offer_details->>'$.max_amt' as max_amt,offer_details->>'$.offer_per' as offer_per FROM `general_offers` WHERE coupon_code='".$couponcode."'  AND active='Y' ");
       if(count($offer_details)!=0){
           $validity_check = DB::SELECT("SELECT usage_limit,offer_details->>'$.name' as name,offer_details->>'$.amt_abv' as amt_abv,offer_details->>'$.max_amt' as max_amt,offer_details->>'$.offer_per' as offer_per FROM `general_offers` WHERE coupon_code='".$couponcode."' AND now() BETWEEN offer_details->>'$.valid_from' AND offer_details->>'$.valid_to' AND active='Y' ");
            if(count($validity_check)!=0){
                 $amount_above = $offer_details[0]->amt_abv;
        $max_amount   = $offer_details[0]->max_amt;
        $offer_perc   = $offer_details[0]->offer_per;
        $coupon_name  = $offer_details[0]->name;
        $usage_limit  = $offer_details[0]->usage_limit;
        $usage_limit_check = DB::SELECT("SELECT count(*) as coupon_count  FROM order_master WHERE customer_id='$userid' and coupon_details->>'$.coupon_label'='$couponcode' AND order_number!='t_$userid' ");
        $usage_count = $usage_limit_check[0]->coupon_count;
        if($usage_count<$usage_limit){
           $order_details = DB::SELECT("SELECT sub_total,final_total,total_details->>'$.discount_amount' as discount_amount,total_details->>'$.packing_charge' as packing_charge,total_details->>'$.delivery_charge' as delivery_charge FROM order_master WHERE order_number='t_$userid' ");
            $sub_total = $order_details[0]->sub_total-$order_details[0]->discount_amount;
            if($sub_total>$amount_above ){
          
            $coupon_offer = ($sub_total*$offer_perc)/100;
            if($coupon_offer>$max_amount){
                $coupon_offer = $max_amount;
            }
                    if($optn=='add') {
                   $new_finaltotal = (($sub_total-$coupon_offer)+$order_details[0]->packing_charge+$order_details[0]->delivery_charge);
                   DB::UPDATE("UPDATE order_master SET coupon_details=json_object('coupon_label','".$couponcode."','coupon_amount','".$coupon_offer."','coupon_per','".$offer_perc."') WHERE order_number='t_$userid' ");
                        $result = 'valid';
                   }
                   else{
                      $new_finaltotal = $order_details[0]->final_total+$coupon_offer;
                       DB::UPDATE("UPDATE order_master SET coupon_details=NULL WHERE order_number='t_$userid' ");
                           $result = 'Removed';
                   }
          $new_finaltotal = floatval(round($new_finaltotal));
          DB::UPDATE("UPDATE order_master SET final_total='".$new_finaltotal."' WHERE order_number='t_$userid' ");

          
        }
        else
        {
            DB::UPDATE("UPDATE order_master SET coupon_details=NULL WHERE order_number='t_$userid' ");
           $result = 'Coupon Not Applicable'; 
        } 
        }
        else
        {
            $result = 'Coupon Usage Limit Exceeded!';  
        }
            }
            else
            {
                $result = 'Coupon Expired!';
            }
       }
       else
       {
          $result = 'Invalid Coupon Code!'; 
       }
       return ['msg'=>$result];
     }


    public static function googleapikey()
    {
        $general= GeneralSetting::where('id','1')->select('googleapi_key')->first();
        return $general['googleapi_key'];
    }

    public static function deliverylocking()
    {
        $general= GeneralSetting::where('id','1')->select('deliveryaddress_locking')->first();
        return $general['deliveryaddress_locking'];
    }

    public static function distance_calculate($lat1,$lat2,$lon1,$lon2)
    {
        $theta = $lon1 - $lon2;
        $dist  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist  = acos($dist);
        $dist  = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($miles * 1.609344);
    }

    //calculate restaurant location
    public static function locaterestaurant($request,$line1)
    {
        $arr = array();
        $restaurantarr = array();
        foreach ($request as $item)
        {
            $origin = $item->cordinates;
            if (!isset($origin) && ($origin == '' || $origin == '0'|| $origin == '0'|| $origin == 'null' || $origin == ' '))
            {
                $arr[] = $item;
            }
            else
            {
                $cordnate = explode(',',$origin);
                $radius = $item->range_unit;
                if(count($line1)>0 && count($cordnate)>0)
                {
                   /* if($cordnate[0] == null)
                    {
                        return $cordnate[0];

                    }
                    else{
                        return 'ok';
                    }*/

                 if($line1[0] == '' || $line1[0] == null ||$line1[1] == null || $cordnate[0] == null || $cordnate[1] == null || $line1[0] == 'NULL' || $line1[0] == '0' ||$line1[0] == ' ' || $cordnate[1] == ''|| $cordnate[1] == 'NULL'|| $cordnate[1] == '0'|| $cordnate[1] == ' '|| $cordnate[0] == ''|| $cordnate[0] == 'NULL'|| $cordnate[0] == '0'|| $cordnate[0] == ' ') {

                }
                    else{
                        $dest_distance = Commonsource::distance_calculate($line1[0], $cordnate[0], $line1[1], $cordnate[1]);
                        if ($dest_distance <= $radius)
                        {
                            $msg = 'success';
                            $arr[] = $item;
                            if(!in_array($item->res_id,$restaurantarr))
                            {
                                $restaurantarr[] = $item->res_id;
                            }
                        } else {
                            $msg = 'not success';
                        }
                    }
                }

                /*    $snw = urlencode($origin) . '&destinations=' . urlencode($line1) . '&key=' . $googlekey;
                      $menus = file_get_contents($snw);
                      $menulist = json_decode($menus, true);
                      $distance = $menulist['rows'][0]['elements'][0]['distance']['text'];
                      $distance_unit = explode(' ', $distance)[1];
                      $dest_distance = explode(' ', $distance)[0];
                      if (strtoupper($distance_unit) == 'KM')
                      {
                         /* if ($radius > 0)
                          {*/
                /*  if ($dest_distance <= $radius)
                  {
                      $msg = 'success';
                      $arr[] = $item;

                  } else {
                      $msg = 'not success';
                  }*/
                /* } else {
                     $msg = 'Restaurant Distance Radius Not Set';
                 }*/
                /*   }
                   else if (strtoupper($distance_unit) == 'M')
                   {
                       $msg = 'success';
                       $arr[] = $item;
                   }*/
            }
        }
        return array($arr,$restaurantarr);
    }

    //get latitude & longitude of restaurants based on address passed as parameter
    public static function latitude_longitude($line1)
    {
            $googlekey = Commonsource::googleapikey();
            $snw = 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input='.urlencode($line1).'&inputtype=textquery&fields=geometry&key=' . $googlekey;
            $distance = file_get_contents($snw);
            $list = json_decode($distance, true);
            if(strtoupper($list['status']) == 'ZERO_RESULTS')
            {
                $count = '0';
                $latitude ="";
                $longitude ="";
            }
            else if(strtoupper($list['status']) == 'OK')
            {
            $latitude = $list['candidates'][0]['geometry']['location']['lat'];
            $longitude = $list['candidates'][0]['geometry']['location']['lng'];
            $count = '1';
           }
           return array($latitude,$longitude,$count);
    }

    public static function notification($post)
    {
        $app_type = $post['app_type'];
        $to = $post['to'];
        $title = $post['title'];
        $message = $post['message'];
        $image_url = $post['image'];
        $action = $post['action'];
        $action_destination = $post['action_destination'];
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'body' => $message,
            'sound' => 'default',
            'priority'=> 'high',
            'content_available' =>true,
            'title' => $title,
            'click_action' =>$action,
        ];
        $extraNotificationData = [
            'title' => $title,
            "message" => $message,
            "sound" => 'default',
        ];
        if(isset($image_url) && $image_url != 'null')
        {
            $extraNotificationData['image'] = $image_url;
        }
        if(isset($action) && $action != 'null')
        {
            $extraNotificationData['action'] = $action;
        }
        if(isset($action_destination) && $action_destination != 'null')
        {
            $extraNotificationData['action_destination'] = $action_destination;
        }
		
		if($action == 'orders')
		{
			$fcmNotification = [
            'to'        => $to,
            'data'      => $extraNotificationData,
           ];
			
		}
		else
		{
		$fcmNotification = [
            'to'        => $to,
            'data'      => $extraNotificationData,
            'notification' => $notification,
        ];
		}
				

        if(isset($app_type) && $app_type != 'null')
        {
            if(strtolower($app_type) == 'deliveryapp')
            {
                $headers = [
                   'Authorization: key=AAAAEsd9m18:APA91bGYCX0kmwNdEcxPa3QjfYTKVWu2j65hDVTJtA-Fe1eafTNYgs4PlkSS7sLt4j54ZN6Ys8saaI8xaoXmKUQ7Oa82KRb4Hi91RNuR7eplYk5dquIMnCYpMSnr75CqQE1YGSys6tZC',
                    'Content-Type: application/json',
                ];
            }
            else if(strtolower($app_type) == 'customerapp')
            {
                $headers = [
                  'Authorization: key=AIzaSyDHSnD12dXziacSveYlEI86EW5QHJRumyc',
                    'Content-Type: application/json',
                ];
            }
			else if(strtolower($app_type) == 'partnerapp')
            {
                $headers = [
                  'Authorization: key=AAAAFjL-aZA:APA91bHuYF4YLlI4h02B8HsEsVbdowzQW6aBmyZ726Yuw_CWVg5nWmnHlUQ05Apr6ORpa3o7W8kH8oznoMwKptPspiAKKf_TMCIVKsWm4sg_oym8cNKoE5orptCGyec5iL4pET4W18U2',
                    'Content-Type: application/json',
                ];
            }
            else if(strtolower($app_type) == 'potafo_mart')
            {
                $headers = [
                  'Authorization: key=AAAA9Kn6Ono:APA91bEDjurKmnG9dPzrSLt8ooFhz9hEuBt1s7UuH05lBDWJ_VI6wRMx-ugFss_PefhbbJPa-IywjBS4Y61HP4ynDZcS0XXmvRLviU7BDFJPpy4zntCFVJH9gnYd18UreBd8VbNc8AxJ',
                    'Content-Type: application/json',
                ];
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        $results = json_decode($result,true);
        if(isset($results['results'][0]['message_id']))
        {
            return $results['results'][0]['message_id'];
        }
        else
        {
            if(strtolower($to) == '/topics/potafo')
            {
            }
            else
            {
               $select =  DB::SELECT('SELECT COUNT(*) FROM ftoken_master WHERE ftoken = "'.trim($to).'"');
               if(count($select) >0)
               {
                   DB::SELECT('delete FROM ftoken_master WHERE ftoken = "'.$to.'"');
               }
            }
        }
        return $results;
    }
    public static function group_notification($post)
    {
        $app_type = $post['app_type'];
        $tokens = $post['tokens'];
        $title = $post['title'];
        $message = $post['message'];
        $image_url = $post['image'];
        $action = $post['action'];
        $action_destination = $post['action_destination'];
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'body' => $message,
            'sound' => 'default',
            'priority'=> 'high',
            'content_available' =>true,
            'title' => $title,
            'click_action' =>$action,
        ];
        $extraNotificationData = [
            'title' => $title,
            "message" => $message,
            "sound" => 'default',
        ];
        if(isset($image_url) && $image_url != 'null')
        {
            $extraNotificationData['image'] = $image_url;
        }
        if(isset($action) && $action != 'null')
        {
            $extraNotificationData['action'] = $action;
        }
        if(isset($action_destination) && $action_destination != 'null')
        {
            $extraNotificationData['action_destination'] = $action_destination;
        }
        $fcmNotification = [
            'registration_ids' => $tokens,
            'data'      => $extraNotificationData,
            'notification' => $notification,
        ];
		
		if(isset($app_type) && $app_type != 'null')
        {
            if(strtolower($app_type) == 'deliveryapp')
            {
                $headers = [
                   'Authorization: key=AAAAEsd9m18:APA91bGYCX0kmwNdEcxPa3QjfYTKVWu2j65hDVTJtA-Fe1eafTNYgs4PlkSS7sLt4j54ZN6Ys8saaI8xaoXmKUQ7Oa82KRb4Hi91RNuR7eplYk5dquIMnCYpMSnr75CqQE1YGSys6tZC',
                    'Content-Type: application/json',
                ];
            }
            else if(strtolower($app_type) == 'customerapp')
            {
                $headers = [
                  'Authorization: key=AIzaSyDHSnD12dXziacSveYlEI86EW5QHJRumyc',
                    'Content-Type: application/json',
                ];
            }
			else if(strtolower($app_type) == 'partnerapp')
            {
                $headers = [
                  'Authorization: key=AAAAFjL-aZA:APA91bHuYF4YLlI4h02B8HsEsVbdowzQW6aBmyZ726Yuw_CWVg5nWmnHlUQ05Apr6ORpa3o7W8kH8oznoMwKptPspiAKKf_TMCIVKsWm4sg_oym8cNKoE5orptCGyec5iL4pET4W18U2',
                    'Content-Type: application/json',
                ];
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        $results = json_decode($result,true);
        if(isset($results['results'][0]['message_id']))
        {
            return $results['results'][0]['message_id'];
        }
        return $results;
    }

    public static function repeatavailability($user)
    {
        $status = 'N';
        $portionkey = '';
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y m d h:i:s a');
        $time = strtoupper($date->format('h:i a'));
        $times = strtoupper($date->format('H:i:s'));
        $day = strtoupper($date->format('D'));
        $list = DB::select("select `rest_id`,sl_no,menu_details->>'$.portion' as portion,menu_details->>'$.single_rate' as single_rate,menu_id as menu_id,qty from order_details where order_number = 't_$user'");
        foreach($list as $item)
        {
               $slno = $item->sl_no;
               $portion=  $item->portion;
               $single_rate  =$item->single_rate;
               $details = DB::SELECT("SELECT m_rest_id,json_length(m_por_rate) as len,m_pack_rate,JSON_KEYS(m_por_rate) as portion_keys,m_por_rate,m_days,m_time,m_status,m_menu_id FROM restaurant_menu left Join restaurant_master  on restaurant_menu.m_rest_id = restaurant_master.id WHERE m_rest_id ='$item->rest_id'  and m_menu_id = '$item->menu_id'  and JSON_SEARCH(UPPER(m_days), 'one','".$day."') is not null  and UPPER(m_status) = 'Y'  and '$times' >= m_time->>'$.from' AND '$times' <= m_time->>'$.to'");
                if(count($details) > 0)
               {
                   $portionkeys= JSON_DECODE($details[0]->portion_keys,true);
                   $portionval= JSON_DECODE($details[0]->m_por_rate,true);
                   if(count($portionkeys) >0)
                   {
                       $status = 'N';
                       foreach($portionkeys as $key=>$val)
                       {
                             $por = $portionval[$val]['portion'];
                             if(strtolower($por) == strtolower($portion))
                             {
                                 $status = 'Y';
                                 $portionkey = $val;
                             }
                       }
                   }

                   if($status  == 'N')
                   {
                       self::deleteorder_detail($user,$slno);
                   }
                   else if($status == 'Y')
                   {
                       $incrate = $portionval[$portionkey]['inc_rate'];
                       $excrate = $portionval[$portionkey]['exc_rate'];
                       $extraval = $portionval[$portionkey]['extra_val'];
                       $pack_rate = $details[0]->m_pack_rate;
                       $finalrate = $portionval[$portionkey]['final_rate'] + $pack_rate;
                       $totalrate = $finalrate*$item->qty;
                       if($single_rate != $portionval[$portionkey]['final_rate'])
                       {
                              $porrate  = '{"exc_rate": "'.$excrate.'", "inc_rate": "'.$incrate.'", "extra_val": "'.$extraval.'", "pack_rate": "'.$pack_rate.'"}';
                               DB::SELECT("update `order_details` set single_rate_details = '".$porrate."',menu_details =JSON_SET(menu_details, '$.single_rate','".$finalrate."'),final_rate = $totalrate where order_number = 't_$user' and sl_no = '$slno'");
                       }
                   }
               }
                else
                {
                  self::deleteorder_detail($user,$slno);
                }
        }
    }

    public static function deleteorder_detail($user,$slno)
    {
        DB::SELECT("DELETE from order_details where order_number = 't_$user' and sl_no = '$slno'");
    }

    public static function checkrestaurantvalidity($id,$key)
    {
        $details = DB::SELECT('SELECT id FROM users WHERE restaurant_id="'.$id.'" and token ="'.$key.'"');
        if(count($details)>0)
        {
            return 'Exist';
        }
        else
        {
            return 'Invalid Credentials';
        }

    }

      //COD CALL CONFIRM LIMIT
    public static function codcallconfirmlimit()
    {
        $general= GeneralSetting::where('id','1')->select('cod_call_confirm_limit')->first();
        return $general['cod_call_confirm_limit'];
    }
    
     //REST confirmation alert secs
    public static function restconfirmationalert()
    {
        $general= GeneralSetting::where('id','1')->select('rest_conf_alert_sec')->first();
        return $general['rest_conf_alert_sec'];
    }
	
	public static function getDatabaseName()
    {
    $databaseName = \DB::connection()->getDatabaseName();
    return $databaseName;
    
    }
}