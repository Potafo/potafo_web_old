<?php

namespace App\Http\Controllers;

use App\RestaurantMenu;
use App\RestaurantOffer;
use App\TaxMaster;
use Helpers\Datasource;
use Helpers\Commonsource;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Image;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Input;
class RestaurantOfferController extends Controller
{
    //view restaurant offer page
    public function restaurantoffer($id)
    {
        $resid = $id;
        $restaurant_name = DB::SELECT('SELECT name_tagline->>"$.name" as name FROM `restaurant_master` WHERE `id` = "'.$resid.'"' );
        $details_bill = DB::SELECT('SELECT rest_id,sl_no,type,item_type,description,image,active,offer_details->>"$.valid_to" as valid_to,offer_details->>"$.max_amount" as max_amount,offer_details->>"$.offer_name" as offer_name,offer_details->>"$.valid_from" as valid_from,offer_details->>"$.amount_above" as amount_above,offer_details->>"$.offer_percent" as offer_percent
                        FROM `restaurant_offers` WHERE `restaurant_offers`.`rest_id` = "'.$resid.'" and type = "B" ');
        
        $det_pack = DB::SELECT('SELECT rest_id,sl_no,type,item_type,description,image,active,offer_details->>"$.offer_name" as offer_name,offer_details->>"$.qty" as qty,offer_details->>"$.off_qty" as off_qty,offer_details->>"$.valid_to" as valid_to,offer_details->>"$.item_name" as item_name,offer_details->>"$.offer_item" as offer_item,offer_details->>"$.valid_from" as valid_from
                       FROM `restaurant_offers` WHERE `restaurant_offers`.`rest_id` = "'.$resid.'" and type = "I" and item_type = "P" ');
        
        $det_individual = DB::SELECT('SELECT rest_id,sl_no,type,item_type,description,image,active,offer_details->>"$.offer_name" as offer_name,offer_details->>"$.item_name" as item_name,offer_details->>"$.offer_rate" as offer_rate,offer_details->>"$.valid_to" as valid_to,offer_details->>"$.original_rate" as original_rate,offer_details->>"$.valid_from" as valid_from
                       FROM `restaurant_offers` WHERE `restaurant_offers`.`rest_id` = "'.$resid.'" and type = "I" and item_type = "I" ');
        return view('restaurant_offer.restaurant_offer',compact('rows','resid','details_bill','det_pack','det_individual','restaurant_name'));
    }                                                                  
    //add restaurant offer
    public function restaurant_offer(Request $request)
    {
        $type = $request['offer_type'];
        $item_type = $request['item_type'];
        $resid = $request['res_id'];
        $timeDate = date("jmYhis") . rand(991, 9999);
        if($type == 'B') 
        {
         $amt_above = $request['amount_above'];
            $offername = $request['offer_name'];
            $offerpercent = $request['offer_percent'];
            $max_amount = $request['max_amount'];
             if($request['valid_from']>=$request['valid_to']){
                    $msg = 'bill_invaliddate_range';
                       return response::json(compact('msg'));
             }
            $valid_from =date(' Y-m-d H:i:s',strtotime($request['valid_from']));
            $valid_to = date(' Y-m-d H:i:s', strtotime($request['valid_to']));
            $desc = $request['desc'];
            $img = Input::file('img');
            if (isset($img) && $img != '')
            {
                $uploadfile = $timeDate . '.' . strtolower($img->getClientOriginalExtension());
                Image::make($img)->save(base_path() . '/uploads/offers/res_offers/' . $uploadfile);
                $image_url = 'uploads/offers/res_offers/' . $uploadfile;
            }
            else
            {
                $image_url = '';
            }
            $details = DB::select("SELECT sl_no FROM restaurant_offers where offer_details->>'$.offer_name'  = '" . trim($offername) . "' and rest_id = '" . trim($resid) . "'");
            if($request['opertntype']=='replace'){
                 DB::UPDATE("UPDATE restaurant_master SET bill_offer_exist='Y', bill_offer_slno='".$details[0]->sl_no."' WHERE id='" . trim($resid) . "' ");  
                    $msg = 'success';
                    DB::UPDATE("UPDATE restaurant_offers SET active='N' WHERE rest_id='" . trim($resid) . "'  ");
                    DB::UPDATE("UPDATE restaurant_offers SET active='Y' WHERE rest_id='" . trim($resid) . "' AND sl_no='".$details[0]->sl_no."' ");
                      return response::json(['msg' => $msg]);

            }
            if(count($details)<=0)
            {
                $bill_offer = DB::SELECT("SELECT a.bill_offer_exist,MAX(b.sl_no) as slno FROM restaurant_master a LEFT JOIN  restaurant_offers b on a.id=b.rest_id WHERE a.id='" . trim($resid) . "' ");
                if($bill_offer[0]->bill_offer_exist=='N') {
                   DB::INSERT("INSERT INTO `restaurant_offers`(`rest_id`, `sl_no`, `type`, `offer_details`, `description`, `image`,active) VALUES ('" . trim($resid) . "','0','" . trim($type) . "',json_object('offer_name','".$offername."','offer_percent','".$offerpercent."','amount_above','".$amt_above."','max_amount','".$max_amount."','valid_from','".$valid_from."','valid_to','".$valid_to."'),'".$desc."','".$image_url."','Y')");
		   $det = DB::select("SELECT sl_no FROM restaurant_offers where offer_details->>'$.offer_name'  = '" . trim($offername) . "' and rest_id = '" . trim($resid) . "'");
                    DB::UPDATE("UPDATE restaurant_master SET bill_offer_exist='Y', bill_offer_slno='".$det[0]->sl_no."' WHERE id='" . trim($resid) . "' ");  
                    $msg = 'success';
                }
                else{
                   DB::INSERT("INSERT INTO `restaurant_offers`(`rest_id`, `sl_no`, `type`, `offer_details`, `description`, `image`,active) VALUES ('" . trim($resid) . "','0','" . trim($type) . "',json_object('offer_name','".$offername."','offer_percent','".$offerpercent."','amount_above','".$amt_above."','max_amount','".$max_amount."','valid_from','".$valid_from."','valid_to','".$valid_to."'),'".$desc."','".$image_url."','N')");
                    $msg = 'bill_offer_exit';
                }                
            }
            else
            {
                $msg = 'exist';
            }
        }
        elseif($type == 'I' && $item_type == 'I')
        {
            $types = 'I';
            if($request['item_valid_from']>=$request['item_valid_to']){
                $msg = 'item_invaliddate_range';
                return response::json(compact('msg'));
            }
            $offer_name = $request['ii_offername'];
            $item_name = $request['item_name'];
            $item_portion = $request['item_portion'];
            $item_id = $request['item_id'];
            $offer_rate = $request['offer_rate'];
            $original_rate = $request['original_rate'];
            $exc_rate = $offer_rate;
			
            $taxdetails = TaxMaster::where('restaurant_id',$resid)->select('t_value')->first();
            if(count($taxdetails)>0 && isset($taxdetails['t_value'])) {
                $tax_rate = ($taxdetails['t_value'] * $exc_rate) / 100;
            }
            else
            {
                $tax_rate = 0.00;
            }
            $inc_tax_rate =$tax_rate + $exc_rate;
            $valid_from =date(' Y-m-d H:i:s',strtotime($request['item_valid_from']));
            $valid_to= date(' Y-m-d H:i:s',strtotime($request['item_valid_to']));
            $desc = $request['desc'];
            $img = Input::file('item_img');

            if (isset($img) && $img != '')
            {
                $uploadfile = $timeDate . '.' . strtolower($img->getClientOriginalExtension());
                Image::make($img)->save(base_path() . '/uploads/offers/res_offers/' . $uploadfile);
                $image_url = 'uploads/offers/res_offers/' . $uploadfile;
            }
            else
            {
                $image_url = '';
            }

            $details = DB::select("SELECT sl_no FROM restaurant_offers where offer_details->>'$.item_name'  = '" . trim($item_name) . "' and rest_id = '" . trim($resid) . "' and  offer_details->>'$.offer_rate' = '" . trim($offer_rate) . "' and  offer_details->>'$.valid_from' = '" . trim($valid_from) . "' and  offer_details->>'$.valid_to' = '" . trim($valid_to) . "' and description = '" . trim($desc) . "'");
            if(count($details)<=0)
            {
                $inv_offer_exit = DB::SELECT("SELECT IFNULL(inv_offer_details->>'$.$item_portion.offer_rate',0) as itemoffer,m_pack_rate as pack_rate FROM restaurant_menu WHERE m_rest_id='" . trim($resid) . "' AND m_menu_id='".$item_id."' ");
                if($inv_offer_exit[0]->itemoffer==0){
                    $restoffer = DB::SELECT("SELECT  IFNULL(extra_rate_percent,0) AS extra_rate_percent FROM restaurant_master a  WHERE a.id='" . trim($resid) . "' ");
                    $pack_rate = $inv_offer_exit[0]->pack_rate;
                    $extra_value = ($restoffer[0]->extra_rate_percent * $inc_tax_rate)/100;
					$final_offer_rate = $inc_tax_rate + $extra_value + $pack_rate;
                    DB::INSERT("INSERT INTO `restaurant_offers`(`rest_id`, `sl_no`, `type`, `item_type`,`offer_details`, `description`, `image`) VALUES ('" . trim($resid) . "','0','" . $types . "','" . trim($item_type) . "',json_object('offer_name','".$offer_name."','item_name','".$item_name."','exc_rate','".$exc_rate."','tax_rate','".$tax_rate."','inc_tax_rate','".$inc_tax_rate."','pack_rate','".$pack_rate."','extra_val','".$extra_value."','offer_rate','".$final_offer_rate."','original_rate','".$original_rate."','valid_from','".$valid_from."','valid_to','".$valid_to."'),'".$desc."','".$image_url."')");
                    $take_slno = DB::SELECT("SELECT sl_no FROM restaurant_offers WHERE rest_id='" . trim($resid) . "' AND type='I' AND item_type='I' AND offer_details->>'$.item_name'='".$item_name."' ");
                    $new_slno = $take_slno[0]->sl_no;
                    DB::UPDATE('UPDATE restaurant_menu SET inv_offer_details=JSON_OBJECT("'.$item_portion.'",JSON_OBJECT("valid_from","'.$valid_from.'","valid_to","'.$valid_to.'","offer_rate","'.$final_offer_rate.'","offer_slno","'.$new_slno.'","exc_rate","'.$exc_rate.'","tax_rate","'.$tax_rate.'","inc_tax_rate","'.$inc_tax_rate.'","pack_rate","'.$pack_rate.'","extra_val","'.$extra_value.'")) WHERE m_rest_id="' . trim($resid) . '" AND m_menu_id="'.$item_id.'" ');
                    $msg = 'success';
                    return response::json(['msg' => $msg]);
                }
                else{
                    $msg = 'item_offer_exist';
                    return response::json(['msg' => $msg]);

                }

            }
            else
            {
                $msg = 'exist';
            }
        }
        elseif($type == 'I' && $item_type == 'P')
        {
          $types = 'I';
          if($request['p_valid_from']>=$request['p_valid_to']){
                    $msg = 'pack_invaliddate_range';
                       return response::json(compact('msg'));
             }
            $item_name = $request['p_item'];
            $offer_name = $request['ip_offername'];
            $p_item_portion = $request['p_item_portion'];
            $p_item_id = $request['p_item_id'];
            $qty = $request['p_qty'];
            $offer_item = $request['p_off_item'];
            $off_qty = $request['p_off_qty'];
            $valid_from =date(' Y-m-d H:i:s',strtotime($request['p_valid_from']));
            $valid_to=  date(' Y-m-d H:i:s',strtotime($request['p_valid_to']));
            $desc = $request['pdesc'];
            $img = Input::file('p_img');

            if (isset($img) && $img != '')
            {
                $uploadfile = $timeDate . '.' . strtolower($img->getClientOriginalExtension());
                Image::make($img)->save(base_path() . '/uploads/offers/res_offers/' . $uploadfile);
                $image_url = 'uploads/offers/res_offers/' . $uploadfile;
            }
            else
            {
                $image_url = '';
            }

            $details = DB::select("SELECT sl_no FROM restaurant_offers where offer_details->>'$.item_name'  = '" . trim($item_name) . "' and rest_id = '" . trim($resid) . "' and  offer_details->>'$.qty' = '" . trim($qty) . "' ");
            if(count($details)<=0)
            {
                DB::INSERT("INSERT INTO `restaurant_offers`(`rest_id`, `sl_no`, `type`, `item_type`,`offer_details`, `description`, `image`) VALUES ('" . trim($resid) . "','0','" . $types . "','" . trim($item_type) . "',json_object('offer_name','".$offer_name."', 'item_name','".$item_name."','qty','".$qty."','offer_item','".$offer_item."','off_qty','".$off_qty."','valid_from','".$valid_from."','valid_to','".$valid_to."'),'".$desc."','".$image_url."')");
                 $take_slno = DB::SELECT("SELECT sl_no FROM restaurant_offers WHERE rest_id='" . trim($resid) . "' AND type='I' AND item_type='P' AND offer_details->>'$.item_name'='".$item_name."' ");
                        $new_slno = $take_slno[0]->sl_no;
                         DB::UPDATE('UPDATE restaurant_menu SET m_present_offers=JSON_OBJECT("'.$p_item_portion.'",JSON_OBJECT("offer_slno","'.$new_slno.'")) WHERE m_rest_id="' . trim($resid) . '" AND m_menu_id="'.$p_item_id.'" ');
                        
                $msg = 'success';
                return response::json(['msg' => $msg]);
            }
            else
            {
                $msg = 'exist';
            }
        }
                        return response::json(['msg' => $msg]);

    }

    //offer menu item name search
    public function offeritem_search(Request $request)
    {
        $term    = $request['searchterm'];
        $rest_id = $request['rest_id'];//return "SELECT m_menu_id,JSON_UNQUOTE(m_name_type->'$.name') as name,JSON_UNQUOTE(`m_por_rate`) as portion,JSON_LENGTH(`m_por_rate`) as count,m_pack_rate FROM restaurant_menu where LOWER(m_name_type->>'$.name') LIKE '%".strtolower($term)."%' and m_rest_id = '".trim($rest_id)."' and m_status = 'Y'";
        $details = DB::SELECT("SELECT m_menu_id,JSON_UNQUOTE(m_name_type->'$.name') as name,JSON_UNQUOTE(`m_por_rate`) as portion,JSON_LENGTH(`m_por_rate`) as count,m_pack_rate FROM restaurant_menu where LOWER(m_name_type->>'$.name') LIKE '%".strtolower($term)."%' and m_rest_id = '".trim($rest_id)."' and m_status = 'Y'");
        return $details;
    }
    //Front End API(restaurant and General offers list
     public function restaurant_offerslist(Request $request)
     {
        $arr = array();
        $restaurantarr = array();
        $restaurants = array();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y m d h:i:s a');
        $time = strtoupper($date->format('h:i a'));
        $day   = trim(strtoupper($date->format('l')));
        $line1 = trim($request['line1']);
        $restaurant = DB::SELECT('SELECT rt.id as res_id,rt.google_location as rest_location,rt.geo_cordinates as cordinates,rt.delivery_range_unit->>"$.range" as range_unit FROM `restaurant_master` rt right join  `restaurant_offers` ro on rt.id = ro.rest_id WHERE rt.status = "Y" and  ro.active = "Y" and ro.image != "" AND now() BETWEEN offer_details->>"$.valid_from" AND offer_details->>"$.valid_to" and (SELECT rt_rest_id from restaurant_timings where rt_day = "'.$day.'" AND rt_rest_id = rt.id) IS NOT NULL');
        $deliverylocking_status = Commonsource::deliverylocking();
        if($deliverylocking_status == 'Y')
        {
            if($line1 == 'null')
            {
                $restaurants = $restaurant;
            }
            else
            {
                $typearr = explode('_',$line1);
                if(count($typearr)>0)
                {
                    if(strtoupper($typearr[0]) == 'LAT')
                    {
                        if (strpos($typearr[1], '$') !== false)
                        {
                            $cordinates = explode('$',$typearr[1]);
                        }
                        else
                        {
                            return response::json(['msg' => 'Latitude/Longitude should be separated by `$`']);
                        }
                    }
                    else if(strtoupper($typearr[0]) == 'ADD')
                    {
                        $cordnt = Commonsource::latitude_longitude($typearr[1]);
                        if($cordnt[2] == '0')
                        {
                            return response::json(['msg' => 'Not Exist']);
                        }
                        else
                        {
                            $cordinates = $cordnt;
                        }
                    }
                    else
                    {
                        return response::json(['msg' => 'line1 should have lat/add as prefix']);
                    }
                }
                $restaurantlist = Commonsource::locaterestaurant($restaurant,$cordinates);
                if(count($restaurantlist) >0)
                {
                    $restaurants = $restaurantlist[0];
                    $restarr = $restaurantlist[1];
                    $restlist = implode(',',$restaurantlist[1]);
                }
            }
        }
         if(isset($restarr) && count($restarr)>0)
         {
                      $rest_offer = DB::SELECT("SELECT JSON_UNQUOTE(offer_details->'$.name') as offername,`image`,0  as restaurant_id FROM `general_offers`  where active = 'Y' and image != ''
                      union SELECT JSON_UNQUOTE(offer_details->'$.offer_name') as offername,`image`,rest_id  as restaurant_id FROM `restaurant_offers`  where active = 'Y' and image != '' AND now() BETWEEN offer_details->>'$.valid_from' AND offer_details->>'$.valid_to' and rest_id in ($restlist)");
         }
         else if(isset($restarr) && count($restarr) <= 0)
         {
             $rest_offer = DB::SELECT("SELECT JSON_UNQUOTE(offer_details->'$.name') as offername,`image`,0  as restaurant_id FROM `general_offers`  where active = 'Y' and image != ''");
         }
         else if(!isset($restarr) && $line1 == 'null')
         {
                $rest_offer = DB::SELECT("SELECT JSON_UNQUOTE(offer_details->'$.name') as offername,`image`,0  as restaurant_id FROM `general_offers`  where active = 'Y' and image != ''
                              union SELECT JSON_UNQUOTE(offer_details->'$.offer_name') as offername,`image`,rest_id  as restaurant_id FROM `restaurant_offers`   left join restaurant_master on restaurant_master.id = restaurant_offers.rest_id where active = 'Y' and image != '' AND now() BETWEEN offer_details->>'$.valid_from' AND offer_details->>'$.valid_to' and (SELECT rt_rest_id from restaurant_timings where rt_day = '".$day."' AND rt_rest_id = restaurant_master.id) IS NOT NULL");
         }
         if(isset($rest_offer) && count($rest_offer)>0)
         {
             foreach ($rest_offer as $i => $item)
             {
                 $offerarr = array();
                     $id = $item->restaurant_id;
                     $detail = DB::SELECT("SELECT min_delivery_time,min_prepration_time,rt_from_time,rt_to_time,pure_veg,busy,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_value,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,JSON_UNQUOTE(name_tagline->'$.name') as name FROM `restaurant_master` join restaurant_timings on restaurant_master.id = restaurant_timings.rt_rest_id  WHERE  id = '" . $id . "'  and rt_day ='".$day."'");
                     if (count($detail) > 0)
                     {
                         if (isset($detail[0]))
                         {
                             if ($detail[0]->busy == 'Y')
                             {
                                 $status = 'Busy';
                             }
                             else
                             {
                                 foreach ($detail as $key=>$val)
                                 {
                                     $open = strtoupper($val->rt_from_time);
                                     $close = strtoupper($val->rt_to_time);
                                     if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close)) {
                                         $status = 'Open';
                                         break;
                                     }
                                     else {
                                         $status = 'Closed';
                                     }
                                 }
                             }
                         }
                         $offerarr['status']   = $status;
                         $offerarr['name']     = $detail[0]->name;
                         $offerarr['tag_line'] = $detail[0]->tag_line;
                         $offerarr['pure_veg'] = $detail[0]->pure_veg;
                         $offerarr['delivery_time'] = $detail[0]->min_delivery_time;
                         $offerarr['delivery_time'] = $detail[0]->min_delivery_time;
                         $offerarr['prepration_time'] = $detail[0]->min_prepration_time;
                         $offerarr['review_count'] = $detail[0]->review_count;
                         $offerarr['star_value'] = $detail[0]->star_value;
                     }

                        /* if(isset($status))
                         {*/
                             $offerarr['restaurant_id'] = $item->restaurant_id;
                             $offerarr['offername'] = $item->offername;
                             $offerarr['image'] = $item->image;
//                         }
//                 }
                 if(count($offerarr)>0)
                 {
                     $arr[] = $offerarr;
                 }
             }
         }
        if(count($arr)>0)
        {
            $msg = 'Exist';
        }
        else{
            $msg = 'Not Exist';
        }
        return response::json(['msg' => $msg,'offers' => $arr]);
    }
    
     public function rest_offer_status(Request $request)
    {
         $rest_id   = $request['ids'];
         $slno      = $request['slno'];
         $status    = $request['status'];
         $type      = $request['type'];
//          if($type == 'B'){
//            DB::UPDATE("UPDATE restaurant_offers SET active='N' WHERE rest_id='".$rest_id."' AND type = 'B' ");
//            //DB::UPDATE("UPDATE restaurant_master SET bill_offer_slno=0,bill_offer_exist='N' WHERE id='".$rest_id."'  ");
//
//         }
        if($status =='Y' && $type == 'B'){
             DB::UPDATE("UPDATE restaurant_offers SET active='N' WHERE rest_id ='".$rest_id."' AND sl_no ='".$slno."' ");
             DB::UPDATE("UPDATE restaurant_master SET bill_offer_slno=0,bill_offer_exist='N' WHERE id='".$rest_id."'  ");
             $msg= "billinactive";
             
         }
         else if($status =='N' && $type == 'B'){
             DB::UPDATE("UPDATE restaurant_offers SET active='N' WHERE rest_id='".$rest_id."' AND type = 'B' ");
             DB::UPDATE("UPDATE restaurant_offers SET active='Y' WHERE rest_id ='".$rest_id."' AND sl_no ='".$slno."' ");
              DB::UPDATE("UPDATE restaurant_master SET bill_offer_slno='".$slno."',bill_offer_exist='Y'  WHERE id='".$rest_id."'  ");
              $msg= "billactive";
         }
         else if($status =='Y' && $type != 'B'){
             DB::UPDATE("UPDATE restaurant_offers SET active='N' WHERE rest_id ='".$rest_id."' AND sl_no ='".$slno."' ");
             $msg= "iteminactive";
         }
         else if($status =='N' && $type != 'B'){
             DB::UPDATE("UPDATE restaurant_offers SET active='Y' WHERE rest_id ='".$rest_id."' AND sl_no ='".$slno."' ");
             $msg= "itemactive";
         }
         
         return $msg;
//        $rest_offer = RestaurantOffer::where('rest_id','=',$request['ids'])
//                      ->where('sl_no','=',$request['slno'])
//                      ->where('active','=','Y')
//                      ->get();
//        if(count($rest_offer)>0)
//        {
//            RestaurantOffer::where('rest_id', $request['ids'])
//                    ->where('sl_no','=',$request['slno'])
//                    ->update(
//                [
//                    'active' => 'N'
//                ]);
//        }
//        else
//        {
//            RestaurantOffer::where('rest_id', $request['ids'])
//                    ->where('sl_no','=',$request['slno'])
//                    ->update(
//                [
//                    'active' => 'Y'
//                ]);
//        }
        

    }
    public function edit_rest_offers(Request $request)
    {
      
      $type=$request['ed_type'];
      $item_type=$request['ed_itemtype'];
       if($type=='B')
       {
            if($request['edvalid_from']>=$request['edvalid_to']){
                    $msg = 'bill_invaliddate_range';
                       return response::json(compact('msg'));
             }
            $edimg = Input::file('edimg');
            $url = Datasource::geturl();
            $timeDate = date("jmYhis") . rand(991, 9999);
            $date = date('Y-m-d');

            if (Input::file('edimg') != '') {
                $image = Input::file('edimg');
                $uploadfile = time() . '.' . $image->getClientOriginalExtension();
                Image::make($image)->resize(250, 250)->save(base_path() . '/uploads/offers/res_offers/' . $uploadfile);
                if ($request['editoldimg'] == '')
                {
                  $edimg = 'uploads/offers/res_offers/' . $uploadfile;
                }
                $edimg = 'uploads/offers/res_offers/' . $uploadfile;
                } elseif($request['editoldimg'] != '')
                {
                  $edimg = $request['editoldimg'];
                }
                else
                {
                  $edimg = '';
                }
                DB::SELECT("UPDATE `restaurant_offers` SET `offer_details`=json_object('offer_name','" . $request['edoffer_name'] . "','amount_above','" . $request['edamount_above'] . "','max_amount','" . $request['edmax_amount'] . "','valid_to','" . date(' Y-m-d H:i:s',strtotime($request['edvalid_to'])) . "','offer_percent','" . $request['edoffer_percent'] . "','valid_from','" . date(' Y-m-d  H:i:s',strtotime($request['edvalid_from'])) . "'),`description`='" . $request['ed_descdetail'] . "',`image`='$edimg'  WHERE `rest_id` = '" . $request['edres_id'] . "' and sl_no = '".$request['edslno']."'");
                $msg = 'success';
                return response::json(compact('msg'));
        }
        elseif($type=='I' && $item_type=='I')
       {
             if($request['editem_valid_from']>=$request['editem_valid_to']){
                    $msg = 'item_invaliddate_range';
                       return response::json(compact('msg'));
             }
            $edimg = Input::file('editem_img');
            $url = Datasource::geturl();
            $timeDate = date("jmYhis") . rand(991, 9999);
            $date = date('Y-m-d');

            if (Input::file('editem_img') != '') {
                $image = Input::file('editem_img');
                $uploadfile = time() . '.' . $image->getClientOriginalExtension();
                Image::make($image)->resize(250, 250)->save(base_path() . '/uploads/offers/res_offers/' . $uploadfile);
                if ($request['editoldimg'] == '') {
                  $edimg = 'uploads/offers/res_offers/' . $uploadfile;
                }
                $edimg = 'uploads/offers/res_offers/' . $uploadfile;
                } elseif($request['editoldimg'] != '')
                {
                  $edimg = $request['editoldimg'];
                }
                else
                {
                  $edimg = '';
                }
               $exc_rate = $request['edoffer_rate'];
               $taxdetails = TaxMaster::where('restaurant_id',$request['edres_id'])->select('t_value')->first();
               if(count($taxdetails)>0 && isset($taxdetails['t_value'])) {
                   $tax_rate = ($taxdetails['t_value'] * $exc_rate) / 100;
               }
               else {
                   $tax_rate = 0.00;
               }
                 $itemname = explode(',', $request['editem_name']);
                $inv_offer_exit = DB::SELECT("SELECT IFNULL(m_pack_rate,0) as pack_rate FROM restaurant_menu WHERE m_rest_id='" . trim( $request['edres_id']) . "' AND  m_name_type->>'$.name'='".$itemname[0]."'");
                $inc_tax_rate =$tax_rate + $exc_rate;
                $restoffer = DB::SELECT("SELECT  IFNULL(extra_rate_percent,0) AS extra_rate_percent FROM restaurant_master a  WHERE a.id='" . trim( $request['edres_id']) . "' ");
                $pack_rate = $inv_offer_exit[0]->pack_rate;
                $extra_value = ($restoffer[0]->extra_rate_percent * $inc_tax_rate)/100;
				$final_offer_rate = $inc_tax_rate + $extra_value + $pack_rate;
                DB::SELECT("UPDATE `restaurant_offers` SET `offer_details`=json_object('offer_name','".$request['edii_offername']."','item_name','" . $request['editem_name'] . "','offer_rate','".$final_offer_rate."','original_rate','" . $request['edoriginal_rate'] . "','exc_rate','".$exc_rate."','tax_rate','".$tax_rate."','inc_tax_rate','".$inc_tax_rate."','pack_rate','".$pack_rate."','extra_val','".$extra_value."','valid_to','" . date(' Y-m-d H:i:s',strtotime($request['editem_valid_to'])) . "','valid_from','" . date(' Y-m-d H:i:s',strtotime($request['editem_valid_from'])) . "'),`description`='" . $request['ed_descdetail'] . "',`image`='$edimg'  WHERE `rest_id` = '" . $request['edres_id'] . "' and sl_no = '".$request['edslno']."'");
                DB::UPDATE('UPDATE restaurant_menu SET inv_offer_details=JSON_OBJECT("'.trim($itemname[1]).'",JSON_OBJECT("valid_from","'.date(' Y-m-d H:i:s',strtotime($request['editem_valid_from'])).'","valid_to","'.date(' Y-m-d H:i:s',strtotime($request['editem_valid_to'])).'","offer_rate","'.$final_offer_rate.'","offer_slno","'.$request['edslno'].'","exc_rate","'.$exc_rate.'","tax_rate","'.$tax_rate.'","inc_tax_rate","'.$inc_tax_rate.'","pack_rate","'.$pack_rate.'","extra_val","'.$extra_value.'")) WHERE m_rest_id="' . $request['edres_id'] . '" AND  m_name_type->>"$.name"="'.$itemname[0].'" ');
                         
                $msg = 'success';
                return response::json(compact('msg'));
        }
        elseif($type=='I' && $item_type=='P')
       {
             if($request['edp_valid_from']>=$request['edp_valid_to']){
                    $msg = 'pack_invaliddate_range';
                       return response::json(compact('msg'));
             }
            $edimg = Input::file('edp_img');
            $url = Datasource::geturl();
            $timeDate = date("jmYhis") . rand(991, 9999);
            $date = date('Y-m-d');

            if (Input::file('edp_img') != '') {
                $image = Input::file('edp_img');
                $uploadfile = time() . '.' . $image->getClientOriginalExtension();
                Image::make($image)->resize(250, 250)->save(base_path() . '/uploads/offers/res_offers/' . $uploadfile);
                if ($request['editoldimg'] == '') {
                  $edimg = 'uploads/offers/res_offers/' . $uploadfile;
                }
                $edimg = 'uploads/offers/res_offers/' . $uploadfile;
                } elseif($request['editoldimg'] != '')
                {
                  $edimg = $request['editoldimg'];
                }
                else
                {
                  $edimg = '';
                }
                //return "UPDATE `restaurant_offers` SET `offer_details`=json_object('item_name','" . $request['edp_item'] . "','offer_item','" . $request['edp_off_item'] . "','qty','" . $request['edp_qty'] . "','off_qty','" . $request['edp_off_qty'] . "','valid_to','" . date(' Y-m-d H:i:s',strtotime($request['edp_valid_to'])) . "','valid_from','" . date(' Y-m-d H:i:s',strtotime($request['edp_valid_from'])) . "'),`description`='" . $request['ed_descdetail'] . "',`image`='$edimg'  WHERE `rest_id` = '" . $request['edres_id'] . "' and sl_no = '".$request['edslno']."'";
                DB::UPDATE("UPDATE `restaurant_offers` SET `offer_details`=json_object('offer_name','".$request['edip_offername']."','item_name','" . $request['edp_item'] . "','offer_item','" . $request['edp_off_item'] . "','qty','" . $request['edp_qty'] . "','off_qty','" . $request['edp_off_qty'] . "','valid_to','" . date(' Y-m-d H:i:s',strtotime($request['edp_valid_to'])) . "','valid_from','" . date(' Y-m-d H:i:s',strtotime($request['edp_valid_from'])) . "'),`description`='" . $request['ed_descdetail'] . "',`image`='$edimg'  WHERE `rest_id` = '" . $request['edres_id'] . "' and sl_no = '".$request['edslno']."'");
                $msg = 'success';
                return response::json(compact('msg'));
        }
        
    }
    public function remove_rest_offers($rid,$slno){
        $offer_details = DB::SELECT("SELECT type,item_type,offer_details->>'$.item_name' as menuname FROM restaurant_offers WHERE rest_id='".$rid."' AND sl_no='".$slno."' ");
        if($offer_details[0]->type == 'B'){
            DB::UPDATE("UPDATE restaurant_master SET bill_offer_slno=0 WHERE id='".$rid."' ");
        } else if($offer_details[0]->item_type == 'I'){
             $itemname = explode(',', $offer_details[0]->menuname);
             DB::UPDATE("UPDATE restaurant_menu set inv_offer_details=NULL WHERE m_rest_id='".$rid."' AND m_name_type->>'$.name'='".$itemname[0]."' ");
        }
        else if($offer_details[0]->item_type == 'P'){
             $itemname = explode(',', $offer_details[0]->menuname);
             DB::UPDATE("UPDATE restaurant_menu set m_present_offers=NULL WHERE m_rest_id='".$rid."' AND m_name_type->>'$.name'='".$itemname[0]."' ");
        }
        DB::DELETE("DELETE FROM restaurant_offers WHERE rest_id='".$rid."' AND sl_no='".$slno."' ");
        return "removed";
    }
}
