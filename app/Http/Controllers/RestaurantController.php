<?php

namespace App\Http\Controllers;

use App\GeneralSetting;
use App\RestaurantOffer;
use Helpers\Commonsource;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Image;
use Session;
use App\UserMaster;
use Response;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
use App\Group;
use App\Restaurant_Master;
class RestaurantController extends Controller
{
    //View Manage Restaurant Page
    public function view_restaurant(Request $request)
    {
          $staffid = Session::get('staffid');
		if(!$staffid){return redirect('');}
          $logingroup = Session::get('logingroup');
        $filterarr = array();
       /* $details = DB::SELECT('SELECT name_tagline->>"$.name" as name,mobile->>"$.ind" as code,star_rating->>"$.value" as value,mobile->>"$.mobile" as mob,id,point_of_contact,star_rating,
          popular_display_order,busy,min_cart_value,extra_rate_percent,phone,pure_veg FROM `restaurant_master` WHERE `restaurant_master`.`id` != " " ORDER BY popular_display_order ASC');
       */
        $cat_test='';
        $order_cat = DB::SELECT("SELECT `order_list_cat` FROM `internal_staffs` WHERE `id`='".$staffid."'");
        foreach($order_cat as $valt){
           $newval=$valt->order_list_cat;
           if($newval == "potafo_mart")
                {
               $cat_test=" AND r.category = 'Potafo Mart'";
                }else if($newval == "restaurant")
                {
                    $cat_test=" AND r.category <> 'Potafo Mart'";
                }
           }
          $details = DB::SELECT('SELECT name_tagline->>"$.name" as name,mobile->>"$.ind" as code,star_rating->>"$.value" as value,mobile->>"$.mobile" as mob,r.id,point_of_contact,star_rating,
          popular_display_order,busy,force_close,min_cart_value,extra_rate_percent,phone,pure_veg,login_group as force_login_group,(select login_group from users where users.id = r.busy_by ) as busy_login_group   from restaurant_master r left join users u on r.forceclose_by = u.id where r.`id` != " " and r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = "'.$staffid .'") '. $cat_test .' ORDER BY popular_display_order ASC');
          return view('restaurant.manage_restaurant',compact('details','logingroup'));
    }

    public function view_restaurantdetails(Request $request)
    {
		 $staffid = Session::get('staffid');
          if(!$staffid){return redirect('');}
        $filterarr = array();
        return view('restaurant.restaurant_details');
    }

