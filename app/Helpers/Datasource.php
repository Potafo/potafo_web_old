<?php

namespace Helpers;
use App\TaxMaster;
use Request;
use Config;
use App\Category;
use App\GeneralSetting;
use App\SubCategory;

class Datasource
{

    public static function geturl()
    {
        $url = "http://potafo.in/pd1/api/";
        return $url;
    }

    public static function getsiteurl()
    {
        $siteurl = "http://potafo.in/pd1/";
        return $siteurl;
    }

    public static function smsurl($to,$msg)
    {
        $siteurl = "http://app.smsbits.in/api/users?id=ODYwNjAyMDEzMw&senderid=POTAFO&to=$to&msg=$msg&port=TA";
        return $siteurl;
    }

    public static function getip()
    {
        $ip = Request::ip();
        return $ip;
    }
    public static function encr_method() {
        $method = 'AES-256-CBC';
        return $method;
    }

    //category undre particular restaurant
    public static function restaurantcategory($id,$status)
    {
       if($status == 'all') {
           $category = Category::where('restaurant_id',trim($id))
               ->select('slno', 'name', 'status')
               ->get();
       }
        else
        {
            $category = Category::where('restaurant_id',trim($id))
                ->where('status', 'Y')
                ->select('slno', 'name', 'status')
                ->get();
        }
        return $category;
    }
    //sub category undfer particular restaurant
    public static function restaurantsubcategory($id,$status)
    {
        if($status == 'all') {
            $subcategory = SubCategory::where('restaurant_id',trim($id))
                ->select('slno', 'name', 'status')
                ->get();
        }
        else{
            $subcategory = SubCategory::where('restaurant_id',trim($id))
                ->where('status', 'Y')
                ->select('slno', 'name', 'status')
                ->get();
        }
        return $subcategory;
    }

    //Tax Details under particular restaurant
    public static function restauranttax($id,$status)
    {
        if($status == 'all') {
            $subcategory = TaxMaster::where('restaurant_id',trim($id))
                ->select('t_slno', 't_name', 't_status','t_value')
                ->get();
        }
        else{
            $subcategory = TaxMaster::where('restaurant_id',trim($id))
                ->where('t_status', 'Y')
                ->select('t_slno', 't_name', 't_status','t_value')
                ->get();
        }
        return $subcategory;
    }

    //Decimal Digit
    public static function generalsettings()
    {
        $general= GeneralSetting::where('id','1')->select('decimal_digit')->first();
        return $general['decimal_digit'];
    }
    
    
    //Decimal Digit
    public static function codcallconfirmlimit()
    {
        $general= GeneralSetting::where('id','1')->select('cod_call_confirm_limit')->first();
        return $general['cod_call_confirm_limit'];
    }
   
}