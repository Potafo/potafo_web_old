<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\TaxMaster;
use Helpers\Datasource;
use App\Http\Requests;
use Response;
use DB;
use Image;
use App\GeneralOffer;
use Helpers\Commonsource;
use Session;

class GeneralOfferController extends Controller
{
    public function generaloffer()
    {
		$staffid = Session::get('staffid');
        if(!$staffid){return redirect('');}
        $filterarr = array();
        $details = DB::SELECT('SELECT id,usage_limit,offer_details->>"$.name" as name,offer_details->>"$.amt_abv" as amtabove,offer_details->>"$.max_amt" as max_amount,offer_details->>"$.valid_to" as valid_to,offer_details->>"$.offer_per" as offer_per,offer_details->>"$.valid_from" as valid_from,active,description,coupon_code,image
                        FROM `general_offers` WHERE  `general_offers`.`id` != " " ORDER BY ID');
        return view('general_offers.general_offers',compact('details'));
    }
    
    // Adding of General Offers
    public function add_gen_offers(Request $request)
    {
        $img = Input::file('offer_image');
        $url = Datasource::geturl();
        $timeDate = date("jmYhis") . rand(991, 9999);
        $date = date('Y-m-d');
         if($request['valid_from']>=$request['valid_to']){
             $msg = 'invaliddate_range';
                return response::json(compact('msg'));
        }
        if($img == "")
        {
            $image_url = null;
        }
        else
        {
            $uploadfile = $timeDate . '.' .strtolower($img->getClientOriginalExtension());
            Image::make($img)->resize(225, 225)->save(base_path() . '/uploads/offers/gen_offers/' . $uploadfile);
            $image_url = 'uploads/offers/gen_offers/' . $uploadfile;
        }

        if($request['o_perc']== '')
        {
            $o_perc = '0';
        }
        else
        {
            $o_perc = $request['o_perc'];
        }
        if($request['amt_abv']== '')
        {
            $amt_abv = '0';
        }
        else
        {
            $amt_abv = $request['amt_abv'];
        }
        if($request['max_amt']== '')
        {
            $max_amt = '0';
        }
        else
        {
            $max_amt = $request['max_amt'];
        }

       
        $gen_offer = DB::select("SELECT offer_details->>'$.name' FROM general_offers where offer_details->>'$.name' = '".$request['o_name']."'");

            if(count($gen_offer)>0)
            {
                $msg = 'already exist';
                return response::json(compact('msg'));
            }
            else
            {
               
                DB::INSERT("INSERT INTO `general_offers`(`type`, `offer_details`, `description`,`image`,`coupon_code`,usage_limit)
                   VALUES('B',json_object('name','" . $request['o_name'] . "','offer_per','" . $request['o_perc'] . "','amt_abv','" . $request['amt_abv'] . "','max_amt','" . $request['max_amt'] . "','valid_from','" . $request['valid_from'] . "','valid_to','" . $request['valid_to'] . "'),'" . $request['descdetail'] . "','$image_url','" . $request['code'] . "','".$request['usage_limit']."')");
                $msg = 'success';
                return response::json(compact('msg'));
            }
            return redirect('general_offers');
        
    }
     public function genoffer_status(Request $request)
    {
        $genoffer = GeneralOffer::where('id','=',$request['ids'])
                      ->where('active','=','Y')
                      ->get();
        if(count($genoffer)>0)
        {
            GeneralOffer::where('id', $request['ids'])->update(
                [
                    'active' => 'N'
                ]);
        }
        else
        {
            GeneralOffer::where('id', $request['ids'])->update(
                [
                    'active' => 'Y'
                ]);
        }

    }
    
     //Updation of General Offers
    public function edit_gen_offers(Request $request)
    {
        $id = $request['edid'];
//        return $request['ed_desc'];
        $ed_offer_image = Input::file('ed_offer_image');
        $url = Datasource::geturl();
        $timeDate = date("jmYhis") . rand(991, 9999);
        $date = date('Y-m-d');
        if($request['ed_valid_from']>=$request['ed_valid_to']){
             $msg = 'invaliddate_range';
                return response::json(compact('msg'));
        }
        if (Input::file('ed_offer_image') != '') {
            $image = Input::file('ed_offer_image');
            $uploadfile = time() . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(250, 250)->save(base_path() . '/uploads/offers/gen_offers/' . $uploadfile);
            if ($request['editoldimg'] == '') {
              $ed_offer_image = 'uploads/offers/gen_offers/' . $uploadfile;
                
//                $file_path = base_path() . '/' . $request['oldlogo'];
//                unlink($file_path);
            }
            $ed_offer_image = 'uploads/offers/gen_offers/' . $uploadfile;
        } elseif($request['editoldimg'] != '')
        {
            $ed_offer_image = $request['editoldimg'];
        }
        else
        {
            $ed_offer_image = '';
        }

        if($request['ed_o_perc']== '')
        {
            $ed_o_perc = '0';
        }
        else
        {
            $ed_o_perc = $request['ed_o_perc'];
        }
        if($request['ed_amt_abv']== '')
        {
            $ed_amt_abv = '0';
        }
        else
        {
            $ed_amt_abv = $request['ed_amt_abv'];
        }
        if($request['ed_max_amt']== '')
        {
            $ed_max_amt = '0';
        }
        else
        {
            $ed_max_amt = $request['ed_max_amt'];
        }
       
        $gen_off = DB::select("SELECT offer_details->>'$.name' FROM general_offers where offer_details->>'$.name' = '".$request['ed_o_name']."' and id != '".$id."'");

        if(count($gen_off)>0)
        {
            $msg = 'already exist';
            return response::json(compact('msg'));
        }
        else
        {
            DB::SELECT("UPDATE `general_offers` SET `offer_details`=json_object('name','" . $request['ed_o_name'] . "','amt_abv','" . $request['ed_amt_abv'] . "','max_amt','" . $request['ed_max_amt'] . "','valid_to','" . $request['ed_valid_to'] . "','offer_per','" . $request['ed_o_perc'] . "','valid_from','" . $request['ed_valid_from'] . "'),`coupon_code`='" . $request['ed_code'] . "',`description`='" . $request['ed_descdetail'] . "',`image`='$ed_offer_image',usage_limit='".$request['ed_usage_limit']."'  WHERE `id` = '$id'");
            $msg = 'success';
            return response::json(compact('msg'));
        }

        return redirect('general_offers');

    }
    //Filtering of General Offers
    public function filter_genoffer(Request $request)
    {

        $search = '';
        $flt_status = $request['flt_status'];
        if(isset($flt_status) && $flt_status != '')
        {
            if($search == "")
            {
                $search.= "  active  = '".$flt_status."'";
            }
            else
            {
                $search.= " and active  = '".$flt_status."''";
            }
        }
       
        if($search!="")
        {
            $search="where $search and ";
        }
        else
        {
            $search ="where ";
        }
        $details = DB::SELECT('SELECT id,offer_details->>"$.name" as name,offer_details->>"$.amt_abv" as amtabove,offer_details->>"$.max_amt" as max_amount,offer_details->>"$.valid_to" as valid_to,offer_details->>"$.offer_per" as offer_per,offer_details->>"$.valid_from" as valid_from,active,description,coupon_code,image FROM `general_offers` '.$search.' `general_offers`.`id` != " " ORDER BY id');
        return $details;
        
       
    }
    public function remove_gen_offer($offerid) {
       DB::DELETE("DELETE FROM general_offers WHERE id='".$offerid."' "); 
       return "deleted";
    }
    public function add_coupon_discount($userid,$couponcode,$optn) {
        $result =  Commonsource::apply_coupon_offer($userid,$couponcode,$optn);
        return ['msg'=>$result['msg']];
    }
    public function add_coupon_discount_new(Request $request)
    {
        $userid = $request['userid'];
        $couponcode = $request['couponcode'];
        $optn = $request['option'];
        $result =  Commonsource::apply_coupon_offer($userid,$couponcode,$optn);
        return ['msg'=>$result['msg']];
    }
    
}