    // Group auto search
    public function groupautosearch(Request $request)
    {

        $searchterm = $request['searchterm'];
        $groups =Group::where('group_name', 'LIKE', '%'.$searchterm . '%')
            ->get();
        return $groups;
    }
    // Adding of Restaurants and Details
    public function add_restaurant(Request $request)
    {
        $logo = Input::file('logo');
        $banner = Input::file('banner');
        $url = Datasource::geturl();
        $timeDate = date("jmYhis") . rand(991, 9999);
        $date = date('Y-m-d');
        if($logo == "")
        {
            $image_url = null;
        }
        else
        {
            $uploadfile = $timeDate . '.' .strtolower($logo->getClientOriginalExtension());
            Image::make($logo)->resize(260, 200)->save(base_path() . '/uploads/logo/' . $uploadfile);
            $image_url = 'uploads/logo/' . $uploadfile;
        }
        if($banner == "")
        {
            $banner_url = null;
        }
        else
        {
            $uploadbanner = $timeDate . '.' .strtolower($banner->getClientOriginalExtension());
            Image::make($banner)->resize(250, 250)->save(base_path() . '/uploads/restaurant_banner/' . $uploadbanner);
            $banner_url = 'uploads/restaurant_banner/' . $uploadbanner;
        }

        if($request['phone']== '')
        {
            $phone = '0';
        }
        else
        {
            $phone = $request['phone'];
        }
        if($request['del_time']== '')
        {
            $del_time = '0';
        }
        else
        {
            $del_time = $request['del_time'];
        }
        if($request['cart_value']== '')
        {
            $cart_value = '0';
        }
        else
        {
            $cart_value = $request['cart_value'];
        }
        if($request['pre_deltime']== '')
        {
            $pre_deltime = '0';
        }
        else
        {
            $pre_deltime= $request['pre_deltime'];
        }
        if($request['extra_rate']== '')
        {
            $extra_rate = '0';
        }
        else
        {
            $extra_rate= $request['extra_rate'];
        }
        if($request['ind']== '')
        {
            $ind = '+91';
        }
        else
        {
            $ind= $request['ind'];
        }
        if($request['range']== '')
        {
            $range = '0';
        }
        else
        {
            $range= $request['range'];
        }
        if($request['del_charge']== '')
        {
            $del_charge = '0';
        }
        else
        {
            $del_charge= $request['del_charge'];
        }
        if($request['pack_charge']== '')
        {
            $pack_charge = '0';
        }
        else
        {
            $pack_charge= $request['pack_charge'];
        }
        $restaurant = DB::select("SELECT name_tagline->>'$.name' FROM restaurant_master where name_tagline->>'$.name' = '".$request['rname']."'");

        if(count($restaurant)>0)
        {
            $msg = 'already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $dietsave = $request['dietsave'];
            if($dietsave == "false")
            {
                $diet = 'Y';
            }
            else
            {
                $diet = 'N';
            }

            $p_exclusivesave = $request['p_exclusive'];
            if($p_exclusivesave == "true")
            {
                $p_exclusive = 'Y';
            }
            else
            {
                $p_exclusive = 'N';
            }
            $latitude = str_replace(",","", $request['lat']);
            $longitude = str_replace(",","", $request['long']);
            $geocordinates = $latitude.','.$longitude;
            $group = DB::select("select * from restaurant_group where group_name =  '".$request['group']."'");
            $geo_location =  str_replace(array("'","\"","\\","/"),array("","","","_","_"), $request['geo_location']);
            if(count($group)>0)
            {
                DB::INSERT("INSERT INTO `restaurant_master`(`group_id`, `name_tagline`, `pure_veg`,`p_exclusive`,`category`,`address`,`email`,`country`,`mobile`,`phone`,`point_of_contact`,`city`,`delivery_range_unit`,`min_delivery_time`,`min_cart_value`,`min_prepration_time`,`speical_message`,`cuisines`,`license_numbers`,`extra_rate_percent`,`google_location`,`logo`,`banner`,`registeration_date`,`delivery_charge`,`packing_charge`,`expensive_rating`,`geo_cordinates`,star_rating)
                VALUES((select id from restaurant_group where group_name = '".$request['group']."'),json_object('name','" . $request['rname'] . "','tag_line','" . $request['tagline'] . "','description','" . $request['description'] . "'),'$diet','$p_exclusive','" . $request['category'] . "','" . $request['address'] . "','" . $request['email'] . "',json_object('currency','" . $request['currency'] . "','country','" . $request['country'] . "'),json_object('ind','$ind','mobile','" . $request['mobile'] . "'),'$phone','" . $request['ptcontact'] . "','" . $request['city'] . "',json_object('unit','" . $request['unit'] . "','range','$range'),'$del_time','$cart_value','$pre_deltime','" . $request['message'] . "','" . $request['cuisine'] . "','" . $request['lic_cert'] . "','$extra_rate','" . $geo_location . "','$image_url','$banner_url','$date','$del_charge','$pack_charge','" . $request['exp_rating'] . "', '". $geocordinates ."',json_object('count','0','value','4'))");

            }
            else
            {
                DB::INSERT("INSERT INTO `restaurant_group`(`group_name`)VALUES('".$request['group']."')");
                DB::INSERT("INSERT INTO `restaurant_master`(`group_id`, `name_tagline`, `pure_veg`,`p_exclusive`,`category`,`address`,`email`,`country`,`mobile`,`phone`,`point_of_contact`,`city`,`delivery_range_unit`,`min_delivery_time`,`min_cart_value`,`min_prepration_time`,`speical_message`,`cuisines`,`license_numbers`,`extra_rate_percent`,`google_location`,`logo`,`banner`,`registeration_date`,`delivery_charge`,`packing_charge`,`expensive_rating`,`geo_cordinates`,star_rating)
                   VALUES((select id from restaurant_group where group_name = '".$request['group']."'),json_object('name','" . $request['rname'] . "','tag_line','" . $request['tagline'] . "','description','" . $request['description'] . "'),'$diet','$p_exclusive','" . $request['category'] . "','" . $request['address'] . "','" . $request['email'] . "',json_object('currency','" . $request['currency'] . "','country','" . $request['country'] . "'),json_object('ind','$ind','mobile','" . $request['mobile'] . "'),'$phone','" . $request['ptcontact'] . "','" . $request['city'] . "',json_object('unit','" . $request['unit'] . "','range','$range'),'$del_time','$cart_value','$pre_deltime','" . $request['message'] . "','" . $request['cuisine'] . "','" . $request['lic_cert'] . "','$extra_rate','" . $geo_location . "','$image_url','$banner_url','$date','$del_charge','$pack_charge','" . $request['exp_rating'] . "', '". $geocordinates ."',json_object('count','0','value','4'))");
            }
            $resultid='';
            $lastinsertedid=DB::select('SELECT id FROM `restaurant_master` WHERE name_tagline->>"$.name" = "'.$request['rname'].'" and google_location = "'.$geo_location.'" ');
        foreach($lastinsertedid as $items){
           $resultid=$items->id;
        }
            $msg = 'success';
            return response::json(compact('msg','resultid'));
        }
        return redirect('manage_restaurant');

    }

    //Filtering of Manage Restaurant
    public function filter_restaurant(Request $request)
    {
        $staffid = $request['staff_id'];
        $search = '';
        $restaurant_name = $request['restaurant_name'];
        $diet = $request['diet'];
        $phone = $request['phone'];
        $diet = $request['diet'];
        $user = $request['point_contact'];
        if(isset($restaurant_name) && $restaurant_name != '')
        {
            if($search == "")
            {
                $search.="  LOWER(name_tagline->>'$.name')   LIKE '%".strtolower($restaurant_name)."%'";
            }
            else
            {
                $search.=" and  LOWER(name_tagline->>'$.name')   LIKE '%".strtolower($restaurant_name)."%'";
            }
        }
        if(isset($phone) && $phone != '')
        {
            if($search == "")
            {
                $search.="  mobile->>'$.mobile'  LIKE '".$phone."%'";
            }
            else
            {
                $search.=" and  mobile->>'$.mobile'   LIKE '".$phone."%'";
            }
        }
        if(isset($diet) && $diet != '')
        {
            if($search == "")
            {
                $search.= "  pure_veg  = '".$diet."'";
            }
            else
            {
                $search.= " and pure_veg  = '".$diet."''";
            }
        }
        if(isset($user) && $user != '')
        {
            if($search == "")
            {
                $search.= "  point_of_contact  LIKE '%".$user."%'";
            }
            else
            {
                $search.= " and point_of_contact  LIKE '%".$user."%''";
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
         $cat_test='';
        $order_cat = DB::SELECT("SELECT `order_list_cat` FROM `internal_staffs` WHERE `id`='".$staffid."'");
        foreach($order_cat as $valt){
           $newval=$valt->order_list_cat;
           if($newval == "potafo_mart")
                {
               $cat_test=" AND category = 'Potafo Mart'";
                }else if($newval == "restaurant")
                {
                    $cat_test=" AND category <> 'Potafo Mart'";
                }
           }
          $details = DB::SELECT('SELECT popular_display_order,name_tagline->>"$.name" as name,mobile->>"$.ind" as code,star_rating->>"$.value" as value,mobile->>"$.mobile" as mob,point_of_contact,star_rating,busy,restaurant_master.id,
                               min_cart_value,extra_rate_percent,phone,pure_veg,force_close,users.login_group as close_login_group,(select login_group from users where users.id = restaurant_master.busy_by ) as busy_login_group  FROM `restaurant_master` left join `users` on restaurant_master.forceclose_by = users.id '.$search.' `restaurant_master`.`id` != " " and restaurant_master.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = "'.$staffid .'") ' . $cat_test .' ORDER BY id');
        return $details;
    }

    //Edit View of Restaurant
    public function restaurant_edit($id)
    {
		$staffid = Session::get('staffid');
        if(!$staffid){return redirect('');}
        $restaurantdetail = DB::SELECT('SELECT name_tagline->>"$.name" as name,name_tagline->>"$.description" as description,name_tagline->>"$.tag_line" as tagline,address,email,mobile->>"$.ind" as code,mobile->>"$.mobile" as mob,phone,geo_cordinates,point_of_contact,min_delivery_time,min_cart_value,min_prepration_time,speical_message,cuisines,license_numbers,extra_rate_percent,category,google_location,city,country->>"$.country" as country,country->>"$.currency" as currency,delivery_range_unit->>"$.unit" as unit,star_rating->>"$.value" as value,delivery_range_unit->>"$.range" as ranges,restaurant_group.id as id,star_rating,
                       expensive_rating,delivery_charge,packing_charge,status,popular_display_order,pure_veg,p_exclusive,busy,logo,banner,restaurant_master.id as rid,restaurant_group.group_name FROM `restaurant_master` LEFT JOIN restaurant_group ON restaurant_master.group_id = restaurant_group.id WHERE restaurant_master.id = "'.$id.'"');
        $siteurl = Datasource::getsiteurl();
        return view('restaurant.restaurant_edit',compact('restaurantdetail','id','siteurl'));
    }

    //Updation of Restaurant Details
    public function edit_restaurant(Request $request)
    {
        $rid = $request['edrid'];
        $logo = Input::file('logo');
        $banner = Input::file('banner');
        $url = Datasource::geturl();
        $timeDate = date("jmYhis") . rand(991, 9999);
        $date = date('Y-m-d');
        if (Input::file('logo') != '') {
            $image = Input::file('logo');
            $uploadfile = time() . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(260, 200)->save(base_path() . '/uploads/logo/' . $uploadfile);
            if ($request['oldlogo'] == '') {
                $logo = 'uploads/logo/' . $uploadfile;

//                $file_path = base_path() . '/' . $request['oldlogo'];
//                unlink($file_path);
            }
            $logo = 'uploads/logo/' . $uploadfile;
        } elseif($request['oldlogo'] != '')
        {
            $logo = $request['oldlogo'];
        }
        else
        {
            $logo = '';
        }


        if (Input::file('banner') != '') {
            $image = Input::file('banner');
            $uploadfile = time() . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(250, 250)->save(base_path() . '/uploads/restaurant_banner/' . $uploadfile);
            if ($request['oldbanner'] == '') {
                $banner = 'uploads/restaurant_banner/' . $uploadfile;
//                $file_path = base_path() . '/' . $request['oldbanner'];
//                unlink($file_path);
            }
            $banner = 'uploads/restaurant_banner/' . $uploadfile;
        } elseif($request['oldbanner'] != '')
        {
            $banner = $request['oldbanner'];
        }
        else
        {
            $banner = '';
        }


        if($request['edphone']== '')
        {
            $phone = '0';
        }
        else
        {
            $phone = $request['edphone'];
        }
        if($request['eddel_time']== '')
        {
            $del_time = '0';
        }
        else
        {
            $del_time = $request['eddel_time'];
        }
        if($request['edcart_value']== '')
        {
            $cart_value = '0';
        }
        else
        {
            $cart_value = $request['edcart_value'];
        }
        if($request['edpre_deltime']== '')
        {
            $pre_deltime = '0';
        }
        else
        {
            $pre_deltime= $request['edpre_deltime'];
        }
        if($request['edextra_rate']== '')
        {
            $extra_rate = '0';
        }
        else
        {
            $extra_rate= $request['edextra_rate'];
        }
        if($request['edcode']== '')
        {
            $ind = '+91';
        }
        else
        {
            $ind= $request['edcode'];
        }
        if($request['edrange']== '')
        {
            $range = '0';
        }
        else
        {
            $range= $request['edrange'];
        }
        if($request['edorder']== '')
        {
            $order = '0';
        }
        else
        {
            $order= $request['edorder'];
        }
        if($request['eddel_charge']== '')
        {
            $eddel_charge = '0';
        }
        else
        {
            $eddel_charge = $request['eddel_charge'];
        }
        if($request['edpack_charge']== '')
        {
            $edpack_charge = '0';
        }
        else
        {
            $edpack_charge = $request['edpack_charge'];
        }
        $restaurant = DB::select("SELECT name_tagline->>'$.name' FROM restaurant_master where name_tagline->>'$.name' = '".$request['edrname']."' and id != '".$rid."'");
        if(count($restaurant)>0)
        {
            $msg = 'already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $dietsave = $request['eddietsave'];
            if($dietsave == "false")
            {
                $diet = 'Y';
            }
            else
            {
                $diet = 'N';
            }

            $p_exclusivesave = $request['edp_exclusive'];
            if($p_exclusivesave == "true")
            {
                $p_exclusive = 'Y';
            }
            else
            {
                $p_exclusive = 'N';
            }
            $latitude = str_replace(",","", $request['edlat']);
            $longitude = str_replace(",","", $request['edlong']);
            $geocordinates = $latitude.','.$longitude;
            $group = DB::select("select * from restaurant_group where group_name =  '".$request['edgroup']."'");
            $edgeo_location =  str_replace(array("'","\"","\\","/"),array("","","","_","_"), $request['edgeo_location']);
            if(count($group)>0)
            {
//                DB::INSERT("INSERT INTO `restaurant_master`(`group_id`, `name_tagline`, `pure_veg`,`category`,`address`,`email`,`country`,`mobile`,`phone`,`point_of_contact`,`city`,`delivery_range_unit`,`min_delivery_time`,`min_cart_value`,`min_prepration_time`,`speical_message`,`cuisines`,`license_numbers`,`extra_rate_percent`,`google_location`,`logo`,`banner`,`registeration_date`)
//                VALUES((select id from restaurant_group where group_name = '".$request['group']."'),json_object('name','" . $request['rname'] . "','tag_line','" . $request['tagline'] . "','description','" . $request['description'] . "'),'$diet','" . $request['category'] . "','" . $request['address'] . "','" . $request['email'] . "',json_object('currency','" . $request['currency'] . "','country','" . $request['country'] . "'),json_object('ind','$ind','mobile','" . $request['mobile'] . "'),'$phone','" . $request['ptcontact'] . "','" . $request['city'] . "',json_object('unit','" . $request['unit'] . "','range','$range'),'$del_time','$cart_value','$pre_deltime','" . $request['message'] . "','" . $request['cuisine'] . "','" . $request['lic_cert'] . "','$extra_rate','" . $request['geo_location'] . "','$image_url','$banner_url','$date')");
                  DB::SELECT("UPDATE `restaurant_master` SET `group_id`=(select id from restaurant_group where group_name = '".$request['edgroup']."'),`name_tagline`=json_object('name','" . $request['edrname'] . "','tag_line','" . $request['edtagline'] . "','description','" . $request['eddescription'] . "'),`pure_veg`='$diet',`p_exclusive`='$p_exclusive',`category`='" . $request['edcategory'] . "',`address`='" . $request['edaddress'] . "',`email`='" . $request['edemail'] . "',`phone`='$phone',`mobile`=json_object('ind','$ind','mobile','" . $request['edmobile'] . "'),`point_of_contact`='" . $request['edptcontact'] . "',`city`='" . $request['edcity'] . "',`country`=json_object('currency','" . $request['edcurrency'] . "','country','" . $request['edcountry'] . "'),`delivery_range_unit`=json_object('unit','" . $request['edunit'] . "','range','$range'),`min_delivery_time`='$del_time',`min_prepration_time`='$pre_deltime',`speical_message`='" . $request['edmessage'] . "',`min_cart_value`='$cart_value',`cuisines`='" . $request['edcuisine'] . "',`license_numbers`='" . $request['edlic_cert'] . "',`extra_rate_percent`='$extra_rate',`google_location`='" . $edgeo_location . "',`registeration_date`='$date',`logo`='$logo',`banner`='$banner',`busy`='" . $request['busy'] . "',`status`='".$request['edstatus']."',`popular_display_order`='$order',`delivery_charge`='$eddel_charge',`packing_charge`='$edpack_charge',`expensive_rating`='" . $request['edexp_rate'] . "',`geo_cordinates` =  '". $geocordinates ."' WHERE `id` = '$rid'");
            }
            else
            {
                DB::INSERT("INSERT INTO `restaurant_group`(`group_name`)VALUES('".$request['group']."')");
//              DB::INSERT("INSERT INTO `restaurant_master`(`group_id`, `name_tagline`, `pure_veg`,`category`,`address`,`email`,`country`,`mobile`,`phone`,`point_of_contact`,`city`,`delivery_range_unit`,`min_delivery_time`,`min_cart_value`,`min_prepration_time`,`speical_message`,`cuisines`,`license_numbers`,`extra_rate_percent`,`google_location`,`logo`,`banner`,`registeration_date`)
//              VALUES((select id from restaurant_group where group_name = '".$request['group']."'),json_object('name','" . $request['rname'] . "','tag_line','" . $request['tagline'] . "','description','" . $request['description'] . "'),'$diet','" . $request['category'] . "','" . $request['address'] . "','" . $request['email'] . "',json_object('currency','" . $request['currency'] . "','country','" . $request['country'] . "'),json_object('ind','$ind','mobile','" . $request['mobile'] . "'),'$phone','" . $request['ptcontact'] . "','" . $request['city'] . "',json_object('unit','" . $request['unit'] . "','range','$range'),'$del_time','$cart_value','$pre_deltime','" . $request['message'] . "','" . $request['cuisine'] . "','" . $request['lic_cert'] . "','$extra_rate','" . $request['geo_location'] . "','$image_url','$banner_url','$date')");
                DB::SELECT("UPDATE `restaurant_master` SET `group_id`=(select id from restaurant_group where group_name = '".$request['edgroup']."'),`name_tagline`=json_object('name','" . $request['edrname'] . "','tag_line','" . $request['edtagline'] . "','description','" . $request['eddescription'] . "'),`pure_veg`='$diet',`p_exclusive`='$p_exclusive',`category`='" . $request['edcategory'] . "',`address`='" . $request['edaddress'] . "',`email`='" . $request['edemail'] . "',`phone`='$phone',`mobile`=json_object('ind','$ind','mobile','" . $request['edmobile'] . "'),`point_of_contact`='" . $request['edptcontact'] . "',`city`='" . $request['edcity'] . "',`country`=json_object('currency','" . $request['edcurrency'] . "','country','" . $request['edcountry'] . "'),`delivery_range_unit`=json_object('unit','" . $request['edunit'] . "','range','$range'),`min_delivery_time`='$del_time',`min_prepration_time`='$pre_deltime',`speical_message`='" . $request['edmessage'] . "',`min_cart_value`='$cart_value',`cuisines`='" . $request['edcuisine'] . "',`license_numbers`='" . $request['edlic_cert'] . "',`extra_rate_percent`='$extra_rate',`google_location`='" . $edgeo_location . "',`registeration_date`='$date',`logo`='$logo',`banner`='$banner',`busy`='" . $request['busy'] . "',`status`='".$request['edstatus']."',`popular_display_order`='$order',`delivery_charge`='$eddel_charge',`packing_charge`='$edpack_charge',`expensive_rating`='" . $request['edexp_rate'] . "',`geo_cordinates` =  '". $geocordinates ."'  WHERE `id` = '$rid'");
            }
            $msg = 'success';
            return response::json(compact('msg'));
        }

        return redirect('manage_restaurant');

    }

    //Updation and Adding of Open Close Time
    public function openclose_time(Request $request)
    {
        $day= strtoupper($request['day']);
        $from1= $request['from1'];
        $fromsec1= $request['fromsec1'];
        $to1= $request['to1'];
        $tosec1= $request['tosec1'];
        $from =$from1.':'.$fromsec1.':00';
        $to = $to1.':'.$tosec1.':00';
        $edrid= $request['edrid'];
        if($day =='ALL') {
            $dayarr = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];
        }
        else
        {
            $dayarr =[$day];
        }
        for($i=0;$i<count($dayarr);$i++) {
            $days = $dayarr[$i];
            $existdata = DB::select("SELECT rt_from_time,rt_to_time	 FROM restaurant_timings WHERE rt_rest_id='$edrid' and rt_day = '$days' and rt_from_time = '$from' and rt_to_time = '$to'");
            if (count($existdata <= 0)) {
                DB::SELECT('INSERT INTO `restaurant_timings`(`rt_rest_id`, `rt_day`,`rt_from_time`, `rt_to_time`) VALUES("' . $edrid . '","' . $days . '","' . $from . '","' . $to . '")');
                $msg = 'success';
            } else {
                $msg = ' Already Exist';
            }
        }
        return response::json(compact('msg'));
    }

    //View Open Close Time in Restaurant
    public function view_time(Request $request)
    {
        $id = $request['edrid'];
        $selectday = strtoupper($request['day']);
        if($selectday == 'ALL')
        {
            $search = '';
        }
        else
        {
            $search = 'and rt_day="'.$selectday.'"';
        }
        $day = DB::SELECT('SELECT rt_day,rt_from_time,rt_to_time,rt_slno FROM `restaurant_timings` where rt_rest_id = "'.$id.'" '.$search);
        $m=0;
        $append = '';
        $count = count($day);
        foreach($day as $key=>$mnth)
        {
                    $key =$key+1;
                    $fromtiming =  explode(':',$mnth->rt_from_time);
                    $totiming =  explode(':',$mnth->rt_to_time);
                    $append .=  "<tr class='timeappend'>";
                    $append .=  "<td style='width:100px'>". $mnth->rt_day."</td>";
                    $append .=  "<td style='width:90px'>".$mnth->rt_from_time."</td>";
                    $append .=  "<td style='width:90px'>".$mnth->rt_to_time."</td>";
                    $append .=  "<td style='width:40px'><a class='btn button_table' onclick=\"time_delete('$mnth->rt_day','$mnth->rt_slno','$key','$count')\";><i class='fa fa-trash'></i></a></td>";
                    $append .=  "</tr>";
        }
        return $append ;
    }

    //Delete Open Close Time in Restaurant
    public function delete_time(Request $request)
    {
        $id = $request['edrid'];
        $day = $request['day'];
        $slno = $request['slno'];
        $datadel = DB::SELECT("delete from `restaurant_timings` where rt_rest_id = '$id' and rt_slno = '$slno' and rt_day = '$day'");
        return $datadel;
    }

    public function restaurant_status(Request $request)
    {
        $userid = Session::get('setuserid');
        $restaurant = Restaurant_Master::where('id','=',$request['ids'])
            ->where('busy','=','Y')
            ->get();
        if(count($restaurant)>0)
        {
            Restaurant_Master::where('id', $request['ids'])->update(
                [
                    'busy' => 'N','busy_by'=>$userid,
                ]);
        }
        else
        {
            Restaurant_Master::where('id', $request['ids'])->update(
                [
                    'busy' => 'Y','busy_by'=>$userid,
                ]);
        }
    }


    public function restaurantclose_status(Request $request)
    {
        $userid = Session::get('setuserid');
        $restaurant = Restaurant_Master::where('id','=',$request['ids'])
            ->where('force_close','=','Y')
            ->get();
        if(count($restaurant)>0)
        {
            Restaurant_Master::where('id', $request['ids'])->update(
                [
                    'force_close' => 'N','forceclose_by'=>$userid,
                ]);
        }
        else
        {
            Restaurant_Master::where('id', $request['ids'])->update(
                [
                    'force_close' => 'Y','forceclose_by'=>$userid,
                ]);
        }
    }

    //restaurant list of particular location for mobile app
    public function restaurantlists($line1,$category,$term)
    {   
        $typearr = array();
        $restaurantarr = array();
        $restaurants = array();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y m d h:i:s a');
        $time = strtoupper($date->format('h:i a'));
        $day = strtoupper($date->format('l'));
        $status = '';
        if($term != 'null')
        {
            //  $restaurants = DB::SELECT('SELECT rt.id as res_id,p_exclusive,json_length(rt.operational_time->"$.' . $day . '")  as count,rt.operational_time as time,rt.busy,rt.pure_veg FROM `restaurant_master` rt join `restaurant_menu` rm on rt.id = rm.m_rest_id WHERE rt.status = "Y" and json_length(rt.operational_time->"$.' . $day . '")  > 0 and JSON_UNQUOTE(rm.m_name_type->"$.name") = "' . trim($term) . '" ORDER BY JSON_UNQUOTE(rt.name_tagline->"$.name")');
            $restaurant = DB::SELECT('SELECT rt.id as res_id,p_exclusive,json_length(rt.operational_time->"$.' . $day . '")  as count,rt.operational_time as time,rt.busy,rt.force_close,rt.pure_veg,rt.google_location as rest_location,rt.geo_cordinates as cordinates,rt.delivery_range_unit->>"$.range" as range_unit FROM `restaurant_master` rt join `restaurant_menu` rm on rt.id = rm.m_rest_id WHERE rt.status = "Y" and json_length(rt.operational_time->"$.' . $day . '")  > 0 and JSON_UNQUOTE(rm.m_name_type->"$.name") = "' . trim($term) . '" ORDER BY popular_display_order asc');
        }
        else
        {
            /* $restaurants = DB::SELECT('SELECT rt.id as res_id,p_exclusive,json_length(rt.operational_time->"$.' . $day . '")  as count,rt.operational_time as time,rt.busy,rt.pure_veg FROM `restaurant_master` rt  WHERE rt.status = "Y" and json_length(rt.operational_time->"$.' . $day . '")  > 0  ORDER BY JSON_UNQUOTE(rt.name_tagline->"$.name")');*/
            $restaurant = DB::SELECT('SELECT rt.id as res_id,p_exclusive,json_length(rt.operational_time->"$.' . $day . '")  as count,rt.operational_time as time,rt.busy,rt.force_close,rt.pure_veg,rt.google_location as rest_location,rt.geo_cordinates as cordinates,rt.delivery_range_unit->>"$.range" as range_unit FROM `restaurant_master` rt  WHERE rt.status = "Y" and json_length(rt.operational_time->"$.' . $day . '")  > 0 ORDER BY popular_display_order asc');
        }
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
        else
        {
            $restaurants = $restaurant;
        }
        if($category == 'all')
        {
            if (count($restaurants) > 0) {

                foreach($restaurants as $key=>$item)
                {
                    if($item->busy == 'Y')
                    {
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,IFNULL(expensive_rating,0) as expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' ORDER BY popular_display_order asc");
                        if(count($detail)>0)
                        {
                            $restaurantarr[] = $detail[0];
                        }
                    }
                    else
                    {
                        for($i = 1; $i<=$item->count;$i++)
                        {
                            $json_data = json_decode($item->time,true);
                            $open      = strtoupper($json_data[$day]['time'.$i]['open']);
                            $close     = strtoupper($json_data[$day]['time'.$i]['close']);
                            if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close))
                            {
                                $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,IFNULL(expensive_rating,0) as expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Open' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' ORDER BY popular_display_order asc");
                                if(count($detail)>0)
                                {
                                    $restaurantarr[] = $detail[0];
                                }
                                break;
                            }
                            else
                            {
                                $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,IFNULL(expensive_rating,0) as expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' ORDER BY popular_display_order asc");
                                if(count($detail)>0)
                                {
                                    $restaurantarr[] = $detail[0];
                                }
                            }
                        }

                    }
                }
            }
        }
        else if($category=='veg')
        {
            if(isset($restarr) && count($restarr)>0)
            {
                $restaurants = DB::SELECT('SELECT id as res_id,p_exclusive,json_length(operational_time->"$.'.$day.'") as count,operational_time as time,busy,pure_veg FROM `restaurant_master` WHERE status = "Y" and pure_veg = "Y" and json_length(operational_time->"$.'.$day.'") > 0 and id in ('.$restlist.') ORDER BY JSON_UNQUOTE(name_tagline->"$.name")');
            }
            else{
                $restaurants = DB::SELECT('SELECT id as res_id,p_exclusive,json_length(operational_time->"$.'.$day.'") as count,operational_time as time,busy,pure_veg FROM `restaurant_master` WHERE status = "Y" and pure_veg = "Y" and json_length(operational_time->"$.'.$day.'") > 0 ORDER BY JSON_UNQUOTE(name_tagline->"$.name")');
            }
            if (count($restaurants) > 0)
            {
                foreach($restaurants as $key=>$item)
                {
                    if($item->busy == 'Y')
                    {
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and pure_veg = 'Y' ORDER BY popular_display_order asc");
                        if(count($detail)>0)
                        {
                            $restaurantarr[] = $detail[0];
                        }
                    }
                    else
                    {
                        for($i = 1; $i<=$item->count;$i++)
                        {
                            $json_data = json_decode($item->time,true);
                            $open      = strtoupper($json_data[$day]['time'.$i]['open']);
                            $close     = strtoupper($json_data[$day]['time'.$i]['close']);
                            if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close))
                            {
                                $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Open' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and pure_veg = 'Y' ORDER BY popular_display_order asc");
                                if(count($detail)>0)
                                {
                                    $restaurantarr[] = $detail[0];
                                }
                                break;
                            }
                            else
                            {
                                $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and pure_veg = 'Y' ORDER BY popular_display_order asc");
                                if(count($detail)>0)
                                {
                                    $restaurantarr[] = $detail[0];
                                }
                            }
                        }
                    }
                }
            }
        }
        else if($category == 'exclusive')
        {
            if(isset($restarr) && count($restarr)>0)
            {
                $restaurants = DB::SELECT('SELECT id as res_id,json_length(operational_time->"$.' . $day . '") as count,operational_time as time,busy,pure_veg FROM `restaurant_master` WHERE status = "Y" and p_exclusive = "Y" and json_length(operational_time->"$.' . $day . '") > 0 and id in ('.$restlist.') ORDER BY JSON_UNQUOTE(name_tagline->"$.name")');
            }
            else
            {
                $restaurants = DB::SELECT('SELECT id as res_id,json_length(operational_time->"$.'.$day.'") as count,operational_time as time,busy,pure_veg FROM `restaurant_master` WHERE status = "Y" and p_exclusive = "Y" and json_length(operational_time->"$.'.$day.'") > 0 ORDER BY JSON_UNQUOTE(name_tagline->"$.name")');
            }
            if (count($restaurants) > 0)
            {
                foreach($restaurants as $key=>$item)
                {
                    if($item->busy == 'Y')
                    {
                        //return "SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and p_exclusive = 'Y' ORDER BY popular_display_order asc";
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and p_exclusive = 'Y' ORDER BY popular_display_order asc");
                        if(count($detail)>0)
                        {
                            $restaurantarr[] = $detail[0];
                        }
                    }
                    else
                    {
                        for($i = 1; $i<=$item->count;$i++)
                        {
                            $json_data = json_decode($item->time,true);
                            $open      = strtoupper($json_data[$day]['time'.$i]['open']);
                            $close     = strtoupper($json_data[$day]['time'.$i]['close']);
                            if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close))
                            {
                                $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Open' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and p_exclusive = 'Y' ORDER BY popular_display_order asc");
                                if(count($detail)>0)
                                {
                                    $restaurantarr[] = $detail[0];
                                }
                                break;
                            }
                            else
                            {
                                $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and p_exclusive = 'Y' ORDER BY popular_display_order asc");
                                if(count($detail)>0)
                                {
                                    $restaurantarr[] = $detail[0];
                                }
                            }
                        }
                    }
                }
            }
        }
        else if($category=='top_rated')
        {
            if(isset($restarr) && count($restarr)>0) {
                $restaurants = DB::SELECT('SELECT id as res_id,json_length(operational_time->"$.' . $day . '") as count,operational_time as time,busy,pure_veg FROM `restaurant_master` WHERE status = "Y" and JSON_UNQUOTE(star_rating->"$.value") !="" and json_length(operational_time->"$.' . $day . '") > 0 and id in (' . $restlist . ') ORDER BY popular_display_order asc limit 0,10');
            }
            else
            {
                $restaurants = DB::SELECT('SELECT id as res_id,json_length(operational_time->"$.'.$day.'") as count,operational_time as time,busy,pure_veg FROM `restaurant_master` WHERE status = "Y" and JSON_UNQUOTE(star_rating->"$.value") !="" and json_length(operational_time->"$.'.$day.'") > 0 ORDER BY popular_display_order asc limit 0,10');
            }
            if (count($restaurants) > 0) {
                foreach($restaurants as $key=>$item)
                {
                    if($item->busy == 'Y')
                    {
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and JSON_UNQUOTE(star_rating->'$.value') !='' ORDER BY popular_display_order asc");
                        if(count($detail)>0)
                        {
                            $restaurantarr[] = $detail[0];
                        }
                    }
                    else
                    {
                        for($i = 1; $i<=$item->count;$i++)
                        {
                            $json_data = json_decode($item->time,true);
                            $open      = strtoupper($json_data[$day]['time'.$i]['open']);
                            $close     = strtoupper($json_data[$day]['time'.$i]['close']);
                            if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close))
                            {
                                $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Open' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and JSON_UNQUOTE(star_rating->'$.value') !='' ORDER BY popular_display_order asc");
                                if(count($detail)>0)
                                {
                                    $restaurantarr[] = $detail[0];
                                }
                                break;
                            }
                            else
                            {
                                $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and JSON_UNQUOTE(star_rating->'$.value') !='' ORDER BY popular_display_order asc");
                                if(count($detail)>0)
                                {
                                    $restaurantarr[] = $detail[0];
                                }
                            }
                        }
                    }
                }
            }
        }

        if(count($restaurantarr)>0)
        {
            $msg = 'Exist';
            return response::json(['msg' => $msg,'count'=>count($restaurantarr),'restaurants' => $restaurantarr]);
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
//      return response::json(['msg' => $msg,'status' => $status,'restaurants' => $restaurantarr]);
    }

    //restaurant list of particular location for mobile app
    public function restaurantlists_new(Request $request)
    {

        $line1 = trim($request['line1']);
        if(trim($line1) == '')
        {
            return response::json(['msg' => 'Not Exist']);
        }
        $category = trim($request['category']);
        //return $category;
        $term = trim($request['term']);
        $typearr = array();
        $restaurantarr = array();
        $restaurants = array();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y m d h:i:s a');
        $time = strtoupper($date->format('h:i a'));
        $operationtime =  strtoupper($date->format('H:i:s'));
        $day = strtoupper($date->format('l'));
        $status = '';
        if($term != 'null')
        {
          //$restaurants = DB::SELECT('SELECT rt.id as res_id,p_exclusive,json_length(rt.operational_time->"$.' . $day . '")  as count,rt.operational_time as time,rt.busy,rt.pure_veg FROM `restaurant_master` rt join `restaurant_menu` rm on rt.id = rm.m_rest_id WHERE rt.status = "Y" and json_length(rt.operational_time->"$.' . $day . '")  > 0 and JSON_UNQUOTE(rm.m_name_type->"$.name") = "' . trim($term) . '" ORDER BY JSON_UNQUOTE(rt.name_tagline->"$.name")');
            $restaurant = DB::SELECT('SELECT distinct(rt.id) as res_id,p_exclusive,rt.busy,rt.force_close,rt.pure_veg,rt.google_location as rest_location,rt.geo_cordinates as cordinates,rt.delivery_range_unit->>"$.range" as range_unit FROM `restaurant_master` rt join `restaurant_menu` rm on rt.id = rm.m_rest_id  LEFT JOIN restaurant_timings rst on rt.id = rst.rt_rest_id  WHERE rt.status = "Y" and rst.rt_day = "'.$day.'" and JSON_UNQUOTE(rm.m_name_type->"$.name") = "' . trim($term) . '" ORDER BY popular_display_order asc');
        }
        else
        {
         /* $restaurants = DB::SELECT('SELECT rt.id as res_id,p_exclusive,json_length(rt.operational_time->"$.' . $day . '")  as count,rt.operational_time as time,rt.busy,rt.pure_veg FROM `restaurant_master` rt  WHERE rt.status = "Y" and json_length(rt.operational_time->"$.' . $day . '")  > 0  ORDER BY JSON_UNQUOTE(rt.name_tagline->"$.name")');*/
            $restaurant = DB::SELECT('SELECT distinct(rt.id) as res_id,p_exclusive,rt.busy,rt.force_close,rt.pure_veg,rt.google_location as rest_location,rt.geo_cordinates as cordinates,rt.delivery_range_unit->>"$.range" as range_unit FROM `restaurant_master` rt   LEFT JOIN restaurant_timings rst on rt.id = rst.rt_rest_id  WHERE rt.status = "Y"  and rst.rt_day = "'. $day . '" ORDER BY popular_display_order asc');
        }
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
                            return response::json(['msg' => 'Not Exist2']);
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
        else
        {
            $restaurants = $restaurant;
        }

        if($category == 'all')
        {
            if (count($restaurants) > 0) {
                foreach($restaurants as $key=>$item)
                {
                    if($item->force_close == 'Y')
                    {
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,IFNULL(expensive_rating,0) as expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart'  ORDER BY popular_display_order asc");
                        if(count($detail)>0)
                        {
                            $restaurantarr[] = $detail[0];
                        }
                    }
                   else if($item->busy == 'Y')
                    {
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,IFNULL(expensive_rating,0) as expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart'  ORDER BY popular_display_order asc");
                        if(count($detail)>0)
                        {
                            if(!in_array($detail[0],$restaurantarr)) {
                                $restaurantarr[] = $detail[0];
                            }
                        }
                    }

                    else
                    {
                        $restlist = DB::SELECT("select rt_rest_id as rest_id,rt_from_time as from_time,rt_to_time as to_time from restaurant_timings  where rt_rest_id='$item->res_id'  and rt_day = '$day' and time(rt_from_time) <= '$operationtime' and time(rt_to_time) >='$operationtime'");

                        if(count($restlist) != 0) {
                            $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,IFNULL(expensive_rating,0) as expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Open' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart' ORDER BY popular_display_order asc");
                                if(count($detail)>0)
                                {
                                    if(!in_array($detail[0],$restaurantarr)) {
                                        $restaurantarr[] = $detail[0];
                                    }
                                }
                            }
                            else
                            {
                                $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,IFNULL(expensive_rating,0) as expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and category != 'Potafo Mart'  ORDER BY popular_display_order asc");
                                if(count($detail)>0)
                                {
                                    if(!in_array($detail[0],$restaurantarr)) {
                                        $restaurantarr[] = $detail[0];
                                    }
                                }
                            }
                    }
                }
            }
        }
        else if($category=='veg')
        {
            if(isset($restarr) && count($restarr)>0)
            {
                $restaurants = DB::SELECT('SELECT distinct(id) as res_id,p_exclusive,force_close,busy,pure_veg FROM `restaurant_master` LEFT JOIN restaurant_timings rst on restaurant_master.id = rst.rt_rest_id WHERE status = "Y" and pure_veg = "Y" and category != "Potafo Mart" and    id in ('.$restlist.') and rst.rt_day = "'.$day.'" ORDER BY JSON_UNQUOTE(name_tagline->"$.name")');
            }
            else{
                $restaurants = DB::SELECT('SELECT distinct(id) as res_id,p_exclusive,force_close,busy,pure_veg FROM `restaurant_master` LEFT JOIN restaurant_timings rst on restaurant_master.id = rst.rt_rest_id WHERE status = "Y" and pure_veg = "Y"   and category != "Potafo Mart" and rst.rt_day = "'.$day.'"  ORDER BY JSON_UNQUOTE(name_tagline->"$.name")');
            }
            if (count($restaurants) > 0)
            {
                foreach($restaurants as $key=>$item)
                {
                    if($item->force_close == 'Y')
                    {
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart'  and pure_veg = 'Y' ORDER BY popular_display_order asc");
                        if(count($detail)>0)
                        {
                            $restaurantarr[] = $detail[0];
                        }
                    }
                    else if($item->busy == 'Y')
                    {
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart'  and pure_veg = 'Y' ORDER BY popular_display_order asc");
                        if(count($detail)>0)
                        {
                            if(!in_array($detail[0],$restaurantarr)) {

                                $restaurantarr[] = $detail[0];
                            }
                        }
                    }
                     else
                    {
                        $restlist = DB::SELECT("select rt_rest_id as rest_id,rt_from_time as from_time,rt_to_time as to_time from restaurant_timings  where rt_rest_id='$item->res_id'  and rt_day = '$day' and time(rt_from_time) <= '$operationtime' and time(rt_to_time) >='$operationtime'");

                       if(count($restlist) != 0) {
                              $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Open' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart'  and pure_veg = 'Y' ORDER BY popular_display_order asc");
                              if(count($detail)>0)
                              {
                                  if(!in_array($detail[0],$restaurantarr)) {

                                      $restaurantarr[] = $detail[0];}
                               }
                        }
                        else
                        {
                               $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'   and category != 'Potafo Mart' and pure_veg = 'Y' ORDER BY popular_display_order asc");
                               if(count($detail)>0)
                               {
                                   if(!in_array($detail[0],$restaurantarr)) {

                                       $restaurantarr[] = $detail[0];
                                   }
                                }
                        }
                    }
                }
            }
        }
        else if($category == 'exclusive')
        {
            if(isset($restarr) && count($restarr)>0)
            {
                $restaurants = DB::SELECT('SELECT distinct(id) as res_id,force_close,busy,pure_veg FROM `restaurant_master` LEFT JOIN restaurant_timings rst on restaurant_master.id = rst.rt_rest_id  WHERE status = "Y" and p_exclusive = "Y" and  rst.rt_day = "'.$day.'"   and category != "Potafo Mart"   and id in ('.$restlist.') ORDER BY JSON_UNQUOTE(name_tagline->"$.name")');
            }
            else
            {
                $restaurants = DB::SELECT('SELECT distinct(id) as res_id,force_close,busy,pure_veg FROM `restaurant_master` LEFT JOIN restaurant_timings rst on restaurant_master.id = rst.rt_rest_id  WHERE status = "Y" and p_exclusive = "Y" and category != "Potafo Mart" and rst.rt_day = "'.$day.'"  ORDER BY JSON_UNQUOTE(name_tagline->"$.name")');
            }
            if (count($restaurants) > 0)
            {
                foreach($restaurants as $key=>$item)
                {
                    if($item->force_close == 'Y')
                    {
                        //return "SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and p_exclusive = 'Y' ORDER BY popular_display_order asc";
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart'   and p_exclusive = 'Y' ORDER BY popular_display_order asc");
                        if(count($detail)>0)
                        {

                            if(!in_array($detail[0],$restaurantarr)) {
                                $restaurantarr[] = $detail[0];
                            }
                        }
                    }
                   else if($item->busy == 'Y') {
                        //return "SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and p_exclusive = 'Y' ORDER BY popular_display_order asc";
                        $detail = DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '" . $item->res_id . "'  and category != 'Potafo Mart'   and p_exclusive = 'Y' ORDER BY popular_display_order asc");
                        if (count($detail) > 0) {
                            if (!in_array($detail[0], $restaurantarr)) {

                                $restaurantarr[] = $detail[0];
                            }
                        }
                    }
                    else
                    {
                        $restlist = DB::SELECT("select rt_rest_id as rest_id,rt_from_time as from_time,rt_to_time as to_time from restaurant_timings  where rt_rest_id='$item->res_id'  and rt_day = '$day' and time(rt_from_time) <= '$operationtime' and time(rt_to_time) >='$operationtime'");
                        if(count($restlist) != 0)
                        {
                            $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Open' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart'   and p_exclusive = 'Y' ORDER BY popular_display_order asc");
                            if(count($detail)>0)
                            {
                                if(!in_array($detail[0],$restaurantarr)) {

                                    $restaurantarr[] = $detail[0];
                                }
                            }
                        }
                        else
                        {
                            $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart'   and p_exclusive = 'Y' ORDER BY popular_display_order asc");
                            if(count($detail)>0)
                            {
                                if(!in_array($detail[0],$restaurantarr)) {

                                    $restaurantarr[] = $detail[0];
                                }
                            }
                        }

                    }
                }
            }
        }
        else if($category=='top_rated')
        {
            if(isset($restarr) && count($restarr)>0) {
                $restaurants = DB::SELECT('SELECT distinct(id) as res_id,force_close,busy,pure_veg FROM `restaurant_master`  LEFT JOIN restaurant_timings rst on restaurant_master.id = rst.rt_rest_id WHERE status = "Y" and JSON_UNQUOTE(star_rating->"$.value") !="" and category != "Potafo Mart" and rst.rt_day = "'.$day.'" and id in (' . $restlist . ') ORDER BY star_rating->>"$.value" desc, popular_display_order asc limit 0,10');
            }
            else
            {
                $restaurants = DB::SELECT('SELECT distinct(id) as res_id,force_close,busy,pure_veg FROM `restaurant_master`  LEFT JOIN restaurant_timings rst on restaurant_master.id = rst.rt_rest_id WHERE status = "Y" and JSON_UNQUOTE(star_rating->"$.value") !="" and category != "Potafo Mart" and rst.rt_day = "'.$day.'" ORDER BY star_rating->>"$.value" desc, popular_display_order asc limit 0,10');
            }
            if (count($restaurants) > 0) {
                foreach($restaurants as $key=>$item)
                {
                 if($item->force_close == 'Y')
                {
                    $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart'  and JSON_UNQUOTE(star_rating->'$.value') !='' ORDER BY star_rating->>'$.value' desc, popular_display_order asc");
                    if(count($detail)>0)
                    {
                        if(!in_array($detail[0],$restaurantarr)) {

                            $restaurantarr[] = $detail[0];
                        }
                    }
                }
                   else if($item->busy == 'Y')
                    {
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart'  and JSON_UNQUOTE(star_rating->'$.value') !='' ORDER BY star_rating->>'$.value' desc, popular_display_order asc");
                        if(count($detail)>0)
                        {
                            if(!in_array($detail[0],$restaurantarr)) {

                                $restaurantarr[] = $detail[0];
                            }
                        }
                    }

                    else
                    {
                        $restlist = DB::SELECT("select rt_rest_id as rest_id,rt_from_time as from_time,rt_to_time as to_time from restaurant_timings  where rt_rest_id='$item->res_id'  and rt_day = '$day' and time(rt_from_time) <= '$operationtime' and time(rt_to_time) >='$operationtime'");
                        if(count($restlist) != 0)
                        {
                            $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Open' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'  and category != 'Potafo Mart'   and JSON_UNQUOTE(star_rating->'$.value') !='' ORDER BY star_rating->>'$.value' desc, popular_display_order asc");
                            if(count($detail)>0)
                            {
                                if(!in_array($detail[0],$restaurantarr)) {

                                    $restaurantarr[] = $detail[0];
                                }
                            }
                        }
                        else
                        {
                            $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."'   and category != 'Potafo Mart'  and JSON_UNQUOTE(star_rating->'$.value') !='' ORDER BY star_rating->>'$.value' desc, popular_display_order asc");
                            if(count($detail)>0)
                            {
                                if(!in_array($detail[0],$restaurantarr)) {

                                    $restaurantarr[] = $detail[0];
                                }
                            }
                        }

                    }
                }
            }
        }
        // for potafo mart
        else if($category=='potafo_mart')
        {
            if(isset($restarr) && count($restarr)>0)
            {
                $restaurants = DB::SELECT('SELECT distinct(id) as res_id,p_exclusive,force_close,busy,pure_veg FROM `restaurant_master` LEFT JOIN restaurant_timings rst on restaurant_master.id = rst.rt_rest_id WHERE status = "Y" and category = "Potafo Mart" and id in ('.$restlist.') and rst.rt_day = "'.$day.'" ORDER BY popular_display_order asc, JSON_UNQUOTE(name_tagline->"$.name")');
            }
            else{
                $restaurants = DB::SELECT('SELECT distinct(id) as res_id,p_exclusive,force_close,busy,pure_veg FROM `restaurant_master` LEFT JOIN restaurant_timings rst on restaurant_master.id = rst.rt_rest_id WHERE status = "Y" and category = "Potafo Mart" and rst.rt_day = "'.$day.'"  ORDER BY popular_display_order asc, JSON_UNQUOTE(name_tagline->"$.name")');
            }
            if (count($restaurants) > 0)
            {
                foreach($restaurants as $key=>$item)
                {
                    if($item->force_close == 'Y')
                    {
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,0 as min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and category = 'Potafo Mart' ORDER BY popular_display_order asc");
                        if(count($detail)>0)
                        {
                            $restaurantarr[] = $detail[0];
                        }
                    }
                    else if($item->busy == 'Y')
                    {
                        $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,0 as min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and category = 'Potafo Mart' ORDER BY popular_display_order asc");
                        if(count($detail)>0)
                        {
                            if(!in_array($detail[0],$restaurantarr)) {

                                $restaurantarr[] = $detail[0];
                            }
                        }
                    }
                     else
                    {
                        $restlist = DB::SELECT("select rt_rest_id as rest_id,rt_from_time as from_time,rt_to_time as to_time from restaurant_timings  where rt_rest_id='$item->res_id'  and rt_day = '$day' and time(rt_from_time) <= '$operationtime' and time(rt_to_time) >='$operationtime'");

                       if(count($restlist) != 0) {
                              $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,0 as min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Open' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and category = 'Potafo Mart' ORDER BY popular_display_order asc");
                              if(count($detail)>0)
                              {
                                  if(!in_array($detail[0],$restaurantarr)) {

                                      $restaurantarr[] = $detail[0];}
                               }
                        }
                        else
                        {
                               $detail =  DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,0 as min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Closed' as status  FROM `restaurant_master` WHERE  id = '".$item->res_id."' and category = 'Potafo Mart' ORDER BY popular_display_order asc");
                               if(count($detail)>0)
                               {
                                   if(!in_array($detail[0],$restaurantarr)) {

                                       $restaurantarr[] = $detail[0];
                                   }
                                }
                        }
                    }
                }
            }
        } 




        if(count($restaurantarr)>0)
        {
            $msg = 'Exist';
            return response::json(['msg' => $msg,'count'=>count($restaurantarr),'restaurants' => $restaurantarr]);
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }


