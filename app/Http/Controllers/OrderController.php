<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use DB;
use Response;
use Session;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
use Helpers\Commonsource;
use App\Designation;
use App\CustomerList;
use App\Restaurant_Master;
use App\OrderDetails;
use App\OrderMaster;
use App\RestaurantMenu;
use DateTime;
use DateTimeZone;
use Razorpay\Api\Api;
class OrderController extends Controller
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
    public function cart_order($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion)//API to add the details to the cart
    {
        $orderexist = DB::SELECT('SELECT order_number FROM `order_master` WHERE order_number = "t_'.$userid.'"');

        if(count($orderexist)>0)
        {
            $changeexist = DB::SELECT("select order_number from order_details WHERE order_number ='t_$userid'");
            if(count($changeexist)>0)
            {
                $order = DB::SELECT('SELECT order_number FROM `order_master` WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
                if(count($order)>0)
                {
                    $addcart = $this->addtocart($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                    $msg = $addcart;
                }
                else
                {
                    $ord = DB::SELECT('SELECT order_number FROM `order_details` WHERE order_number = "t_'.$userid.'" and menu_id = "'.$item_id.'"and rest_id="'.$rest_id.'"');
                    if(count($ord)>0)
                    {
                        $addcart = $this->addtocart($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                        $msg =$addcart;
                    }
                    else
                    {
                        $msg = "Restaurant has been changed. Clear the Cart.";
                    }
                }
            }
            else
            {
                $restlist = Restaurant_Master::where('id',$rest_id)->select('name_tagline->name as restname')->first();
                $rest_name=json_decode($restlist->restname);
                DB::SELECT("UPDATE order_master SET rest_id='$rest_id',rest_details=JSON_OBJECT('name','$rest_name') WHERE order_number ='t_$userid'");
                $addcart = $this->addtocart($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                $msg = $addcart;
            }
        }
        else
        {
            $custmerlist = CustomerList::where('id',$userid)->select("name","mobile_contact")->first();
            if(count($custmerlist)>0)
            {
                $cust_name=$custmerlist->name;
                $cust_mobile=$custmerlist->mobile_contact;
                $restlist = Restaurant_Master::where('id',$rest_id)->select('name_tagline->name as restname')->first();
                $rest_name=json_decode($restlist->restname);
                DB::INSERT("INSERT INTO `order_master`(`order_number`,`customer_id`,`customer_details`,`rest_id`,`rest_details`) VALUES ('t_" . $userid . "','" . trim($userid) . "',json_object('name','" .$cust_name. "','mobile','" .$cust_mobile. "'),'".$rest_id."',json_object('name','" .$rest_name. "'))");
                $addcart = $this->addtocart($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                $msg = $addcart;
            }
        }
        $cartlist = DB::SELECT("SELECT sl_no,JSON_UNQUOTE(menu_details->'$.menu_name') as menu,`qty`,JSON_UNQUOTE(menu_details->'$.single_rate') as single_rate,`final_rate`,JSON_UNQUOTE(menu_details->'$.preference') as preference,JSON_UNQUOTE(menu_details->'$.portion') as portion from order_details WHERE order_number = 't_$userid' ORDER BY sl_no");
        if (count($cartlist)>0)
        {
            return response::json(compact('msg','cartlist'));
        }
        else
        {
            return response::json(compact('msg'));
        }
    }

    public function cart_order_new(Request $request)//API to add the details to the cart
    {

        $item_id = $request['itemid'];
        $rate = $request['rate'];
        $qty = $request['qty'];
        $prefrnce = $request['preference'];
        $userid = $request['userid'];
        $cust_token = $request['cust_token'];
        $rest_id = $request['rest_id'];
        $portion = $request['portion'];

      
        
        $is_valid_user = DB::SELECT("SELECT id FROM `customer_list` WHERE id='".$userid."' AND cust_token ='".$cust_token."' ");
        if(count($is_valid_user)!=0){
            $orderexist = DB::SELECT('SELECT order_number FROM `order_master` WHERE order_number = "t_'.$userid.'"');
            if(count($orderexist)>0)
            {
                $changeexist = DB::SELECT("select order_number from order_details WHERE order_number ='t_$userid'");
                if(count($changeexist)>0)
                {
                    $order = DB::SELECT('SELECT order_number FROM `order_master` WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
                    if(count($order)>0)
                    {
                        $addcart = $this->addtocart($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                        $msg = $addcart;
                    }
                    else
                    {
                        $ord = DB::SELECT('SELECT order_number FROM `order_details` WHERE order_number = "t_'.$userid.'" and menu_id = "'.$item_id.'"and rest_id="'.$rest_id.'"');
                        if(count($ord)>0)
                        {
                            $addcart = $this->addtocart($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                            $msg =$addcart;
                        }
                        else
                        {
                            $msg = "Restaurant has been changed. Clear the Cart.";
                        }
                    }
                }
                else
                {
                    $restlist = Restaurant_Master::where('id',$rest_id)->select('name_tagline->name as restname')->first();

                    $rest_name=json_decode($restlist->restname);
                    DB::SELECT("UPDATE order_master SET rest_id='$rest_id',rest_details=JSON_OBJECT('name','$rest_name') WHERE order_number ='t_$userid'");
                    $addcart = $this->addtocart($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                    $msg = $addcart;
                }
            }
            else
            {
                
                $custmerlist = CustomerList::where('id',$userid)->select("name","mobile_contact")->first();
                if(count($custmerlist)>0)
                {
                    $cust_name=$custmerlist->name;
                    $cust_mobile=$custmerlist->mobile_contact;
                    $restlist = Restaurant_Master::where('id',$rest_id)->select('name_tagline->name as restname')->first();
                    $rest_name=json_decode($restlist->restname);
                    DB::INSERT("INSERT INTO `order_master`(`order_number`,`customer_id`,`customer_details`,`rest_id`,`rest_details`) VALUES ('t_" . $userid . "','" . trim($userid) . "',json_object('name','" .$cust_name. "','mobile','" .$cust_mobile. "'),'".$rest_id."',json_object('name','" .$rest_name. "'))");
                    $addcart = $this->addtocart($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                    $msg = $addcart;
                }
            }
            $cartlist = DB::SELECT("SELECT sl_no,JSON_UNQUOTE(menu_details->'$.menu_name') as menu,`qty`,JSON_UNQUOTE(menu_details->'$.single_rate') as single_rate,`final_rate`,JSON_UNQUOTE(menu_details->'$.preference') as preference,JSON_UNQUOTE(menu_details->'$.portion') as portion from order_details WHERE order_number = 't_$userid' ORDER BY sl_no");
            if (count($cartlist)>0)
            {
                return response::json(compact('msg','cartlist'));
            }
            else
            {
                return response::json(compact('msg'));
            }
        }
        else{
            $msg = "invalid info";
            return response::json(compact('msg'));
        }

    }

    public function addtocart($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i A');
        $samemenu  = DB::SELECT('SELECT order_number FROM `order_details` WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'" and menu_id="'.$item_id.'" and menu_details->"$.single_rate" = "'.$rate.'"');
        if(count($samemenu)>0)
        {
            $finalqty= $qty;
            $finalrate = $finalqty * $rate;
            DB::UPDATE('UPDATE `order_details` SET `qty`="'.$finalqty.'",`final_rate`="'.$finalrate.'" WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'" and menu_id="'.$item_id.'" and menu_details->"$.single_rate" = "'.$rate.'"');
            Commonsource::item_pack_offer("t_$userid",$rest_id,$item_id,$finalqty);
            $sub_totallist = DB::SELECT('SELECT (SUM(final_rate)) as subtotal FROM order_details WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            foreach($sub_totallist as $key=>$list)
            {
                $subtotal=$list->subtotal;
            }
            $final = DB::SELECT('SELECT delivery_charge,packing_charge,bill_offer_exist,bill_offer_slno FROM restaurant_master WHERE id="'.$rest_id.'"');
            foreach($final as $key=>$list)
            {
                $discount_amount =0;
                $offer_percent=0;
                if($list->bill_offer_exist=='Y'){
                    //$offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND offer_details->>'$.valid_to' >= '$datetime' AND offer_details->>'$.valid_from' <= '$datetime'");
                    $offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND DATE_FORMAT(offer_details->>'$.valid_to','%Y-%m-%d %T') >= now() AND DATE_FORMAT(offer_details->>'$.valid_from','%Y-%m-%d %T') <= now() ");
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
                $delivery=$list->delivery_charge;
                $packing=$list->packing_charge;
            }
            $finaltotal = ($subtotal + $delivery + $packing)-$discount_amount;
            $final_total =  round($finaltotal,0);
            $checknull = DB::SELECT('SELECT total_details from order_master WHERE total_details is NULL and order_number ="t_'.$userid.'" and rest_id="'.$rest_id.'"');
            if(count($checknull)>0)
            {
                DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_OBJECT("delivery_charge","'.$delivery.'","packing_charge","'.$packing.'","discount_amount","'.$discount_amount.'","discount_label","'.$offer_percent.'") WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            }
            else
            {
                DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_SET(total_details,"$.delivery_charge","'.$delivery.'","$.packing_charge","'.$packing.'","$.discount_amount","'.$discount_amount.'","$.discount_label","'.$offer_percent.'") WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            }
            $coupon_exist = DB::SELECT("SELECT coupon_details->>'$.coupon_label' as coupon_label FROM order_master WHERE order_number='t_$userid' AND coupon_details IS NOT NULL");
            if(count($coupon_exist)!=0){
                $couponcode = $coupon_exist[0]->coupon_label;
                $addcoupoun =  Commonsource::apply_coupon_offer($userid,$couponcode,'add');
            }
//            }
        }
        else
        {
            
            $final_rate = $qty * $rate;
            $menulist =  DB::SELECT("SELECT JSON_UNQUOTE(m_name_type->'$.name') as menu,m_por_rate as portion,m_pack_rate as packrate,JSON_LENGTH(`m_por_rate`) as count,JSON_UNQUOTE(m_category) as category FROM `restaurant_menu` WHERE  m_menu_id = '".$item_id."' and m_rest_id ='".$rest_id."'");
            foreach($menulist as $key=>$list)
            {
                $menu_name=$list->menu;
                $category = implode(",",json_decode($list->category));
                $pack_rate=$list->packrate;
                for($i = 1; $i<=$list->count;$i++)
                {
                    $json_data = json_decode($list->portion,true);
                    $portionsave = strtoupper($json_data['portion'.$i]['portion']);
                    $portiongiven = strtoupper($portion);
                   // return $portionsave;
                    if($portionsave == $portiongiven)
                    {
                        $exc_rate = strtoupper($json_data['portion'.$i]['exc_rate']);
                        $inc_rate = strtoupper($json_data['portion'.$i]['inc_rate']);
                        $extra_val = strtoupper($json_data['portion'.$i]['extra_val']);
                        
                    }
                    else
                    {
                        continue;
                    }
					
					
					$inv_offer_details = DB::SELECT("SELECT IFNULL(a.inv_offer_details->>'$.$portion.exc_rate',0) as exc_rate,IFNULL(a.inv_offer_details->>'$.$portion.inc_tax_rate',0) as inc_tax_rate,IFNULL(a.inv_offer_details->>'$.$portion.extra_val',0) as extra_val,IFNULL(a.inv_offer_details->>'$.$portion.pack_rate',0) as pack_rate FROM restaurant_menu a LEFT JOIN restaurant_offers b on a.m_rest_id=b.rest_id AND a.inv_offer_details->>'$.$portion.offer_slno'=b.sl_no WHERE a.m_rest_id='" .$rest_id . "' AND a.m_menu_id='" . $item_id . "' AND now() BETWEEN a.inv_offer_details->>'$.$portion.valid_from' AND inv_offer_details->>'$.$portion.valid_to' AND b.active='Y'");
					if(count($inv_offer_details)!=0){
						$exc_rate = $inv_offer_details[0]->exc_rate;
						$inc_rate = $inv_offer_details[0]->inc_tax_rate;
						$extra_val = $inv_offer_details[0]->extra_val;
						$pack_rate = $inv_offer_details[0]->pack_rate;
					}
					else
                    {
                        continue;
                    }
					
				 
				 
                }
            }
            DB::INSERT("INSERT INTO `order_details`(`order_number`,`rest_id`,`menu_id`,`menu_details`,`qty`,`final_rate`,`single_rate_details`) VALUES ('t_" . $userid . "','" . trim($rest_id) . "','" . trim($item_id) . "',json_object('menu_name','" .$menu_name. "','single_rate','" .$rate. "','preference','".$prefrnce."','portion','".$portion."','category','".$category."'),'".$qty."','$final_rate',json_object('exc_rate','" .$exc_rate. "','inc_rate','" .$inc_rate. "','extra_val','".$extra_val."','pack_rate','".$pack_rate."'))");
            Commonsource::item_pack_offer("t_$userid",trim($rest_id),trim($item_id),$qty);
            $sub_totallist = DB::SELECT('SELECT (SUM(final_rate)) as subtotal FROM order_details WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            foreach($sub_totallist as $key=>$list)
            {
                $subtotal=$list->subtotal;
            }
            $final = DB::SELECT('SELECT delivery_charge,packing_charge,bill_offer_exist,bill_offer_slno FROM restaurant_master WHERE id="'.$rest_id.'"');
            foreach($final as $key=>$list)
            {
                $discount_amount =0;
                $offer_percent=0;
                if($list->bill_offer_exist=='Y'){
                    //$offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND offer_details->>'$.valid_to' >= '$datetime' AND offer_details->>'$.valid_from' <= '$datetime'");
                    $offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND DATE_FORMAT(offer_details->>'$.valid_to','%Y-%m-%d %T') >= now() AND DATE_FORMAT(offer_details->>'$.valid_from','%Y-%m-%d %T') <= now() ");
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
                $delivery=$list->delivery_charge;
                $packing=$list->packing_charge;
            }
            $finaltotal = ($subtotal + $delivery + $packing)-$discount_amount;
            $final_total = round($finaltotal,0);
            $checknull = DB::SELECT('SELECT total_details from order_master WHERE total_details is NULL and order_number ="t_'.$userid.'" and rest_id="'.$rest_id.'"');
            if(count($checknull)>0)
            {
                DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_OBJECT("delivery_charge","'.$delivery.'","packing_charge","'.$packing.'","discount_amount","'.$discount_amount.'","discount_label","'.$offer_percent.'") WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            }
            else
            {
                DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_SET(total_details,"$.delivery_charge","'.$delivery.'","$.packing_charge","'.$packing.'","$.discount_amount","'.$discount_amount.'","$.discount_label","'.$offer_percent.'") WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            }
            $coupon_exist = DB::SELECT("SELECT coupon_details->>'$.coupon_label' as coupon_label FROM order_master WHERE order_number='t_$userid' AND coupon_details IS NOT NULL");
            if(count($coupon_exist)!=0){
                $couponcode = $coupon_exist[0]->coupon_label;
                $addcoupoun =  Commonsource::apply_coupon_offer($userid,$couponcode,'add');
            }
        }
        $msg = "Added to Cart";
        return $msg;
    }

    public function cart_list($userid)//API to list the cart details as per user ID
    {
        $decimal_point = Commonsource::generalsettings();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y m d h:i:s a');
        $time = strtoupper($date->format('h:i a'));
        $operationtime = strtoupper($date->format('H:i:s'));
        $day = strtoupper($date->format('l'));
        $show_contact_less = DB::SELECT("SELECT show_contact_less FROM general_settings");
        $contact_less = $show_contact_less[0]->show_contact_less;
        $exist = DB::SELECT("select order_details.order_number from order_details LEFT JOIN order_master ON order_master.order_number=order_details.order_number where order_details.order_number='t_$userid'");
        if (count($exist)>0)
        {
            $restlist = DB::SELECT("SELECT rest_id,JSON_UNQUOTE(name_tagline->'$.name') as name,status,busy,force_close,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,logo,category FROM restaurant_master LEFT JOIN order_master ON restaurant_master.id=order_master.rest_id WHERE order_number='t_$userid'");
            
            if( $restlist[0]->category == 'Potafo Mart')
            {
                $contact_less = 'N';
            }            
            $resttimelist = DB::SELECT("select rt_rest_id as rest_id,rt_from_time as from_time,rt_to_time as to_time from restaurant_timings left join order_master on restaurant_timings.rt_rest_id=order_master.rest_id join restaurant_master on restaurant_master.id = restaurant_timings.rt_rest_id where order_number='t_$userid' and rt_day = '$day' and time(rt_from_time) <= '$operationtime' and time(rt_to_time) >='$operationtime'");

            if( $restlist[0]->busy == 'Y')
            {
                $restlist = DB::SELECT("SELECT rest_id,JSON_UNQUOTE(name_tagline->'$.name') as name,busy,force_close,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,logo,'Busy' as status FROM restaurant_master LEFT JOIN order_master ON restaurant_master.id=order_master.rest_id WHERE order_number='t_$userid'");

            }
            else if( $restlist[0]->force_close == 'Y')
            {
                $restlist = DB::SELECT("SELECT rest_id,JSON_UNQUOTE(name_tagline->'$.name') as name,busy,force_close,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,logo,'Closed' as status FROM restaurant_master LEFT JOIN order_master ON restaurant_master.id=order_master.rest_id WHERE order_number='t_$userid'");

            }
            else
            {
//                foreach($resttimelist as $key=>$value)
//                {
//                    $reststatus = $restlist[0]->status;
//                    $open      = strtoupper($value->from_time);
//                    $close     = strtoupper($value->to_time);
//                    if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close) && $reststatus=='Y' )
//                    {
//                        $c =0;
//                        $restlist = DB::SELECT("SELECT rest_id,JSON_UNQUOTE(name_tagline->'$.name') as name,busy,forceclose_by,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,logo,'Open' as status FROM restaurant_master LEFT JOIN order_master ON restaurant_master.id=order_master.rest_id WHERE order_number='t_$userid'");
//                        break;
//                    }
//                    else
//                    {
//                        $c= 1;
//                        $restlist = DB::SELECT("SELECT rest_id,JSON_UNQUOTE(name_tagline->'$.name') as name,busy,forceclose_by,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,logo,'Closed' as status FROM restaurant_master LEFT JOIN order_master ON restaurant_master.id=order_master.rest_id WHERE order_number='t_$userid'");
//                    }
//                }
                if(count($resttimelist) != 0)
                {
                    $restlist = DB::SELECT("SELECT rest_id,JSON_UNQUOTE(name_tagline->'$.name') as name,busy,force_close,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,logo,'Open' as status FROM restaurant_master LEFT JOIN order_master ON restaurant_master.id=order_master.rest_id WHERE order_number='t_$userid'");
                }
                else
                {
                    $restlist = DB::SELECT("SELECT rest_id,JSON_UNQUOTE(name_tagline->'$.name') as name,busy,force_close,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,logo,'Closed' as status FROM restaurant_master LEFT JOIN order_master ON restaurant_master.id=order_master.rest_id WHERE order_number='t_$userid'");
                }
            }
            $rest_id = $restlist[0]->rest_id;
            $cartlist = DB::SELECT("SELECT sl_no,JSON_UNQUOTE(menu_details->'$.menu_name') as menu,m_diet as diet,`qty`,ROUND(JSON_UNQUOTE(menu_details->'$.single_rate'),$decimal_point) as single_rate,ROUND(`final_rate`,$decimal_point) AS final_rate,JSON_UNQUOTE(menu_details->'$.preference') as preference,JSON_UNQUOTE(menu_details->'$.portion') as portion from order_details LEFT JOIN restaurant_menu ON restaurant_menu.m_menu_id=order_details.menu_id WHERE order_number = 't_$userid' and m_rest_id = '$rest_id' ORDER BY sl_no");
            $total = DB::SELECT("SELECT ROUND(sub_total,$decimal_point) AS sub_total,ROUND(final_total,$decimal_point) AS final_total,ROUND(JSON_UNQUOTE(total_details->'$.delivery_charge'),$decimal_point) as delivery_charge,ROUND(JSON_UNQUOTE(total_details->'$.packing_charge'),$decimal_point) as packing_charge,ROUND(JSON_UNQUOTE(total_details->'$.discount_amount'),$decimal_point) as discount,total_details->>'$.discount_label' as discount_label,count(sl_no) as menu_count,IFNULL(coupon_details->>'$.coupon_per',0) as coupon_per,IFNULL(coupon_details->>'$.coupon_label',0) as coupon_label,IFNULL(coupon_details->>'$.coupon_amount',0) as coupon_amount FROM order_master LEFT JOIN order_details ON order_details.order_number=order_master.order_number WHERE order_master.order_number = 't_$userid'");
            if (count($cartlist)>0)
            {
                $msg = 'Exist';
                return response::json(['msg' => $msg,'cartlist' => $cartlist,'restaurant'=>$restlist[0],'total' => $total[0],'show_contact_less' => $contact_less]);
            }
            else
            {
                $msg = 'Not Exist'; 
                return response::json(['msg' => $msg,'show_contact_less' => $contact_less]);
            }
        }
        else
        {
            $msg = 'Cart Empty';
            return response::json(['msg' => $msg,'show_contact_less' => $contact_less]);
        }
    }

    public function cart_clear(Request $request)//API to clear the cart as per User ID
    {
        $userid     = $request['user_id'];
        $cust_token = $request['cust_token'];
        $cust_exist = DB::SELECT("SELECT id FROM customer_list WHERE id='$userid' AND cust_token='$cust_token' ");
        if(count($cust_exist)!=0) {
            $exist      = DB::SELECT("select order_number from order_details where order_number = 't_$userid'");
            if(count($exist)>0)
            {
                DB::select("delete from order_details where order_number = 't_$userid'");
                DB::select("delete from order_master where order_number = 't_$userid'");
                $msg = 'Cart Cleared';
            }
            else
            {
                $msg = 'Cart Empty';
            }

        } else{
            $msg = 'Cart Empty';
        }

        return response::json(['msg' => $msg]);
    }


    

    public function cart_edit($userid,$slno,$qty,$prefrnce)//API to edit the details of the cart like preference and qty as per slno and User Id
    {
        $ratelist = DB::SELECT("SELECT menu_id,rest_id,JSON_UNQUOTE(menu_details->'$.single_rate') as single_rate,JSON_UNQUOTE(menu_details->'$.portion') as portion,JSON_UNQUOTE(menu_details->'$.menu_name') as menu_name,JSON_UNQUOTE(menu_details->'$.category') as category from order_details WHERE order_number = 't_$userid' and sl_no = '$slno'");
        $single_rate=0;
        foreach($ratelist as $key=>$list)
        {
            $single_rate=$list->single_rate;
            $portion =$list->portion;
            $menu_name=$list->menu_name;
            $menu_id=$list->menu_id;
            $rest_id=$list->rest_id;
            $category=$list->category;
        }
        $finalrate = $single_rate * $qty;
        DB::SELECT("UPDATE order_details SET `menu_details`=json_object('portion','" . $portion . "','menu_name','" . $menu_name . "','preference','" . $prefrnce . "','single_rate','".$single_rate."','category','".$category."'),qty = '$qty',final_rate = '$finalrate' WHERE order_number='t_$userid' and sl_no = '$slno'");
        $abcd = Commonsource::subtotal($userid,'Edit');
        $msg = 'Edited';
        Commonsource::item_pack_offer("t_$userid",$rest_id,$menu_id,$qty);
//$orderid="t_$userid";$restid=$rest_id;$menuid=$menu_id;$order_qty=$qty;
        $coupon_exist = DB::SELECT("SELECT coupon_details->>'$.coupon_label' as coupon_label FROM order_master WHERE order_number='t_$userid' AND coupon_details IS NOT NULL");
        if(count($coupon_exist)!=0){
            $couponcode = $coupon_exist[0]->coupon_label;
            $addcoupoun =  Commonsource::apply_coupon_offer($userid,$couponcode,'add');
        }
        return response::json(['msg' => $msg,'item_rate'=>round($finalrate,2),'qty'=>$qty]);
    }

    public function cart_edit_new(Request $request)//API to edit the details of the cart like preference and qty as per slno and User Id
    {
        $userid = $request['userid'];
        $slno = $request['slno'];
        $qty = $request['qty'];
        $prefrnce = $request['preference'];
        $ratelist = DB::SELECT("SELECT menu_id,rest_id,JSON_UNQUOTE(menu_details->'$.single_rate') as single_rate,JSON_UNQUOTE(menu_details->'$.portion') as portion,JSON_UNQUOTE(menu_details->'$.menu_name') as menu_name,JSON_UNQUOTE(menu_details->'$.category') as category from order_details WHERE order_number = 't_$userid' and sl_no = '$slno'");
        $single_rate=0;
        foreach($ratelist as $key=>$list)
        {
            $single_rate=$list->single_rate;
            $portion =$list->portion;
            $menu_name=$list->menu_name;
            $menu_id=$list->menu_id;
            $rest_id=$list->rest_id;
            $category=$list->category;
        }
        $finalrate = $single_rate * $qty;
        DB::SELECT("UPDATE order_details SET `menu_details`=json_object('portion','" . $portion . "','menu_name','" . $menu_name . "','preference','" . $prefrnce . "','single_rate','".$single_rate."','category','".$category."'),qty = '$qty',final_rate = '$finalrate' WHERE order_number='t_$userid' and sl_no = '$slno'");
        $abcd = Commonsource::subtotal($userid,'Edit');
        $msg = 'Edited';
        Commonsource::item_pack_offer("t_$userid",$rest_id,$menu_id,$qty);
//$orderid="t_$userid";$restid=$rest_id;$menuid=$menu_id;$order_qty=$qty;
        $coupon_exist = DB::SELECT("SELECT coupon_details->>'$.coupon_label' as coupon_label FROM order_master WHERE order_number='t_$userid' AND coupon_details IS NOT NULL");
        if(count($coupon_exist)!=0){
            $couponcode = $coupon_exist[0]->coupon_label;
            $addcoupoun =  Commonsource::apply_coupon_offer($userid,$couponcode,'add');
        }
        return response::json(['msg' => $msg,'item_rate'=>round($finalrate,2),'qty'=>$qty]);
    }
    public function cartitem_delete($userid,$slno)//API to delete the details of the cart as per slno and User Id
    {
        $details = DB::SELECT("SELECT order_number,menu_id from order_details WHERE order_number = 't_$userid' and sl_no = '$slno'");
        if(count($details)>0)
        {
            DB::select("delete from order_details where order_number = 't_$userid' and sl_no = '$slno'");
            DB::DELETE("DELETE FROM `order_details` WHERE order_number='t_$userid' AND offer_of_id='".$details[0]->menu_id."' ");
            $sum = Commonsource::subtotal($userid,'Delete');
            $msg = 'Item Deleted';
        }
        else
        {
            $msg = 'No Item';
        }
        $coupon_exist = DB::SELECT("SELECT coupon_details->>'$.coupon_label' as coupon_label FROM order_master WHERE order_number='t_$userid' AND coupon_details IS NOT NULL");
        if(count($coupon_exist)!=0){
            $couponcode = $coupon_exist[0]->coupon_label;
            $addcoupoun =  Commonsource::apply_coupon_offer($userid,$couponcode,'add');
        }
        return response::json(['msg' => $msg]);

    }
    public function cartitem_delete_new(Request $request)//API to delete the details of the cart as per slno and User Id
    {
        $userid = $request['userid'];
        $slno   = $request['slno'];
        $cust_token   = $request['cust_token'];
        $is_valid = DB::SELECT("SELECT id FROM `customer_list` WHERE id='".$userid."' AND cust_token ='".$cust_token."' ");
        if(count($is_valid)!=0) {
            $details = DB::SELECT("SELECT order_number,menu_id from order_details WHERE order_number = 't_$userid' and sl_no = '$slno'");
            if(count($details)>0)
            {
                DB::select("delete from order_details where order_number = 't_$userid' and sl_no = '$slno'");
                DB::DELETE("DELETE FROM `order_details` WHERE order_number='t_$userid' AND offer_of_id='".$details[0]->menu_id."' ");
                $sum = Commonsource::subtotal($userid,'Delete');
                $msg = 'Item Deleted';
            }
            else
            {
                $msg = 'No Item';
            }
            $coupon_exist = DB::SELECT("SELECT coupon_details->>'$.coupon_label' as coupon_label FROM order_master WHERE order_number='t_$userid' AND coupon_details IS NOT NULL");
            if(count($coupon_exist)!=0){
                $couponcode = $coupon_exist[0]->coupon_label;
                $addcoupoun =  Commonsource::apply_coupon_offer($userid,$couponcode,'add');
            }
        } else{
            $msg = 'Invalid Info';
        }

        return response::json(['msg' => $msg]);

    }
    public function cart_total($userid)//API to return the menu count and final total for particular User Id
    {
        $lists=DB::SELECT("select order_number from order_details where order_number = 't_$userid'");
        $list = DB::SELECT("select count(od.order_number) as item_count,om.final_total as final_total FROM order_details as od LEFT JOIN order_master om ON od.order_number=om.order_number WHERE od.order_number = 't_$userid'");
        if(count($lists)>0)
        {
            $msg = 'Exist';
            return response::json(['msg' => $msg,'cart_total' => $list[0]]);
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }

    public function cart_order_web(Request $request)//API to add the details to the cart
    {
        $item_id  = $request['item_id'];
        $rate     = $request['rate'];
        $qty      = $request['qty'];
        $prefrnce = $request['prefrnce'];
        $userid   = $request['userid'];
        $rest_id  = $request['rest_id'];
        $portion  = $request['portion'];
        $cust_token  = $request['cust_token'];
        $is_valid = DB::SELECT("SELECT id FROM `customer_list` WHERE id='".$userid."' AND cust_token ='".$cust_token."' ");
        if(count($is_valid)!=0) {
            $orderexist = DB::SELECT('SELECT order_number FROM `order_master` WHERE order_number = "t_'.$userid.'"');
            if(count($orderexist)>0)
            {
                $changeexist = DB::SELECT("select order_number from order_details WHERE order_number ='t_$userid'");
                if(count($changeexist)>0)
                {
                    $order = DB::SELECT('SELECT order_number FROM `order_master` WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
                    if(count($order)>0)
                    {
                        $addcart = $this->addtocart_web($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                        $msg = $addcart;
                    }
                    else
                    {

                        $ord = DB::SELECT('SELECT order_number FROM `order_details` WHERE order_number = "t_'.$userid.'" and menu_id = "'.$item_id.'"and rest_id="'.$rest_id.'"');
                        if(count($ord)>0)
                        {
                            $addcart = $this->addtocart_web($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                            $msg =$addcart;
                        }
                        else
                        {
                            $msg = "Restaurant has been changed. Clear the Cart.";
                        }
                    }
                }
                else
                {
                    $restlist = Restaurant_Master::where('id',$rest_id)->select('name_tagline->name as restname')->first();

                    $rest_name=json_decode($restlist->restname);
                    DB::SELECT("UPDATE order_master SET rest_id='$rest_id',rest_details=JSON_OBJECT('name','$rest_name') WHERE order_number ='t_$userid'");
                    $addcart = $this->addtocart_web($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                    $msg = $addcart;
                }
            }
            else
            {
                $custmerlist = CustomerList::where('id',$userid)->select("name","mobile_contact")->first();
                $cust_name=$custmerlist->name;
                $cust_mobile=$custmerlist->mobile_contact;
                $restlist = Restaurant_Master::where('id',$rest_id)->select('name_tagline->name as restname')->first();
                $rest_name=json_decode($restlist->restname);
                DB::INSERT("INSERT INTO `order_master`(`order_number`,`customer_id`,`customer_details`,`rest_id`,`rest_details`) VALUES ('t_" . $userid . "','" . trim($userid) . "',json_object('name','" .$cust_name. "','mobile','" .$cust_mobile. "'),'".$rest_id."',json_object('name','" .$rest_name. "'))");
                $addcart = $this->addtocart_web($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion);
                $msg = $addcart;
            }
            $cartlist = DB::SELECT("SELECT sl_no,JSON_UNQUOTE(menu_details->'$.menu_name') as menu,`qty`,JSON_UNQUOTE(menu_details->'$.single_rate') as single_rate,`final_rate`,JSON_UNQUOTE(menu_details->'$.preference') as preference,JSON_UNQUOTE(menu_details->'$.portion') as portion from order_details WHERE order_number = 't_$userid' and rest_id='$rest_id' ORDER BY sl_no");
            if (count($cartlist)>0)
            {
                return response::json(compact('msg','cartlist'));
            }
            else
            {
                return response::json(compact('msg'));
            }
        } else{
            $msg = "Invalid Info";
            return response::json(compact('msg'));
        }

    }

    public function addtocart_web($item_id,$rate,$qty,$prefrnce,$userid,$rest_id,$portion)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i A');
        $samemenu  = DB::SELECT('SELECT order_number FROM `order_details` WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'" and menu_id="'.$item_id.'" and menu_details->"$.single_rate" = "'.$rate.'"');
        if(count($samemenu)>0)
        {
            $qtylist =  DB::SELECT('SELECT qty FROM `order_details` WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'" and menu_id="'.$item_id.'" and menu_details->"$.single_rate" = "'.$rate.'"');
            foreach($qtylist as $key=>$list)
            {
                $firstqty=$list->qty;
            }
            $finalqty= (($firstqty) + ($qty));
            $finalrate = $finalqty * $rate;
            DB::SELECT('UPDATE `order_details` SET `qty`="'.$finalqty.'",`final_rate`="'.$finalrate.'" WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'" and menu_id="'.$item_id.'" and menu_details->"$.single_rate" = "'.$rate.'"');
            Commonsource::item_pack_offer("t_$userid",$rest_id,$item_id,$finalqty);
            //$orderid="t_$userid";$restid=$rest_id;$menuid=$item_id;$order_qty=$finalqty;

            $sub_totallist = DB::SELECT('SELECT (SUM(final_rate)) as subtotal FROM order_details WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            foreach($sub_totallist as $key=>$list)
            {
                $subtotal=$list->subtotal;
            }
            $final = DB::SELECT('SELECT delivery_charge,packing_charge,bill_offer_exist,bill_offer_slno FROM restaurant_master WHERE id="'.$rest_id.'"');
            foreach($final as $key=>$list)
            {
                $discount_amount =0;
                $offer_percent=0;
                if($list->bill_offer_exist=='Y'){
                    //$offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND offer_details->>'$.valid_to' >= '$datetime' AND offer_details->>'$.valid_from' <= '$datetime'");
                    $offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND DATE_FORMAT(offer_details->>'$.valid_to','%Y-%m-%d %T') >= now() AND DATE_FORMAT(offer_details->>'$.valid_from','%Y-%m-%d %T') <= now() ");
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

                $delivery=$list->delivery_charge;
                $packing=$list->packing_charge;

            }
            $finaltotal = ($subtotal + $delivery + $packing)-$discount_amount;
            $final_total =round($finaltotal,0);
            $checknull = DB::SELECT('SELECT total_details from order_master WHERE total_details is NULL and order_number ="t_'.$userid.'" and rest_id="'.$rest_id.'"');
            if(count($checknull)>0)
            {
                DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_OBJECT("delivery_charge","'.$delivery.'","packing_charge","'.$packing.'","discount_amount","'.$discount_amount.'","discount_label","'.$offer_percent.'") WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            }
            else
            {
                DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_SET(total_details,"$.delivery_charge","'.$delivery.'","$.packing_charge","'.$packing.'","$.discount_amount","'.$discount_amount.'","$.discount_label","'.$offer_percent.'") WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            }
            $coupon_exist = DB::SELECT("SELECT coupon_details->>'$.coupon_label' as coupon_label FROM order_master WHERE order_number='t_$userid' AND coupon_details IS NOT NULL");
            if(count($coupon_exist)!=0){
                $couponcode = $coupon_exist[0]->coupon_label;
                $addcoupoun =  Commonsource::apply_coupon_offer($userid,$couponcode,'add');
            }
//            }
        }
        else
        {
            $final_rate = $qty * $rate;
            $menulist =  DB::SELECT("SELECT JSON_UNQUOTE(m_name_type->'$.name') as menu,m_por_rate as portion,m_pack_rate as packrate,JSON_LENGTH(`m_por_rate`) as count,JSON_UNQUOTE(m_category) as category  FROM `restaurant_menu` WHERE  m_menu_id = '".$item_id."' and m_rest_id ='".$rest_id."'");
            foreach($menulist as $key=>$list)
            {
                $menu_name=$list->menu;
                $category = implode(",",json_decode($list->category,true));
                $pack_rate=$list->packrate;
                for($i = 1; $i<=$list->count;$i++)
                {
                    $json_data = json_decode($list->portion,true);
                    $portionsave = strtoupper($json_data['portion'.$i]['portion']);
                    $portiongiven = strtoupper($portion);
                    if($portionsave == $portiongiven)
                    {
                        $exc_rate = strtoupper($json_data['portion'.$i]['exc_rate']);
                        $inc_rate = strtoupper($json_data['portion'.$i]['inc_rate']);
                        $extra_val = strtoupper($json_data['portion'.$i]['extra_val']);
                        break;
                    }
                    else
                    {
                        continue;
                    }
					
					$inv_offer_details = DB::SELECT("SELECT IFNULL(a.inv_offer_details->>'$.$portion.exc_rate',0) as exc_rate,IFNULL(a.inv_offer_details->>'$.$portion.inc_tax_rate',0) as inc_tax_rate,IFNULL(a.inv_offer_details->>'$.$portion.extra_val',0) as extra_val,IFNULL(a.inv_offer_details->>'$.$portion.pack_rate',0) as pack_rate FROM restaurant_menu a LEFT JOIN restaurant_offers b on a.m_rest_id=b.rest_id AND a.inv_offer_details->>'$.$portion.offer_slno'=b.sl_no WHERE a.m_rest_id='" .$rest_id . "' AND a.m_menu_id='" . $item_id . "' AND now() BETWEEN a.inv_offer_details->>'$.$portion.valid_from' AND inv_offer_details->>'$.$portion.valid_to' AND b.active='Y'");
					if(count($inv_offer_details)!=0){
						$exc_rate = $inv_offer_details[0]->exc_rate;
						$inc_rate = $inv_offer_details[0]->inc_tax_rate;
						$extra_val = $inv_offer_details[0]->extra_val;
						$pack_rate = $inv_offer_details[0]->pack_rate;
					}
					else
                    {
                        continue;
                    }
					
                }

            }
            DB::INSERT("INSERT INTO `order_details`(`order_number`,`rest_id`,`menu_id`,`menu_details`,`qty`,`final_rate`,`single_rate_details`) VALUES ('t_" . $userid . "','" . trim($rest_id) . "','" . trim($item_id) . "',json_object('menu_name','" .$menu_name. "','single_rate','" .$rate. "','preference','".$prefrnce."','portion','".$portion."','category','".$category."'),'".$qty."','$final_rate',json_object('exc_rate','" .$exc_rate. "','inc_rate','" .$inc_rate. "','extra_val','".$extra_val."','pack_rate','".$pack_rate."'))");
            Commonsource::item_pack_offer("t_$userid",$rest_id,$item_id,$qty);

            //$orderid = $order_details[0]->order_number;$restid=$rest_id;$menuid=$item_id;$order_qty=$qty;$optn='add';

            $sub_totallist = DB::SELECT('SELECT (SUM(final_rate)) as subtotal FROM order_details WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            foreach($sub_totallist as $key=>$list)
            {
                $subtotal=$list->subtotal;
            }
            $final = DB::SELECT('SELECT delivery_charge,packing_charge,bill_offer_exist,bill_offer_slno FROM restaurant_master WHERE id="'.$rest_id.'"');
            foreach($final as $key=>$list)
            {
                $discount_amount =0;
                $offer_percent=0;
                if($list->bill_offer_exist=='Y'){
                    //$offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND offer_details->>'$.valid_to' >= '$datetime' AND offer_details->>'$.valid_from' <= '$datetime'");
                    $offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND DATE_FORMAT(offer_details->>'$.valid_to','%Y-%m-%d %T') >= now() AND DATE_FORMAT(offer_details->>'$.valid_from','%Y-%m-%d %T') <= now() ");
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
                $delivery=$list->delivery_charge;
                $packing=$list->packing_charge;
            }
            $finaltotal = ($subtotal + $delivery + $packing)-$discount_amount;
            $final_total = round($finaltotal,0);
            $checknull = DB::SELECT('SELECT total_details from order_master WHERE total_details is NULL and order_number ="t_'.$userid.'" and rest_id="'.$rest_id.'"');
            if(count($checknull)>0)
            {
                DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_OBJECT("delivery_charge","'.$delivery.'","packing_charge","'.$packing.'","discount_amount","'.$discount_amount.'","discount_label","'.$offer_percent.'") WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            }
            else
            {
                DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_SET(total_details,"$.delivery_charge","'.$delivery.'","$.packing_charge","'.$packing.'","$.discount_amount","'.$discount_amount.'","$.discount_label","'.$offer_percent.'") WHERE order_number = "t_'.$userid.'" and rest_id="'.$rest_id.'"');
            }
            $coupon_exist = DB::SELECT("SELECT coupon_details->>'$.coupon_label' as coupon_label FROM order_master WHERE order_number='t_$userid' AND coupon_details IS NOT NULL");
            if(count($coupon_exist)!=0){
                $couponcode = $coupon_exist[0]->coupon_label;
                $addcoupoun =  Commonsource::apply_coupon_offer($userid,$couponcode,'add');
            }
        }
        $msg = "Added to Cart";
        return $msg;
    }


    public function view_order(Request $request)
    {
        $staffid = Session::get('staffid');
		if(!$staffid){return redirect('');}
        /*      $filterarr = array();
                $time1 = new DateTime('09:00:59');
                $time2 = new DateTime('11:01:00');
                $interval = $time2->diff($time1);
                return $interval->format('%s second(s)');*/
//      $difference = strtotime( 'h:i:s') - strtotime( '2:35:00' );
//      return $difference;
        $keydata = new DateTime("3:40:10");
        $nextAppt = new DateTime("3:55:23");
        $diff = $keydata->getTimestamp() - $nextAppt->getTimestamp();
        if (abs($diff) > 60*15)
        {
            // TODO
        }
        $cat_test='';
        
//      $all_orders = DB::SELECT("SELECT `rest_id`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`customer_details`->>'$.mobile' as mobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time,payment_method FROM `order_master` WHERE current_status != 'T' AND current_status != 'D' AND current_status != 'CA' order by order_date DESC");
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
        $all_orders = DB::SELECT("SELECT `rest_id`,`payment_method`,`order_number`,`rest_confirmed`,`on_hold`,`on_hold_release_time`,`customer_id`,`customer_details`->>'$.name' as name,IF(`customer_details`->>'$.latitude' IS NULL or `customer_details`->>'$.latitude' = '', '0', `customer_details`->>'$.latitude') as latitude,IF(`customer_details`->>'$.longitude' IS NULL or `customer_details`->>'$.longitude' = '', '0', `customer_details`->>'$.longitude') as longitude,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`customer_details`->>'$.mobile' as mobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time,time(order_date) as ordertime,payment_method,no_contact_del,readytopick,assign_status FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '".$staffid."' ) ".$cat_test." and current_status != 'T' AND current_status != 'D' AND current_status != 'CA'   order by order_date DESC");//AND DATE(order_date) = CURRENT_DATE()
        return view('order.manage_order',compact('rows','filterarr','all_orders','order_cat'));
    }

    public function manage_order_filter_div(Request $request)
    {
        $staffid                =   $request['staff_id'];
        $order_rest_filter      =   $request['order_rest_filter'];
        $order_name_filter      =   $request['order_name_filter'];
        $order_phone_filter     =   $request['order_phone_filter'];
        $order_status_filter    =   $request['order_status_filter'];
        $order_number_filter    =   $request['order_number_filter'];
        $order_cat_filter    = $request['order_cat_filter'];
       /* $order_cat = DB::SELECT("SELECT `order_list_cat` FROM `internal_staffs` WHERE `id`='".$staffid."'");
        foreach($order_cat as $valt){
           $newval=$valt->order_list_cat;
           if($newval == "potafo_mart")
                {
               $order_cat_filter    ="Potafo Mart";
                }else if($newval == "restaurant")
                {
                    $order_cat_filter    ="Restaurant";
                }else if($newval == "all")
                {
                    $order_cat_filter    = "All";//  $request['order_cat_filter'];
                }
           }*/
               
              
        
        $search = "";

        if($order_rest_filter != "")
        {
            if($search != "")
            {
                $search.= "and `rest_details`->>'$.name' LIKE '".$order_rest_filter."%'";
            }else{
                $search.= "`rest_details`->>'$.name' LIKE '".$order_rest_filter."%'";
            }
        }

        if($order_name_filter != "")
        {
            if($search != "")
            {
                $search.= " and `customer_details`->>'$.name' LIKE '".$order_name_filter."%'";
            }else{
                $search.= "`customer_details`->>'$.name' LIKE  '".$order_name_filter."%'";
            }
        }

        if($order_phone_filter != "")
        {
            if($search != "")
            {
                $search.= " and `customer_details`->>'$.mobile' LIKE '".$order_phone_filter."%'";
            }else{
                $search.= "`customer_details`->>'$.mobile' LIKE  '".$order_phone_filter."%'";
            }
        }

        if($order_status_filter == "")
        {
            if($search != "")
            {
                $search.= " and `current_status` != 'D' and `current_status` != 'T' and `current_status` != 'CA'";
            }else{
                $search.= "`current_status` != 'D' and `current_status` != 'T' and `current_status` != 'CA'";
            }
        }else{
            if($search != "")
            {
                $search.= " and `current_status` = '".$order_status_filter."'";
            }else{
                $search.= "`current_status` =  '".$order_status_filter."'";
            }
        }

        if($order_number_filter != "")
        {
            if($search != "")
            {
                $search.= " and `order_number` LIKE '".$order_number_filter."%'";
            }else{
                $search.= "`order_number` LIKE  '".$order_number_filter."%'";
            }
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

        if($search != "")
        {
            $new_serach =  "and $search";
        }
        else
        {
            $new_serach = "";
        }
        //and DATE(order_date) = CURRENT_DATE()
        $datecheck='';
        if($order_status_filter == "" || $order_status_filter == "P" || $order_status_filter == "C" || $order_status_filter == "OP")
        {
        $datecheck='';
        }else
        {
           $datecheck= " and DATE(order_date) = CURRENT_DATE() ";
        }

        $all_orders = DB::SELECT("SELECT `rest_id`,time(order_date) as ordertime,`order_number`,`on_hold`,`on_hold_release_time`,`rest_confirmed`,`customer_id`,`customer_details`->>'$.name' as name,IF(`customer_details`->>'$.latitude' IS NULL or `customer_details`->>'$.latitude' = '', '0', `customer_details`->>'$.latitude') as latitude,IF(`customer_details`->>'$.longitude' IS NULL or `customer_details`->>'$.longitude' = '', '0', `customer_details`->>'$.longitude') as longitude,`customer_details`->>'$.mobile' as mobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time,	no_contact_del,assign_status FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '".$staffid."') $new_serach  $datecheck order by order_date DESC");
//         $all_orders = DB::SELECT("SELECT `rest_id`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,`customer_details`->>'$.mobile' as mobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time FROM `order_master` $new_serach and DATE(order_date) = CURRENT_DATE() order by order_date DESC");

        $append     = "";
        $append_2   = "";

        if(count($all_orders)>0){
            foreach($all_orders as $orders){
                if(timediff($orders->time) == 'Y')
                {
                    $stats = " delayed_order";
                }
                else
                {
                    $stats = ' ';
                }
                  if(confirmationdiff($orders->ordertime,isset($orders->on_hold_release_time)?$orders->on_hold_release_time:0) == 'Y' && $orders->rest_confirmed == 'N')
                {
                    $dlystats = " delayed_confirmation";
                }
                else
                {
                    $dlystats = ' ';
                }
                 if($orders->on_hold == 'Y')
                {
                    $holdstats = "on_hold";
                }
                else
                {
                    $holdstats = ' ';
                }
                if($orders->rest_confirmed == 'Y')
                {
                                        $sts='confirm_place';

                }
                else
                {
                                     $sts= 'new_order_1';
   
                }
                $inpg="";
                if($orders->assign_status == 'Inprogress')
                {
                   $inpg=" <span style='color:Red'> (I)<span>"; 
                }
                if($orders->current_status == 'P')
                {
                    $append .=    '<div class="col-md-3 col-sm-6 col-xs-12 '.$holdstats.'">';
                    $append .=        '<div class="current_order_box  '.$sts.' '.$dlystats.'" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.',,'.$orders->latitude.','.$orders->longitude.',\''.$orders->no_contact_del.'\'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');">';
                    $append .=            '<div class="current_order_number">'.$orders->order_number.'</div>';
                    $append .=            '<div class="current_order_time">';
                    $append .=               '<strong >'.$orders->time.$inpg.'</strong>';
                    $append .=            '</div>';
                    $append .=            '<div class="current_order_restaurant">'.$orders->rest_name.'</div>';
                    $append .=            '<div class="current_order_user_detail">';
                    $append .=                '<div class="current_order_user_name">'.$orders->name.' -- '.$orders->mobile.'</div>';
                    $append .=            '</div>';
                    $append .=        '</div>';
                    $append .=    '</div>';
                }else if($orders->current_status == 'C')
                {
                    
//                    $append .=    '<div class="delayed_order">';
                    $append .=    '<div class="col-md-3 col-sm-6 col-xs-12'.$stats.'">';
                    $append .=        '<div class="current_order_box near_delivery_1 '.$holdstats.'" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.',,'.$orders->latitude.','.$orders->longitude.',\''.$orders->no_contact_del.'\'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');">';
                    $append .=            '<div class="current_order_number">'.$orders->order_number.'</div>';
                    $append .=            '<div class="current_order_time">';
                    $append .=               '<strong >'.$orders->time.$inpg.'</strong>';
                    $append .=            '</div>';
                    $append .=            '<div class="current_order_restaurant">'.$orders->rest_name.'</div>';
                    $append .=            '<div class="current_order_user_detail">';
                    $append .=                '<div class="current_order_user_name">'.$orders->name.' -- '.$orders->mobile.'</div>';
                    $append .=            '</div>';
                    $append .=        '</div>';
                    $append .=    '</div>';
//                $append .=    '</div>';
                }else if($orders->current_status == 'OP')
                {
//                    $append .=    '<div class="delayed_order">';
                    $append .=    '<div class="col-md-3 col-sm-6 col-xs-12'.$stats.'">';
                    $append .=        '<div class="current_order_box new_pick_up" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.',,'.$orders->latitude.','.$orders->longitude.',\''.$orders->no_contact_del.'\'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');">';
                    $append .=            '<div class="current_order_number">'.$orders->order_number.'</div>';
                    $append .=            '<div class="current_order_time">';
                    $append .=               '<strong >'.$orders->time.$inpg.'</strong>';
                    $append .=            '</div>';
                    $append .=            '<div class="current_order_restaurant">'.$orders->rest_name.'</div>';
                    $append .=            '<div class="current_order_user_detail">';
                    $append .=                '<div class="current_order_user_name">'.$orders->name.' -- '.$orders->mobile.'</div>';
                    $append .=            '</div>';
                    $append .=        '</div>';
                    $append .=    '</div>';
//                $append .=    '</div>';
                }else if($orders->current_status == 'D')
                {
                    $append .=    '<div class="col-md-3 col-sm-6 col-xs-12">';
                    $append .=        '<div class="current_order_box new_deliverd" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.',,'.$orders->latitude.','.$orders->longitude.',\''.$orders->no_contact_del.'\'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');">';
                    $append .=            '<div class="current_order_number">'.$orders->order_number.'</div>';
                    $append .=            '<div class="current_order_time">';
                    $append .=               '<strong >'.$orders->time.$inpg.'</strong>';
                    $append .=            '</div>';
                    $append .=            '<div class="current_order_restaurant">'.$orders->rest_name.'</div>';
                    $append .=            '<div class="current_order_user_detail">';
                    $append .=                '<div class="current_order_user_name">'.$orders->name.' -- '.$orders->mobile.'</div>';
                    $append .=            '</div>';
                    $append .=        '</div>';
                    $append .=    '</div>';
                }
                else if($orders->current_status == 'CA')
                {
                    $append .=    '<div class="col-md-3 col-sm-6 col-xs-12">';
                    $append .=        '<div class="current_order_box new_cancelled" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.',,'.$orders->latitude.','.$orders->longitude.',\''.$orders->no_contact_del.'\'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');">';
                    $append .=            '<div class="current_order_number">'.$orders->order_number.'</div>';
                    $append .=            '<div class="current_order_time">';
                    $append .=               '<strong >'.$orders->time.$inpg.'</strong>';
                    $append .=            '</div>';
                    $append .=            '<div class="current_order_restaurant">'.$orders->rest_name.'</div>';
                    $append .=            '<div class="current_order_user_detail">';
                    $append .=                '<div class="current_order_user_name">'.$orders->name.' -- '.$orders->mobile.'</div>';
                    $append .=            '</div>';
                    $append .=        '</div>';
                    $append .=    '</div>';
                }
                else if($orders->current_status == 'SA')
                {
                    $append .=    '<div class="col-md-3 col-sm-6 col-xs-12">';
                    $append .=        '<div class="current_order_box new_assigned" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.',,'.$orders->latitude.','.$orders->longitude.',\''.$orders->no_contact_del.'\'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');">';
                    $append .=            '<div class="current_order_number">'.$orders->order_number.'</div>';
                    $append .=            '<div class="current_order_time">';
                    $append .=               '<strong >'.$orders->time.$inpg.'</strong>';
                    $append .=            '</div>';
                    $append .=            '<div class="current_order_restaurant">'.$orders->rest_name.'</div>';
                    $append .=            '<div class="current_order_user_detail">';
                    $append .=                '<div class="current_order_user_name">'.$orders->name.' -- '.$orders->mobile.'</div>';
                    $append .=            '</div>';
                    $append .=        '</div>';
                    $append .=    '</div>';
                }
            }
        }
//$sss="SELECT `rest_id`,time(order_date) as ordertime,`order_number`,`on_hold`,`on_hold_release_time`,`rest_confirmed`,`customer_id`,`customer_details`->>'$.name' as name,`customer_details`->>'$.latitude' as latitude,`customer_details`->>'$.longitude' as longitude,`customer_details`->>'$.mobile' as mobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '".$staffid."') $new_serach and DATE(order_date) = CURRENT_DATE() order by order_date DESC";
//return $sss;
        return $append;
    }

    public function manage_order_filter_tables(Request $request)
    {
        $staff_id               =   $request['staff_id'];
        $order_rest_filter      =   strtoupper($request['order_rest_filter']);
        $order_name_filter      =   strtoupper($request['order_name_filter']);
        $order_phone_filter     =   $request['order_phone_filter'];
        $order_status_filter    =   $request['order_status_filter'];
        $order_number_filter    =   $request['order_number_filter'];
       
         /* $order_cat = DB::SELECT("SELECT `order_list_cat` FROM `internal_staffs` WHERE `id`='".$staffid."'");
        foreach($order_cat as $valt){
           $newval=$valt->order_list_cat;
           if($newval == "potafo_mart")
                {
               $order_cat_filter    ="Potafo Mart";
                }else if($newval == "restaurant")
                {
                    $order_cat_filter    ="Restaurant";
                }else
                {
                    $order_cat_filter    = "All";//  $request['order_cat_filter'];
                }
           }*/
              
        $order_cat_filter    =   $request['order_cat_filter'];
        $search = "";

        if($order_rest_filter != "")
        {
            if($search != "")
            {
                $search.= "and UPPER(rest_details->>'$.name') LIKE '".$order_rest_filter."%'";
            }else{
                $search.= "UPPER(rest_details->>'$.name') LIKE '".$order_rest_filter."%'";
            }
        }

        if($order_name_filter != "")
        {
            if($search != "")
            {
                $search.= " and UPPER(customer_details->>'$.name') LIKE '".$order_name_filter."%'";
            }else{
                $search.= "UPPER(customer_details->>'$.name') LIKE  '".$order_name_filter."%'";
            }
        }

        if($order_phone_filter != "")
        {
            if($search != "")
            {
                $search.= " and `customer_details`->>'$.mobile' LIKE '".$order_phone_filter."%'";
            }else{
                $search.= "`customer_details`->>'$.mobile' LIKE  '".$order_phone_filter."%'";
            }
        }

        if($order_status_filter == "")
        {
            if($search != "")
            {
                $search.= " and `current_status` != 'D' and `current_status` != 'T' and `current_status` != 'CA'";
            }else{
                $search.= "`current_status` != 'D' and `current_status` != 'T' and `current_status` != 'CA'";
            }
        }else{
            if($search != "")
            {
                $search.= " and `current_status` = '".$order_status_filter."'";
            }else{
                $search.= "`current_status` =  '".$order_status_filter."'";
            }
        }

        if($order_number_filter != "")
        {
            if($search != "")
            {
                $search.= " and `order_number` LIKE '".$order_number_filter."%'";
            }else{
                $search.= "`order_number` LIKE  '".$order_number_filter."%'";
            }
        }
        
        if($order_cat_filter != "")
        {
            if($search != "")
            {
                if($order_cat_filter == "Potafo Mart")
                {
                    $search.= " and `category` = '".$order_cat_filter."'";
                }else
                {
                     $search.= " and `category` <> 'Potafo Mart'";
                }
            }else{
                if($order_cat_filter == "Potafo Mart")
                {
                    $search.= "`category` =  '".$order_cat_filter."'";
                }else
                {
                     $search.= "`category` <> 'Potafo Mart'";   
                }
            }
        }

        if($search != "")
        {
            $new_serach =  " and $search";
        }else{
            $new_serach = "";
        }
         //and DATE(order_date) = CURRENT_DATE()
        $datecheck='';
        if($order_status_filter == "" || $order_status_filter == "P" || $order_status_filter == "C" || $order_status_filter == "OP")
        {
        $datecheck='';
        }else
        {
           $datecheck= " and DATE(order_date) = CURRENT_DATE() ";
        }
        //return "SELECT `rest_id`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,`customer_details`->>'$.mobile' as mobile,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time  FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '".$staff_id."') $new_serach and DATE(order_date) = CURRENT_DATE() order by order_date DESC";
        $all_orders = DB::SELECT("SELECT `rest_id`,time(order_date) as ordertime,`on_hold`,`on_hold_release_time`,`rest_confirmed`,`payment_method`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,IF(`customer_details`->>'$.latitude' IS NULL or `customer_details`->>'$.latitude' = '', '0', `customer_details`->>'$.latitude') as latitude,IF(`customer_details`->>'$.longitude' IS NULL or `customer_details`->>'$.longitude' = '', '0', `customer_details`->>'$.longitude') as longitude,`customer_details`->>'$.mobile' as mobile,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time,no_contact_del,readytopick,assign_status  FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '".$staff_id."') $new_serach $datecheck order by order_date DESC");
//         $all_orders = DB::SELECT("SELECT `rest_id`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,`customer_details`->>'$.mobile' as mobile,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time FROM `order_master` $new_serach and DATE(order_date) = CURRENT_DATE() order by order_date DESC");
        $append     = "";
        $append_2   = "";

        $append .=  '<table id="example2" class="table table-bordered">';
        $append .=    '<thead>';
        $append .=    '<tr>';
        $append .=        '<th style="min-width:30px">Sl No</th>';
        $append .=        '<th style="min-width:80px">Order No</th>';
        $append .=        '<th style="min-width:60px">Time</th>';
        $append .=        '<th style="min-width:140px">Restaurant/Shop </th>';
        $append .=        '<th style="min-width:140px">Customer Name</th>';
        $append .=        '<th style="min-width:140px">Staff Name</th>';
        $append .=        '<th style="min-width:100px">Staff Mobile</th>';
//            $append .=        '<th style="min-width:100px">Mobile</th>';
        $append .=        '<th style="min-width:70px">Paymode</th>';
        $append .=        '<th style="min-width:70px">Status</th>';
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
                  if(confirmationdiff($orders->ordertime,isset($orders->on_hold_release_time)?$orders->on_hold_release_time:0) == 'Y' && $orders->rest_confirmed == 'N')
                {
                    $dlystats = " delayed_confirmation";
                }
                else
                {
                    $dlystats = ' ';
                }
                if($orders->rest_confirmed == 'Y')
                {
                    $sts ='confirm_place';
                }
                else
                {
                    $sts = 'new_order_1';
                }
                $inpg="";
                if($orders->assign_status == 'Inprogress')
                {
                   $inpg=" <span style='color:Red'> (I)<span>"; 
                }
                if($orders->current_status == 'P'){
                     if($orders->on_hold == 'Y')
                    {
                    $append .=        '<tr role="row" class="'.$sts.' on_hold'.$dlystats.'">';
                    }
                    else
                    {
                    $append .=        '<tr role="row" class="'.$sts.' '.$dlystats.'">';
                    }

                    $append .=            '<td style="min-width:30px;">'.$i.'</td>';
                    $append .=            '<td style="min-width:80px;"><strong style="color: #227b73">'.$orders->order_number.'</strong></td>';
                    $append .=            '<td style="min-width:90px;">'.$orders->time.$inpg.'</td>';
                    $append .=            '<td style="min-width:140px;"><strong style="color: #77541f">'.$orders->rest_name.'</strong></td>';
                    $append .=            '<td style="min-width:140px;">'.$orders->name.'</td>';
                    $append .=            '<td style="min-width:140px;"></td>';
                    $append .=            '<td style="min-width:140px;"></td>';
//            $append .=            '<td style="min-width:100px;">'.$orders->mobile.'</td>';
                    if(isset($orders->payment_method) && $orders->payment_method == 'COD')
                    {
                         $append .=                '<td style="min-width:70px;">COD</td>';
                    }
                    else
                    {
                          $append .=                '<td style="min-width:70px;">ONLINE</td>';
     
                    }                    
                    $ready="";
                    if($orders->readytopick == "Y")
                    {
                        $ready =" <font color='#f5351b'>Placed(P)</font>";
                    } else
                    {
                        $ready ="Placed";
                    }
                    $append .=                '<td style="min-width:70px; ">'.$ready.'</td>';
                    $append .=            '<td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.',\''.$orders->on_hold.'\','.$orders->latitude.','.$orders->longitude.',\''.$orders->no_contact_del.'\'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');"><i class="fa fa-cog"></i></a></td>';
                    $append .=        '</tr>';
                }else if($orders->current_status == 'C'){
                    if($orders->on_hold == 'Y')
                    {
                        $append .=        '<tr role="row" class="near_delivery_1 on_hold'.$stats.'">';
                    }
                    else
                    {
                         $append .=        '<tr role="row" class="near_delivery_1'.$stats.'">';
                    }
                    $append .=            '<td style="min-width:30px;">'.$i.'</td>';
                    $append .=            '<td style="min-width:80px;"><strong style="color: #227b73">'.$orders->order_number.'</strong></td>';
                    $append .=            '<td style="min-width:90px;">'.$orders->time.$inpg.'</td>';
                    $append .=            '<td style="min-width:140px;"><strong style="color: #77541f">'.$orders->rest_name.'</strong></td>';
                    $append .=            '<td style="min-width:140px;">'.$orders->name.'</td>';
                    $append .=            '<td style="min-width:140px;">'.$orders->staffname.'</td>';
                    $append .=            '<td style="min-width:140px;">'.$orders->staffmobile.'</td>';
//            $append .=            '<td style="min-width:100px;">'.$orders->mobile.'</td>';
                     if(isset($orders->payment_method) && $orders->payment_method == 'COD')
                    {
                         $append .=                '<td style="min-width:70px;">COD</td>';
                    }
                    else
                    {
                          $append .=                '<td style="min-width:70px;">ONLINE</td>';
     
                    }
                    $ready="";
                    if($orders->readytopick == "Y")
                    {
                        $ready =" <font color='#f5351b'>Confirmed(P)</font>";
                    } else
                    {
                        $ready ="Confirmed";
                    }
                    $append .=            '<td style="min-width:70px;">'.$ready.'</td>';
                    $append .=            '<td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.',\''.$orders->on_hold.'\','.$orders->latitude.','.$orders->longitude.',\''.$orders->no_contact_del.'\'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');"><i class="fa fa-cog"></i></a></td>';
                    $append .=        '</tr>';
                }else if($orders->current_status == 'OP'){
                    $append .=        '<tr role="row" class="new_pick_up'.$stats.'">';
                    $append .=            '<td style="min-width:30px;">'.$i.'</td>';
                    $append .=            '<td style="min-width:80px;"><strong style="color: #227b73">'.$orders->order_number.'</strong></td>';
                    $append .=            '<td style="min-width:90px;">'.$orders->time.$inpg.'</td>';
                    $append .=            '<td style="min-width:140px;"><strong style="color: #77541f">'.$orders->rest_name.'</strong></td>';
                    $append .=            '<td style="min-width:140px;">'.$orders->name.'</td>';
                    $append .=            '<td style="min-width:140px;">'.$orders->staffname.'</td>';
                    $append .=            '<td style="min-width:140px;">'.$orders->staffmobile.'</td>';
//            $append .=            '<td style="min-width:100px;">'.$orders->mobile.'</td>';
                    if(isset($orders->payment_method) && $orders->payment_method == 'COD')
                    {
                         $append .=                '<td style="min-width:70px;">COD</td>';
                    }
                    else
                    {
                          $append .=                '<td style="min-width:70px;">ONLINE</td>';
     
                    }
                    $append .=                '<td style="min-width:70px;">Picked</td>';
                    $append .=            '<td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.',\''.$orders->on_hold.'\','.$orders->latitude.','.$orders->longitude.',\''.$orders->no_contact_del.'\'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');"><i class="fa fa-cog"></i></a></td>';
                    $append .=        '</tr>';
                }else if($orders->current_status == 'D'){
                    $append .=        '<tr role="row" class="new_deliverd">';
                    $append .=            '<td style="min-width:30px;">'.$i.'</td>';
                    $append .=            '<td style="min-width:80px;"><strong style="color: #227b73">'.$orders->order_number.'</strong></td>';
                    $append .=            '<td style="min-width:90px;">'.$orders->time.$inpg.'</td>';
                    $append .=            '<td style="min-width:140px;"><strong style="color: #77541f">'.$orders->rest_name.'</strong></td>';
                    $append .=            '<td style="min-width:140px;">'.$orders->name.'</td>';
                    $append .=            '<td style="min-width:140px;">'.$orders->staffname.'</td>';
                    $append .=            '<td style="min-width:140px;">'.$orders->staffmobile.'</td>';
//            $append .=            '<td style="min-width:100px;">'.$orders->mobile.'</td>';
                     if(isset($orders->payment_method) && $orders->payment_method == 'COD')
                    {
                         $append .=                '<td style="min-width:70px;">COD</td>';
                    }
                    else
                    {
                          $append .=                '<td style="min-width:70px;">ONLINE</td>';
     
                    }
                    $append .=                '<td style="min-width:70px;">Delivered</td>';
                    $append .=            '<td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.',\''.$orders->on_hold.'\','.$orders->latitude.','.$orders->longitude.',\''.$orders->no_contact_del.'\'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');"><i class="fa fa-cog"></i></a></td>';
                    $append .=        '</tr>';
                }else if($orders->current_status == 'CA'){
                    $append .=        '<tr role="row" class="new_cancelled">';
                    $append .=            '<td style="min-width:30px;">'.$i.'</td>';
                    $append .=            '<td style="min-width:80px;"><strong style="color: #227b73">'.$orders->order_number.'</strong></td>';
                    $append .=            '<td style="min-width:90px;">'.$orders->time.$inpg.'</td>';
                    $append .=            '<td style="min-width:140px;"><strong style="color: #77541f">'.$orders->rest_name.'</strong></td>';
                    $append .=            '<td style="min-width:140px;">'.$orders->name.'</td>';
                    $append .=            '<td style="min-width:140px;"></td>';
                    $append .=            '<td style="min-width:140px;"></td>';
//            $append .=            '<td style="min-width:100px;">'.$orders->mobile.'</td>';
                     if(isset($orders->payment_method) && $orders->payment_method == 'COD')
                    {
                         $append .=                '<td style="min-width:70px;">COD</td>';
                    }
                    else
                    {
                          $append .=                '<td style="min-width:70px;">ONLINE</td>';
     
                    }
                    $append .=            '<td style="min-width:70px;">Cancelled</td>';
                    $append .=            '<td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.',\''.$orders->on_hold.'\','.$orders->latitude.','.$orders->longitude.',\''.$orders->no_contact_del.'\'),view_pop_up_address('.$orders->order_number.','.$orders->rest_id.','.$orders->customer_id.');"><i class="fa fa-cog"></i></a></td>';
                    $append .=        '</tr>';
                }
            }
        }
        $append .=    '</tbody>';
        $append .='</table>';
//$sql="SELECT `rest_id`,time(order_date) as ordertime,`on_hold`,`on_hold_release_time`,`rest_confirmed`,`payment_method`,`order_number`,`customer_id`,`customer_details`->>'$.name' as name,`customer_details`->>'$.latitude' as latitude,`customer_details`->>'$.longitude' as longitude,`customer_details`->>'$.mobile' as mobile,`delivery_assigned_details`->>'$.name' as staffname,`delivery_assigned_details`->>'$.phone' as staffmobile,`rest_details`->>'$.name' as rest_name,`current_status`,`status_details`->>'$.P' as time  FROM order_master o, restaurant_master r where o.`rest_id` = r.id and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '".$staff_id."') $new_serach and DATE(order_date) = CURRENT_DATE() order by order_date DESC";
        return $append;
//return $sql;
    }

    //View the order details of particular order
    public function view_order_details_list(Request $request)
    {
        $decimal_point = Commonsource::generalsettings();
        $order_number = $request['order_number'];
        $rest_id     = $request['rest_id'];
        $details = DB::SELECT("SELECT IFNULL(om.coupon_details->>'$.coupon_amount',0) as coupon_amount,om.mode_of_entry,om.app_version,om.current_status,od.order_number,od.sl_no,od.rest_id,od.menu_id,od.menu_details->>'$.portion' as odportion,od.menu_details->>'$.menu_name' as odmenu,od.menu_details->>'$.category' as odcategory,od.menu_details->>'$.preference' as odpreference,od.menu_details->>'$.single_rate' as odsinglerate,od.qty as odqty,od.final_rate as odfinalrate,om.order_number,om.sub_total as omsubtotal,ROUND(om.final_total,$decimal_point) as omfinal_total,om.total_details as omtotal FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number = '$order_number' and od.rest_id ='$rest_id' ");//and DATE(order_date) = CURRENT_DATE()
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
        $append .=        '<th style="width:60px">Actions</th>';
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
                if($orders->current_status == 'C' || $orders->current_status == 'OP' || $orders->current_status == 'D' || $orders->current_status == 'CA' || $masterdetails[0]->payment_method =='ONLINE'){
                    $append .=            '<td style="width:60px;text-align:center" class="'.$class.'"><a style="color: red;">Confirmed</td>';
                } else{
                    $append .=            '<td style="width:60px;" class=""><a class="btn button_table"><i class="fa fa-pencil" onclick = "return edit_menu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.');" ></i></a><a class="btn button_table '.$class.' "><i class="fa fa-trash  " onclick="return deletemenu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.','.$orders->menu_id.');"></i></a></td>';
                }
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

    //To give the add field order details of particular order
    public function addmenuorder(Request $request)
    {
        $decimal_point = Commonsource::generalsettings();
        $order_number = $request['order_number'];
        $rest_id     = $request['rest_id'];
        $details = DB::SELECT("SELECT od.order_number,od.sl_no,od.rest_id,od.menu_id,od.menu_details->>'$.portion' as odportion,od.menu_details->>'$.menu_name' as odmenu,od.menu_details->>'$.category' as odcategory,od.menu_details->>'$.preference' as odpreference,ROUND(od.menu_details->>'$.single_rate',$decimal_point) as odsinglerate,od.qty as odqty,ROUND(od.final_rate,$decimal_point) as odfinalrate,om.order_number,ROUND(om.sub_total,$decimal_point) as omsubtotal,ROUND(om.final_total,$decimal_point) as omfinal_total,om.total_details as omtotal FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number = '$order_number' and od.rest_id ='$rest_id' ");//and DATE(order_date) = CURRENT_DATE()
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
        $append .=        '<th style="width:60px"></th>';
        $append .=    '</tr>';
        $append .=    '</thead>';
        $append .=    '<tbody>';
        $append .=        '<tr>';
        $append .=        '<td style="min-width:30px">';
        $append .=        '</td>';
        $append .=        '<td style="max-width:135px">';
        $append .=        '<div class="restaurant_more_detail_text">';
        $append .=        "<input type='text' name = 'menu_name' id='menu_name' placeholder='Enter Menu Name' onKeyUp = 'return menunamechange(this.value)' autocomplete='off'>";
        $append .=        '<div class="searchlist" style="display:none;width: 130%;background:#FFF;margin-top:1px;float:left;" id="suggesstionsmenu"  onMouseOut="mouseoutfnctn(this);">';
        $append .=        '</div>';
        $append .=        '</div>';
        $append .=        '</td>';
        $append .=        '<td style="max-width:55px">';
        $append .=        '<div class="restaurant_more_detail_text">';
        $append .=        '<input type="text" id="menu_qty" name="menu_qty" placeholder="QTY" onKeyUp = "return qtychange(this.value)">';
        $append .=        '</div>';
        $append .=        '</td>';
        $append .=        '<td style="min-width:70px" name="menu_rate" id= "menu_rate">';
        $append .=        '</td>';
        $append .=        '<td style="min-width:70px" name="final_rate" id= "final_rate">';
        $append .=        '</td>';
        $append .=        '<td style="min-width:60px"><a class="btn button_table"><i class="fa fa-save" onclick = "return addmenu_save('.$order_number.','.$rest_id.');"></i></a><a class="btn button_table"><i class="fa fa-times" onclick="return view_pop_up('.$order_number.','.$rest_id.');"></i></a>';
        $append .=        '</td>';
        $append .=        '</tr>';
        $append .=        '<tr>';
        $append .=        '<td col-span="6">';
        $append .=        '<div class="restaurant_more_detail_text">';
        $append .=        '<input type="text" placeholder="Preference" id="menu_preference" name="menu_preference">';
        $append .=        '</div>';
        $append .=        '</td>';
        $append .=        '</tr>';
        if(count($details)>0){
            $i=0;
            foreach($details as $orders){
                $i++;

                $append .=        '<tr>';
                $append .=            '<td style="width:30px;">'.$i.'</td>';
                $append .=            '<td style="width:135px;">'.$orders->odmenu.','.$orders->odportion.'</td>';
                $append .=            '<td style="width:55px";">'.$orders->odqty.'</td>';
                $append .=            '<td style="width:70px";">'.round($orders->odsinglerate,$decimal_point).'</td>';
                $append .=            '<td style="width:70px";">'.round($orders->odfinalrate,$decimal_point).'</td>';
                $append .=            '<td style="width:60px;"><a class="btn button_table"><i class="fa fa-pencil"></i></a><a class="btn button_table"><i class="fa fa-trash" onclick="return deletemenu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.','.$orders->menu_id.');"></i></a></td>';
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
        $append .=        '<td style="width:70px"><strong>'.round($orders->omsubtotal,$decimal_point).'</strong></td>';
        $append .=        '<td style="width:60px"></td>';
        $append .=    '</tr>';
        $append .=    '</tfoot>';
        $append .=    '</table>';
        $append .=  '<table class="timing_sel_popop_tbl tbl_totalcharge">';
        $append .=    ' <tr>';
        $append .=    '<tr>';
        $append .=        '<td>Pck Charge: <strong>'.round($total['packing_charge'],$decimal_point).'</strong></td>';
        $append .=        '<td>Dlv Charge: <strong>'.round($total['delivery_charge'],$decimal_point).'</strong></td>';
        $append .=        '<td>Discount: <strong>'.round($total['discount_amount'],$decimal_point).'</strong></td>';
        $append .=    '<td><strong>Final: </strong><strong style="font-size:15px;">'.round($details[0]->omfinal_total,0).'</strong></td>';
        $append .=        '</tr>';
        $append .=        '</table>';
        $append .=    '</tr>';
        $append .=    '</tfoot>';
        $append .=    '</table>';
        return $append;

    }
    //To add the new menus for particular order
    public function addnewmenuorder(Request $request)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i A');
        $decimal_point = Commonsource::generalsettings();
        $order_number = $request['order_number'];
        $rest_id     = $request['rest_id'];
        $menu     = $request['menu'];
        $menuname = explode(',',$menu)[0];
        $portion = explode(',',$menu)[1];
        $rate = $request['rate'];
        $prefrnce = $request['prefrnce'];
        $qty = $request['qty'];
        $final_rate = $request['final_rate'];
        $menuids = DB::SELECT("SELECT m_menu_id,m_por_rate as portion,m_pack_rate as packrate,JSON_LENGTH(`m_por_rate`) as count,JSON_UNQUOTE(m_category) as category from restaurant_menu WHERE m_name_type->>'$.name' = '$menuname' and m_rest_id ='$rest_id' ");
        $check_exist = DB::SELECT("SELECT sl_no FROM order_details WHERE order_number='".$order_number."' AND menu_id='".$menuids[0]->m_menu_id."' ");
        if(count($check_exist)!=0){
            return "exist";
        }
        foreach($menuids as $key=>$item)
        {
            $menuid = $item->m_menu_id;
            $category = implode(",",json_decode($item->category));
            $pack_rate=$item->packrate;
            for($i = 1; $i<=$item->count;$i++)
            {
                $json_data = json_decode($item->portion,true);
                $portionsave = strtoupper($json_data['portion'.$i]['portion']);
                $portiongiven = strtoupper($portion);
                if($portionsave = $portiongiven)
                {
                    $exc_rate = strtoupper($json_data['portion'.$i]['exc_rate']);
                    $inc_rate = strtoupper($json_data['portion'.$i]['inc_rate']);
                    $extra_val = strtoupper($json_data['portion'.$i]['extra_val']);
                    break;
                }
                else
                {
                    continue;
                }

            }
        }
        DB::INSERT("INSERT INTO `order_details`(`order_number`,`rest_id`,`menu_id`,`menu_details`,`qty`,`final_rate`,`single_rate_details`) VALUES ('" . $order_number . "','" . trim($rest_id) . "','".$menuid."',json_object('menu_name','" .$menuname. "','single_rate','" .$rate. "','preference','".$prefrnce."','portion','".$portion."','category','".$category."'),'".$qty."','$final_rate',json_object('exc_rate','" .$exc_rate. "','inc_rate','" .$inc_rate. "','extra_val','".$extra_val."','pack_rate','".$pack_rate."'))");
        Commonsource::item_pack_offer($order_number,$rest_id,$menuid,$qty);
        $sub_totallist = DB::SELECT('SELECT (SUM(final_rate)) as subtotal FROM order_details WHERE order_number = "'.$order_number.'" and rest_id="'.$rest_id.'"');
        foreach($sub_totallist as $key=>$list)
        {
            $subtotal=$list->subtotal;
        }
        $final = DB::SELECT('SELECT delivery_charge,packing_charge,bill_offer_exist,bill_offer_slno FROM restaurant_master WHERE id="'.$rest_id.'"');
        foreach($final as $key=>$list)
        {
            $discount_amount =0;
            $offer_percent=0;
            if($list->bill_offer_exist=='Y'){
                //$offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND offer_details->>'$.valid_to' >= '$datetime' AND offer_details->>'$.valid_from' <= '$datetime'");
                $offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND DATE_FORMAT(offer_details->>'$.valid_to','%Y-%m-%d %T') >= now() AND DATE_FORMAT(offer_details->>'$.valid_from','%Y-%m-%d %T') <= now() ");
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

            $delivery=$list->delivery_charge;
            $packing=$list->packing_charge;
        }
        $finaltotal = ($subtotal + $delivery + $packing) - $discount_amount;
        $final_total = round($finaltotal,0);

        DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_SET(total_details,"$.delivery_charge","'.$delivery.'","$.packing_charge","'.$packing.'","$.discount_amount","'.$discount_amount.'") WHERE order_number = "'.$order_number.'" and rest_id="'.$rest_id.'"');
       
       
        $menudetails = DB::SELECT("SELECT single_rate_details->'$.exc_rate'*qty as total_amount_exc ,single_rate_details->'$.inc_rate'*qty as total_amount_inc ,single_rate_details->'$.pack_rate'*qty as pack_rate FROM order_details where order_number  ='$order_number'");
            
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
		DB::SELECT('UPDATE order_master SET rest_item_total_details = JSON_OBJECT("inc_rate","'.$inc_rate.'","excl_rate","'.$excl_rate.'","pack_rate","'.$pack_rate.'","tax_rate","'.$tax_rate.'") WHERE order_number="'.$order_number.'"');

        $details = DB::SELECT("SELECT od.order_number,od.sl_no,od.rest_id,od.menu_id,od.menu_details->>'$.portion' as odportion,od.menu_details->>'$.menu_name' as odmenu,od.menu_details->>'$.preference' as odpreference,od.menu_details->>'$.single_rate' as odsinglerate,od.menu_details->>'$.category' as odcategory,od.qty as odqty,od.final_rate as odfinalrate,om.order_number,ROUND(om.sub_total,$decimal_point) as omsubtotal,ROUND(om.final_total,$decimal_point) as omfinal_total,om.total_details as omtotal FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number = '$order_number' and od.rest_id ='$rest_id' ");//and DATE(order_date) = CURRENT_DATE()
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
        $append .=        '<th style="width:60px"></th>';
        $append .=    '</tr>';
        $append .=    '</thead>';
        $append .=    '<tbody>';
        if(count($details)>0){
            $i=0;
            foreach($details as $orders){
                $i++;
                if($orders->odfinalrate==0){
                    $class='not-active';
                }
                else{
                    $class='';
                }
                $append .=        '<tr>';
                $append .=            '<td style="width:30px;">'.$i.'</td>';
                $append .=            '<td style="width:135px;">'.$orders->odmenu.','.$orders->odportion.'</td>';
                $append .=            '<td style="width:55px";">'.$orders->odqty.'</td>';
                $append .=            '<td style="width:70px";">'.round($orders->odsinglerate,$decimal_point).'</td>';
                $append .=            '<td style="width:70px";">'.round($orders->odfinalrate,$decimal_point).'</td>';
                $append .=            '<td style="width:60px;" class=""><a class="btn button_table"><i class="fa fa-pencil" onclick = "return edit_menu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.');" ></i></a><a class="btn button_table '.$class.' "><i class="fa fa-trash" onclick="return deletemenu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.','.$orders->menu_id.');"></i></a></td>';
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
        $append .=        '<td style="width:70px"><strong>'.round($orders->omsubtotal,$decimal_point).'</strong></td>';
        $append .=        '<td style="width:60px"></td>';
        $append .=    '</tr>';
        $append .=    '</tfoot>';
        $append .=    '</table>';
        $append .=  '<table class="timing_sel_popop_tbl tbl_totalcharge">';
        $append .=    ' <tr>';
        $append .=    '<tr>';
        $append .=        '<td>Pck Charge: <strong>'.round($packing,$decimal_point).'</strong></td>';
        $append .=        '<td>Dlv Charge: <strong>'.round($delivery,$decimal_point).'</strong></td>';
        $append .=        '<td>Discount: <strong>'.round($discount_amount,$decimal_point).'</strong></td>';
        $append .=    '<td><strong>Final: </strong><strong style="font-size:15px;">'.round($final_total,$decimal_point).'</strong></td>';
        $append .=        '</tr>';
        $append .=        '</table>';
        $append .=    '</tr>';
        $append .=    '</tfoot>';
        $append .=    '</table>';
        return $append;
    }

    public function edit_order_details(Request $request)
    {
        $decimal_point = Commonsource::generalsettings();
        $order_number = $request['order_number'];
        $rest_id     = $request['rest_id'];
        $sl_no    = $request['sl_no'];
        $details = DB::SELECT("SELECT od.order_number,od.sl_no,od.rest_id,od.menu_id,od.menu_details->>'$.portion' as odportion,od.menu_details->>'$.menu_name' as odmenu,od.menu_details->>'$.category' as odcategory,od.menu_details->>'$.preference' as odpreference,od.menu_details->>'$.single_rate' as odsinglerate,od.qty as odqty,od.final_rate as odfinalrate,om.order_number,ROUND(om.sub_total,$decimal_point) as omsubtotal,ROUND(om.final_total,$decimal_point) as omfinal_total,om.total_details as omtotal FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number = '$order_number' and od.rest_id ='$rest_id' ");//and DATE(order_date) = CURRENT_DATE()
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
        $append .=        '<th style="width:60px"></th>';
        $append .=    '</tr>';
        $append .=    '</thead>';
        $append .=    '<tbody>';
        if(count($details)>0){
            $i=0; $class1= '';
            if(count($details)==1){
                $class1 = 'not-active';
            }
            foreach($details as $orders){
                $i++;
                if($orders->odfinalrate==0){
                    $class='not-active';
                }
                else{
                    $class='';
                }


                $append .=        '<tr>';
                $append .=            '<td style="width:30px;">'.$i.'</td>';
                $append .=            '<td style="width:135px;">'.$orders->odmenu.','.$orders->odportion.'</td>';
                if($orders->sl_no == $sl_no)
                {
                    $append .=        '<td style="width:55px;">';
                    $append .=        '<div class="restaurant_more_detail_text">';
                    $append .=        '<input type="text" id="menu_qty" name="menu_qty" value="'.$orders->odqty.'" onKeyUp = "return qtychange(this.value)";>';
                    $append .=        '</div>';
                    $append .=        '</td>';
                }
                else
                {
                    $append .=     '<td style="width:55px;">'.$orders->odqty.'</td>';
                }
                if($orders->sl_no == $sl_no)
                {
                    $append .=     '<td id="menu_rate" name="menu_rate" style="width:70px;">'.$orders->odsinglerate.'</td>';
                }
                else
                {
                    $append .=    '<td style="width:70px;">'.round($orders->odsinglerate,$decimal_point).'</td>';
                }
                if($orders->sl_no == $sl_no)
                {
                    $append .=    '<td id="final_rate" name="final_rate" style="width:70px;">'.round($orders->odfinalrate,$decimal_point).'</td>';
                }
                else
                {
                    $append .=       '<td style="width:70px;">'.round($orders->odfinalrate,$decimal_point).'</td>';
                }
                if($orders->sl_no == $sl_no)
                {
                    $append .=       '<td style="width:60px;"><a class="btn button_table"><i class="fa fa-save" onclick = "return saveedit_menu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.','.$orders->menu_id.');" ></i></a><a class="btn button_table '.$class1.'"><i class="fa fa-trash" onclick="return deletemenu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.','.$orders->menu_id.');"></i></a></td>';
                }
                else
                {
                    $append .=       '<td style="width:60px;" class=""><a class="btn button_table"><i class="fa fa-pencil" onclick = "return edit_menu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.');" ></i></a><a class="btn button_table '.$class1.' "><i class="fa fa-trash " onclick="return deletemenu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.','.$orders->menu_id.');"></i></a></td>';
                }
                $append .=        '</tr>';
                $append .=        '<tr>';
                $append .=            '<div class="restaurant_more_detail_text">';
                if($orders->sl_no == $sl_no)
                {

                    $append .=        '<td col-span="6">';
                    $append .=        '<div class="restaurant_more_detail_text">';
                    $append .=        '<input type="text" placeholder="Preference" id="menu_preference" name="menu_preference" value="'.$orders->odpreference.'">';
                    $append .=        '</div>';
                    $append .=        '</td>';

                }
                elseif(($orders->odpreference== 'null') || ($orders->odpreference== ''))
                {
                    $append .=        '<td style="text-align:left;width:10px;">Category: '.$orders->odcategory.'</td>';
                    $append .=        '<td style="text-align:left;width:10px;">Preference: </td>';

                }
                else
                {
                    $append .=        '<td style="text-align:left;width:10px;">Category: '.$orders->odcategory.'</td>';
                    $append .=        '<td style="text-align:left;width:10px;">Preference: '.$orders->odpreference.'</td>';

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
        $append .=        '<td style="width:70px"><strong>'.round($orders->omsubtotal,$decimal_point).'</strong></td>';
        $append .=        '<td style="width:60px"></td>';
        $append .=    '</tr>';
        $append .=    '</tfoot>';
        $append .=    '</table>';
        $append .=  '<table class="timing_sel_popop_tbl tbl_totalcharge">';
        $append .=    ' <tr>';
        $append .=    '<tr>';
        $append .=        '<td>Pck Charge: <strong>'.round($total['packing_charge'],$decimal_point).'</strong></td>';
        $append .=        '<td>Dlv Charge: <strong>'.round($total['delivery_charge'],$decimal_point).'</strong></td>';
        $append .=        '<td>Discount: <strong>'.round($total['discount_amount'],$decimal_point).'</strong></td>';
        $append .=    '<td><strong>Final: </strong><strong style="font-size:15px;">'.round($details[0]->omfinal_total,0).'</strong></td>';
        $append .=        '</tr>';
        $append .=        '</table>';
        $append .=    '</tr>';
        $append .=    '</tfoot>';
        $append .=    '</table>';
        return $append;
    }




    public function view_order_address_list(Request $request)
    {
        $order_number = $request['order_number'];
        $rest_id     = $request['rest_id'];

        $order_details = DB::SELECT("SELECT om.coupon_details,om.payment_details->>'$.razorpay_order_id' as razopayorderid,om.payment_details->>'$.refundid' as refundid,om.payment_details->>'$.razorpay_payment_id' as razopaypaymentid,om.payment_details->>'$.method' as method,om.mode_of_entry,om.app_version,om.payment_method,om.delivery_assigned_to,delivery_assigned_details->>'$.name' as staff_name,IFNULL(delivery_assigned_details->>'$.note',0) as assignednote,om.customer_details->>'$.pincode' as pincode,om.customer_details->>'$.landmark' as landmark,om.customer_details->>'$.addresstype' as addresstype,om.customer_details->>'$.addressline1' as addressline1,om.customer_details->>'$.addressline2' as addressline2,"
            . "rs.address,rs.mobile->>'$.ind' as ind,rs.mobile->>'$.mobile' as mob,om.rest_id,om.order_number,om.customer_id,om.customer_details->>'$.name' as name,om.customer_details->>'$.mobile' as mobile"
            . ",om.rest_details->>'$.name' as rest_name,om.current_status,om.status_details->>'$.P' as time FROM `order_master` as om "
            . "LEFT JOIN restaurant_master rs ON om.rest_id=rs.id WHERE  om.order_number = '$order_number' "
            . "order by om.order_date DESC");
        return $order_details;
    }
//Delete particular menu from the particular order
    public function delete_menuorder(Request $request)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i A');
        $decimal_point = Commonsource::generalsettings();
        $order_number = $request['order_number'];
        $rest_id     = $request['rest_id'];
        $sl_no     = $request['sl_no'];
        $menuid     = $request['menuid'];
        $details_empty = DB::SELECT("SELECT count(order_number) as ordercount FROM order_details WHERE order_number='".$order_number."' ");
        if($details_empty[0]->ordercount==1){
            return "delete_restricted";
        }
        DB::DELETE('delete from order_details where order_number="'.$order_number.'" and rest_id="'.$rest_id.'" and sl_no="'.$sl_no.'"');
        DB::DELETE("DELETE FROM `order_details` WHERE order_number='$order_number' AND offer_of_id='".$menuid."' ");
        $sub_totallist = DB::SELECT('SELECT (SUM(final_rate)) as subtotal FROM order_details WHERE order_number = "'.$order_number.'" and rest_id="'.$rest_id.'"');
        foreach($sub_totallist as $key=>$list)
        {
            $subtotal=$list->subtotal;
        }
        if(!$subtotal){
            $subtotal=0;
        }
        $final = DB::SELECT('SELECT delivery_charge,packing_charge,bill_offer_exist,bill_offer_slno FROM restaurant_master WHERE id="'.$rest_id.'"');
        foreach($final as $key=>$list)
        {
            $discount_amount =0;
            $offer_percent=0;
            if($list->bill_offer_exist=='Y'){
                //$offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND offer_details->>'$.valid_to' >= '$datetime' AND offer_details->>'$.valid_from' <= '$datetime'");
                $offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND DATE_FORMAT(offer_details->>'$.valid_to','%Y-%m-%d %T') >= now() AND DATE_FORMAT(offer_details->>'$.valid_from','%Y-%m-%d %T') <= now() ");
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

            $delivery=$list->delivery_charge;
            $packing=$list->packing_charge;
        }
        $finaltotal = ($subtotal + $delivery + $packing)-$discount_amount;
        $final_total =  round($finaltotal,0);
        DB::UPDATE('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.round($final_total,$decimal_point).'",total_details= JSON_SET(total_details,"$.delivery_charge","'.$delivery.'","$.packing_charge","'.$packing.'","$.discount_amount","'.$discount_amount.'") WHERE order_number = "'.$order_number.'" and rest_id="'.$rest_id.'"');
        
        $menudetails = DB::SELECT("SELECT single_rate_details->'$.exc_rate'*qty as total_amount_exc ,single_rate_details->'$.inc_rate'*qty as total_amount_inc ,single_rate_details->'$.pack_rate'*qty as pack_rate FROM order_details where order_number  ='$order_number'");
            
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
		DB::SELECT('UPDATE order_master SET rest_item_total_details = JSON_OBJECT("inc_rate","'.$inc_rate.'","excl_rate","'.$excl_rate.'","pack_rate","'.$pack_rate.'","tax_rate","'.$tax_rate.'") WHERE order_number="'.$order_number.'"');
         
        $details = DB::SELECT("SELECT od.order_number,od.sl_no,od.rest_id,od.menu_id,od.menu_details->>'$.portion' as odportion,od.menu_details->>'$.menu_name' as odmenu,od.menu_details->>'$.category' as odcategory,od.menu_details->>'$.preference' as odpreference,od.menu_details->>'$.single_rate' as odsinglerate,od.qty as odqty,od.final_rate as odfinalrate,om.order_number,ROUND(om.sub_total,$decimal_point) as omsubtotal,ROUND(om.final_total,$decimal_point) as omfinal_total,om.total_details as omtotal FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number = '$order_number' and od.rest_id ='$rest_id' ");//and DATE(order_date) = CURRENT_DATE()
        $count = count($details);
        $append     = "";


        if(count($details)>0){
            $append .=  '<table id="example" class="timing_sel_popop_tbl">';
            $append .=    '<thead>';
            $append .=    '<tr>';
            $append .=        '<th style="width:30px"></th>';
            $append .=        '<th style="width:130px;">Items</th>';
            $append .=        '<th style="width:50px">Qty</th>';
            $append .=        '<th style="width:70px">Rate</th>';
            $append .=        '<th style="width:70px">Amount</th>';
            $append .=        '<th style="width:60px"></th>';
            $append .=    '</tr>';
            $append .=    '</thead>';
            $append .=    '<tbody>';
            $total =  json_decode($details[0]->omtotal,true);
            $i=0;
            foreach($details as $orders){
                $i++;
                if($orders->odfinalrate==0  || count($details)==1){
                    $class='not-active';
                }
                else{
                    $class='';
                }

                $append .=        '<tr>';
                $append .=            '<td style="width:30px;">'.$i.'</td>';
                $append .=            '<td style="width:135px;">'.$orders->odmenu.','.$orders->odportion.'</td>';
                $append .=            '<td style="width:55px";">'.$orders->odqty.'</td>';
                $append .=            '<td style="width:70px";">'.round($orders->odsinglerate,$decimal_point).'</td>';
                $append .=            '<td style="width:70px";">'.round($orders->odfinalrate,$decimal_point).'</td>';
                $append .=            '<td style="width:60px;" class=""><a class="btn button_table"><i class="fa fa-pencil" onclick = "return edit_menu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.');"></i></a><a class="btn button_table '.$class.' "><i class="fa fa-trash" onclick="return deletemenu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.','.$orders->menu_id.');"></i></a></td>';
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
            $append .=    '</tbody>';
            $append .='</table>';
            $append .=  '<table class="timing_sel_popop_tbl tfooter_ttl_order">';
            $append .=    '<tfoot>';
            $append .=    '<tr>';
            $append .=        '<td style="width:40px"></td>';
            $append .=        '<td style="width:160px">Items '.$count.'</td>';
            $append .=        '<td style="width:50px"></td>';
            $append .=        '<td style="width:70px;text-align:right">Total</td>';
            $append .=        '<td style="width:70px"><strong>'.round($orders->omsubtotal,$decimal_point).'</strong></td>';
            $append .=        '<td style="width:60px"></td>';
            $append .=    '</tr>';
            $append .=    '</tfoot>';
            $append .=    '</table>';
            $append .=  '<table class="timing_sel_popop_tbl tbl_totalcharge">';
            $append .=    ' <tr>';
            $append .=    '<tr>';
            $append .=        '<td>Pck Charge: <strong>'.round($total['packing_charge'],$decimal_point).'</strong></td>';
            $append .=        '<td>Dlv Charge: <strong>'.round($total['delivery_charge'],$decimal_point).'</strong></td>';
            $append .=        '<td>Discount: <strong>'.round($total['discount_amount'],$decimal_point).'</strong></td>';
            $append .=    '<td><strong>Final: </strong><strong style="font-size:15px;">'.round($details[0]->omfinal_total,0).'</strong></td>';
            $append .=        '</tr>';
            $append .=        '</table>';
            $append .=    '</tr>';
            $append .=    '</tfoot>';
            $append .=    '</table>';
            return $append;
        }
        else{
            DB::UPDATE('UPDATE `order_master` SET current_status="CA" WHERE order_number = "'.$order_number.'" and rest_id="'.$rest_id.'"');
            return "canceled";
        }
    }

    public function saveedit_order(Request $request)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i A');
        $decimal_point = Commonsource::generalsettings();
        $order_number = $request['order_number'];
        $rest_id     = $request['rest_id'];
        $sl_no     = $request['sl_no'];
        $menuid     = $request['menuid'];
        $prefrnce     = $request['prefrnce'];
        $qty     = $request['qty'];

        $final_rate    = $request['final_rate'];
        DB::UPDATE("UPDATE `order_details` SET qty='$qty',final_rate='$final_rate',menu_details= JSON_SET(menu_details,'$.preference','$prefrnce') WHERE `rest_id` = '$rest_id' and sl_no = '$sl_no' and order_number = '$order_number'");
        //DB::UPDATE("SELECT menu_id FROM order_details WHERE `rest_id` = '$rest_id' and sl_no = '$sl_no' and order_number = '$order_number'");
        Commonsource::item_pack_offer($order_number,$rest_id,$menuid,$qty);
        $sub_totallist = DB::SELECT('SELECT (SUM(final_rate)) as subtotal FROM order_details WHERE order_number = "'.$order_number.'" and rest_id="'.$rest_id.'"');
        foreach($sub_totallist as $key=>$list)
        {
            $subtotal=$list->subtotal;
        }
        $final = DB::SELECT('SELECT delivery_charge,packing_charge,bill_offer_exist,bill_offer_slno FROM restaurant_master WHERE id="'.$rest_id.'"');
        foreach($final as $key=>$list)
        {
            $discount_amount =0;
            $offer_percent=0;
            if($list->bill_offer_exist=='Y'){
                //$offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND offer_details->>'$.valid_to' >= '$datetime' AND offer_details->>'$.valid_from' <= '$datetime'");
                $offer_info= DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='".$list->bill_offer_slno."' AND rest_id='".$rest_id."' AND DATE_FORMAT(offer_details->>'$.valid_to','%Y-%m-%d %T') >= now() AND DATE_FORMAT(offer_details->>'$.valid_from','%Y-%m-%d %T') <= now() ");
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

            $delivery=$list->delivery_charge;
            $packing=$list->packing_charge;
        }
        $finaltotal = ($subtotal + $delivery + $packing)-$discount_amount;
        $final_total =  round($finaltotal,0);
        DB::SELECT('UPDATE `order_master` SET `sub_total`="'.$subtotal.'",final_total="'.$final_total.'",total_details= JSON_SET(total_details,"$.delivery_charge","'.$delivery.'","$.packing_charge","'.$packing.'","$.discount_amount","'.$discount_amount.'") WHERE order_number = "'.$order_number.'" and rest_id="'.$rest_id.'"');
        
		$menudetails = DB::SELECT("SELECT single_rate_details->'$.exc_rate'*qty as total_amount_exc ,single_rate_details->'$.inc_rate'*qty as total_amount_inc ,single_rate_details->'$.pack_rate'*qty as pack_rate FROM order_details where order_number  ='$order_number'");
            
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
		DB::SELECT('UPDATE order_master SET rest_item_total_details = JSON_OBJECT("inc_rate","'.$inc_rate.'","excl_rate","'.$excl_rate.'","pack_rate","'.$pack_rate.'","tax_rate","'.$tax_rate.'") WHERE order_number="'.$order_number.'"');
							
		$details = DB::SELECT("SELECT current_status,od.order_number,od.sl_no,od.rest_id,od.menu_id,od.menu_details->>'$.portion' as odportion,od.menu_details->>'$.menu_name' as odmenu,od.menu_details->>'$.category' as odcategory,od.menu_details->>'$.preference' as odpreference,od.menu_details->>'$.single_rate' as odsinglerate,od.qty as odqty,od.final_rate as odfinalrate,om.order_number,ROUND(om.sub_total,$decimal_point) as omsubtotal,ROUND(om.final_total,$decimal_point) as omfinal_total,om.total_details as omtotal FROM order_details as od LEFT JOIN order_master as om ON od.order_number=om.order_number WHERE od.order_number = '$order_number' and od.rest_id ='$rest_id' ");//and DATE(order_date) = CURRENT_DATE()
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
        $append .=        '<th style="width:60px"></th>';
        $append .=    '</tr>';
        $append .=    '</thead>';
        $append .=    '<tbody>';
        if(count($details)>0){
            $i=0;
            foreach($details as $orders){
                $i++;
                if($orders->odfinalrate==0){
                    $class='not-active';
                }
                else{
                    $class='';
                }

                $append .=        '<tr>';
                $append .=            '<td style="width:30px;">'.$i.'</td>';
                $append .=            '<td style="width:135px;">'.$orders->odmenu.','.$orders->odportion.'</td>';
                $append .=            '<td style="width:55px";">'.$orders->odqty.'</td>';
                $append .=            '<td style="width:70px";">'.round($orders->odsinglerate,$decimal_point).'</td>';
                $append .=            '<td style="width:70px";">'.round($orders->odfinalrate,$decimal_point).'</td>';
                if($orders->current_status == 'C'){
                    $append .=            '<td style="width:60px;text-align:center"><a style="color: red;">Confirmed</td>';
                }else{
                    $append .=            '<td style="width:60px;" class=""><a class="btn button_table"><i class="fa fa-pencil" onclick = "return edit_menu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.');" ></i></a><a class="btn button_table '.$class.' "><i class="fa fa-trash" onclick="return deletemenu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.','.$orders->menu_id.');"></i></a></td>';
                }
//            $append .=            '<td style="width:60px;"><a class="btn button_table"><i class="fa fa-pencil"></i></a><a class="btn button_table"><i class="fa fa-trash" onclick="return deletemenu('.$orders->order_number.','.$orders->rest_id.','.$orders->sl_no.','.$orders->menu_id.');"></i></a></td>';
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
        $append .=        '<td>Pck Charge: <strong>'.round($total['packing_charge'],$decimal_point).'</strong></td>';
        $append .=        '<td>Dlv Charge: <strong>'.round($total['delivery_charge'],$decimal_point).'</strong></td>';
        $append .=        '<td>Discount: <strong>'.round($total['discount_amount'],$decimal_point).'</strong></td>';
        $append .=    '<td><strong>Final: </strong><strong style="font-size:15px;">'.round($details[0]->omfinal_total,0).'</strong></td>';
        $append .=        '</tr>';
        $append .=        '</table>';
        $append .=    '</tr>';
        $append .=    '</tfoot>';
        $append .=    '</table>';
        return $append;

    }

    public function take_assign_staff_list(Request $request)
    {
//        $database = $this->firebase_db;     
//        
//        $assign_staff = DB::SELECT("SELECT a.staff_id,b.first_name,b.mobile FROM delivery_staff_attendence a LEFT JOIN internal_staffs b on a.staff_id=b.id  LEFT JOIN internal_staffs_area sa on sa.staff_id = b.id, restaurant_master r WHERE a.out_time is NULL and b.active = 'Y' and r.city in(sa.area_id) and r.id =  '".$request['res_id']."'");
//        $ct=count($assign_staff);
//        
//        $i=0;
//        foreach($assign_staff as $staff) {
//            $i++; 
//            $distance_val=$database->getReference('location')->getChild($staff->staff_id)->getChild('restaurant_distance')->getChild($request['res_id'])->getChild('distance')->getValue();
//            $order_val=$database->getReference('location')->getChild($staff->staff_id)->getChild('current_order')->getChild('order_count')->getValue();
//            $staff_fuldet[$i]['distance_val']=$distance_val;
//            $staff_fuldet[$i]['order_val']=$order_val;
//        }
        $staff_fuldet='';
        $staffid  = $request['staffid'];
        $append = '';
        $assign_staff = DB::SELECT("SELECT a.staff_id,b.first_name,b.mobile,b.current_confirmed_count as confirmed_count ,IFNULL(b.dlv_area,'-') as dlv_area FROM delivery_staff_attendence a LEFT JOIN internal_staffs b on a.staff_id=b.id  LEFT JOIN internal_staffs_area sa on sa.staff_id = b.id, restaurant_master r WHERE a.out_time is NULL and b.active = 'Y' and r.city in(sa.area_id) and r.id =  '".$request['res_id']."'");
//     $assign_staff = DB::SELECT("SELECT a.staff_id,b.first_name,b.mobile FROM delivery_staff_attendence a LEFT JOIN internal_staffs b on a.staff_id=b.id  WHERE a.out_time is NULL and b.active = 'Y' GROUP by a.staff_id");
        $ct=count($assign_staff);
        $i=0;
        foreach($assign_staff as $staff) {
            $i++;
            $area_details = array();
            $staffamount = array();
            $staff_fuldet[$i]['staff_id']=$staff->staff_id;
            $staff_fuldet[$i]['first_name']=$staff->first_name;
            $staff_fuldet[$i]['mobile']=$staff->mobile;
            $staff_fuldet[$i]['area']=$staff->dlv_area;
            $staff_fuldet[$i]['order_val']=$staff->confirmed_count;
            $total_amount =0;
            
            //area to be shown for each staff

           /* $area_current_order = DB::SELECT("SELECT customer_details->>'$.addressline1' as cst_address1 FROM `order_master` WHERE `delivery_assigned_to` = '".$staff->staff_id."' and `current_status` IN ('C','OP') and date(order_date) = CURRENT_DATE() ORDER BY `order_date` desc limit 1");
            if (count($area_current_order) > 0) 
            {
                $lastarea = $area_current_order[0]->cst_address1;
                $staff_fuldet[$i]['area']=$lastarea;
            }
            else
            {
                $area_last_order = DB::SELECT("SELECT customer_details->>'$.addressline1' as cst_address1 FROM `order_master` WHERE `delivery_assigned_to` = '".$staff->staff_id."' and `current_status` IN ('D') and date(order_date) = CURRENT_DATE() ORDER BY str_to_date(status_details->>'$.D','%l:%i %p') DESC limit 1");
                if (count($area_last_order) > 0) 
                {
                    $lastarea = $area_last_order[0]->cst_address1;
                    $staff_fuldet[$i]['area']=$lastarea;
                }
            }
            */
            
            $staffamount = DB::SELECT("select a.staff_credit - b.total  as pending_amount from ( select staff_max_credit as staff_credit,id from  `internal_staffs` where id = '".$staff->staff_id."') as a Join (select sum(final_total) as total,staff_id from `internal_staffs_credits` where status in ('Reserve','Credit') and staff_id = '".$staff->staff_id."') as b on a.id = b.staff_id");
            if(count($staffamount) != 0)
            {
                if($staffamount[0]->pending_amount) {
                    $total_amount = $staffamount[0]->pending_amount;
                    $staff_fuldet[$i]['total_amount']=$total_amount;
                }
                else
                {
                    $staffcreditmax = DB::SELECT("select staff_max_credit as staff_credit from  `internal_staffs` where id =  '".$staff->staff_id."'");
                    $total_amount =$staffcreditmax[0]->staff_credit;
                    $staff_fuldet[$i]['total_amount']=$total_amount;
                }
            }
            else
            {
                $staffcreditmax = DB::SELECT("select staff_max_credit as staff_credit from  `internal_staffs` where id =  '".$staff->staff_id."'");
                $total_amount =$staffcreditmax[0]->staff_credit;
                $staff_fuldet[$i]['total_amount']=$total_amount;
            }
            if($total_amount <=0)
            {
                $attr= 'style="pointer-events:none;"';
                $amount = '<span style="color:red;">'.$total_amount.'</span>';
                $staff_fuldet[$i]['attr']=$attr;
                $staff_fuldet[$i]['amount']=$amount;
            }
            else
            {
                $attr= '';
                $amount = '<span>'.$total_amount.'</span>';
                $staff_fuldet[$i]['attr']=$attr;
                $staff_fuldet[$i]['amount']=$amount;
            }
           // $pendingcount = DB::SELECT("SELECT * FROM order_master WHERE delivery_assigned_to='".$staff->staff_id."' AND current_status in ('C','OP')");
          
        }
        
      // array_multisort(array_map(function($element) {return $element['distance_val'];}, $staff_fuldet), SORT_ASC, $staff_fuldet);
      //return  "111:".print_r($staff_fuldet);die();  
        array_multisort( $staff_fuldet, SORT_ASC, $staff_fuldet);
        for($i=0;$i<$ct;$i++)
        {
            //$dis=((float)$staff_fuldet[$i]['distance_val'])/1000;
            $append .= '<tr><td>';
            $append .= '<div class="men_cl_left_filter_txt_chk" '.$staff_fuldet[$i]['attr'].'>';
            $append .= '<input type="checkbox" id="test'.$i.'"  class="assign_check" onClick="selct_staf('.$i.','."'".$staff_fuldet[$i]['first_name']."'".','.$staff_fuldet[$i]['staff_id'].','.$staff_fuldet[$i]['mobile'].');">';
            $append .= '<label for="test'.$i.'"></label></div>';
            $append .= '</td>';
            //$append .= '<td>'.number_format($dis, 3).'(km)</td>';
            $append .= '<td>'.$staff_fuldet[$i]['first_name'].'</td>';
            $append .= '<td><span class="label label-purple">'.$staff_fuldet[$i]['order_val'].'</span></td>';
           $append .= '<td>'.$staff_fuldet[$i]['mobile'].'</td>';
           $append .= '<td>'.$staff_fuldet[$i]['amount'].'</td>';
           $append .= '<td>'.$staff_fuldet[$i]['area'].'</td>';
            //$append .= '<td>'.substr($area_det, 0,-1).'</td>';
            $append .= '</tr>';
        }
        return $append;
        // return ['append'=>$append];


    }
   
    public function confirm_order(Request $request)
    {
        $database = $this->firebase_db;//firebase connection code
        $decimal_point = Commonsource::generalsettings();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $time = strtoupper($date->format('h:i a'));
        $order_number = $request['order_number'];
        $staff_id = $request['staff_id'];
        $staff_name = $request['staff_name'];
        $staff_number = $request['staff_number'];
        $psw = $request['staff_code'];
        $mode_optn = $request['optn_mode'];
        $assigned_note = $request['assigned_note'];
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($psw, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
//ALTER TABLE `order_master` ADD `assigned_time` DATETIME NULL AFTER `dlv_review_details`;
//ALTER TABLE `order_master` ADD `assigned_to` INT(11) NULL AFTER `assigned_time`;
        $area_current_order = DB::SELECT("SELECT customer_details->>'$.addressline1' as cst_address1 FROM `order_master` WHERE order_number = '$order_number'");
        $cst_address1 = $area_current_order[0]->cst_address1;

        $delstaff = DB::SELECT("SELECT delivery_assigned_to from order_master where order_number = '$order_number'");
        $delivery_staff =  $delstaff[0]->delivery_assigned_to;
        $check = DB::SELECT("SELECT authcode,id from internal_staffs where authcode ='$password' and confirm_permission = 'Y'");
        if(count($check)>0)
        {
            $confirm_staff =  $check[0]->id;
            $exist = DB::SELECT("SELECT ftoken,staff_max_credit from internal_staffs where id=$staff_id");
            if($mode_optn=='changestaff')
            {
                $existdata = DB::select("SELECT staff_change_details FROM order_master WHERE order_number='$order_number'");
                if($existdata[0]->staff_change_details=='')
                {
                    $staffdetail =  '{"staff1":{"fromstaff":"'.$delivery_staff.'","tostaff":"'.$staff_id.'","time":"'.$time.'"}}';
                    DB::SELECT("UPDATE order_master SET staff_change_details='$staffdetail',staff_notified='N',auth_status=JSON_SET(auth_status,'$.S','$confirm_staff'),delivery_staff_change = 'Y', delivery_assigned_to='$staff_id',`assigned_time` =now(),`assigned_to`='$staff_id' , assign_status='Manual', delivery_assigned_details= JSON_OBJECT('name','$staff_name','phone','$staff_number','note','$assigned_note') WHERE order_number ='$order_number'");
                    $contact = DB::SELECT("select delivery_assigned_details->>'$.phone' as mobile,customer_details->>'$.name' as cst_name,customer_details->>'$.mobile' as cst_mobile,customer_details->>'$.addressline2' as line2 FROM order_master WHERE order_number = '$order_number'");
                    $mobile=$contact[0]->mobile;
                    $cst_name=$contact[0]->cst_name;
                    $cst_mobile=$contact[0]->cst_mobile;
                    $line2=$contact[0]->line2;
                    $sendmsg = "You have New Order Assigned with --  Order Number- $order_number ,  Customer Name - $cst_name , Phone - $cst_mobile , Area - $line2 , Note - $assigned_note ";
                    /* $smsurl = Datasource::smsurl($mobile,$sendmsg);
                     $data = file_get_contents($smsurl);*/
                    if(count($exist)>0)
                    {
//                            if(isset($exist[0]->ftoken) && ($exist[0]->ftoken != '' ||$exist[0]->ftoken != ' ' ||$exist[0]->ftoken!= 'null')) {
                        $arr['to'] = $exist[0]->ftoken;
                        $arr['title'] = 'Order Assigned';
                        $arr['message'] = $sendmsg;
                        $arr['image'] = 'null';
                        $arr['action'] = 'orderhistory';
                        $arr['action_destination'] = 'null';
                        $arr['app_type'] = 'deliveryapp';
                       $result = Commonsource::notification($arr);
//                            }
                    }
                }
                else
                {
                    $length = DB::SELECT("SELECT JSON_LENGTH(`staff_change_details`) as count FROM `order_master` where order_number='$order_number'");
                    $countstaff =$length[0]->count +1;$staff_count = "staff".$countstaff;
                    DB::SELECT("UPDATE order_master SET auth_status=JSON_SET(auth_status,'$.S','$confirm_staff'),staff_notified='N',staff_change_details=JSON_INSERT(staff_change_details,'$.$staff_count',JSON_OBJECT('fromstaff','$delivery_staff','tostaff','$staff_id','time','$time')),delivery_assigned_to='$staff_id',assign_status='Manual',`assigned_time` =now(),`assigned_to`='$staff_id',delivery_assigned_details= JSON_OBJECT('name','$staff_name','phone','$staff_number','note','$assigned_note') WHERE order_number ='$order_number'");
                    $contact = DB::SELECT("select delivery_assigned_details->>'$.phone' as mobile,customer_details->>'$.name' as cst_name,customer_details->>'$.mobile' as cst_mobile,customer_details->>'$.addressline2' as line2 FROM order_master WHERE order_number = '$order_number'");
                    $mobile=$contact[0]->mobile;
                    $cst_name=$contact[0]->cst_name;
                    $cst_mobile=$contact[0]->cst_mobile;
                    $line2=$contact[0]->line2;
                    $sendmsg = "You have New Order Assigned with --  Order Number- $order_number ,  Customer Name - $cst_name , Phone - $cst_mobile , Area - $line2 , Note - $assigned_note ";
                    /*$smsurl = Datasource::smsurl($mobile,$sendmsg);
                     $data = file_get_contents($smsurl);*/
                   // $exist = DB::SELECT("SELECT ftoken from internal_staffs where id=$staff_id");
                    if(count($exist)>0)
                    {
//                            if(isset($exist[0]->ftoken) && ($exist[0]->ftoken != '' ||$exist[0]->ftoken != ' ' ||$exist[0]->ftoken!= 'null')) {

                        $arr['to'] = $exist[0]->ftoken;
                        $arr['title'] = 'Order Assigned';
                        $arr['message'] = $sendmsg;
                        $arr['image'] = 'null';
                        $arr['action'] = 'orderhistory';
                        $arr['action_destination'] = 'null';
                        $arr['app_type'] = 'deliveryapp';
                        $result = Commonsource::notification($arr);
//                            }
                    }
                }
                        //firebase insert code       
                // remove th specified order number from this delivery staff    
                //$database->getReference('location')->getChild($delivery_staff)->getChild("orders")->getChild($order_number)->remove();         
                $deletecheck = DB::SELECT("SELECT isco_staff_id from internal_staffs_current_orders WHERE isco_staff_id = $delivery_staff and isco_order_no = $order_number");
                if(count($deletecheck)>0)
                {
                    DB::SELECT("DELETE FROM internal_staffs_current_orders WHERE isco_staff_id = $delivery_staff and isco_order_no = $order_number");
                
                }
                //decrease current order count of the delivery staff 
                $staff_current_order_count_delivery = DB::SELECT("SELECT count(order_number) as current_count FROM order_master WHERE delivery_assigned_to = '$delivery_staff' AND current_status in ('C','OP')");
                $order_val_delivery = $staff_current_order_count_delivery[0]->current_count;
                DB::SELECT("UPDATE internal_staffs SET current_confirmed_count = $order_val_delivery where id = $delivery_staff");


                //$count_orderno = $database->getReference('location')->getChild($delivery_staff)->getChild("current_order")->getChild("order_count")->getValue();          
                //$count_orderno_ct['order_count']= $count_orderno - 1;
                //$database->getReference('location')->getChild($delivery_staff)->getChild("current_order")->update($count_orderno_ct);          
                
                // increase the count to the assigned staff

            
                //$count_orderno = $database->getReference('location')->getChild($staff_id)->getChild("current_order")->getChild("order_count")->getValue();          
                //$count_orderno_staf['order_count']= $count_orderno + 1;
                //$database->getReference('location')->getChild($staff_id)->getChild("current_order")->update($count_orderno_staf);          
                
                // add the details
                $insrt_orders = DB::SELECT("select rest_details->>'$.name' as restname,customer_details->>'$.latitude' as latitude,customer_details->>'$.longitude' as longitude FROM order_master WHERE order_number = '$order_number'");
                $restlist_data['rest_name']=$insrt_orders[0]->restname;             
                $restlist_data['cust_lat']=$insrt_orders[0]->latitude;
                $restlist_data['cust_long']=$insrt_orders[0]->longitude;
                //$newUserKey = $database->getReference('location')->getChild($staff_id)->getChild("orders")->getChild($order_number)->update($restlist_data);          
                DB::SELECT("INSERT INTO `internal_staffs_current_orders`(`isco_staff_id`, `isco_slno`, `isco_order_no`,`isco_cust_lat`, `isco_cust_long`) VALUES ('".$staff_id."','0','".$order_number."','".$restlist_data['cust_lat']."','".$restlist_data['cust_long']."')");
       
                $staff_current_order_count = DB::SELECT("SELECT count(order_number) as current_count FROM order_master WHERE delivery_assigned_to = '$staff_id' AND current_status in ('C','OP')");
                $order_val = $staff_current_order_count[0]->current_count;
                DB::SELECT("UPDATE internal_staffs SET current_confirmed_count = $order_val , dlv_area = '$cst_address1' where id = $staff_id");

            }
            else
            {
                DB::SELECT('UPDATE `order_master` SET `current_status`="C",status_details= JSON_INSERT(status_details,"$.C","'.$time.'"),delivery_assigned_to="'.$staff_id.'",assign_status="Manual",`assigned_time` =now(),`assigned_to`="'.$staff_id.'",delivery_assigned_details= JSON_OBJECT("name","'.$staff_name.'","phone","'.$staff_number.'","note","'.$assigned_note.'"),auth_status= JSON_OBJECT("C","'.$confirm_staff.'") WHERE order_number = "'.$order_number.'"');
                $contact = DB::SELECT("select delivery_assigned_details->>'$.phone' as mobile,customer_details->>'$.name' as cst_name,customer_details->>'$.mobile' as cst_mobile,customer_details->>'$.addressline2' as line2 FROM order_master WHERE order_number = '$order_number'");
                $mobile=$contact[0]->mobile;
                $cst_name=$contact[0]->cst_name;
                $cst_mobile=$contact[0]->cst_mobile;
                $line2=$contact[0]->line2;
                $sendmsg = "You have New Order Assigned with --  Order Number- $order_number ,  Customer Name - $cst_name , Phone - $cst_mobile , Area - $line2 , Note - $assigned_note ";
                /* $smsurl = Datasource::smsurl($mobile,$sendmsg);
                 $data = file_get_contents($smsurl);*/
                if(count($exist)>0)
                {
//                   if(isset($exist[0]->ftoken) && ($exist[0]->ftoken != '' ||$exist[0]->ftoken != ' ' ||$exist[0]->ftoken!= 'null')) {
                    $arr['to'] = $exist[0]->ftoken;
                    $arr['title'] = 'Order Assigned';
                    $arr['message'] = $sendmsg;
                    $arr['image'] = 'null';
                    $arr['action'] = 'orderhistory';
                    $arr['action_destination'] = 'null';
                    $arr['app_type'] = 'deliveryapp';
                    $result = Commonsource::notification($arr);
//                   }
                }
                           // increase th count of th delivery staff

                
                //$count_orderno = $database->getReference('location')->getChild($staff_id)->getChild("current_order")->getChild("order_count")->getValue(); 
                //$count_orderno_ct['order_count']= $count_orderno + 1;
               // $database->getReference('location')->getChild($staff_id)->getChild("current_order")->update($count_orderno_ct); 
                //add the details
                $restlist_data='';
                $insrt_orders = DB::SELECT("select rest_details->>'$.name' as restname,customer_details->>'$.latitude' as latitude,customer_details->>'$.longitude' as longitude FROM order_master WHERE order_number = '$order_number'");
                $restlist_data['rest_name']=$insrt_orders[0]->restname;             
                $restlist_data['cust_lat']=$insrt_orders[0]->latitude;
                $restlist_data['cust_long']=$insrt_orders[0]->longitude;
                //$newUserKey = $database->getReference('location')->getChild($staff_id)->getChild("orders")->getChild($order_number)->update($restlist_data);          
                DB::SELECT("INSERT INTO `internal_staffs_current_orders`(`isco_staff_id`, `isco_slno`, `isco_order_no`,`isco_cust_lat`, `isco_cust_long`) VALUES ('".$staff_id."','0','".$order_number."','".$restlist_data['cust_lat']."','".$restlist_data['cust_long']."')");
       
                $staff_current_order_count = DB::SELECT("SELECT count(order_number) as current_count FROM order_master WHERE delivery_assigned_to = '$staff_id' AND current_status in ('C','OP')");
                $order_val = $staff_current_order_count[0]->current_count;
                DB::SELECT("UPDATE internal_staffs SET current_confirmed_count = $order_val, dlv_area = '$cst_address1' where id = $staff_id");

            }
            $total_amount=0;
            $orderdetails = DB::SELECT("SELECT ROUND(om.final_total,$decimal_point) as omfinal_total,order_date,upper(payment_method) as paymethod From order_master as om WHERE om.order_number = '$order_number'");
            if(count($orderdetails)>0 && $orderdetails[0]->paymethod == 'COD')
            {     
                DB::select("delete from `internal_staffs_credits` where order_number = '" . $order_number . "'");
                DB::select("INSERT INTO `internal_staffs_credits`(`staff_id`, `order_number`, `staff_number`, `order_date`, `final_total`) VALUES ('" . $staff_id . "','" . $order_number . "','" . $staff_number . "','". $orderdetails[0]->order_date. "','" . $orderdetails[0]->omfinal_total . "')");
                $staffamount = DB::SELECT("select a.staff_credit - b.total  as pending_amount from ( select staff_max_credit as staff_credit,id from  `internal_staffs` where id = '".$staff_id."') as a Join (select sum(final_total) as total,staff_id from `internal_staffs_credits` where status in ('Reserve','Credit') and staff_id = '".$staff_id."') as b on a.id = b.staff_id");
                if(count($staffamount) != 0)
                {
                    if($staffamount[0]->pending_amount) {
                        $total_amount = $staffamount[0]->pending_amount;
                    }
                    else
                    {
                        $staffcreditmax = DB::SELECT("select staff_max_credit as staff_credit from  `internal_staffs` where id =  '".$staff_id."'");
                        $total_amount =$staffcreditmax[0]->staff_credit;
                    }
                }
                else
                {
                    $staffcreditmax = DB::SELECT("select staff_max_credit as staff_credit from  `internal_staffs` where id =  '".$staff_id."'");
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
            $userid = $request['customer_id'];
            $staff = $request['staff'];
            $hotel_name = $request['hotel_name'];
            $assigned_note = $request['assigned_note'];
            $is_exist= DB::SELECT("SELECT ftoken,customer_id,name FROM ftoken_master fm join customer_list cm on fm.customer_id = cm.id WHERE customer_id ='".trim($userid)."'");
            if(count($is_exist)>0)
            {
                foreach($is_exist as $item)
                {
//               if(isset($item->ftoken) && ($item->ftoken != '' ||$item->ftoken != ' ' ||$item->ftoken!= 'null')) {
                    $arr['to'] = $item->ftoken;
                    $arr['title'] = 'Order Assigned';
                    $arr['message'] = 'Your Order from '.$hotel_name.' ,Order Number- '.$order_number.' has been assigned to our Delivery staff - ' . $staff . ' for picking.';
                    $arr['image'] = 'null';
                    $arr['action'] = 'orderhistory';
                    $arr['action_destination'] = 'null';
                    $arr['app_type'] = 'customerapp';
                   $result = Commonsource::notification($arr);
//               }
                }
            }
            $msg = "success";
        }
        else
        {
            $msg = "Not Exist";
        }
        return response::json(compact('msg'));
    }

    public function cancel_order(Request $request)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $time = strtoupper($date->format('h:i a'));
        $order_number = $request['order_number'];
        $psw = $request['staff_code'];
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($psw, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        $cancel_reason = $request['cancel_reason'];
        $userid = $request['customer_id'];
        $staff = $request['staff'];
        $hotel_name = $request['hotel_name'];
        $order_details = DB::SELECT("SELECT current_status,payment_method,delivery_assigned_to from order_master where trim(order_number) = '".trim($order_number)."' ");
        $cstatus = $order_details[0]->current_status;
        $cancel_flag= 'N';
        $database = $this->firebase_db;
        if($cstatus =='P'){
            $condition = "confirm_permission";
        }
        else
        {
            $condition = "cancel_permission";
        }
        $check = DB::SELECT("SELECT authcode from internal_staffs where authcode ='$password' AND $condition='Y' ");
        if(count($check)>0)
        {
            $paydetails = DB::SELECT("SELECT final_total,payment_method,payment_details->>'$.razorpay_payment_id' as razorpay_payment_id,rest_details->>'$.name' as restname,customer_details->>'$.mobile' as mobile,customer_details->>'$.name' as cname  FROM order_master WHERE order_number='$order_number'");
            $razor_payid      = $paydetails[0]->razorpay_payment_id;
            $razor_pay_amount = $paydetails[0]->final_total;
            $refund_amount    = $razor_pay_amount*100;
            $payment_method   = $paydetails[0]->payment_method;
            $restname         = $paydetails[0]->restname;
            $mobile           = $paydetails[0]->mobile;
            $cname            = $paydetails[0]->cname;
            $message = "Dear $cname, Sorry  Your Potafo Order - $order_number from $restname has been cancelled due to $cancel_reason . ";
            if($payment_method =='ONLINE')
            {
                if($request['refund_status'] == 'Y') {
                    $message .= "Refund of Rs $razor_pay_amount has been Initiated to your original payment method,which will be reflected in max of 5-7 business days.";
                    $this->key_id = config('razor.key_id');
                    $this->secret = config('razor.key_secret');
                    $this->api = new Api($this->key_id, $this->secret);
                    $api = $this->api;
                    // Refunds
                    $refund = $api->refund->create(array('payment_id' => $razor_payid)); // Creates refund for a payment
                    //$refund = $api->refund->create(array('payment_id' => $razor_payid, 'amount'=>$refund_amount)); // Creates partial refund for a payment
                    $refundId = $refund->id;
                    $refund = $api->refund->fetch($refundId); // Returns a particular refund
                    if (isset($refundId) && $refundId != '') {
                        DB::UPDATE('UPDATE order_master SET `current_status`="CA",delivery_assigned_to=NULL,delivery_assigned_details=NULL,status_details= JSON_INSERT(status_details,"$.CA","' . $time . '"),cancel_reason="' . $cancel_reason . '",payment_details=JSON_SET(payment_details,"$.refundid","' . $refundId . '") WHERE order_number="' . $order_number . '" ');
                        $cancel_flag= 'Y';
                    }
                }
                else
                {
                    DB::UPDATE('UPDATE `order_master` SET `current_status`="CA",delivery_assigned_to=NULL,delivery_assigned_details=NULL,status_details= JSON_INSERT(status_details,"$.CA","'.$time.'"),cancel_reason="'.$cancel_reason.'",payment_details=JSON_SET(payment_details,"$.refundid","No_data") WHERE order_number = "'.$order_number.'"');
                    $cancel_flag= 'Y';
                }
            }
            else
            {
                DB::UPDATE('UPDATE `order_master` SET `current_status`="CA",delivery_assigned_to=NULL,delivery_assigned_details=NULL,status_details= JSON_INSERT(status_details,"$.CA","'.$time.'"),cancel_reason="'.$cancel_reason.'" WHERE order_number = "'.$order_number.'"');
                $cancel_flag= 'Y';
            }
            if(strtolower($order_details[0]->payment_method) =='cod') {
               DB::select("delete from `internal_staffs_credits` where order_number = '" . $order_number . "'");
            }

            $sendmsg = urlencode($message);
            $smsurl = Datasource::smsurl($mobile,$sendmsg);
            $data = file_get_contents($smsurl);
            $is_exist= DB::SELECT("SELECT ftoken,customer_id,name FROM ftoken_master fm join customer_list cm on fm.customer_id = cm.id WHERE customer_id =$userid");
            if(count($is_exist)>0)
            {
                foreach($is_exist as $item)
                {
//                   if(isset($item->ftoken) && ($item->ftoken != '' ||$item->ftoken != ' ' ||$item->ftoken!= 'null')) {
                    $arr['to'] = $item->ftoken;
                    $arr['title'] = 'Order Cancelled';
                    $arr['message'] = "Sorry!Your Order from $hotel_name,Order Number- '.$order_number.' has been cancelled due to $cancel_reason .";
                    $arr['image'] = 'null';
                    $arr['action'] = 'orderhistory';
                    $arr['action_destination'] = 'null';
                    $arr['app_type'] = 'customerapp';
                   $result = Commonsource::notification($arr);
//                   }
                }
            }
            $msg = "success";
            if($cancel_flag== 'Y')
            {
                if($order_details[0]->delivery_assigned_to != '')
                {
                    $delivery_staff = $order_details[0]->delivery_assigned_to;
                    //$database->getReference('location')->getChild($delivery_staff)->getChild("orders")->getChild($order_number)->remove();         
                
                    //decrease current order count of the delivery staff 
                    $staff_current_order_count = DB::SELECT("SELECT count(order_number) as current_count FROM order_master WHERE delivery_assigned_to = '$delivery_staff' AND current_status in ('C','OP')");
                    $order_val = $staff_current_order_count[0]->current_count;
                    DB::SELECT("DELETE FROM internal_staffs_current_orders WHERE isco_staff_id = $delivery_staff and isco_order_no = $order_number");
                    DB::SELECT("UPDATE internal_staffs SET current_confirmed_count = $order_val,dlv_area = NULL where id = $delivery_staff");
        
                   // $count_orderno = $database->getReference('location')->getChild($delivery_staff)->getChild("current_order")->getChild("order_count")->getValue();          
                   // $count_orderno_ct['order_count']= $count_orderno - 1;
                   // $database->getReference('location')->getChild($delivery_staff)->getChild("current_order")->update($count_orderno_ct);          
                    
                }
                // remove th specified order number from this delivery staff    
             }
            $reason_exist = DB::SELECT("SELECT id FROM order_cancel_reason WHERE reason='".$cancel_reason."' ");
            if(count($reason_exist)==0){
                DB::INSERT("INSERT INTO order_cancel_reason(reason) VALUES ('".$cancel_reason."') ");
            }
        }
        else
        {
            $msg = "Not Exist";
        }
        return response::json(compact('msg'));
    }
    public function autocomplete_reason(Request $request) {
        $append ='';
        $reasonlist = DB::SELECT("SELECT id,reason FROM order_cancel_reason WHERE reason LIKE '%".$request['reason']."%' ");
        foreach($reasonlist as $list){
            $append .= '<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="searchreason_'.$list->id.'" onclick=\'selectreasonexist("'.$list->reason.'")\'><a href="javascript:vojavasid(0);"><p>'.$list->reason.'</p></a></div>';

        }
        return $append;
    }
    //Front End Confirm Order
    public function order_confirmation($userid,$type,$line1,$line2,$landmark,$pincode,$paymethod)//API to confirm the order for particular User Id
    {
        /*  $exist=DB::SELECT("select order_number from order_details where order_number='t_$userid'");
          if(count($exist)>0){
          $timezone = 'ASIA/KOLKATA';
          $date = new DateTime('now', new DateTimeZone($timezone));
          $time = strtoupper($date->format('h:i a'));
          $datetime = $date->format('Y-m-d G:i:s');
          $length = DB::SELECT("SELECT json_length(`address`) as count FROM `customer_list` WHERE id='".$userid."'");
          $address_count =$length[0]->count;
          for($l=1;$l<=25;$l++)
          {
              $addresslimit = 'ADDRESS'.$l;
              DB::SELECT("UPDATE customer_list SET address=JSON_SET(address,'$.$addresslimit.DEFAULT','N') WHERE id ='$userid'");
            //DB::SELECT("SELECT address->>'$' FROM WHERE  address->>'$.*.LINE1' = '".$line1."'");
            //$address = DB::SELECT("UPDATE customer_list SET address=JSON_SET(address,'$.ADDRESS$l.DEFAULT','Y') where address->>'$.$addresslimit.TYPE' = '$type' and address->>'$.$addresslimit.LINE1' = '$line1' and id = '$userid'");
          }
          $dtl=    DB::select("SELECT substring_index(substring_index(JSON_UNQUOTE(JSON_SEARCH(address, 'ONE', '".$line1."')),'.', -2),'.', 1) AS result FROM customer_list  where id='$userid'");
          $aupdateadd = TRIM($dtl[0]->result);
          $address = DB::SELECT("UPDATE customer_list SET address=JSON_SET(address,'$.$aupdateadd.DEFAULT','Y') where address->>'$.$aupdateadd.TYPE' = '$type' and address->>'$.$aupdateadd.LINE1' = '$line1' and id = '$userid'");
          DB::SELECT('UPDATE `order_master` SET `current_status`="P",status_details= JSON_OBJECT("P","'.$time.'"),order_date ="'.$datetime.'",payment_method ="'.$paymethod.'",customer_details= JSON_INSERT(customer_details,"$.addresstype","'.$type.'","$.addressline1","'.$line1.'","$.addressline2","'.$line2.'","$.pincode","'.$pincode.'","$.landmark","'.$landmark.'")  WHERE order_number = "t_'.$userid.'"');
          $lastdata = DB::SELECT("select order_number from order_master where customer_id = '$userid' ORDER BY order_date DESC limit 1");
          $msg = 'Confirmed';
          return response::json(['msg' => $msg,'order_number'=>$lastdata[0]->order_number]);
          }
          else
          {
             $msg = 'Not Exist';
             return response::json(['msg' => $msg]);
          }*/
    }
    public function user_info(Request $request) {
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1  = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key  = hash('sha256', $key1);
        $iv   = substr(hash('sha256', $iv1), 0, 16);
        $customer = DB::SELECT("select name,lname,email,mobile_contact as mobile,password from customer_list where mobile_contact='9995776801' ");
        $pswd = base64_decode($customer[0]->password);
        $password = openssl_decrypt($pswd, $encr_method, $key, 0, $iv);
        // return $password;
        return "ok";
    }
    public function user_orderlist(Request $request)
    {
        $userid     = $request['user_id'];
        $cust_token = $request['cust_token'];
        $decimal_point = Commonsource::generalsettings();
        $detailarr=array();
        $detail=array();
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1  = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key  = hash('sha256', $key1);
        $iv   = substr(hash('sha256', $iv1), 0, 16);
        $customer = DB::SELECT("select name,lname,email,mobile_contact as mobile,password from customer_list where id = '$userid' AND cust_token='$cust_token' ");
        if(count($customer)!=0) {
            $pswd = base64_decode($customer[0]->password);
            $password = openssl_decrypt($pswd, $encr_method, $key, 0, $iv);
            $custarr['name'] = $customer[0]->name;
            if($customer[0]->lname!='null') {
                $custarr['lname'] = $customer[0]->lname;
            }
            else{
                $custarr['lname'] = "";
            }
            $custarr['email'] = $customer[0]->email;
            $custarr['mobile'] = $customer[0]->mobile;
            //        $custarr['password'] = $password;
            $exist = DB::SELECT("select order_details.order_number from order_details LEFT JOIN order_master ON order_master.order_number=order_details.order_number where customer_id='$userid'");
            if(count($exist)>0){
                $order = DB::SELECT("select order_number,order_date,name_tagline->>'$.name' as rest_name,current_status,review_star,final_total,IFNULL(coupon_details->>'$.coupon_per',0) as coupon_per,IFNULL(coupon_details->>'$.coupon_label',0) as coupon_label,IFNULL(coupon_details->>'$.coupon_amount',0) as coupon_amount FROM order_master LEFT JOIN restaurant_master ON restaurant_master.id=order_master.rest_id where customer_id = '$userid' and current_status !='T' ORDER BY order_date desc");
                foreach($order as $key=>$item)
                {
                    $details = DB::SELECT("select menu_details->>'$.menu_name' as menu_name,qty from order_master LEFT JOIN order_details ON order_details.order_number=order_master.order_number where customer_id = '$userid' and current_status != 'T' and order_details.order_number='$item->order_number' ORDER BY order_date desc");
                    $det=array();
                    foreach($details as $key=>$items)
                    {
                        $det[] = $items->menu_name . '*' .  $items->qty;
                    }
                    $date = $item->order_date;
                    $datetime= date(' d/m/Y - h:i:s A', strtotime($date));
                    $menu = implode(',',$det);
                    if($item->current_status == "P")
                    {
                        $status = 'Placed';
                    }
                    if($item->current_status == "C")
                    {
                        $status = 'Confirmed';
                    }
                    if($item->current_status == "OP")
                    {
                        $status = 'Order Picked';
                    }
                    if($item->current_status == "D")
                    {
                        $status = 'Delivered';
                    }
                    if($item->current_status == "CA")
                    {
                        $status = 'Cancelled';
                    }
                    $detailarr['order_date'] = date(' d/m/Y - h:i:s A', strtotime($date));
                    $detailarr['order_number'] = strtoupper($item->order_number);
                    $detailarr['name'] = strtoupper($item->rest_name);
                    $detailarr['menu'] =$menu;
                    $detailarr['status'] = $status;
                    $detailarr['review_star'] = $item->review_star;
                    $detailarr['final_total'] = round($item->final_total,$decimal_point);
                    $detailarr['coupon_per'] = $item->coupon_per;
                    $detailarr['coupon_label'] = $item->coupon_label;
                    $detailarr['coupon_amount'] = $item->coupon_amount;
                    $detail[] = $detailarr;
                }
                if(count($detail)>0)
                {
                    $msg = 'Exist';
                    return response::json(['msg' => $msg,'customer'=>$custarr,'details'=>$detail,'count' =>count($detail)]);
                }
                else
                {
                    $msg = 'Not Exist';
                    return response::json(['msg' => $msg,'customer'=>$custarr]);
                }
            }
            else
            {
                $msg = 'Not Exist';
                return response::json(['msg' => $msg,'customer'=>$custarr]);
            }
        }
        else{
            $msg = '‘invalid_data’';
            return response::json(['msg' => $msg,'customer'=>$custarr]);
        }

    }

    public function order_review($order_number)
    {
        $details = DB::SELECT("SELECT rest_details->>'$.name' as res_name,IFNULL(review_star,0) as res_rate,IFNULL(review_details->>'$.review','null') as res_review,delivery_assigned_details->>'$.name' as staff_name,IFNULL(delivery_assigned_details->>'$.star_rate'*1,0) as staff_rate,IFNULL(delivery_assigned_details->>'$.review','null') as staff_review from `order_master` where order_number = '".$order_number."'");
        if(count($details)>0)
        {
            return response::json(['msg' => 'Exist','detail' => $details[0]]);
        }
        else
        {
            return response::json(['msg' => 'Not Exist']);
        }
    }
//        public function order_review_add($order_number,$star,$review,$dlvrystar,$dlvryreview)
//    {
//        $timezone = 'ASIA/KOLKATA';
//        if($review=='null' || $review=='Null' || $review=='NULL'){
//            $review ='';
//        }
//        $date = new DateTime('now', new DateTimeZone($timezone));
//        $datetime = $date->format('Y-m-d h:i: A');
//        DB::SELECT('UPDATE `order_master` SET `review_star`="'.$star.'",review_details= JSON_OBJECT("review","'.$review.'","date","'.$datetime.'","status","N"),delivery_assigned_details=JSON_SET(delivery_assigned_details,"$.star_rate","'.$dlvrystar.'","$.review","'.$dlvryreview.'") WHERE order_number = "'.$order_number.'"');
//        $msg = 'Successful';
//        return response::json(['msg' => $msg]);
//    }

    public function user_orderdetails($order_number)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i A');
        $decimal_point = commonSource::generalsettings();
        $exist = DB::SELECT("select order_number from order_details where order_number = '$order_number'");
        if(count($exist)>0){
            $detailarr=array();
            $details = DB::SELECT("SELECT customer_id,IFNULL((status_details->>'$.P'),0) as placed_time,IFNULL((status_details->>'$.C'),0) as confirmed_time,IFNULL((status_details->>'$.OP'),0) as picked_time,IFNULL((status_details->>'$.D'),0) as delivered_time,IFNULL((status_details->>'$.CA'),0) as cancelled_time,current_status,name_tagline->>'$.name' as rest_name,customer_details->>'$.addresstype' as addresstype,customer_details->>'$.addressline1' as addressline1,customer_details->>'$.addressline2' as addressline2,customer_details->>'$.landmark' as landmark,customer_details->>'$.pincode' as pincode,order_master.order_number,menu_details->>'$.menu_name' as menu_name,qty,final_rate,final_total,sub_total,total_details->>'$.packing_charge' as packing_charge,total_details->>'$.delivery_charge' as delivery_charge,total_details->>'$.discount_amount' as discount,total_details->>'$.discount_label' as discount_label,payment_method,order_date,order_master.rest_id as rest_id,delivery_assigned_details->>'$.name' as staff_name,delivery_assigned_details->>'$.phone' as staff_mobile,IFNULL(coupon_details->>'$.coupon_per',0) as coupon_per,IFNULL(coupon_details->>'$.coupon_label',0) as coupon_label,IFNULL(coupon_details->>'$.coupon_amount',0) as coupon_amount FROM order_master LEFT JOIN order_details ON order_details.order_number=order_master.order_number LEFT JOIN restaurant_master ON restaurant_master.id=order_master.rest_id WHERE order_master.order_number='$order_number'");
            foreach($details as $key=>$item)
            {
                if($item->current_status == 'P')
                {
                    $status = 'Placed';
                }
                if($item->current_status == 'C')
                {
                    $status = 'Confirmed';
                }
                if($item->current_status == 'OP')
                {
                    $status = 'Order Picked';
                }
                if($item->current_status == 'D')
                {
                    $status = 'Delivered';
                }
                if($item->current_status == 'CA')
                {
                    $status = 'Cancelled';
                }
                $cust_id = $item->customer_id;
                $rest_id = $item->rest_id;
                $detai = DB::SELECT("select menu_details->>'$.menu_name' as menu_name,'General' as diet,qty,ROUND(final_rate,$decimal_point) AS final_rate from order_master LEFT JOIN order_details ON order_details.order_number=order_master.order_number where order_details.order_number='$item->order_number'");
                $detailarr['order_number'] = $item->order_number;
                $detailarr['order_date'] = date(' d/m/Y - h:i:s A', strtotime($item->order_date));
                $detailarr['rest_name'] = $item->rest_name;
                $detailarr['current_status'] = $status;
                $detailarr['placed_time'] = $item->placed_time;
                $detailarr['confirmed_time'] = $item->confirmed_time;
                $detailarr['picked_time'] = $item->picked_time;
                $detailarr['delivered_time'] = $item->delivered_time;
                $detailarr['cancelled_time'] = $item->cancelled_time;
                $detailarr['sub_total'] = round($item->sub_total,$decimal_point);
                $detailarr['final_total'] = round($item->final_total,0);
                $detailarr['coupon_per'] = $item->coupon_per;
                $detailarr['coupon_label'] = $item->coupon_label;
                $detailarr['coupon_amount'] = $item->coupon_amount;
                $detailarr['delivery_charge'] = round($item->delivery_charge,$decimal_point);
                $detailarr['packing_charge'] = round($item->packing_charge,$decimal_point);
                $detailarr['discount'] = round($item->discount,$decimal_point);
                $detailarr['discount_label'] = $item->discount_label;
                $detailarr['payment_method'] = $item->payment_method;
                $detailarr['addresstype'] = $item->addresstype;
                $detailarr['addressline1'] = $item->addressline1;
                $detailarr['addressline2'] = $item->addressline2;
                $detailarr['landmark'] = $item->landmark;
                $detailarr['pincode'] = $item->pincode;
                $detailarr['staff_name'] = $item->staff_name;
                $detailarr['staff_mobile'] = $item->staff_mobile;
                $detailarr['menudetails'] = $detai;
            }
            $customer = DB::SELECT("select name,lname,email,mobile_contact as mobile from customer_list where id = '$cust_id'");
            $msg = 'Exist';
            return response::json(['msg' => $msg,'customer'=>$customer[0],'details'=>$detailarr]);
        }else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }
    public function home_orderstatus($userid)
    {
        $detailarr=array();
        $order = DB::SELECT("select order_number,order_date,name_tagline->>'$.name' as rest_name,IFNULL((status_details->>'$.P'),0) as placed_time,IFNULL((status_details->>'$.C'),0) as confirmed_time,IFNULL((status_details->>'$.OP'),0) as picked_time,final_total FROM order_master LEFT JOIN restaurant_master ON restaurant_master.id=order_master.rest_id where customer_id = '$userid' and current_status NOT IN ('CA','D','T') ORDER BY order_date desc limit 1");
        if(count($order)>0)
        {
            $msg = 'Exist';
            return response::json(['msg' => $msg,'details'=>$order]);
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }

    //offer menu item name search
    public function orderitem_search(Request $request)
    {
        $term    = $request['searchterm'];
        $rest_id = $request['rest_id'];
        $details = DB::SELECT("SELECT m_menu_id,JSON_UNQUOTE(m_name_type->'$.name') as name,JSON_UNQUOTE(`m_por_rate`) as portion,JSON_LENGTH(`m_por_rate`) as count,m_pack_rate FROM restaurant_menu where LOWER(m_name_type->>'$.name') LIKE '".strtolower($term)."%' and m_rest_id = '".trim($rest_id)."' and m_status = 'Y'");
        $append ='';
        foreach($details as $orders) {
            $menu_portions= json_decode($orders->portion,true);
            for($pr=1;$pr<=$orders->count;$pr++) {
                $single_portion = $menu_portions['portion'.$pr];
                $pname = $single_portion['portion'];
                $inv_offer_details = DB::SELECT("SELECT IFNULL(a.inv_offer_details->>'$.$pname.offer_rate',0) as offer_rate FROM restaurant_menu a LEFT JOIN restaurant_offers b on a.m_rest_id=b.rest_id AND a.inv_offer_details->>'$.$pname.offer_slno'=b.sl_no WHERE a.m_rest_id='" . trim($rest_id) . "' AND a.m_menu_id='" . $orders->m_menu_id . "' AND now() BETWEEN a.inv_offer_details->>'$.$pname.valid_from' AND inv_offer_details->>'$.Single.valid_to' AND b.active='Y'");
                if(count($inv_offer_details)!=0){
                    $inv_final_rate = $inv_offer_details[0]->offer_rate;
                    $rate=$inv_final_rate + $orders->m_pack_rate;
                }
                else{
                    $rate = $single_portion['final_rate'] + $orders->m_pack_rate;
                }
                if($rate!=0) {
                    $search_id = $orders->m_menu_id.'_'.$pname;
                    $append .= '<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="search_'.$search_id.'" onclick=\'selectname("'.$orders->name.'","'.$orders->m_menu_id.'","'.$pname.'","'.$rate.'")\'><p>'.$orders->name.', '.$pname.'</p></div>';
                }
            }
        }
        return $append;
    }

    public function repeat_order($userid,$order_number)
    {
        DB::select("delete from order_details where order_number = 't_$userid'");
        DB::select("delete from order_master where order_number = 't_$userid'");
        DB::SELECT("INSERT INTO order_master(`order_number`,`customer_id`,`customer_details`,`rest_id`,`rest_details`,`sub_total`,`final_total`,`total_details`,`current_status`) SELECT 't_$userid','$userid',`customer_details`,`rest_id`,`rest_details`,`sub_total`,`final_total`,`total_details`,'T' FROM order_master WHERE order_number = '$order_number'");
        DB::SELECT("INSERT INTO order_details(`order_number`,`rest_id`,`menu_id`,`menu_details`,`single_rate_details`,`qty`,`final_rate`,offer_of_id) SELECT 't_$userid',`rest_id`,`menu_id`,`menu_details`,`single_rate_details`,`qty`,`final_rate`,offer_of_id FROM order_details WHERE order_number = '$order_number'");
        Commonsource::repeatavailability($userid);
        $order_details = DB::SELECT("SELECT order_number,`rest_id`,`menu_id`,`final_rate`,offer_of_id,menu_details->>'$.portion' as portion,sl_no,qty FROM order_details WHERE order_number = 't_$userid'");
        if(count($order_details) >0)
        {
            foreach ($order_details as $details)
            {
                if ($details->final_rate == 0)
                {
                    $parentmenu_id = $details->offer_of_id;
                    $portion_details = DB::SELECT("SELECT menu_details->>'$.portion' as portion FROM `order_details` WHERE `menu_id`='" . $parentmenu_id . "' AND`order_number`='t_$userid' ");
                    $parent_portion = $portion_details[0]->portion;
                    $rest_id = $details->rest_id;
                    $menu_id = $details->menu_id;
                    $sl_no = $details->sl_no;
                    $menu_qty = $details->qty;
                    $offer_details = DB::SELECT("SELECT b.* FROM restaurant_menu a LEFT JOIN restaurant_offers b on a.m_rest_id=b.rest_id AND a.m_present_offers->>'$.$parent_portion.offer_slno'=b.sl_no WHERE a.m_menu_id='" . $parentmenu_id . "' AND a.m_rest_id='" . $rest_id . "' AND b.active='Y' AND now() BETWEEN b.offer_details->>'$.valid_from' AND b.offer_details->>'$.valid_to' ");
                    if (count($offer_details) == 0)
                    {
                        DB::DELETE("DELETE FROM `order_details` WHERE sl_no='" . $sl_no . "' AND rest_id='" . $rest_id . "' AND menu_id='" . $menu_id . "' AND order_number='t_$userid' ");
                    }
                } else {
                    $menu_portion = $details->portion;
                    $order_restid = $details->rest_id;
                    $order_menuid = $details->menu_id;
                    $order_slno = $details->sl_no;
                    $order_qty = $details->qty;
                    $item_offer_exist = DB::SELECT("SELECT a.m_rest_id FROM restaurant_menu a LEFT JOIN restaurant_offers b on a.m_rest_id=b.rest_id AND a.inv_offer_details->>'$.$menu_portion.offer_slno'=b.sl_no WHERE a.inv_offer_details is NOT NULL AND a.m_rest_id='" . $order_restid . "' AND a.m_menu_id='" . $order_menuid . "' AND now() BETWEEN b.offer_details->>'$.valid_from' AND b.offer_details->>'$.valid_to' AND b.active='Y'  ");
                    if (count($item_offer_exist) == 0) {
                        $portion_length = DB::SELECT("SELECT json_length(m_por_rate) as portions FROM restaurant_menu WHERE m_rest_id='" . $order_restid . "' AND m_menu_id ='" . $order_menuid . "' ");
                        $portion_len = $portion_length[0]->portions;
                        for ($l = 1; $l <= $portion_len; $l++) {
                            $final_rate = DB::SELECT("SELECT m_por_rate->>'$.portion$l.final_rate' as finalrate,m_pack_rate FROM restaurant_menu WHERE m_por_rate->>'$.portion$l.portion'='" . $menu_portion . "' AND m_rest_id='" . $order_restid . "' AND m_menu_id ='" . $order_menuid . "' ");
                            if (count($final_rate) != 0) {
                                $rate = $order_qty * ($final_rate[0]->finalrate + $final_rate[0]->m_pack_rate);
                                DB::UPDATE("UPDATE order_details set final_rate='" . $rate . "' WHERE sl_no='" . $order_slno . "' AND rest_id='" . $order_restid . "' AND menu_id='" . $order_menuid . "' AND order_number='t_$userid' ");
                            }
                        }
                    }
                    Commonsource::item_pack_offer("t_$userid", $order_restid, $order_menuid, $order_qty);
                    //$orderid="t_$userid";$restid=$order_restid;$menuid=$order_menuid;$order_qty=$order_qty;
                }
            }
            $restid = $order_details[0]->rest_id;

            $bill_details = DB::SELECT("SELECT SUM(final_rate) as subtotal FROM order_details WHERE order_number = 't_$userid'");

            $subtotal = $bill_details[0]->subtotal;
            $final = DB::SELECT('SELECT delivery_charge,packing_charge,bill_offer_exist,bill_offer_slno FROM restaurant_master WHERE id="' . $restid . '"');
            foreach ($final as $key => $list)
            {
                $discount_amount = 0;
                $offer_percent = 0;
                if ($list->bill_offer_exist == 'Y')
                {
                    $offer_info = DB::SELECT("SELECT offer_details->>'$.max_amount' as max_amount,offer_details->>'$.amount_above' as amount_above,offer_details->>'$.offer_percent' as offer_percent  FROM restaurant_offers WHERE sl_no='" . $list->bill_offer_slno . "' AND rest_id='" . $restid . "' AND DATE_FORMAT(offer_details->>'$.valid_to','%Y-%m-%d %T') >= now() AND DATE_FORMAT(offer_details->>'$.valid_from','%Y-%m-%d %T') <= now() ");
                    if (count($offer_info) != 0)
                    {
                        $max_amount = $offer_info[0]->max_amount;
                        $amount_above = $offer_info[0]->amount_above;
                        $offer_percent = $offer_info[0]->offer_percent;
                        if ($subtotal > $amount_above) {
                            $discount_amount = ($subtotal * $offer_percent) / 100;
                            if ($discount_amount > $max_amount)
                            {
                                $discount_amount = $max_amount;
                            }
                        }
                    }
                }

                $delivery = $list->delivery_charge;
                $packing = $list->packing_charge;

            }
            $finaltotal = ($subtotal + $delivery + $packing) - $discount_amount;
            $final_total = round($finaltotal, 0);
            DB::SELECT('UPDATE `order_master` SET `sub_total`="' . $subtotal . '",final_total="' . $final_total . '",total_details= JSON_OBJECT("delivery_charge","' . $delivery . '","packing_charge","' . $packing . '","discount_amount","' . $discount_amount . '","discount_label","' . $offer_percent . '") WHERE order_number = "t_' . $userid . '" and rest_id="' . $restid . '"');
        }
        $msg = 'Successful';
        return response::json(['msg' => $msg]);
    }

    public function order_notification(Request $request)
    {
        $arr = array();
        $post = $request->all();
        $userid = $post['customer_id'];
        $staff = $post['staff'];
        $hotel_name = $post['hotel_name'];
        $order_number = $post['order_number'];
        $assigned_note = $request['assigned_note'];
        $is_exist= DB::SELECT("SELECT ftoken,customer_id,name FROM ftoken_master fm join customer_list cm on fm.customer_id = cm.id WHERE customer_id =$userid");
        if(count($is_exist)>0)
        {
            foreach($is_exist as $item)
            {
//               if(isset($item->ftoken) && ($item->ftoken != '' ||$item->ftoken != ' ' ||$item->ftoken!= 'null')) {
                $arr['to'] = $item->ftoken;
                $arr['title'] = 'Order Assigned';
                $arr['message'] = 'Your Order from '.$hotel_name.' ,Order Number- '.$order_number.' has been assigned to our Delivery staff - ' . $staff . ' for picking.';
                $arr['image'] = 'null';
                $arr['action'] = 'orderhistory';
                $arr['action_destination'] = 'null';
                $arr['app_type'] = 'customerapp';
               $result = Commonsource::notification($arr);
//               }
            }
        }
    }

    public function check_paymentstatus(Request $request)
    {
        $order_number = $request['order_number'];
        $select = DB::SELECT("SELECT payment_details->>'$.razorpay_payment_id' as paymentid FROM  order_master WHERE order_number = $order_number");
        if(isset($select) && ($select != ''  || $select != NULL ))
        {
            $msg = 'Exist';
            $data = $select[0]->paymentid;
            return response::json(['msg' => $msg,'data' => $data]);
        }
        else
        {
            $msg = 'NotExist';
            return response::json(['msg' => $msg]);

        }
    }
    
    public function update_releasehold(Request $request)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $time = strtoupper($date->format('H:i:s'));
        $orderno = $request['order_number'];
        $details = DB::SELECT('update order_master set on_hold = "N",on_hold_release_time = "' . $time . '" WHERE order_number = "' . $orderno . '"');
        $orderdetails = DB::SELECT('SELECT rest_id from order_master where order_number = "' . $orderno . '"');
        $is_exist = DB::SELECT("SELECT ftoken FROM users WHERE restaurant_id ='" . $orderdetails[0]->rest_id . "' and active = 'Y' and login_group ='H' and ftoken is not null");
        $order_restcat = DB::SELECT("SELECT r.category as category  FROM order_master o left join restaurant_master r on r.id = o.rest_id WHERE o.order_number  ='$orderno'");
        if($order_restcat[0]->category== 'Potafo Mart')
        {
                    $not_app_type = 'potafo_mart';
        }
         else
         {
                    $not_app_type = 'partnerapp';
         }
        
        if (count($is_exist) > 0) {
            foreach ($is_exist as $item) {
                $arr['to'] = $item->ftoken;
                $arr['title'] = 'New Potafo Order!';
                $arr['message'] = "Please accept the order to proceed.Order Number - " . $orderno . "";
                $arr['image'] = 'null';
                $arr['action'] = 'orders';
                $arr['action_destination'] = 'null';
                $arr['app_type'] = $not_app_type;
               $result = Commonsource::notification($arr);
            }
        }
        return 'success';
    }


    public function tests()
    {
      $testing  = confirmationdiff('14:16:15','2:18:40');
        return $testing;
    }
//ALTER TABLE `order_master` ADD `on_hold_release_time` TIME NULL DEFAULT NULL AFTER `razorpay_oldorders`;

    //order map load
     public function order_mapload(Request $request)
    {
         $latitude=$request['id'];
         $longitude=$request['long'];
          return view('order.order_mapload',compact('latitude','longitude'));
    }
}