//      return response::json(['msg' => $msg,'status' => $status,'restaurants' => $restaurantarr]);
    }


    //most popular restaurants are listed
    public function mostpopular_restaurants()
    {
        $restarr = array();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y m d h:i:s a');
        $time = strtoupper($date->format('h:i a'));
        $day = strtoupper($date->format('l'));
        $restaurants = DB::SELECT("SELECT id as res_id,JSON_UNQUOTE(name_tagline->'$.name') as name,logo,JSON_UNQUOTE(star_rating->'$.value') as rating FROM `restaurant_master` WHERE id!= '' and status = 'Y' and logo != '' ORDER BY popular_display_order asc limit 0,8");
        if(count($restaurants)>0)
        {
            foreach($restaurants as $item)
            {
                $arr = array();
                $id = $item->res_id ;
                $detail =DB::SELECT('SELECT min_delivery_time,min_prepration_time,rt_from_time,rt_to_time,pure_veg,busy,IFNULL(JSON_UNQUOTE(star_rating->"$.count"),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->"$.value"),0) as star_value,JSON_UNQUOTE(name_tagline->"$.tag_line") as tag_line,JSON_UNQUOTE(name_tagline->"$.name") as name from restaurant_master join restaurant_timings on restaurant_master.id = restaurant_timings.rt_rest_id   where rt_rest_id="'.$id.'" and rt_day ="'.$day.'"');
             // $detail = DB::SELECT("SELECT min_delivery_time,min_prepration_time,pure_veg,busy,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_value,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,JSON_UNQUOTE(name_tagline->'$.name') as name FROM `restaurant_master` WHERE  id = '".$id."'");
                if(count( $detail)>0)
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
                                if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close))
                                {
                                    $status = 'Open';
                                    break;
                                }
                                else
                                {
                                    $status = 'Closed';
                                }
                            }
                        }

                    }
                    $arr['res_id'] = $id;
                    $arr['name']   = $item->name;
                    $arr['logo']   = $item->logo;
                    $arr['rating']   = $item->rating;
                    $arr['status'] = $status;
                }
                $restarr[] = $arr;
            }
            if(count($restarr)>0)
            {
                $msg = 'Exist';
                return response::json(['msg' => $msg,'restaurants' => $restarr]);
            }
            else
            {
                $msg = 'Not Exist';
                return response::json(['msg' => $msg]);
            }
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }



    //restaurant offers
    public function restaurant_offers(Request $request)
    {
        $details =  DB::SELECT("SELECT `image` as image from `restaurant_offers` where `active` = 'Y'");
        if(count($details)>0)
        {
            $msg = 'Exist';
            return response::json(['msg' => $msg,'offers' => $details]);
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }

    }

    //About restaurants(Restaurant Detaisl)
    public function about_restaurants($id)
    {
        $timearr= array();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $array = array();
        $datetime = $date->format('Y m d h:i:s a');
        $time = strtoupper($date->format('h:i a'));
        $day = strtoupper($date->format('l'));
        $details = Restaurant_Master::where('id',$id)->select("id","name_tagline->name as name","address","star_rating->count as review_count","star_rating->value as star_vaue","delivery_charge")->first();
        if(count($details)>0)
        {
            $restaurantdetail = DB::SELECT('SELECT rt_from_time,rt_to_time from restaurant_timings where rt_rest_id="'.$id.'" order by rt_slno asc limit 1');

            $timearr['time1']=(object)["open"=>date('h:i A',strtotime($restaurantdetail[0]->rt_from_time)),"close"=>date('h:i A',strtotime($restaurantdetail[0]->rt_to_time))];
            $id=$details->id;
            $name=strtoupper($details->name);
            $address=$details->address;
            $charge=$details->delivery_charge;
            $time=$details->time;
            if($details->star_vaue == '')
            {
                $star_vaue = '0';
            }
            else
            {
                $star_vaue = $details->star_vaue;
            }
            if($details->review_count == '')
            {
                $review_count = '0';
            }
            else
            {
                $review_count = $details->review_count;
            }
            $array['id'] = $id;
            $array['name'] = json_decode($name);
            $array['address'] = $address;
            $array['delivery_charge'] = $charge;
            $array['star_rate'] = json_decode($star_vaue);
            $array['review_count'] = json_decode($review_count);
            $array['time'] = $timearr;
            $msg = 'Exist';
            return response::json(['msg' => $msg,'details' => $array]);
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }


    }
    //Review of restaurants(Restaurant Detaisl)
    public function review_restaurants($id)
    {
        //$review = DB::SELECT("SELECT a.id,a.rest_id as res_id,UPPER(JSON_UNQUOTE(name_tagline->'$.name')) as restaurant,JSON_UNQUOTE(entry_by->'$.name') as name,IFNULL(JSON_UNQUOTE(review->'$.star'),0) as star,IFNULL(JSON_UNQUOTE(review->'$.text'),0) as text,DATE_FORMAT(entry_date,'%d %b %Y') as date FROM `restaurant_reviews` a LEFT join restaurant_master b on b.id=a.rest_id  WHERE a.id!= '' and a.status = 'Y' and a.rest_id ='$id' ORDER BY a.entry_date desc limit 0,8");
        $review = DB::SELECT("SELECT rest_id,rest_details->>'$.name' as restaurant,customer_details->>'$.name' as name,review_star as star,review_details->>'$.review' as text,review_details->>'$.date' as date FROM order_master WHERE review_details->>'$.status' = 'Y' and rest_id ='$id' ORDER BY review_details->>'$.date' desc");
        if(count($review)>0)
        {
            $msg = 'Exist';
            return response::json(['msg' => $msg,'review' => $review]);
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }

    }

//details of particular restaurant
    public function restaurant_web($id)
    {
        $timearr = array();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $detailarray = array();
        $datetime = $date->format('Y m d h:i:s a');
        $time = strtoupper($date->format('h:i a'));
        $day = strtoupper($date->format('l'));
        $detail =  DB::SELECT("SELECT id as res_id,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,address,delivery_charge,pure_veg,cuisines,min_delivery_time,min_prepration_time,JSON_UNQUOTE(star_rating->'$.count') as review_count,
                   JSON_UNQUOTE(star_rating->'$.value') as star_value,logo FROM `restaurant_master` WHERE  id = '".$id."'");
        foreach($detail as $key=>$item)
        {
            $timedetail =DB::SELECT("SELECT rt_from_time,rt_to_time from restaurant_timings where rt_rest_id = '".$id."' order by rt_slno asc limit 1");
            $detailarray['res_id']   =   $item->res_id;
            $detailarray['name']     =   $item->name;
            $detailarray['tag_line'] =   $item->tag_line;
            $detailarray['address']  =   $item->address;
            $detailarray['delivery_charge']  =   $item->delivery_charge;
            $detailarray['pure_veg']  =   $item->pure_veg;
            $detailarray['cuisines']  =   $item->cuisines;
            $detailarray['min_delivery_time']  =   $item->min_delivery_time;
            $detailarray['min_prepration_time']  =   $item->min_prepration_time;
            $detailarray['review_count']  =   $item->review_count;
            $detailarray['star_value']  =   $item->star_value;
            $detailarray['logo']  =   $item->logo;
                $str_time  =    date("h:i A",strtotime($timedetail[0]->rt_from_time)).' - '.   date('h:i A',strtotime($timedetail[0]->rt_to_time));
                $timearr[] =    $str_time;
            $detailarray['time']  = implode (" , ", $timearr);
        }
        if(count($detailarray)>0)
        {
            $msg = "Exist";
            return response::json(['msg' => $msg,'details' => $detailarray]);

        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }

    //restaurant category
    public function restaurant_category($id)
    {
        $categoryarr =array();
        $category = DB::SELECT("select name,image_view  from category where restaurant_id = '".$id."' and status = 'Y' order by order_no asc");
        if(count($category)>0)
        {
            $msg = "Exist";
            foreach($category as $ky=>$val)
            {
                $cat = $val->name;
                $image_view = $val->image_view;
                $menu = DB::SELECT("SELECT m_menu_id as res_id,JSON_UNQUOTE(m_name_type->'$.name') as menu,m_most_selling as most_selling,JSON_UNQUOTE(m_name_type->'$.type') as type,m_por_rate as portion,m_subcategory,m_description,m_tax,m_image,m_diet,JSON_UNQUOTE(m_time->'$.from') as open,JSON_UNQUOTE(m_time->'$.to') as close,m_offer_exists,JSON_UNQUOTE(m_present_offers->'$.type') as offer_type,JSON_UNQUOTE(m_present_offers->'$.offer_rate') as offer_rate,JSON_UNQUOTE(m_present_offers->'$.desc') as description FROM `restaurant_menu` WHERE  m_rest_id = '" . $id . "' and JSON_CONTAINS(m_category, '[\"" . $cat . "\"]')");
                if(count($menu) > 0 )
                {
                    $categoryarr = $category;
                }
            }
        }
        else
        {
            $msg = 'Not Exist';
        }
        return response::json(['msg' => $msg,'category' => $categoryarr]);
    }
    public function search_restaurent_menu($value)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y m d h:i:s a');
        $time = strtoupper($date->format('h:i a'));
        $day = strtoupper($date->format('l'));
        $restaurentarr= array();
        $resttarr= array();
        $menu_list= array();
        $timeformat = strtoupper($date->format('h:i:s'));
        $restaurent = DB::SELECT("SELECT DISTINCT(name_tagline->>'$.name') as name,id,operational_time as time,json_length(operational_time->'$." . $day . "')  as count,busy FROM restaurant_master WHERE status='Y' AND upper(name_tagline->>'$.name') LIKE UPPER('".$value."%') order by name");
        $menu_list = DB::SELECT("SELECT DISTINCT(m_name_type->>'$.name') as menu FROM restaurant_menu WHERE m_status='Y' AND upper(m_name_type->>'$.name') LIKE UPPER('".$value."%') and '".$timeformat."' >= m_time->>'$.from' and '".$timeformat."' <= m_time->>'$.to' group by menu");
        foreach($restaurent as $item)
        {
            $restaurentarr['name'] = $item->name;
            $restaurentarr['id']   = $item->id;
            $count                 = $item->count;

            if($item->busy == 'Y')
            {
                $status = 'Busy';
            }
            else
            {
                for($i = 1; $i<=$count;$i++)
                {
                    $json_data = json_decode( $item->time,true);
                    $open      = strtoupper($json_data[$day]['time'.$i]['open']);
                    $close     = strtoupper($json_data[$day]['time'.$i]['close']);
                    if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close))
                    {
                        $status = 'Open';
                        break;
                    }
                    else
                    {
                        $status = 'Closed';
                    }
                }
            }
            $restaurentarr['status']   =$status;
            $resttarr[] =$restaurentarr;
        }
        return ['restaurant'=>$resttarr,'menu_list'=>$menu_list,'restaurant_count'=>count($resttarr)];
    }
    public function search_restaurent_menu_new(Request $request)
    {
        $value = $request['term'];
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y m d h:i:s a');
        $time = strtoupper($date->format('h:i a'));
        $day = strtoupper($date->format('l'));
        $restaurentarr= array();
        $resttarr= array();
        $menu_list= array();
        $restaurent = DB::SELECT("SELECT DISTINCT(name_tagline->>'$.name') as name,id,operational_time as time,json_length(operational_time->'$." . $day . "')  as count,busy FROM restaurant_master WHERE status='Y' AND upper(name_tagline->>'$.name') LIKE  UPPER('%".$value."%') order by name");
        $menu_list = DB::SELECT("SELECT DISTINCT(m_name_type->>'$.name') as menu FROM restaurant_menu WHERE m_status='Y' AND upper(m_name_type->>'$.name') LIKE UPPER('".$value."%') group by menu");
        foreach($restaurent as $item)
        {
            $restaurentarr['name'] = $item->name;
            $restaurentarr['id']   = $item->id;
            $count                 = $item->count;

            if($item->busy == 'Y')
            {
                $status = 'Busy';
            }
            else
            {
                for($i = 1; $i<=$count;$i++)
                {
                    $json_data = json_decode( $item->time,true);
                    $open      = strtoupper($json_data[$day]['time'.$i]['open']);
                    $close     = strtoupper($json_data[$day]['time'.$i]['close']);
                    if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close))
                    {
                        $status = 'Open';
                        break;
                    }
                    else
                    {
                        $status = 'Closed';
                    }
                }
            }
            $restaurentarr['status']   =$status;
            $resttarr[] =$restaurentarr;
        }
        return ['restaurant'=>$resttarr,'menu_list'=>$menu_list,'restaurant_count'=>count($resttarr)];
    }

    public function search_restaurent_menu_new_location(Request $request)
    {
        $restaurent =array();
        $value = $request['term'];
        $location = $request['location'];
         if($location=='' || $location == 'NULL' || $location=='null') {
            $location = 'lat_11.2528194$75.7710131';
         }
        $cordinates = explode('_', trim($location));
        $location = $cordinates[1];
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y m d h:i:s a');
        $time = strtoupper($date->format('h:i a'));
        $day = strtoupper($date->format('l'));
        $times = strtoupper($date->format('H:i:s'));
        $days = strtoupper($date->format('D'));
        $restaurentarr= array();
        $resttarr= array();
        $menu_list= array();
        $restaurents = DB::SELECT("SELECT DISTINCT(name_tagline->>'$.name') as name,id as res_id,id,busy,google_location as rest_location,geo_cordinates as cordinates,delivery_range_unit->>'$.range' as range_unit FROM restaurant_master WHERE status='Y' AND upper(name_tagline->>'$.name') LIKE UPPER('%".$value."%') and category != 'Potafo Mart'  order by name");
        if(isset($location) && $location!= 'null')
        {
            if (strpos($location, '$') !== false)
            {
                $cordinates = explode('$', $location);
                $restaurantlist = Commonsource::locaterestaurant($restaurents, $cordinates);
                if (count($restaurantlist) > 0)
                {
                    $restaurent = $restaurantlist[0];
                    $restarr = $restaurantlist[1];
                    $restlist = implode(',', $restaurantlist[1]);
                }
            }
            else
            {
                return response::json(['msg' => 'Latitude/Longitude should be separated by `$`']);
            }
        }
        else
        {
            return response::json(['msg' => 'Latitude/Longitude should be given']);
        }
        $restaurant = DB::SELECT("SELECT DISTINCT(name_tagline->>'$.name') as name,id as res_id,id,operational_time as time,json_length(operational_time->'$." . $day . "')  as count,busy,google_location as rest_location,geo_cordinates as cordinates,delivery_range_unit->>'$.range' as range_unit FROM restaurant_master WHERE status='Y' and category != 'Potafo Mart'  order by name");
        if(isset($location) && $location!= 'null')
        {
            if (strpos($location, '$') !== false)
            {
                $cordnt = explode('$', $location);
                $restaurantlists = Commonsource::locaterestaurant($restaurant, $cordnt);
                if (count($restaurantlists) > 0)
                {
                    $restlists = implode(',', $restaurantlists[1]);
                }
            }
        }
        $menu_list = DB::SELECT("SELECT DISTINCT(m_name_type->>'$.name') as menu FROM restaurant_menu left Join restaurant_master on restaurant_menu.m_rest_id = restaurant_master.id  WHERE m_status='Y' AND JSON_SEARCH(UPPER(m_days), 'one','".$days."') is not null  and upper(m_name_type->>'$.name') LIKE UPPER('".$value."%') and  restaurant_master.id in (".$restlists.") and '$times' >= m_time->>'$.from' AND '$times' <= m_time->>'$.to' and restaurant_master.category != 'Potafo Mart'  group by menu");
//      $menu_list = DB::SELECT("SELECT DISTINCT(m_name_type->>'$.name') as menu FROM restaurant_menu left Join restaurant_master on restaurant_menu.m_rest_id = restaurant_master.id  WHERE m_status='Y' AND JSON_SEARCH(UPPER(m_days), 'one','".$days."') is not null  and upper(m_name_type->>'$.name') LIKE UPPER('".$value."%') and '$times' >= m_time->>'$.from' AND '$times' <= m_time->>'$.to' and  restaurant_master.id in (".$restlist.") group by menu");
        foreach($restaurent as $item)
        {
            $timedetail =DB::SELECT("SELECT rt_from_time,rt_to_time from restaurant_timings where rt_rest_id = '".$item->id."' order by rt_slno asc");
            $restaurentarr['name'] = $item->name;
            $restaurentarr['id']   = $item->id;

            if($item->busy == 'Y')
            {
                $status = 'Busy';
            }
            else
            {
                for($i = 0; $i<count($timedetail);$i++)
                {
                    $open      = strtoupper($timedetail[$i]->rt_from_time);
                    $close     = strtoupper($timedetail[$i]->rt_to_time);
                    if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close))
                    {
                        $status = 'Open';
                        break;
                    }
                    else
                    {
                        $status = 'Closed';
                    }
                }
            }
            $restaurentarr['status']   =$status;
            $resttarr[] =$restaurentarr;
        }
        return ['restaurant'=>$resttarr,'menu_list'=>$menu_list,'restaurant_count'=>count($resttarr)];
    }

    public function tax_apply_to_all_menu(Request $request)
    {
        $rest_id = $request['id'];
        $edextra_rate = $request['edextra_rate'];



        $sel_all_category  = DB::SELECT("SELECT m_rest_id,m_tax,m_menu_id,json_length(m_por_rate) as len,m_menu_id  FROM `restaurant_menu` WHERE m_rest_id='".$rest_id."'");

        $extra_rate_percent  = DB::UPDATE("UPDATE `restaurant_master` SET `extra_rate_percent`='$edextra_rate' WHERE `id`='$rest_id'");
        foreach ($sel_all_category as $value) {
            $len       = $value->len;
            $m_menu_id = $value->m_menu_id;
            $res_id    = $rest_id;
            $i=0;
            for($k = 0; $k<$len;$k++)
            {
                $i++;

                $update   = DB::UPDATE("UPDATE `restaurant_menu` SET `m_por_rate` = JSON_SET(`m_por_rate`,'$.portion$i.extra_percent',$edextra_rate) WHERE `m_rest_id`='$res_id' ");
                $update   = DB::UPDATE("UPDATE `restaurant_menu` SET `m_por_rate` = JSON_SET(`m_por_rate`,'$.portion$i.extra_val',(`m_por_rate`->>'$.portion$i.exc_rate' * `m_por_rate`->>'$.portion$i.extra_percent') / 100) WHERE `m_rest_id`='$res_id' AND `m_menu_id`='$m_menu_id'");

                $update3  = DB::UPDATE("UPDATE `restaurant_menu` SET `m_por_rate` = JSON_SET(`m_por_rate`,'$.portion$i.final_rate',(`m_por_rate`->>'$.portion$i.inc_rate' + `m_por_rate`->>'$.portion$i.extra_val')) WHERE `m_rest_id`='$res_id' AND `m_menu_id`='$m_menu_id'");
            }


        }

        return 'succ';
    }

    //update favourites of restaurant
    public function favourite_update($userid,$restid,$status)
    {
        $msg = "";
        $restarr = array();
        $detail = DB::SELECT('select count(*) as count,favourites from customer_list where  id="' . $userid . '"');
        if (count($detail) > 0) {
            $fav = json_decode($detail[0]->favourites, true);
            if(strtoupper($status) == 'U')
            {
                if (count($fav) <= 0)
                {
                    $fav[] = $restid;
                    $msg = 'updated';
                }
                else
                {
                    if (in_array($restid, $fav))
                    {
                        unset($fav[array_search($restid, $fav)]);
                    }
                    else
                    {
                        array_push($fav, $restid);
                    }
                }

                DB::SELECT('update customer_list set favourites = \'' . json_encode($fav) . '\' where id="' . $userid . '"');
                $msg = 'updated';
                return response::json(['msg' => $msg]);
            }
            else if(strtoupper($status) == 'S')
            {
                //$status = '';
                $msg = 'Exist';
                if(count($fav)>0)
                {
                    if (in_array($restid, $fav))
                    {
                        $status = 'Y';
                    } else
                    {
                        $status = 'N';
                    }
                }
                else
                {
                    $status = 'N';
                }
                return response::json(['msg' => $msg,'status' => $status]);

            }
        }
        else
        {
            $msg = "User Doesn't Exist";
            return response::json(['msg' => $msg]);
        }
    }

    public function fav_list($uesrid)
    {
        $detail = DB::SELECT('select count(*) as count,favourites from customer_list where  id="' . $uesrid . '"');
        if (count($detail) > 0)
        {
            if(isset($detail[0]->favourites)) {
                $fav = json_decode($detail[0]->favourites, true);
                $fav = array_filter($fav, 'strlen');
                if (count($fav) > 0) {
                    $count = count($fav);
                    for ($i = 0; $i < count($fav); $i++) {
                        if ($fav[$i] != '') {
                            $detail = DB::SELECT("SELECT id as res_id,p_exclusive,JSON_UNQUOTE(name_tagline->'$.name') as name,JSON_UNQUOTE(name_tagline->'$.tag_line') as tag_line,pure_veg,category,cuisines,min_delivery_time,min_prepration_time,speical_message,expensive_rating,JSON_UNQUOTE(delivery_range_unit->'$.range')  as delivery_range,JSON_UNQUOTE(delivery_range_unit->'$.unit')  as delivery_unit,IFNULL(JSON_UNQUOTE(star_rating->'$.count'),0) as review_count,IFNULL(JSON_UNQUOTE(star_rating->'$.value'),0) as star_vaue,logo,'Busy' as status  FROM `restaurant_master` WHERE  id = '" . $fav[$i] . "'  ORDER BY popular_display_order asc");
                            if (count($detail[0]) > 0) {
                                $restaurantarr[] = $detail[0];
                                $msg = 'Exist';
                                return response::json(['msg' => $msg, 'count' => $count, 'favlist' => $restaurantarr]);
                            }
                        } else {
                            $msg = 'Not Exist';
                            return response::json(['msg' => $msg, 'count' => $count]);
                        }
                    }
                } else {
                    return response::json(['msg' => 'Not Exist']);
                }
            }
            else{
                return response::json(['msg' => 'Not Exist']);
            }
        }
        else
        {
            return response::json(['msg' => 'User Not Exist']);

        }

    }

//Restaurant order edit
    public function rest_order($id,$val)
    {
        Restaurant_Master::where('id',trim($id))->update(['popular_display_order' =>$val]);
        $details = $this->view_restaurant();
        $msg = 'editted';
        return response::json(['msg' => $msg,'details' => $details]);
    }

    //android_version
    public function android_version()
    {
        $setting = GeneralSetting::where('id','1')->select('android_present_version','android_custom_message','force_update')->first();
        if(count($setting)>0)
        {
            if(strtoupper($setting['force_update']) == 'Y')
            {
                $msg = 'Exist';
                return response::json(['msg' => $msg,'version' => $setting['android_present_version'],'message' => $setting['android_custom_message'],'clear_data' => 'N']);
            }
            else if(strtoupper($setting['force_update']) == 'N')
            {
                $msg = 'no_force_update';
                return response::json(['msg' => $msg,'version' => $setting['android_present_version'],'message' => $setting['android_custom_message'],'clear_data' => 'N']);
            }
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }

    }

    //ios_version
    public function ios_version()
    {
        $skip_login = 'N';
        $setting = GeneralSetting::where('id','1')->select('ios_present_version','ios_custom_message','ios_force_update')->first();
        if(count($setting)>0)
        {
            if(strtoupper($setting['ios_force_update']) == 'Y')
            {
                $msg = 'Exist';
                return response::json(['msg' => $msg,'skip_login' => $skip_login,'version' => $setting['ios_present_version'],'message' => $setting['ios_custom_message']]);
            }
            else if(strtoupper($setting['ios_force_update']) == 'N')
            {
                $msg = 'no_force_update';
                return response::json(['msg' => $msg,'skip_login' => $skip_login,'version' => $setting['ios_present_version'],'message' => $setting['ios_custom_message']]);
            }
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg,'skip_login' => $skip_login]);
        }
    } //ios_version
    public function ios_version_new()
    {
        $skip_login = 'N';
        $setting = GeneralSetting::where('id','1')->select('ios_present_version','ios_custom_message','ios_force_update')->first();
        if(count($setting)>0)
        {
            if(strtoupper($setting['ios_force_update']) == 'Y')
            {
                $msg = 'Exist';
                return response::json(['msg' => $msg,'skip_login' => $skip_login,'version' => $setting['ios_present_version'],'message' => $setting['ios_custom_message']]);
            }
            else if(strtoupper($setting['ios_force_update']) == 'N')
            {
                $msg = 'no_force_update';
                return response::json(['msg' => $msg,'skip_login' => $skip_login,'version' => $setting['ios_present_version'],'message' => $setting['ios_custom_message']]);
            }
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg,'skip_login' => $skip_login]);
        }
    }
    public function login_restaurant($restid)
    {
        $password='';
        $name = '';
        $encr_method = Datasource::encr_method();
        $detail = DB::SELECT("select name,password FROM users where restaurant_id = '$restid'");
        if(count($detail)!=0){
            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $key1 = hash('sha256', $rowkey[0]->explore);
            $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
            $key = hash('sha256', $key1);
            $iv = substr(hash('sha256', $iv1), 0, 16);
            $password = openssl_decrypt(base64_decode($detail[0]->password), $encr_method, $key, 0, $iv);
            $name = $detail[0]->name;
        }
        return ['name'=>$name,'password'=>$password];

    }
    public function update_rest_auth(Request $request) {
        $restname = $request['restname'];
        $restpasw = $request['restpasw'];
        $restid   = $request['restid'];

        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($restpasw, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i:s');

        $exist = DB::SELECT("select staffid from users where restaurant_id = '$restid'");
        if(count($exist)>0)
        {
            DB::SELECT("UPDATE users SET name = '$restname',password='$password',updated_at='$datetime' WHERE restaurant_id='$restid' ");
            $msg = 'update';
        }
        else
        {
            DB::INSERT("INSERT INTO `users`(`login_group`, `name`, `password`, `restaurant_id`,`created_at`,modules) VALUES ('H','" . trim($restname) . "','".$password."','".$restid."','".$datetime."',json_object('RestauranrReports','Y','staff','N','banner','N','offers','N','orders','N','reports','N','customer','N','restaurant','N','designation','N','staff_report','N'))");
            $msg = 'insert';
        }
        return $msg;
    }

    public function radius_calculate(Request $request)
    {
        $lat1 = $request['lat1'];
        $lat2 = $request['lat2'];
        $long1 = $request['long1'];
        $long2 = $request['long2'];/*return $lat1.' '.$lat2.' '.$long1.' '.$long2;*/
        $radius = Commonsource::distance_calculate($lat2,$lat1,$long2,$long1);
        return $radius;
    }


    public function restaurantlogin($id)
    {
		$staffid = Session::get('staffid');
        if(!$staffid){return redirect('');}
        $rows = [];
        $arr = array();
        $restid = $id;
        $encr_method = Datasource::encr_method();
        $detail = DB::SELECT("select name,password,id,active,role FROM users where restaurant_id = '$restid'");
        if(count($detail)!=0){
            foreach($detail as $key=>$value)
            {
                $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
                $key1 = hash('sha256', $rowkey[0]->explore);
                $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
                $key = hash('sha256', $key1);
                $iv = substr(hash('sha256', $iv1), 0, 16);
                $password = openssl_decrypt(base64_decode($value->password), $encr_method, $key, 0, $iv);
                $name = $value->name;
                $arr['name'] =$name;
                $arr['password'] =$password;
                $arr['active'] =$value->active;
                $arr['role'] =$value->role;
                $arr['id'] = $value->id;
                $rows[]=$arr;
            }
        }
        return view('restaurant.manage_restaurant_login',compact('rows','restid'));
    }

    public function add_restaurantlogin(Request $request)
    {
        $type =$request['type'];
        $role= $request['role'];
        $restname = trim($request['restname']);
        $restpasw = trim($request['restpasw']);
        $restid   = $request['resid'];
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($restpasw, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i:s');
        if($type == 'insert')
        {
            $exist = DB::SELECT("select staffid from users where restaurant_id = '$restid' and name ='$restname' and password = '$password'");
            if(count($exist)>0)
            {
                $msg = 'already exist';
                $rows = DB::SELECT("select name,password,id,active,role FROM users where restaurant_id = '$restid'");
                return response::json(compact('msg','rows'));
            }
            else if(count($exist)<=0)
            {
                $datestring = bcrypt(str_replace(['-',":",' '],'', $datetime).uniqid());
                $token_string = str_replace(['"',"'",'/','.','/',],'', $datestring);
                $tknstring = substr($token_string,0,30);
                DB::INSERT("INSERT INTO `users`(`login_group`, `name`, `password`,`role`, `restaurant_id`,`token`,`created_at`,modules) VALUES ('H','" . trim($restname) . "','".$password."','".$role."','".$restid."','".$tknstring."','".$datetime."',json_object('RestauranrReports','Y','staff','N','banner','N','offers','N','orders','N','reports','N','customer','N','restaurant','N','designation','N','staff_report','N'))");
                $msg = 'success';
                $rows = DB::SELECT("select name,password,id,active,role FROM users where restaurant_id = '$restid'");
                return response::json(compact('msg','rows'));
            }
        }
        else if($type == 'update')
        {
            $userid = $request['userid'];
            $exist = DB::SELECT("select staffid,token from users where id != '$userid' and restaurant_id = '$restid' and name ='$restname' and password = '$password'");
            if(count($exist)>0)
            {
                $msg = 'exist';
                $rows = DB::SELECT("select name,password,id,active,role FROM users where restaurant_id = '$restid'");
                return response::json(compact('msg','rows'));
            }
            else
            {
                $exists = DB::SELECT("select staffid,token from users where restaurant_id = '$restid' and  id= '$userid'");
                if($exists[0]->token == '')
                {
                    $datestring = bcrypt(str_replace(['-',":",' '],'', $datetime).uniqid());
                    $token_string = str_replace(['"',"'",'/','.','/',],'', $datestring);
                    $tknstrings = substr($token_string,0,30); 
                    $tokenstring = ",token ='".$tknstrings."'";
                }
                else
                {
                    $tokenstring ='';
                }
                
                DB::SELECT("UPDATE users SET name = '$restname',password='$password',role='$role',updated_at='$datetime'".$tokenstring." WHERE restaurant_id='$restid' and id= '$userid'");
                $msg = 'done';
                $rows = DB::SELECT("select name,password,id,active,role FROM users where restaurant_id = '$restid'");
                return response::json(compact('msg','rows'));
            }
        }
    }

    public function restaurantlogin_status(Request $request)
    {
        $users = UserMaster::where('id','=',$request['ids'])
            ->where('active','=','Y')
            ->get();
        if(count($users)>0)
        {
            UserMaster::where('id', $request['ids'])->update(
                [
                    'active' => 'N'
                ]);
        }
        else
        {
            UserMaster::where('id', $request['ids'])->update(
                [
                    'active' => 'Y'
                ]);
        }

    }


    public function restaurant_login(Request $request)
    {
        $username      = $request['username'];
        $pswd       = trim($request['password']);
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($pswd, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        $list = DB::SELECT('select `id`,`token`,`restaurant_id`,`ftoken`,`role`  from `users` where name = "'.trim($username).'" and password =  "'.trim($password).'" and active = "Y"');
        if(count($list) >0)
        {  
            $msg =  'Login Success';
            $rest_details =DB::SELECT('SELECT JSON_UNQUOTE(name_tagline->"$.name") as restaurant_name,address FROM restaurant_master where id ="'.$list[0]->restaurant_id.'"');
            return response::json(['msg' => $msg,'user_id'=>$list[0]->id,'ftoken'=>(string)$list[0]->ftoken,'role'=>$list[0]->role,'name'=>$rest_details[0]->restaurant_name,'address' => $rest_details[0]->address,'token'=>$list[0]->token,'restaurant_id'=>$list[0]->restaurant_id]);
        }
        else
        {
            $msg =  'Invalid Login';
            return response::json(['msg' => $msg]);
        }
    }

    public function dasboard_details(Request $request)
    {
        $decimal_point = Commonsource::generalsettings();
        $salearr = array();
        $arr = array();
        $restaurantid = $request['restaurantid'];
        $key = $request['key'];
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $todate = $date->format('Y-m-d');
        $yesterday= date('Y-m-d',strtotime('-1 day'));
        $lastweekdate   = date('Y-m-d',strtotime('-7 days'));
        $lastmontdate   = date('Y-m-d',strtotime('-1 month'));
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
        if($details == 'Exist')
        {
            $weeksale = DB::SELECT("select ROUND(sum(((rest_item_total_details->'$.inc_rate' + rest_item_total_details->'$.pack_rate' + total_details->'$.packing_charge')-(total_details->'$.discount_amount'))),$decimal_point) as total,count(*) as count FROM order_master WHERE  rest_id = '".$restaurantid."' and date(order_date) >= '".$lastweekdate."' and date(order_date) <= '".$todate."' and current_status ='D'");
            if($weeksale[0]->total)
            {
                $week = $weeksale[0]->total;
                $weekcount = $weeksale[0]->count;
            }
            else{
                $week =0;
                $weekcount=0;
            }

            $todaysale = DB::SELECT("select ROUND(sum(((rest_item_total_details->'$.inc_rate' + rest_item_total_details->'$.pack_rate' + total_details->'$.packing_charge')-(total_details->'$.discount_amount'))),$decimal_point) as total,count(*) as count FROM order_master WHERE rest_id = '".$restaurantid."' and date(order_date) = '".$todate."' and current_status ='D'");
            if( $todaysale[0]->total)
            {
                $today = $todaysale[0]->total;
                $todaycount = $todaysale[0]->count;
            }
            else{
                $today =0;
                $todaycount=0;
            }
            $yesterdaysale = DB::SELECT("select ROUND(sum(((rest_item_total_details->'$.inc_rate' + rest_item_total_details->'$.pack_rate' + total_details->'$.packing_charge')-(total_details->'$.discount_amount'))),$decimal_point) as total,count(*) as count FROM order_master WHERE rest_id = '".$restaurantid."' and date(order_date) = '".$yesterday."' and current_status ='D'");
            if( $yesterdaysale[0]->total)
            {
                $yesterday = $yesterdaysale[0]->total;
                $yesterdaycount = $yesterdaysale[0]->count;

            }
            else{
                $yesterday =0;
                $yesterdaycount =0;
            }
            $salearr['today_sale'] = (string)number_format($today,$decimal_point);
            $salearr['today_sale_count'] =  (string)$todaycount;
            $salearr['yesterday_sale'] = (string)number_format($yesterday,$decimal_point);
            $salearr['yesterday_sale_count'] =  (string)$yesterdaycount;
            $salearr['week_sale'] =  (string)number_format($week,$decimal_point);
            $salearr['week_sale_count'] = (string)$weekcount;
            $arr['sale'] = $salearr;
            $topmenus = DB::SELECT('SELECT menu_id,JSON_UNQUOTE(menu_details->"$.menu_name") as menu_name,JSON_UNQUOTE(single_rate_details->"$.inc_rate") as rate FROM `order_details` od join `order_master` om on od.order_number = om.order_number where om.rest_id =  "'.$restaurantid.'" and date(om.order_date) >="'.$lastmontdate.'" and date(order_date) <= "'.$todate.'"   group by menu_id,menu_details order by count(menu_id)  desc limit 0,5');
            if(count($topmenus)!=0){
            $topmenuarr =array();
            foreach($topmenus as $key=>$value)
            {
                $topmenuarr['menu_id'] = $value->menu_id;
                $topmenuarr['menu_name'] =$value->menu_name;
                $topmenuarr['rate'] = number_format($value->rate,$decimal_point);
                $arr['top_menus'][] = $topmenuarr;
            }
            }
            else
            {
            $arr['top_menus'] =[];
            }
            $recentorders = DB::SELECT('select order_number,if(current_status = "CA","Cancelled",if(current_status = "D","Delivered",if(current_status = "C","Confirmed",if(current_status = "OP","Picked",if(current_status = "P","Confirmed","Staff Assigned"))))) as current_status,order_date from order_master where rest_id = "'.$restaurantid.'" and (current_status in ("C")  OR (current_status = ("P") and rest_confirmed =("Y") and on_hold = ("N"))) order by date(order_date)  desc limit 3');
            $arr['recentorders'] = $recentorders;
            return response::json(['data' =>$arr]);
        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }


    public function orderstatus(Request $request)
    {
           $arr = array();
            $arr[0] = 'All';
            $arr[1] = 'Confirmed';
            $arr[2] = 'Picked';
            $arr[3] = 'Delivered';
            $arr[4] = 'Cancelled';
            return response::json(['data' =>$arr]);

    }

    public function ordermanagement(Request $request)
    {
        $decimal_point = Commonsource::generalsettings();
        $restaurantid = $request['restaurantid'];
        $key = $request['key'];
		$pagecount =$request['pagecount'];
        $from_date = date('Y-m-d',strtotime($request['from_date']));
        $to_date = date('Y-m-d',strtotime($request['to_date']));
        $orderstatus = strtoupper($request['status']);
        $limit = 20;
        $first = 0;
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
        if($details == 'Exist') {
            if($pagecount ==1)
            {
                $last = $limit;
            }
            else if($pagecount >1)
            {
                $first =((($pagecount-1)*$limit));
                $last = $limit;
            }

            if($orderstatus == 'ALL')
            {
				 $statusstring = "(current_status != 'P' OR (current_status = 'P' and rest_confirmed ='Y' and on_hold = 'N')) and current_status != 'CA' and";
            }
            else
            {
                if($orderstatus =='CONFIRMED')
                {
                    $status = 'C';
                }
                else if($orderstatus == 'CANCELLED')
                {
                    $status = 'CA';
                }
                else if($orderstatus == 'PICKED')
                {
                    $status = 'OP';
                }
                else if($orderstatus == 'DELIVERED')
                {
                    $status = 'D';
                }
                $statusstring = " current_status ='".$status."' and ";

            }
            $detail 	 = DB::SELECT("SELECT order_number,date(order_date) as date,time(order_date) as time,IFNULL(JSON_UNQUOTE(delivery_assigned_details->'$.name'),0) as delivery_staff,review_star,ROUND(((rest_item_total_details->'$.inc_rate' + rest_item_total_details->'$.pack_rate' + total_details->'$.packing_charge')-(total_details->'$.discount_amount')),$decimal_point) as final_total,if(current_status = 'CA','Cancelled',if(current_status = 'D','Delivered',if(current_status = 'P','Confirmed',if(current_status = 'C','Confirmed',if(current_status = 'OP','Picked',if(current_status = 'SA','Staff Assigned','')))))) as current_status FROM `order_master` left join status_master  on  trim(order_master.current_status) = trim(status_master.id) WHERE  order_number NOT LIKE 't_%' and rest_id = '".$restaurantid."' and $statusstring date(order_date) >= '".$from_date."' and date(order_date) <= '".$to_date."'  order by status_master.order asc limit $first,$last");
            
			
			if($pagecount ==1)
            {
                $orderdetail = DB::SELECT("SELECT order_number,date(order_date) as date,time(order_date) as time,IFNULL(JSON_UNQUOTE(delivery_assigned_details->'$.name'),0) as delivery_staff,review_star,ROUND(final_total,$decimal_point) as final_total,if(current_status = 'CA','Cancelled',if(current_status = 'D','Delivered',if(current_status = 'P','Confirmed',if(current_status = 'C','Confirmed',if(current_status = 'OP','Picked',if(current_status = 'SA','Staff Assigned','')))))) as current_status FROM `order_master` left join status_master  on  trim(order_master.current_status) = trim(status_master.id) WHERE order_number NOT LIKE 't_%' and rest_id = '".$restaurantid."' and $statusstring date(order_date) >= '".$from_date."' and date(order_date) <= '".$to_date."' and order_number != '' order by status_master.order asc");
				$totalcount  = ceil(count($orderdetail)/$limit);
				$ordertotals = DB::SELECT("SELECT count(order_number) as totalcount,ROUND(sum(((rest_item_total_details->'$.inc_rate' + rest_item_total_details->'$.pack_rate' + total_details->'$.packing_charge')-(total_details->'$.discount_amount'))),$decimal_point) as totalamount FROM `order_master` WHERE order_number NOT LIKE 't_%' and rest_id = '".$restaurantid."' and $statusstring date(order_date) >= '".$from_date."' and date(order_date) <= '".$to_date."'");
				$totalorders = $ordertotals[0]->totalcount;
				$totalamount = $ordertotals[0]-> totalamount;
				
				
            }
			else if($pagecount >1)
            {
			   $totalcount  = 0;
			   $totalorders = 0;
			   $totalamount = 0;
			  
			}
			
            $msg = 'Exist';
            return response::json(['msg'=>$msg,'data' =>$detail,'totalcount' =>(string)$totalcount,'totalorders'=>(string)$totalorders,'totalamount'=>  number_format($totalamount,$decimal_point)]);


	   }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }

    public function orderreviewdetails(Request $request)
    {
        $restaurantid = $request['restaurantid'];
        $orderid = $request['orderid'];
        $key = $request['key'];
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
        if($details == 'Exist') {
            $detail = DB::SELECT("SELECT review_star,JSON_UNQUOTE(customer_details->'$.name') as customer_details,JSON_UNQUOTE(review_details->'$.review') as review FROM `order_master` WHERE   order_number = '".$orderid."'");
            if(count($detail)>0) {
                $msg = 'Exist';
                return response::json(['msg'=>$msg,'data' => $detail[0]]);
            }
            else
            {
                $msg ='Result Not Exist';
                return response::json(compact('msg'));
            }
        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }


    public function restaurantreviews(Request $request)
    {
        $restaurantid = $request['restaurantid'];
        $key = $request['key'];
        $pagecount =$request['pagecount'];
        $limit = 20;
        $first = 0;
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
        if($details == 'Exist') {
            if($pagecount ==1)
            {
                $last = $limit;
            }
            else if($pagecount >1)
            {
                $first =((($pagecount-1)*$limit));
                $last = $limit;
            }

            $detail =      DB::SELECT("SELECT review_star,JSON_UNQUOTE(customer_details->'$.name') as customer_details,JSON_UNQUOTE(review_details->'$.review') as review,order_number,order_date FROM `order_master` WHERE   rest_id = '".trim($restaurantid)."' and (JSON_UNQUOTE(review_details->'$.review') IS NOT NULL) order by order_date desc limit $first,$last");
            
			if($pagecount ==1)
            {
                $totaldetail = DB::SELECT("SELECT review_star,JSON_UNQUOTE(customer_details->'$.name') as customer_details,JSON_UNQUOTE(review_details->'$.review') as review,order_number,order_date  FROM `order_master` WHERE   rest_id = '".trim($restaurantid)."' and (JSON_UNQUOTE(review_details->'$.review') IS NOT NULL) order by order_date desc");
				$totalcount = ceil(count($totaldetail)/$limit);
			
            }
            else if($pagecount >1)
            {
                $totalcount = 0;
            }
			
			
            if(count($detail)>0) {
                $msg = 'Exist';
            }
            else
            {
                $msg ='Result Not Exist';
            }
            return response::json(['msg'=>$msg,'data' => $detail,'totalcount' =>(string)$totalcount]);

        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }

    public function orderdetails(Request $request)
    {
        $decimal_point = Commonsource::generalsettings();
        $restaurantid = $request['restaurantid'];
        $orderid = $request['orderid'];
        $key = $request['key'];
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
        if($details == 'Exist') {
            $detail = DB::SELECT("SELECT order_number,rest_confirmed,date(order_date) as date,JSON_UNQUOTE(total_details->'$.packing_charge') as packaging_charge,JSON_UNQUOTE(total_details->'$.discount_amount') as discount_amount,sub_total,time(order_date) as time,if(current_status = 'CA','Cancelled',if(current_status = 'D','Delivered',if(current_status = 'C','Confirmed',if(current_status = 'OP','Picked',if(current_status = 'P','Placed','Staff Assigned'))))) as current_status,JSON_UNQUOTE(delivery_assigned_details->'$.name') as delivery_staff,JSON_UNQUOTE(delivery_assigned_details->'$.phone') as phone,final_total,readytopick FROM `order_master` WHERE  order_number ='".$orderid."'");
            $menudetails = DB::SELECT("SELECT JSON_UNQUOTE(menu_details->'$.category') as category,JSON_UNQUOTE(menu_details->'$.menu_name') as menu_name,JSON_UNQUOTE(menu_details->'$.portion') as portion,JSON_UNQUOTE(menu_details->'$.preference') as preference,JSON_UNQUOTE(single_rate_details->'$.exc_rate') as single_rate,JSON_UNQUOTE(single_rate_details->'$.inc_rate') as inc_single_rate,qty,single_rate_details->'$.exc_rate'*qty as total_amount_exc ,single_rate_details->'$.inc_rate'*qty as total_amount_inc ,single_rate_details->'$.pack_rate'*qty as pack_rate,if(current_status = 'CA','Cancelled',if(current_status = 'D','Delivered',if(current_status = 'C','Confirmed',if(current_status = 'OP','Picked',if(current_status = 'P','Placed','Staff Assigned')))))  as status FROM order_details join order_master on order_details.order_number = order_master.order_number where order_master.order_number  ='".$orderid."'");
            //$sumdetails = DB::SELECT("SELECT sum(JSON_UNQUOTE(single_rate_details->'$.exc_rate')*qty) as total_amount FROM order_details where order_number  ='".$orderid."'");
            $arr = array();
            $inc_rate =0;
            $excl_rate=0;
            $tax_rate =0;
            $pack_rate =0;
            foreach($menudetails as $key=>$value)
            {
                $arr['category'] = $value->category;
                $arr['menu_name'] = $value->menu_name;
                $arr['portion'] = $value->portion;
				$arr['preference'] = $value->preference;
                $arr['single_rate'] = number_format($value->single_rate,$decimal_point);
                $arr['qty'] = $value->qty;
                $arr['total_amount'] = number_format($value->total_amount_exc,$decimal_point);
                $arr['status'] = $value->status;
                $inc_rate = $inc_rate + $value->total_amount_inc;
                $excl_rate = $excl_rate +$value->total_amount_exc;
                $pack_rate= $pack_rate+$value->pack_rate;
                $menuarr[] = $arr;
            }  
            $tax_rate = $inc_rate - $excl_rate;
            $packaging_rate = $pack_rate + $detail[0]->packaging_charge;
			$discount_amount = $detail[0]->discount_amount;            
            //$final_total = (($detail[0]->sub_total+$detail[0]->packaging_charge)-$detail[0]->discount_amount);
			$final_total =  (($excl_rate+ $tax_rate+ $packaging_rate)-$discount_amount);
           // $final_total = (($excl_rate + $packaging_rate + $tax_rate)-$discount_amount);
           if(strtoupper($detail[0]->current_status) == 'PLACED' && $detail[0]->rest_confirmed == 'Y')
           {
             $currentstatus = 'Confirmed';
           }  
           else
           {
             $currentstatus =  $detail[0]->current_status; 
           }
            return response::json(['msg'=>'Exist','order_number' =>$detail[0]->order_number,'date' =>$detail[0]->date,'time' =>$detail[0]->time,
               'current_status' =>$currentstatus,'readytopick'=>$detail[0]->readytopick, 
               'delivery_staff'=>(string)isset($detail[0]->delivery_staff)?$detail[0]->delivery_staff:'',
			   'phone'=>(string)isset($detail[0]->phone)?$detail[0]->phone:'',
               'final_total'=>(string)number_format($final_total,$decimal_point),
                'menudetails'=>$menuarr,
                'total_exclusive_rate' =>(string) number_format($excl_rate,$decimal_point),
                'tax_rate' => (string) number_format($tax_rate,$decimal_point),
                'packaging_charge' =>(string) number_format($packaging_rate,$decimal_point),
				'discount_amount' =>(string) number_format($discount_amount,$decimal_point),
                'totalcount' =>(string)count($menudetails)]);
        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }

    public function menucategory(Request $request)
    {
        $restaurantid = $request['restaurantid'];
        $key = $request['key'];
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
        if($details == 'Exist') {
            $arr = array();
            $categoryarr = array();
            $category = DB::SELECT("select slno as id,name from category where restaurant_id = '".$restaurantid."' order by order_no asc");
            foreach($category as $value=>$item)
            {
                      $categorys = $item->name;
                      $details = DB::SELECT("select * from restaurant_menu where json_search(UPPER(m_category), 'all', UPPER('%".title_case($categorys)."%')) is not null and m_rest_id = '".$restaurantid."'");
                      if(count($details)>0) {
                         $arr['id'] = $item->id;
                         $arr['name'] = $item->name;
                         $categoryarr[] = $arr;
                      }
            }
            $msg = 'Exist';
            return response::json(['msg'=>$msg,'data' =>$categoryarr,'totalcount' =>(string)count($categoryarr)]);
        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }

    public function menumanagement(Request $request)
    {
        $decimal_point = Commonsource::generalsettings();
        $catarr = array();
        $arr = array();
        $produtarr = array();
        $restaurantid = $request['restaurantid'];
        $key = $request['key'];
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
                
        if($details == 'Exist') {
            $allcategory = DB::SELECT("select slno as id,name,status,order_no from category where restaurant_id = '".$restaurantid."' order by order_no asc");
            $activecategory = DB::SELECT("select slno as id,name from category where status = 'Y' and restaurant_id = '".$restaurantid."'");
            $inactivecategory = DB::SELECT("select slno as id,name from category where status = 'N' and restaurant_id = '".$restaurantid."'");
            $count= (object) array('all' =>count($allcategory),'actve' =>count($activecategory),'inactive' =>count($inactivecategory));
            $allmenu = DB::SELECT("select count(*) as count from restaurant_menu where m_rest_id = '".$restaurantid."'");
            $activemenu = DB::SELECT("select count(*) as count from restaurant_menu where m_status = 'Y' and m_rest_id = '".$restaurantid."'");
            $activemenu_count = $activemenu[0]->count;
            $inactivemenu = DB::SELECT("select count(*) as count from restaurant_menu where m_status = 'N' and m_rest_id = '".$restaurantid."'");
            $inactivemenu_count = $inactivemenu[0]->count;
            foreach($allcategory as $key=>$item)
            {
                
                $category_id = $item->id;
                $category_name = $item->name;
                $category_for_check   = '"'.$category_name.'"';
                $cat_string = "and json_contains(m_category, '[$category_for_check]')";
                if($item->status == 'Y')
                {
                    $status = 'Active';
                }
                else
                {
                    $status = 'Inactive';
                    $inactivemenu_new_count = DB::SELECT("select count(*) as count from restaurant_menu where m_status = 'N' and m_rest_id = '".$restaurantid."' $cat_string");
                    $inactivemenu_count = $inactivemenu_count - $inactivemenu_new_count[0]->count;
                    $activemenu_new_count = DB::SELECT("select count(*) as count from restaurant_menu where m_status = 'Y' and m_rest_id = '".$restaurantid."' $cat_string");
                    $activemenu_count = $activemenu_count - $activemenu_new_count[0]->count;

                }

                $menudetails = DB::SELECT("select m_menu_id as menu_id,JSON_UNQUOTE(m_name_type->'$.name') as menu_name,JSON_UNQUOTE(m_time->'$.from') as from_time,JSON_UNQUOTE(m_time->'$.to') as to_time,m_status as menu_status,m_por_rate as portion,JSON_LENGTH(`m_por_rate`) as count from restaurant_menu where m_rest_id = '".$restaurantid."' $cat_string");
                if(count($menudetails)>0) {
                    $arr[] = (object) array('type'=>'category','id'=>$category_id,'name'=>$category_name,'from_time'=>'','to_time'=>'','status'=>$item->status,'cat_status'=>$item->status,
                    'portion'=>'','portionid'=>'','rate'=>'','dis_order'=>(string)$item->order_no,'chnage_status'=>'');
                    foreach ($menudetails as $value) {
                        for ($i = 1; $i <= $value->count; $i++) {
                            $json_data = json_decode($value->portion, true);
                            if($item->status == 'N')
                            {
                                $menu_status = 'N';
                                $chnage_status = 'N';
                            }
                            else
                            {
                                $menu_status = $value->menu_status;
                                $chnage_status = 'Y';
                            }
                            $portionid = strtoupper('portion' . $i);
                            $portion = strtoupper($json_data['portion' . $i]['portion']);
                            $inc_rate = strtoupper(number_format($json_data['portion' . $i]['inc_rate'],$decimal_point));
                            $arr[] = (object)array('type' => 'menu', 'id' => $value->menu_id, 'name' => $value->menu_name, 'from_time' => $value->from_time,
                                'to_time' => $value->to_time, 'status' => $menu_status,'cat_status'=>$item->status, 'portion' => $portion, 'rate' => (string)$inc_rate,
                                'portionid' => (string)$portionid,'dis_order'=>'','chnage_status'=>$chnage_status);
                        }
                    }
                }
            }
            return response::json(['msg'=>'Exist','category_count' =>count($allcategory),'all_count'=>$allmenu[0]->count,'active_count'=>$activemenu_count,'inactive_count'=>$inactivemenu_count,'data' =>$arr]);
        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }

    public function menuupdate(Request $request)
    {
        $menuid =$request['menu_id'];
        $restaurantid = $request['restaurantid'];
        $from_time = date('H:i:s',strtotime($request['from_time']));
        $to_time=  date('H:i:s',strtotime($request['to_time']));
        $status = $request['status'];
        $portionid =$request['portionid'];
        $portionname = $request['portionname'];
        $key = $request['key'];
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
        if($details == 'Exist') 
		{
			if(strtotime($from_time) < strtotime($to_time))
			{
				
				$msg ='Success';
				DB::SELECT("UPDATE restaurant_menu SET m_time=JSON_OBJECT('from','".$from_time."','to','".$to_time."') , m_status = '$status' WHERE m_rest_id ='$restaurantid' and m_menu_id = '$menuid'");
				return response::json(compact('msg'));	
				
			}
			else
			{                   
			   $msg = 'Error!! To Time Not Greater Than From Time'; 
			   return response::json(compact('msg'));
			}			
            
        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }

    public function categorystatus(Request $request)
    {
        $cat_id =$request['cat_id'];
        $restaurantid = $request['restaurantid'];
        $status = $request['status'];
        $dis_order = $request['dis_order'];
        $key = $request['key'];
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
        if($details == 'Exist') 
		{
            
			DB::SELECT("UPDATE category SET status = '".$status."',order_no = ".$dis_order." WHERE restaurant_id = ".$restaurantid." and slno = ".$cat_id."");
            $msg ='Success';
            return response::json(compact('msg'));		
            
        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }

    public function restauranttimedelete(Request $request)
    {
        $day = strtoupper($request['day']);
        $slno = $request['slno'];
        $id = $request['restaurantid'];
        $key = $request['key'];
        $details = Commonsource::checkrestaurantvalidity($id,$key);
        if($details == 'Exist')
        {
            //DB::SELECT("delete from `restaurant_timings` where rt_rest_id = '$id' and rt_slno = '$slno' and rt_day = '$day'");
            $msg = 'Deleted';
            return response::json(compact('msg'));
        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }
    public function restauranttimeadd(Request $request)
    {
        
        $id = $request['restaurantid'];
        $key = $request['key'];
        $from =$request['from'];
        $to =$request['to'];
        $days= strtoupper($request['day']);
        $details = Commonsource::checkrestaurantvalidity($id, $key);
        if ($details == 'Exist')
        {
            $general= GeneralSetting::where('id','1')->select('restaurant_from_time','restaurant_to_time')->first();
            if(strtotime($general['restaurant_from_time']) <= strtotime($from) && strtotime($general['restaurant_to_time']) >= strtotime($to))
            {
                if(strtotime($from) < strtotime($to))
                {     
                $timingcount = DB::select("SELECT rt_from_time,rt_to_time FROM restaurant_timings WHERE rt_rest_id='$id' and rt_day = '$days'");
                if(count($timingcount)<=0)
                {
                $msg = 'Success';
                DB::SELECT('INSERT INTO `restaurant_timings`(`rt_rest_id`, `rt_day`,`rt_from_time`, `rt_to_time`) VALUES("' . $id . '","' . $days . '","' . $from . '","' . $to . '")');
                }
                else
                {                    
                $existdata = DB::select("SELECT rt_from_time,rt_to_time	 FROM restaurant_timings WHERE rt_rest_id='$id' and rt_day = '$days' and rt_from_time = '$from' and rt_to_time = '$to'");
                if (count($existdata) <= 0) {
                    
                    if(strtotime($timingcount[0]->rt_to_time) < strtotime($from))
                    {
                    $msg = 'Success';
                    DB::SELECT('INSERT INTO `restaurant_timings`(`rt_rest_id`, `rt_day`,`rt_from_time`, `rt_to_time`) VALUES("' . $id . '","' . $days . '","' . $from . '","' . $to . '")');
                    }
                    else
                    {
                    $msg = 'Error!! Time Overlap With Previous Time.'; 
                    }
                    
                }
                else
                {
                    $msg = ' Already Exist';
                }   
                }                
                }
                else
                {                   
                   $msg = 'Error!! To Time Not Greater Than From Time'; 
                }
            }
            else
            {
                $msg = 'Error!! Time Exceeds With Potafo Working Time';
            }
            return response::json(compact('msg'));
        } else {
            $msg = $details;
            return response::json(compact('msg'));
        }
    }

    public function tokenupdate(Request $request)
    {
        $id = $request['restaurantid'];
        $key = $request['key'];
        $userid =$request['userid'];
        $ftoken =$request['ftoken'];
        $details = Commonsource::checkrestaurantvalidity($id, $key);
        if ($details == 'Exist')
        {
            $list = DB::SELECT('update users set ftoken = "'.$ftoken.'" where id = "'.$userid.'" and restaurant_id ="'.$id.'"');
            $msg = 'Success';
        }
        else
        {
            $msg = $details;
        }
        return response::json(compact('msg'));
    }

    public function orderslist(Request $request)
    {
        $id = $request['restaurantid'];
        $key = $request['key'];
        $details = Commonsource::checkrestaurantvalidity($id, $key);
        if ($details == 'Exist')
        {
            $list = DB::SELECT('select order_number,time(order_date) as order_time from `order_master` where rest_id = "'.$id.'" and current_status="P" and rest_confirmed = "N" and on_hold = "N"');
            $msg = 'Success';
            if(count($list)>0)
            {
                $count = count($list);
            }
            else
            {
                $count =0;
            }
            return response::json(compact('msg','list','count'));
        }
        else
        {
            $msg = $details;
            return response::json(compact('msg'));
        }
    }
    
    public function restaurantconfirmation(Request $request)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $time = strtoupper($date->format('H:i a'));
        $id = $request['restaurantid'];
        $key = $request['key'];
        $orderno = trim($request['orderno']);
        $user_id = $request['user_id'];
        $details = Commonsource::checkrestaurantvalidity($id, $key);
        if ($details == 'Exist')
        {
            $list = DB::SELECT("SELECT customer_id,customer_details->>'$.mobile' as cust_mobile,customer_details->>'$.name' as cust_name from `order_master` where  rest_id ='$id' and rest_confirmed ='N' and order_number=  '$orderno'");
            if(count($list)>0)
            {
                DB::UPDATE('update order_master set restaurant_confirmation_by = "'.$user_id.'",rest_confirmed ="Y",rest_confirmed_time="'.$time.'",assign_after_time =DATE_ADD(NOW(), INTERVAL 35 SECOND) , assign_status= "Pending"  where order_number = "'.$orderno.'"');
				$msg = 'Success';
                //start of : to notify customer
                $rest_name = DB::SELECT("SELECT name_tagline->>'$.name' as r_name FROM restaurant_master WHERE id  = $id");
                $customerid =  $list[0]->customer_id;
                $custmobile =  $list[0]->cust_mobile;
                $custname = title_case($list[0]->cust_name);
                $name_rest =title_case($rest_name[0]->r_name);
				$is_exist= DB::SELECT("SELECT fm.ftoken,fm.customer_id FROM ftoken_master fm WHERE fm.customer_id = $customerid");
				if(count($is_exist)>0)
                {
                    foreach($is_exist as $item)
                    {
                        $arr['to'] = $item->ftoken;
                        $arr['title'] = 'Order Accepted';
                        $arr['message'] = 'Hi '.$custname.', Thanks for using POTAFO. Your order no. '.$orderno.' has been Accepted & Confirmed by '.$name_rest.'.';
                        $arr['image'] = 'null';
                        $arr['action'] = 'orderhistory';
                        $arr['action_destination'] = 'null';
                        $arr['app_type'] = 'customerapp';
                        $result = Commonsource::notification($arr);

                    }
                }
                 $message = "Hi $custname, Thanks for using POTAFO. Your Potafo order no. $orderno has been Accepted & Confirmed by $name_rest. ";
                $sendmsg = urlencode($message);
                $smsurl = Datasource::smsurl($custmobile,$sendmsg);
                $data = file_get_contents($smsurl);  
				//end of : to notify customer
            }
            else
            {
                   $msg = 'Order Number Does Not Exist';
            }
            return response::json(compact('msg'));
        }
        else
        {
            $msg = $details;
            return response::json(compact('msg'));
        } 
    }
    
    public function placedcount(Request $request)
    {
        $id = $request['restaurantid'];
        $key = $request['key'];
        $details = Commonsource::checkrestaurantvalidity($id, $key);
        if ($details == 'Exist')
        {
            $list = DB::SELECT('select order_number,time(order_date) as order_time from `order_master` where rest_id = "'.$id.'" and current_status="P" and rest_confirmed ="N" and on_hold = "N"');
            $msg = 'Success';
            if(count($list)>0)
            {
                $count = count($list);
            }
            else
            {
                $count =0;
            }
            return response::json(compact('msg','','count'));
        }
        else
        {
            $msg = $details;
            return response::json(compact('msg'));
        }
    }
    
    public function restaurant_force_update(Request $request)
    {
   
		$setting = GeneralSetting::where('id','1')->select('rest_app_currnt_version','rest_app__custom_msg','rest_force_update')->first();
        if(count($setting)>0)
        {
            if(strtoupper($setting['rest_force_update']) == 'Y')
            {
                $msg = 'Exist';
                return response::json(['msg' => $msg,'version' => $setting['rest_app_currnt_version'],'message' => $setting['rest_app__custom_msg'],'clear_data' => 'N']);
            }
            else if(strtoupper($setting['rest_force_update']) == 'N')
            {
                $msg = 'no_force_update';
                return response::json(['msg' => $msg,'version' => $setting['rest_app_currnt_version'],'message' => $setting['rest_app__custom_msg'],'clear_data' => 'N']);
            }
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }
    
    
    public function restaurantgroupnotification(Request $request)
    {
        $id = $request['restaurantid'];
        $key = $request['key'];
        $userid = $request['userid'];
        $details = Commonsource::checkrestaurantvalidity($id, $key);
        if ($details == 'Exist')
        {
            $detail = DB::SELECT("select id,message,title,DATE_FORMAT(notifications.entry_date, '%Y-%m-%d %h:%i:%s %p') as entry_date from notifications  WHERE is_all = 'N' AND groupid = (select g_id from notification_group ng where ng.g_name = 'PARTNERS') and now()< expiry and  json_contains(user_list,'[\"$userid\"]')");
            if(count($detail)>0)
			{
				foreach($detail as $key=>$val)
				{
					$data['id'] = $val->id;
					$data['title'] = $val->title;
					$data['message'] = $val->message;
					$data['entry_date'] = $val->entry_date;
					$arr[]=$data;
				}
				$msg = 'success';
				return response::json(['msg' => $msg,'data'=>$arr]);
			}
			else
			{
				$msg = 'No Notifications';
				return response::json(['msg' => $msg]);
			}
        }
        else
        {
         $msg = $details;
            return response::json(compact('msg'));   
        }
    }
	
	
	public function restauranttime(Request $request)
    {
         $day = strtoupper($request['day']);
        
		
		
		
		
			$detail = DB::SELECT("SELECT `id` FROM `restaurant_master`");
            foreach($detail as $key=>$val)
				{
					//$data['id'] = $val->id;
					
					$restaurants = DB::SELECT('SELECT rt_from_time,rt_to_time FROM restaurant_timings where rt_day = "'.$day.'" and  rt_rest_id="'.$val->id.'"');
					foreach($restaurants as $key=>$item)
					{
						for($i = 1; $i<=count($restaurants);$i++)
						{
								if(strtoupper($key) == $day) {
									$open = date('H:i',strtotime($item->rt_from_time));
									$close = date('H:i',strtotime($item->rt_to_time));
									$selectdetails = DB::SELECT('select * from restaurant_timings where rt_rest_id="'.$val->id.'" and rt_day="'.$day.'" and rt_from_time="'.$open.'" and rt_to_time="'.$close.'"');
									if(count($selectdetails)<=0) {
										DB::INSERT('INSERT INTO `restaurant_timings`(`rt_rest_id`, `rt_day`, `rt_slno`, `rt_from_time`, `rt_to_time`) VALUES("' . $val->id . '","' . $day . '",0,"' . $open . '","' . $close . '")');
									}
							}
						}
					}
					//$arr[]=$data;
				}
				$msg = 'Success';
				return response::json(['msg' => $msg]);
			
			
			

    }
	
	public function updateitemrates(Request $request)
    {

			$detail = DB::SELECT("SELECT `order_number` FROM `order_master` WHERE `current_status` != 'T'");
            foreach($detail as $key=>$val)
			{
					//$data['id'] = $val->id;
											
               	$menudetails = DB::SELECT("SELECT single_rate_details->'$.exc_rate'*qty as total_amount_exc ,single_rate_details->'$.inc_rate'*qty as total_amount_inc ,single_rate_details->'$.pack_rate'*qty as pack_rate FROM order_details where order_number  ='$val->order_number'");
				
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
				DB::SELECT('UPDATE order_master SET rest_item_total_details = JSON_OBJECT("inc_rate","'.$inc_rate.'","excl_rate","'.$excl_rate.'","pack_rate","'.$pack_rate.'","tax_rate","'.$tax_rate.'") WHERE order_number="'.$val->order_number.'"');
			}
			$msg = 'Success';
			return response::json(['msg' => $msg]);
			
			
			

    }
	
	public function dlvstaffreviews(Request $request)
    {
        $dlvstaffid = $request['staffid'];
        $pagecount =$request['pagecount'];
		$from_date = date('Y-m-d',strtotime($request['from_date']));
        $to_date = date('Y-m-d',strtotime($request['to_date']));
        $limit = 20;
        $first = 0;
		if($pagecount ==1)
		{
			$last = $limit;
		}
		else if($pagecount >1)
		{
			$first =((($pagecount-1)*$limit));
			$last = $limit;
		}

		
		$detail =      DB::SELECT("SELECT JSON_UNQUOTE(delivery_assigned_details->'$.star_rate') as review_star,IFNULL(JSON_UNQUOTE(delivery_assigned_details->'$.review'),'null') as review,date(order_date) as order_date FROM `order_master` WHERE   delivery_assigned_to = '".trim($dlvstaffid)."' and (JSON_UNQUOTE(delivery_assigned_details->'$.star_rate') IS NOT NULL) and date(order_date) >= '".$from_date."' and date(order_date) <= '".$to_date."' and (date(order_date) <= NOW() - INTERVAL 1 DAY) order by order_date desc limit $first,$last");
		
		if($pagecount ==1)
		{
			$totaldetail = DB::SELECT("SELECT JSON_UNQUOTE(delivery_assigned_details->'$.star_rate') as review_star,IFNULL(JSON_UNQUOTE(delivery_assigned_details->'$.review'),'null') as review,date(order_date) as order_date FROM `order_master` WHERE   delivery_assigned_to = '".trim($dlvstaffid)."' and (JSON_UNQUOTE(delivery_assigned_details->'$.star_rate') IS NOT NULL) and date(order_date) >= '".$from_date."' and date(order_date) <= '".$to_date."' and (date(order_date) <= NOW() - INTERVAL 1 DAY) order by order_date desc");
			$totalcount = ceil(count($totaldetail)/$limit);
		
		}
		else if($pagecount >1)
		{
			$totalcount = 0;
		}
		
		
		if(count($detail)>0) {
			$msg = 'Exist';
		}
		else
		{
			$msg ='Result Not Exist';
		}
        return response::json(['msg'=>$msg,'data' => $detail,'totalcount' =>(string)$totalcount]);

        
    }


    public function mart_force_update(Request $request)
    {
   
        $msg = 'no_force_update';
        return response::json(['msg' => $msg,'version' => '1','message' => '']);


		$setting = GeneralSetting::where('id','1')->select('rest_app_currnt_version','rest_app__custom_msg','rest_force_update')->first();
        if(count($setting)>0)
        {
            if(strtoupper($setting['rest_force_update']) == 'Y')
            {
                $msg = 'Exist';
                return response::json(['msg' => $msg,'version' => $setting['rest_app_currnt_version'],'message' => $setting['rest_app__custom_msg']]);
            }
            else if(strtoupper($setting['rest_force_update']) == 'N')
            {
                $msg = 'no_force_update';
                return response::json(['msg' => $msg,'version' => $setting['rest_app_currnt_version'],'message' => $setting['rest_app__custom_msg']]);
            }
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }
    function remove_rest_login(Request $request)
    {//`id`, `login_group`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`, `method`, `explore`, `explore2`, `staffid`, `restaurant_id`, `modules`, `ftoken`, `token`, `role`, `active` FROM `users`
        $staffid = $request['id'];
        
       DB::SELECT("DELETE FROM `users_modules` WHERE `user_id`= ".$staffid."");
        DB::SELECT("DELETE FROM `users` WHERE id= ".$staffid."  ");
        $msg="deleted";
        return response::json(['msg' => $msg]);
    }





    public function restaurant_readytopick(Request $request)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $time = strtoupper($date->format('H:i a'));
        $id = $request['restaurantid'];
        $key = $request['key'];
        $orderno = trim($request['orderno']);
        $user_id = $request['user_id'];
        $details = Commonsource::checkrestaurantvalidity($id, $key);
        if ($details == 'Exist')
        {
            $list = DB::SELECT('select customer_id from `order_master` where rest_id = "'.$id.'" and order_number="'.$orderno.'"');
            if(count($list)>0)
            {
                DB::UPDATE('update order_master set readytopick_by = "'.$user_id.'",readytopick ="Y",readytopick_time="'.$time.'" where order_number = "'.$orderno.'"');
				$msg = 'Success';
            }
            else
            {
                   $msg = 'Order Number Does Not Exist';
            }
            return response::json(compact('msg'));
        }
        else
        {
            $msg = $details;
            return response::json(compact('msg'));
        } 
    }
	
	
	


}

