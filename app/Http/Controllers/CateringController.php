<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Session;
use Helpers\Datasource;
use App\Http\Requests;
use Image;
use App\Cat_category;
use App\Cat_banner;
use App\Cat_city;
use App\Cat_pincode;
use App\Cat_Restaurant_Master;
use App\Cat_tax;
use App\GeneralSetting;
use Response;
use Helpers\Commonsource;
use DateTime;
use DateTimeZone;


class CateringController extends Controller {

    /***********************category starts***************************************/
    //view catering page
    public function view_category(Request $request) {
        $filterarr = array();
        $rows = $this->cat_list();
        return view('catering.category', compact('rows', 'filterarr'));
    }

    // add catering
    public function add_category(Request $request) 
            {
        
        $img = Input::file('cat_icon');
        $type = $request['type'];
        $url = Datasource::geturl();
        $timeDate = date("jmYhis") . rand(991, 9999);
        $date = date('Y-m-d');
        if($img == "")
        {
            $image_url = null;
        }
        else
        {
            $uploadfile = $timeDate . '.' .strtolower($img->getClientOriginalExtension());
            Image::make($img)->resize(225, 225)->save(base_path() . '/uploads/catering/category/' . $uploadfile);
            $image_url = 'uploads/catering/category/' . $uploadfile;
        }
        //return $image_url."  ".$request['cat_cat'];
        if ($type == 'insert') {
            $catlist = Cat_category::where('cc_name', $request['cat_cat'])
                    ->get();
            if (count($catlist) > 0) {

                $msg = 'already exist';
                $rows = $this->cat_list();
                return response::json(compact('msg', 'rows'));
            } else {
                $catgy = new Cat_category();
                $catgy->cc_name = ucwords(strtolower($request['cat_cat']));
                $catgy->cc_icon=$image_url;
                $catgy->save();

                $msg = 'success';
                $rows = $this->cat_list();
                return response::json(compact('msg', 'rows'));
            }

            return redirect('manage_category');
        } 
    } // edit catering
    public function edit_category(Request $request) 
            {
        
        $img = Input::file('cat_icon_edit');
        $type = $request['type_edit'];
        $url = Datasource::geturl();
        $timeDate = date("jmYhis") . rand(991, 9999);
        $date = date('Y-m-d');
        if($img == "")
        {
            $image_url = null;
        }
        else
        {
            $uploadfile = $timeDate . '.' .strtolower($img->getClientOriginalExtension());
            Image::make($img)->resize(225, 225)->save(base_path() . '/uploads/catering/category/' . $uploadfile);
            $image_url = 'uploads/catering/category/' . $uploadfile;
        }
        //return $image_url."  ".$request['cat_cat'];
         if ($type == 'update') {

            $ctys = Cat_category::where('cc_name', $request['cat_cat_edit'])
                    ->where('cc_id', '!=', $request['userid_edit'])
                    ->first();
            if (count($ctys) > 0) {

                $msg = 'exist';
                $rows = $this->cat_list();
                return response::json(compact('msg', 'rows'));
            } else {
                Cat_category::where('cc_id', $request['userid_edit'])->update(
                        ['cc_name' => ucwords(strtolower($request['cat_cat_edit'])),
                            'cc_icon' => $image_url,
                            'cc_status' => $request['status_edit']
                ]);

                $msg = 'done';
                $rows = $this->cat_list();
                return response::json(compact('msg', 'rows'));
            }
            return redirect('manage_category');
        }
    }

    public function cat_list() {
        $rows = Cat_category::select('cc_id', 'cc_name', 'cc_icon', 'cc_status')
                ->orderBy('cc_id', 'desc')
                ->get();
        return $rows;
    }
/***********************banner starts***************************************/
    public function view_banner(Request $request)
    {
        $banner = $this->cat_banner_list();
        return view('catering.banner_index',compact('banner'));
    }
    
     public function cat_banner_list()
    {
         //`cat_banner`(`id`, `app_banner`, `display_order`, `entry_date`, `geo_location`, `geo_cordinates`, `geo_visible_range`)
        $banner = Cat_Banner::where('id','!=','')->select('app_banner','id','display_order')->get();
        return $banner;
    }
    //view banner add page
    public function add_banners(Request $request)
    {
        return view('catering.banner_app_add');
    }
     //banner submit
    public function app_banner_submit(Request $request)
    {
     

        //save App banner image
        $data2 = $request['img1'];
        $range = $request['range'];
        $geolocation = $request['lat'].','.$request['long'];
        $google_location= $request['geo_location'];
        $ext2 = explode(';',explode('/',$data2)[1]);
        $extension2 = $ext2[0];
        $url2 = "banner-".time().".".$extension2;
        $path1 = 'uploads/catering/banner/app/' . $url2;
        $img1 = str_replace('data:image/'.$extension2.';base64,', '', $data2);
        $img1 = str_replace(' ', '+', $img1);
        $base_data2 = base64_decode($img1);
        //file_put_contents( base_path().'/'.$path1, $base_data2);
       
        
        
                $maxDimW = 858;
                $maxDimH = 480;
                list($width, $height, $type, $attr) = getimagesize( $request['img1'] );
                if ( $width > $maxDimW || $height > $maxDimH ) {
                    $target_filename = $url2;
                    $fn = $request['img1'];
                    $size = getimagesize( $fn );
                    //$ratio = $size[0]/$size[1]; // width/height
                    //if( $ratio > 1) {
                        $width = $maxDimW;
                        //$height = $maxDimH/$ratio;
                    //} else {
                    //    $width = $maxDimW*$ratio;
                        $height = $maxDimH;
                    //}
                    $src = imagecreatefromstring(file_get_contents($fn));
                    $dst = imagecreatetruecolor( $width, $height );
                    //$white = imagecolorallocate($src, 255, 255, 255);
                    //imagecolortransparent($src, $white);
                    imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1] );

                    imagejpeg($dst, 'uploads/catering/banner/app/'.$target_filename); // adjust format as needed

 $status = 'success';
        $response = array('status' => 'success');
                }
               // file_put_contents( base_path().'/'.$path1, $base_data2);
               //move_uploaded_file($request['img1'],base_path().'/'.$path1);
        
        

        // `cat_banner`(`id`, `app_banner`, `display_order`, `entry_date`, `geo_location`, `geo_cordinates`, `geo_visible_range`)
        $cat_banner=new Cat_banner();
        $cat_banner->app_banner =$path1;
       // $cat_banner->web_banners = 'uploads/banner/app/dummy.jpg';
        $cat_banner->geo_cordinates = $geolocation;
        $cat_banner->geo_location = $google_location;
        $cat_banner->geo_visible_range = $range;
        $cat_banner->entry_date = date('Y-m-d');
        $cat_banner->save();
        return redirect('manage_banner')->with('status');
    }
    //banner order edit
    public function cat_banner_order($id,$val)
    {
        Cat_banner::where('id',trim($id))->update(['display_order' =>$val]);
        $banners = $this->cat_banner_list();
        $msg = 'editted';
        return response::json(['msg' => $msg,'banners' => $banners]);
    }
     //banner image delete
    public function cat_banner_delete($id)
    {
        /* $catlist = Cat_banner::where('id', $id)
                    ->get();
         if (count($catlist) > 0) {
            
                   $file_path = base_path() . '/' . $catlist['app_banner'];
                   echo $file_path;
                  unlink($file_path);
         }*/
         DB::select('delete from cat_banner where id="'.$id.'"');
        $banners = $this->cat_banner_list();
        $msg = 'deleted';
        return response::json(['msg' => $msg,'banners' => $banners]);
    }
    
    
    
    public function category_cat(Request $request)
    {
		$categoryarr =array();
        $category = DB::SELECT("select cc_id as id, cc_name as name,IFNULL(cc_icon,'') as icon  from cat_category where  cc_status = 'Active' order by cc_display_order asc");
        
        $catering_helpdesk = DB::SELECT("select IFNULL(g.catering_helpdesk,'') as catering_helpdesk from general_settings g");
        
        if(count($category)>0)
        {
            $msg = "Exist";
        }
        else
        {
            $msg = 'Not Exist';
        }
        return response::json(['msg' => $msg, 'catering_helpdesk' => $catering_helpdesk[0]->catering_helpdesk,'category' => $category]);
		return 'success';
    
    }
	
	
	
	public function citylist(Request $request)
    {
        $city = DB::SELECT("SELECT id,city as cityname FROM cat_city order by city ");
        if(count($city)>0)
        {
            $msg = "Exist";
        }
        else
        {
            $msg = 'Not Exist';
        }
        return response::json(['msg' => $msg,'city_data' => $city]);
		return 'success';
    
    }
	
	
	public function city_pincodes(Request $request)
    {  
	    $city_id = $request['city_id'];
        $pincode = DB::SELECT("SELECT sl_no as slno ,pincode,name   FROM `cat_city_pincodes` WHERE city_id = '".($city_id)."' order by name ");
        if(count($pincode)>0)
        {
            $msg = "Exist";
        }
        else
        {
            $msg = 'Not Exist';
        }
        return response::json(['msg' => $msg,'pincodes_data' => $pincode]);
		return 'success';
    
    }
	
	
	
    
    
    
    /***********************City starts***************************************/
    //INSERT INTO `module_master` (`m_id`, `module_name`, `sub_module`, `page_link`, `module_for`, `display_order`) VALUES (NULL, 'Catering', 'City', 'manage_city', 'C', '4');
    //UPDATE `users_modules` SET `active`='Y' WHERE `module_id`='20'
    //view area page
    public function view_city(Request $request)
    {
        $filterarr = array();
        $rows=$this->cat_citylist();
        return view('catering.city',compact('rows','filterarr'));
    }
    public function cat_citylist()
    {
        $rows=Cat_city::select('id','city')
            ->orderBy('id','desc')
            ->get();
        return $rows;
    }
    // add/ edit city
    public function add_city(Request $request)
    {
        $type =$request['type'];
        if($type == 'insert')
        {
            $arealist = Cat_city::where('city',$request['area'])
                        ->get();
            if(count($arealist)>0)
            {

                $msg = 'already exist';
                $rows=$this->cat_citylist();
                return response::json(compact('msg','rows'));
            }
            else
            {
                $cty= new Cat_city();
                $cty->city = ucwords(strtolower($request['area']));
                $cty->save();

                $msg = 'success';
                $rows=$this->cat_citylist();
                return response::json(compact('msg','rows'));
            }

            return redirect('area');
        }

        else if($type == 'update')
        {

            $ctys = Cat_city::where('city',$request['area'])
                ->where('id','!=',$request['userid'])
                ->first();
            if(count($ctys)>0)
            {

                $msg = 'exist';
                $rows=$this->cat_citylist();
                return response::json(compact('msg','rows'));
            }
            else {
                Cat_city::where('id', $request['userid'])->update(
                    ['city' => ucwords(strtolower($request['area']))
                    ]);

                $msg = 'done';
                $rows=$this->cat_citylist();
                return response::json(compact('msg','rows'));
            }
            return redirect('city');
        }
    }
     public function cat_city_delete($id)
    {
        /* $catlist = Cat_banner::where('id', $id)
                    ->get();
         if (count($catlist) > 0) {
            
                   $file_path = base_path() . '/' . $catlist['app_banner'];
                   echo $file_path;
                  unlink($file_path);
         }*/
         DB::select('delete from cat_city where id="'.$id.'"');
        $rows = $this->cat_citylist();
        $msg = 'deleted';
        //return response::json(['msg' => $msg,'banners' => $banners]);
         return response::json(compact('msg','rows'));
    }
    
    //View pincode
    public function view_pincode($id)
    {
//`city_id`, `sl_no`, `pincode`, `name` FROM `cat_city_pincodes`
       //echo  $id = $request['id'];
        //echo 'SELECT * FROM cat_city_pincodes where city_id = "'.$id.'" ';
        $day = DB::SELECT('SELECT * FROM cat_city_pincodes where city_id = "'.$id.'" ');
        $m=0;
        $append = '';
        $count = count($day);
        foreach($day as $key=>$mnth)
        {
                    $key =$key+1;
                    
                    $append .=  "<tr class='timeappend'>";
                    $append .=  "<td style='width:100px'>". $mnth->sl_no."</td>";
                    $append .=  "<td style='width:90px'>".$mnth->pincode."</td>";
                    $append .=  "<td style='width:90px'>".$mnth->name."</td>";
                    $append .=  "<td style='width:40px'><a class='btn button_table' onclick=\"deletepincode('$id','$mnth->sl_no')\";><i class='fa fa-trash'></i></a></td>";
                    $append .=  "</tr>";
        }
        return $append ;
    }
    // add pincode
    public function add_pincode(Request $request)
    {
        $pincode =$request['pincode'];
        $pinname =$request['pinname'];
        $cityid =$request['cityid'];
       // echo 'SELECT * FROM cat_city_pincodes where city_id = "'.$cityid.'"  and pincode="'.$pincode.'"';
        $pincodelist = DB::SELECT('SELECT * FROM cat_city_pincodes where city_id = "'.$cityid.'"  and pincode="'.$pincode.'"');
        
                  
            if(count($pincodelist)>0)
            {

                $msg = 'already exist';
                $rows=$this->cat_pincodelist($cityid);
                return response::json(compact('msg','rows'));
            }
            else
            {   //`cat_city_pincodes`(`city_id`, `sl_no`, `pincode`, `name`)
               // echo "ye";
               /* $cty= new Cat_pincode();
                $cty->city_id = $request['cityid'];
                $cty->sl_no = 3;
                $cty->pincode = $request['pincode'];
                $cty->name = $request['pinname'];
                $cty->save();*/
                
                
                 DB::SELECT('INSERT INTO cat_city_pincodes(city_id,pincode,name) VALUES ("'.$cityid.'","'.$pincode.'","'.$pinname.'")');
                $msg = 'success';
                $rows=$this->cat_pincodelist($cityid);
                return response::json(compact('msg','rows'));
            }

            //return redirect('city');
    
    }
    public function cat_pincodelist($cityid)
    {
       
        $rows=DB::select('SELECT * FROM cat_city_pincodes where city_id = "'.$cityid.'"');
        
        return $rows;
    }
    
    public function cat_pin_delete($id,$slno)
    {
      
         DB::select('delete from cat_city_pincodes where city_id="'.$id.'" and sl_no ="'.$slno.'"');
         $msg = 'deleted';
        $rows = $this->cat_pincodelist($id);
                return response::json(compact('msg','rows'));
       
    }
	

	
	//////***********************restaurant starts***********************
   // INSERT INTO `module_master` (`m_id`, `module_name`, `sub_module`, `page_link`, `module_for`, `display_order`) VALUES (NULL, 'Catering', 'Restaurant', 'catering_restaurant', 'C', '4');
	//UPDATE `users_modules` SET `active` = 'Y' WHERE `users_modules`.`user_id` = 1 AND `users_modules`.`module_id` = 22;
        //ALTER TABLE `cat_restaurants` ADD `cr_status_changeby` VARCHAR(50) NULL AFTER `cr_status`;
	//View Manage Restaurant Page
    
    
    public function view_restaurant(Request $request)
    {
          $staffid = Session::get('staffid');
          $logingroup = Session::get('logingroup');
        $filterarr = array();
        $category = $this->cat_list();
        
            $details = DB::SELECT('SELECT * FROM `cat_restaurants` ORDER BY cr_display_order ASC');
          return view('catering.catering_restaurant',compact('details','logingroup','category'));
    }
    public function view_restaurantdetails(Request $request)
    {
        $filterarr = array();
        return view('catering.cat_restaurant_details');
    }
    //Restaurant order edit
    public function rest_disporder($id,$val)
    {
        Cat_Restaurant_Master::where('cr_id',trim($id))->update(['cr_display_order' =>$val]);
        $details = $this->view_restaurant();
        $msg = 'editted';
        return response::json(['msg' => $msg,'details' => $details]);
    }
    public function restaurant_status(Request $request)
    {
        $userid = Session::get('setuserid');
        $restaurant = Cat_Restaurant_Master::where('cr_id','=',$request['ids'])
            ->where('cr_status','=','Active')
            ->get();
        if(count($restaurant)>0)
        {
            Cat_Restaurant_Master::where('cr_id', $request['ids'])->update(
                [
                    'cr_status' => 'InActive','cr_status_changeby'=>$userid,
                ]);
        }
        else
        {
            Cat_Restaurant_Master::where('cr_id', $request['ids'])->update(
                [
                    'cr_status' => 'Active','cr_status_changeby'=>$userid,
                ]);
        }
    }
    //add catering restaurant
     public function add_catrestaurant(Request $request)
    {
        $logo = Input::file('logo');
        //$banner = Input::file('banner');
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
            Image::make($logo)->resize(300, 300)->save(base_path() . '/uploads/catering/logo/' . $uploadfile);
            $image_url = 'uploads/catering/logo/' . $uploadfile;
        }
        //rest_fromhour rest_fromminut rest_tohour rest_tominut
        $fromtime=$request['rest_fromhour'].":".$request['rest_fromminut'].":00";
        $totime=$request['rest_tohour'].":".$request['rest_tominut'].":00";
        
           /* $dietsave = $request['dietsave'];
            if($dietsave == "false")
            {
                $diet = 'Y';
            }
            else
            {
                $diet = 'N';
            }*/
       
                DB::INSERT("INSERT INTO `cat_restaurants`(`cr_name`, `cr_cusines`, `cr_custom_message`,  `cr_veg_only`, `cr_pic`,  `cr_address`,  `cr_display_order`, `cr_status`,  `cr_owner_name`, `cr_owner_contact`, `cr_manager_name`, `cr_manager_contact`, `cr_office_contact`, `cr_fssai`, `cr_gstin`, `cr_bank_name`, `cr_bank_account_name`, `cr_bank_account_no`, `cr_working_from_time`, `cr_working_to_time`, `cr_service_del`, `cr_preparation_day_count`, `cr_catering`, `cr_max_order_per_day`) "
                        . "VALUES ('".$request['rest_name']."','".$request['rest_cusines']."','".$request['rest_custmsg']."','".$request['rest_veg']."','".$image_url."','".$request['rest_address']."','".$request['rest_disporder']."','".$request['rest_status']."','".$request['rest_owner']."','".$request['rest_ownermobile']."','".$request['rest_manager']."','".$request['rest_manmobile']."','".$request['rest_ofcmobile']."','".$request['rest_fssai']."','".$request['rest_gstin']."','".$request['rest_bankname']."','".$request['rest_bankaccount']."','".$request['rest_bankaccountnmbr']."','".$fromtime."','".$totime."','".$request['rest_serdelivry']."','".$request['rest_prepdayct']."','".$request['rest_catering']."','".$request['rest_maxorder']."')");
         
            $msg = 'success';
            return response::json(compact('msg'));
        return redirect('catering.catering_restaurant');

    }
     public function cat_restaurant_edit($id)
    {
        $restaurantdetail = DB::SELECT('SELECT * FROM `cat_restaurants`  WHERE cr_id = "'.$id.'"');
        $siteurl = Datasource::getsiteurl();
        return view('catering.cat_restaurant_edit',compact('restaurantdetail','id','siteurl'));
    }
     public function edit_catrestaurant(Request $request)
    {
         $rid = $request['edrid'];
         $logo = Input::file('logo');
        //$banner = Input::file('banner');
        $url = Datasource::geturl();
        $timeDate = date("jmYhis") . rand(991, 9999);
        $date = date('Y-m-d');
         if (Input::file('logo') != '') {
            $image = Input::file('logo');
            $uploadfile = time() . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(260, 200)->save(base_path() . '/uploads/catering/logo/' . $uploadfile);
            if ($request['oldlogo'] == '') {
                $logo = 'uploads/catering/logo/' . $uploadfile;

//                $file_path = base_path() . '/' . $request['oldlogo'];
//                unlink($file_path);
            }
            $logo = 'uploads/catering/logo/' . $uploadfile;
        } elseif($request['oldlogo'] != '')
        {
            $logo = $request['oldlogo'];
        }
        else
        {
            $logo = '';
        }
      /*  $diet='';
            $dietsave = $request['dietsave'];
            if($dietsave == "false")
            {
                $diet = 'Y';
            }
            else
            {
                $diet = 'N';
            }*/
       $fromtime=$request['rest_fromhour'].":".$request['rest_fromminut'].":00";
        $totime=$request['rest_tohour'].":".$request['rest_tominut'].":00";
                DB::SELECT("UPDATE `cat_restaurants` SET cr_name='".$request['rest_name']."',`cr_cusines`='".$request['rest_cusines']."', `cr_custom_message`='".$request['rest_custmsg']."',  `cr_veg_only`='".$request['rest_veg']."', `cr_pic`='".$logo."', `cr_address`='".$request['rest_address']."', `cr_display_order`='".$request['rest_disporder']."', `cr_status`='".$request['rest_status']."',  `cr_owner_name`='".$request['rest_owner']."', `cr_owner_contact`='".$request['rest_ownermobile']."', `cr_manager_name`='".$request['rest_manager']."', `cr_manager_contact`='".$request['rest_manmobile']."', `cr_office_contact`='".$request['rest_ofcmobile']."', `cr_fssai`='".$request['rest_fssai']."', `cr_gstin`='".$request['rest_gstin']."', `cr_bank_name`='".$request['rest_bankname']."', `cr_bank_account_name`='".$request['rest_bankaccount']."', `cr_bank_account_no`='".$request['rest_bankaccountnmbr']."', `cr_working_from_time`='".$fromtime."', `cr_working_to_time`='".$totime."', `cr_service_del`='".$request['rest_serdelivry']."', `cr_preparation_day_count`='".$request['rest_prepdayct']."', `cr_catering`='".$request['rest_catering']."', `cr_max_order_per_day`='".$request['rest_maxorder']."' WHERE `cr_id` = '$rid'");
         
            $msg = 'success';
            return response::json(compact('msg'));
        return redirect('catering.catering_restaurant');
     }
     public function cat_rest_category($id){
        $filterarr = array();
        $user_id =  Session::get('staffid');
        $restaurant_id = $id;
        $restaurant_name = DB::SELECT('SELECT cr_name as name FROM `cat_restaurants` WHERE `cr_id` = "'.$restaurant_id.'"' );
         $category = $this->cat_list();
        $details = DB::SELECT('SELECT cc.cc_name as categoryname,cc.cc_id as cid FROM `cat_rstrnt_categories` as crc JOIN cat_category as cc WHERE crc.`cr_cat_id`=cc.cc_id and crc.cr_rest_id= "'.$restaurant_id.'"' );
        return view('catering.rest_category',compact('details','restaurant_id','restaurant_name','user_id','category'));
    }
    public function cat_restcatgy_delete($id,$restid)
    {
        
         DB::select('delete from cat_rstrnt_categories where cr_cat_id="'.$id.'" and cr_rest_id="'.$restid.'"');
       // $banners = $this->cat_banner_list();
        $msg = 'deleted';
        return response::json(['msg' => $msg]);
    }
    public function add_restcategory($id,$restid)
    {
        $restid = $restid;
        $catid = $id;
        $checkexist = DB::SELECT('select * from cat_rstrnt_categories where cr_cat_id="'.$catid.'" and cr_rest_id="'.$restid.'"');
            if(count($checkexist)>0)
            {
                $msg = 'already exist';
            }else
            {
                
                DB::select('INSERT INTO cat_rstrnt_categories (cr_cat_id,cr_rest_id) values ("'.$catid.'" ,"'.$restid.'")');
                $msg = 'success';
            }
        
        return response::json(['msg' => $msg]);
    }
    public function cat_rest_pincode($id){
        $filterarr = array();
        $user_id =  Session::get('staffid');
        $restaurant_id = $id;
        $restaurant_name = DB::SELECT('SELECT cr_name as name FROM `cat_restaurants` WHERE `cr_id` = "'.$restaurant_id.'"' );
        // $category = $this->cat_list();
        $rows=$this->cat_citylist();
        $details = DB::SELECT('SELECT pincodes FROM `cat_rstrnt_pincodes` WHERE `rest_id`= "'.$restaurant_id.'"' );
        return view('catering.rest_pincode',compact('details','restaurant_id','restaurant_name','user_id','rows'));
    }
    public function cat_restpincode_delete($id,$restid)
    {
        
         DB::select('delete from cat_rstrnt_pincodes where pincodes="'.$id.'" and rest_id="'.$restid.'"');
       // $banners = $this->cat_banner_list();
        $msg = 'deleted';
        return response::json(['msg' => $msg]);
    }
    public function load_catpincodes($id)
    {
       $loadpincode = DB::SELECT('SELECT * FROM `cat_city_pincodes` WHERE `city_id`="'.$id.'"'); 
      // $day = DB::SELECT('SELECT * FROM cat_city_pincodes where city_id = "'.$id.'" ');
        $m=0;
        $append = '';
        $count = count($loadpincode);
        if($count>0)
        {
            $append .=  "<select id='rest_pincode' name ='rest_pincode' class='form-control'  >";
            $append .=  "<option value='all'>All</option>";
            foreach($loadpincode as $key=>$mnth)
            {              
                $append .=  "<option value='".$mnth->pincode."'>".$mnth->pincode."</option>";
            }
             $append .=  "</select>";
        }
        else
        {
            $append ="No records found";
        }
        return $append ;
    }
    public function add_restpincode(Request $request)
    {//ALTER TABLE `cat_rstrnt_pincodes` ADD `city_id` INT(11) NOT NULL AFTER `rest_id`;
        //restid rest_cityid rest_pincode
        $restid = $request['restid'];
        $cityid = $request['rest_cityid'];
        $pincode = $request['rest_pincode'];
      
        if($pincode!="all")
            {
                $checkexist = DB::SELECT('select * from cat_rstrnt_pincodes where pincodes="'.$pincode.'" and rest_id="'.$restid.'"');
                if(count($checkexist)>0)
                {
                    $msg = 'already exist';
                }else
                {

                    DB::select('INSERT INTO cat_rstrnt_pincodes (rest_id,pincodes) values ("'.$restid.'","'.$pincode.'" )');
                    $msg = 'success';
                }
            }else
            {
                DB::select('DELETE FROM cat_rstrnt_pincodes WHERE rest_id = "'.$restid.'"  and  pincodes in (SELECT pincode FROM cat_city_pincodes WHERE city_id =  "'.$cityid.'" )  ');
                DB::select('INSERT INTO `cat_rstrnt_pincodes`(`rest_id`, `city_id`,`pincodes`)  SELECT DISTINCT "'.$restid.'","'.$cityid.'",pincode FROM cat_city_pincodes WHERE city_id =  "'.$cityid.'"');
                $msg ='success';
            } 
        //$msg =$restid ."-".$cityid."-".$pincode;
        return response::json(['msg' => $msg]);
    }
      //Filtering of Manage Restaurant
    public function filter_catrestaurant(Request $request)
    {//restaurant_name  category
        $staffid = $request['staff_id'];
        $search = '';
        $restaurant_name = $request['restaurant_name'];
        $category = $request['category'];
        
        if(isset($restaurant_name) && $restaurant_name != '')
        {
            if($search == "")
            {
                $search.="  cat_restaurants.cr_name   LIKE '%".strtolower($restaurant_name)."%'";
            }
            else
            {
                $search.=" and  cat_restaurants.cr_name   LIKE '%".strtolower($restaurant_name)."%'";
            }
        }
       
       
        if(isset($category) && $category != '')
        {
            if($search == "")
            {
                $search.= "  cat_category.cc_id =   '".$category."'";
            }
            else
            {
                $search.= " and cat_category.cc_id =   '".$category."'";
            }
        }
        if($search!="")
        {
            $search="where $search  ";
        }
        else
        {
            $search ="";
        }
          /*$details = DB::SELECT('SELECT popular_display_order,name_tagline->>"$.name" as name,mobile->>"$.ind" as code,star_rating->>"$.value" as value,mobile->>"$.mobile" as mob,point_of_contact,star_rating,busy,restaurant_master.id,
                               min_cart_value,extra_rate_percent,phone,pure_veg,force_close,users.login_group as close_login_group,(select login_group from users where users.id = restaurant_master.busy_by ) as busy_login_group  FROM `restaurant_master` left join `users` on restaurant_master.forceclose_by = users.id '.$search.' `restaurant_master`.`id` != " " and restaurant_master.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = "'.$staffid .'") ORDER BY id');*/
        //return $details;
        // $category = $this->cat_list();//
          //$details = DB::SELECT('SELECT * FROM `cat_restaurants` '.$search.' ORDER BY cr_display_order ASC');
          
          
         $details = DB::SELECT(' SELECT cat_category.cc_id as cc_id,cat_restaurants.cr_id as cr_id,
       cat_restaurants.cr_name as cr_name,
       cat_restaurants.cr_min_pax as cr_min_pax,
       cat_restaurants.cr_display_order as cr_display_order,
       cat_restaurants.cr_status as cr_status,
       cat_restaurants.cr_min_rate as cr_min_rate,
       cat_restaurants.cr_max_rate as cr_max_rate
  FROM (potafo.cat_rstrnt_categories cat_rstrnt_categories
        INNER JOIN potafo.cat_category cat_category
           ON (cat_rstrnt_categories.cr_cat_id = cat_category.cc_id))
       INNER JOIN potafo.cat_restaurants cat_restaurants
          ON (cat_rstrnt_categories.cr_rest_id = cat_restaurants.cr_id)
  '.$search.' GROUP BY cat_restaurants.cr_id');
          
          //return view('catering.catering_restaurant',compact('details','logingroup','category','restaurant_name'));
            return $details;
    }
    //filter pincodes
 public function filter_catpincodes(Request $request)
    {//"restid":restid,"rest_cityid":cityid,"rest_pincode":pincode
        $restid = $request['restid'];
        $search = '';
        $rest_cityid = $request['rest_cityid'];
        $rest_pincode = $request['rest_pincode'];
        
        if(isset($rest_cityid) && $rest_cityid != '')
        {
            if($search == "")
            {
                $search.="  city_id ='".$rest_cityid."'";
            }
            else
            {
                $search.=" and  city_id ='".$rest_cityid."'";
            }
        }
       
       
        if(isset($rest_pincode) && $rest_pincode != '')
        {
            if($search == "")
            {
                $search.= "  pincodes LIKE '%".$rest_pincode."%'";
            }
            else
            {
                $search.= " and pincodes LIKE '%".$rest_pincode."%'";
            }
        }
        if($search!="")
        {
            $search="where $search  and ";
        }
        else
        {
            $search ="Where";
        }
          $details = DB::SELECT('SELECT pincodes FROM `cat_rstrnt_pincodes` '.$search.' `rest_id`= "'.$restid.'" ');
            return $details;
    }
    public function cat_rest_tax($id)
    {
        $resid = $id;
        $restaurant_name = DB::SELECT('SELECT cr_name as name FROM `cat_restaurants` WHERE `cr_id` = "'.$resid.'"' );
        $rows=DB::SELECT('SELECT * FROM `cat_rest_tax_master` WHERE `crt_rest_id` = "'.$resid.'"' );
        return view('catering.rest_tax',compact('rows','resid','restaurant_name'));
    }
public function add_rest_tax(Request $request)
    {
        $type =$request['type']; 
        $rid = $request['res_id'];
          // `cat_rest_tax_master`(`crt_rest_id`, `crt_slno`, `crt_name`, `crt_value`, `crt_status`)  
       if($type == 'insert')
        {
            
        $tax = Cat_tax::where('crt_name',$request['tax'])
              ->where('crt_rest_id','=',$rid)
            ->get();
        if(count($tax)>0)
        {
            $msg = 'already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $tax= new Cat_tax();
            $tax->crt_name = strtoupper($request['tax']);
            $tax->crt_value = $request['value'];
            $tax->crt_rest_id = $request['res_id'];
            $tax->save();
            $msg = 'success';
            return response::json(compact('msg'));
        }
       
//       return view('tax.taxpercentage',compact('msg'));
        }

        else if($type == 'update')
        {
            // `cat_rest_tax_master`(`crt_rest_id`, `crt_slno`, `crt_name`, `crt_value`, `crt_status`)
            Cat_tax::where('crt_rest_id', $request['res_id'])
                    ->where('crt_slno',$request['slno'])->update(
                    [
                     'crt_value' => $request['value'],
                     'crt_status' => $request['status']
                    ]);

            $msg = 'done';
            return response::json(compact('msg'));
     
        }
    }
	public function cat_tax_status(Request $request)
    {// `cat_rest_tax_master`(`crt_rest_id`, `crt_slno`, `crt_name`, `crt_value`, `crt_status`)
        $tax = Cat_tax::where('crt_rest_id','=',$request['rid'])
                       ->where('crt_slno','=',$request['slno'])
                      ->where('crt_status','=','Y')
                      ->get();
        if(count($tax)>0)
        {
            Cat_tax::where('crt_rest_id','=',$request['rid'])
                       ->where('crt_slno','=',$request['slno'])->update(
                [
                    'crt_status' => 'N'
                ]);
        }
        else
        {
            Cat_tax::where('crt_rest_id','=',$request['rid'])
                       ->where('crt_slno','=',$request['slno'])->update(
                [
                    'crt_status' => 'Y'
                ]);
        }

    }
    
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//////////// AASHISH APP FUNCTIONS
	
	public function peoplelimit_app(Request $request)
    {  
        $peoplelimit = DB::SELECT("SELECT IFNULL(min(m.mt_min_pax),1) as min_limit, IFNULL(max(m.mt_max_pax),1000) as max_limit FROM cat_menu_types m left join cat_restaurants r  on  m.mt_rest_id = r.cr_id WHERE m.mt_status = 'Active' and r.cr_status = 'Active'");
       
        //$peoplelimit = DB::SELECT("SELECT IFNULL(min(m.cr_min_pax),1) as min_limit, IFNULL(max(m.cr_min_pax),100) as max_limit FROM cat_restaurants m  WHERE m.cr_status = 'Active'");
		
		return response::json(['min_limit' => $peoplelimit[0]->min_limit,'max_limit' => $peoplelimit[0]->max_limit]);
    
    }
	
	
	public function cat_restaurants(Request $request)
    {  
	
	    $selecteddate = date('Y-m-d',strtotime($request['selecteddate']));
	    $people = $request['people'];
		$cityid = $request['cityid'];
		$pincode = $request['pincode'];
		$vegonly = $request['vegonly'];
		$cat_category_id = $request['cat_category_id'];
		
		
		
		
		/* RETURN "SELECT r.cr_id as id,r.cr_name as name,r.cr_cusines as cusines, r.cr_custom_message as custom_message, r.cr_min_pax as min_pax,concat(r.cr_min_rate,'-',r.cr_max_rate) as min_max_rate,'offer' as offer, IFNULL(JSON_UNQUOTE(cr_avg_rating->'$.value'),0) as star_vaue ,cr_pic as pic 
FROM cat_restaurants r  
left join cat_rstrnt_categories rc on rc.cr_rest_id = r.cr_id 
left join cat_rstrnt_pincodes rp on rp.rest_id = r.cr_id
where rc.cr_cat_id =  " . $cat_category_id . "  and rp.pincodes = '" . trim($pincode) . "'   and  r.cr_veg_only= '" . trim($vegonly) . "' and " . $people . " >= r.cr_min_pax order by r.cr_display_order asc";
 */
    $string = "";

    if($vegonly == 'Y')
    {
        $string .= "and  r.cr_veg_only = '" . trim($vegonly) . "'";
    }
    else 
    {
        $string .= " ";
    }
      $restaurants = DB::SELECT("SELECT r.cr_id as id,r.cr_name as name,r.cr_cusines as cuisines, IFNULL(r.cr_custom_message,'') as custom_message, r.cr_min_pax as min_pax,concat(r.cr_min_rate,'-',r.cr_max_rate) as min_max_rate,'offer' as offer, IFNULL(JSON_UNQUOTE(cr_avg_rating->'$.value'),0) as star_vaue ,IFNULL(cr_pic,'') as pic 
FROM cat_restaurants r  
left join cat_rstrnt_categories rc on rc.cr_rest_id = r.cr_id 
left join cat_rstrnt_pincodes rp on rp.rest_id = r.cr_id
where rc.cr_cat_id =  " . $cat_category_id . "  and rp.pincodes = '" . trim($pincode) . "' $string and " . $people . " >= r.cr_min_pax order by r.cr_display_order asc");
          if(count($restaurants)>0)
        {
            $msg = "Exist";
        }
        else
        {
            $msg = 'Not Exist';
        }
		
		return response::json(['msg' => $msg,'restaurants_data' => $restaurants]);
    
    }
	
	
	
		
	public function cat_rest_menu_types(Request $request)
    {  
	
		$rest_id = $request['rest_id'];

        $menu_types = DB::SELECT("SELECT mt_type_id as id, mt_type_name as name, mt_type_rate as rate , IFNULL(mt_description,'') as description, mt_min_pax as pax, IFNULL(mt_pic,'') as pic, mt_avg_rating as star_rating , 
        IFNULL(mt_special_text,'') as special_text, IFNULL(mt_offer_rate,'') as offer_rate FROM  cat_menu_types WHERE mt_rest_id =  " . $rest_id . "   and mt_status = 'Active' order by mt_display_order asc");
          if(count($menu_types)>0)
        {
            $msg = "Exist";
        }
        else
        {
            $msg = 'Not Exist';
        }
		
		return response::json(['msg' => $msg,'menu_types_data' => $menu_types]);
    
    }
	
	
	public function cat_cust_add_menu(Request $request)
    {  
	    $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i A');
		$rest_id = $request['rest_id'];
		$rest_cat_id = $request['rest_cat_id'];
		$menu_type_id = $request['menu_type_id'];
		$pax = $request['pax'];
		$customer_id = $request['customer_id'];

        $customer = DB::SELECT("SELECT * FROM cat_order_master WHERE com_customer_id = " .trim($customer_id) . " AND  com_order_rest_id  = " .$rest_id. " AND  com_menu_type_id  = " .$menu_type_id. " and com_order_status = 'T' ");
        $mobile_contact = DB::SELECT("SELECT `mobile_contact` FROM `customer_list` WHERE `id` =  " .trim($customer_id) . "");
		$contact = $mobile_contact[0]->mobile_contact;
		if(count($customer)<=0)
        {  
	    	DB::SELECT("DELETE FROM cat_order_master WHERE com_customer_id  = '$customer_id'  and com_order_status = 'T'");
			
			
			
			
			$rate_single = DB::SELECT("SELECT `mt_type_rate`,mt_offer_rate FROM `cat_menu_types` WHERE `mt_rest_id` = " .trim($rest_id) . " and `mt_type_id`  = " .trim($menu_type_id) . " ");
            $rate = $rate_single[0]->mt_type_rate; 
            $org_rate = $rate;   
            $offer_rate = $rate_single[0]->mt_offer_rate; 
            if($offer_rate>0)  
            {
                $rate = $offer_rate;   
            }
			
			
			
			DB::INSERT("INSERT INTO `cat_order_master`(`com_order_id`, `com_customer_id`, `com_reg_number`, `com_order_date`, `com_order_rest_id`, `com_menu_type_id`, `com_pax`, `com_single_rate`, `com_single_rate_org` )VALUES( 't_" .$contact . "','" .  $customer_id . "','" . $contact . "','" . $datetime . "','" . $rest_id . "','" . $menu_type_id . "','" . $pax . "','" . $rate. "','" . $org_rate. "')");
			
			
			
			DB::INSERT("INSERT INTO `cat_order_details`(`cod_order_id`, `cod_slno`, `cod_menu_cat_id`, `cod_menu_cat_name`, `cod_menu_id`, `cod_menu_name`, `cod_menu_details`, `cod_menu_selected`,cod_diet) 
			SELECT  't_" .$contact . "',0,m.cm_type_cat_id,c.mtc_name,m.cm_sl_no,m.cm_menu_name,m.cm_menu_details,'N',m.cm_diet
			FROM cat_menu m 
			left join  cat_menu_types_category c on c.mtc_id = m.cm_type_cat_id 
            where m.cm_rest_id = '" . $rest_id . "'  and m.cm_type_id = '" . $menu_type_id . "' and c.mtc_status = 'Active'");
            
            $updatemenu_selection = 'Y';

        }
        else 
        {
            $updatemenu_selection = 'N';
        }
		
	    $allcategory = DB::SELECT("SELECT DISTINCT c.cod_menu_cat_id as category_id, c.cod_menu_cat_name as category_name, m.mtc_max_item_count as max_count
        FROM cat_order_details c left join  cat_menu_types_category m  on m.mtc_id = c.cod_menu_cat_id WHERE c.cod_order_id = 't_" .$contact . "' order by m.mtc_display_order asc");
		
		foreach($allcategory as $key=>$item)
		{
            $category_id = $item->category_id;
			$category_name = $item->category_name;
			$max_count = $item->max_count;
			$menudetails = DB::SELECT("SELECT c.cod_menu_id AS menu_id, c.cod_menu_name AS menu_name, c.cod_menu_details AS details, cod_menu_selected as menu_selected ,cod_slno AS order_slno, cod_diet as diet FROM cat_order_details c WHERE c.cod_order_id = 't_" . $contact . "' and  cod_menu_cat_id = $category_id");
			if(count($menudetails)>0) 
			{
                if(count($menudetails) != $max_count)
                {
                   $is_choose = 'Y';
                   $category_name = 'Choose '.$category_name;
                   $Custom_msg = '(Select '.$max_count.' Item)';
                   if($updatemenu_selection == 'Y')
                   {
                    DB::SELECT("UPDATE cat_order_details SET cod_menu_selected = 'Y' WHERE cod_order_id = 't_" .$contact . "'  AND cod_menu_cat_id = '" . $category_id . "' LIMIT $max_count");
                   }
                }
                else 
                {
                    $is_choose = 'N';
                    $Custom_msg = '';
                    if($updatemenu_selection == 'Y')
                    {
                        DB::SELECT("UPDATE cat_order_details SET cod_menu_selected = 'Y' WHERE cod_order_id = 't_" .$contact . "'  AND cod_menu_cat_id = '" . $category_id . "'");
                    }
                }

				$arr[] = (object) array('type'=>'category','id'=>$category_id,'name'=>$category_name,'details'=>$Custom_msg,'is_choose'=>$is_choose,'menu_selected'=>'N','order_slno'=>'','diet' => '');
				foreach ($menudetails as $value) 
				{
                    $menu_items = DB::SELECT("SELECT cod_menu_selected AS menu_selected FROM cat_order_details c WHERE c.cod_order_id = 't_" . $contact . "' and  cod_menu_cat_id = $category_id and cod_slno = $value->order_slno");
                
                    $arr[] = (object)array('type' => 'menu', 'id' =>$category_id, 'name' => $value->menu_name,'details' => $value->details,'is_choose'=>$is_choose,'menu_selected'=>$menu_items[0]->menu_selected,'order_slno'=>$value->order_slno,'diet' => $value->diet);
			
				}
			}



		}
            return response::json(['msg'=>'Exist','menu_data' =>$arr]); 
		
		//return response::json(['msg' => $msg]);
    
    }
	
	
    
    



    public function menus_selection_validate(Request $request)
    {
        $customer_id = $request['customer_id'];
        $rest_id = $request['rest_id'];
        $menu_type_id = $request['menu_type_id'];
        $customer = DB::SELECT("SELECT * FROM cat_order_master WHERE com_customer_id = " .trim($customer_id) . " AND  com_order_rest_id  = " .$rest_id. " AND  com_menu_type_id  = " .$menu_type_id. " and com_order_status = 'T' ");
        $mobile_contact = DB::SELECT("SELECT `mobile_contact` FROM `customer_list` WHERE `id` =  " .trim($customer_id) . "");
		$contact = $mobile_contact[0]->mobile_contact;

        $allcategory = DB::SELECT("SELECT DISTINCT c.cod_menu_cat_id as category_id, c.cod_menu_cat_name as category_name, m.mtc_max_item_count as max_count
        FROM cat_order_details c left join  cat_menu_types_category m  on m.mtc_id = c.cod_menu_cat_id WHERE c.cod_order_id = 't_" .$contact . "' order by m.mtc_display_order asc");
		
		
		foreach($allcategory as $key=>$item)
		{
            $category_id = $item->category_id;
			$category_name = $item->category_name;
			$max_count = $item->max_count;
			$menudetails = DB::SELECT("SELECT cod_menu_selected as menu_selected FROM cat_order_details c WHERE c.cod_order_id = 't_" . $contact . "' and  cod_menu_cat_id = $category_id and cod_menu_selected = 'Y'");
			if(count($menudetails) != $max_count)
                {
                    $msg = 'Please select '.$max_count.' Items in '.$category_name.' For Proceeding.';
                    return response::json(compact('msg'));
                }
				else{
					$msg = 'Success';
				}
			
		}
        
        return response::json(compact('msg'));
    }


    public function pax_min_max(Request $request)
    {
        $menu_type_id = $request['menu_type_id'];
        $rest_id = $request['rest_id'];
  
        $pax_min_max = DB::SELECT("SELECT `mt_min_pax` as min_pax_limit, `mt_max_pax` as max_pax_limit FROM `cat_menu_types` WHERE mt_type_id =  " . $menu_type_id . "  and mt_rest_id  =  " . $rest_id . "");
        if(count($pax_min_max)>0)
        {
            $msg = "Exist";
        }
        else
        {
            $msg = 'Not Exist';
        }
		
		return response::json(['min_limit' => $pax_min_max[0]->min_pax_limit,'max_limit' => $pax_min_max[0]->max_pax_limit]);
    }


    public function menus_selection_update(Request $request)
    {
        $customer_id = $request['customer_id'];
        $order_slno = $request['order_slno'];
        $cat_id = $request['cat_id'];
        $update_status =  $request['update_status'];

        $mobile_contact = DB::SELECT("SELECT `mobile_contact` FROM `customer_list` WHERE `id` =  " .trim($customer_id) . "");
		$contact = $mobile_contact[0]->mobile_contact;
        
        $cat_max_count = DB::SELECT("SELECT DISTINCT m.mtc_max_item_count as max_count FROM cat_order_details c left join  cat_menu_types_category m  on m.mtc_id = c.cod_menu_cat_id WHERE c.cod_order_id = 't_" .$contact . "' and c.cod_menu_cat_id = " .$cat_id . "");
        $max_count = $cat_max_count[0]->max_count; 
        
        if($update_status== 'Y')
        {
            $menudetails = DB::SELECT("SELECT cod_menu_selected as menu_selected FROM cat_order_details c WHERE c.cod_order_id = 't_" . $contact . "' and  cod_menu_cat_id = $cat_id and cod_menu_selected = 'Y'");
			if((count($menudetails)+1) > $max_count)
            {
                $msg = 'Maximum '.$max_count.' Items Can Be Selected.';
                return response::json(compact('msg'));
            }
            $reverse_status = 'N';
        }
        else
        {
            $reverse_status = 'Y';
        } 
        
        //RETURN "UPDATE cat_order_details SET cod_menu_selected = '" .$update_status . "'  WHERE cod_order_id = 't_" .$contact . "'  AND cod_slno = " . $order_slno . "";
        DB::SELECT("UPDATE cat_order_details SET cod_menu_selected = '" .$update_status . "'  WHERE cod_order_id = 't_" .$contact . "'  AND cod_slno = " . $order_slno . "");
        //DB::SELECT("UPDATE cat_order_details SET cod_menu_selected = '" .$reverse_status . "'  WHERE cod_order_id = 't_" .$contact . "'  AND cod_slno != " . $order_slno . " AND  cod_menu_selected = '" .$update_status . "'  limit 1");
        
        $msg = 'Success';
        return response::json(compact('msg'));
    }


	
	public function catering_terms(Request $request)
    {  
	
		$rest_id = $request['rest_id'];
        $customer_id = $request['customer_id'];

        $catering_terms= DB::SELECT("SELECT ifnull(catering_terms,'')  as catering_terms  FROM `general_settings` ");
		return response::json(['msg' => $catering_terms[0]->catering_terms]);
    
    }
	
	public function cat_place_order(Request $request)
    {  
     
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d H:i:s');
        $time = strtoupper($date->format('h:i a'));
        $rest_id = $request['rest_id'];
        $customer_id = $request['customer_id'];
        $menu_type_name = $request['menu_type_name'];
        $menu_type_desc = $request['menu_type_desc'];
        $pax = $request['pax'];
        $final_rate = $request['final_rate'];
        $scheduled_date = date('Y-m-d',strtotime($request['scheduled_date']));
        $scheduled_time = date('H:i:s',strtotime($request['scheduled_time']));
        $delivery_location = $request['delivery_location'];
        $mobile = $request['mobile'];
        $alternate_number = $request['alternate_number'];
        $special_notes = $request['special_notes'];
        $city_id = $request['city_id'];
        $pincode = $request['pincode'];

        $mobile_contact = DB::SELECT("SELECT `mobile_contact` FROM `customer_list` WHERE `id` =  " .trim($customer_id) . "");
		$contact = $mobile_contact[0]->mobile_contact;
        


        
        DB::SELECT("UPDATE cat_order_master SET com_order_status = 'P',com_order_date= '$datetime',com_menu_type_name= '" .$menu_type_name . "' ,com_menu_type_desc= '" .$menu_type_desc . "' ,com_pax= " .$pax . " ,com_final_rate= " .$final_rate . ",com_scheduled_date='" .$scheduled_date . "',com_scheduled_time='" .$scheduled_time . "',com_delivery_location='" .$delivery_location . "',com_mobile='" .$mobile . "',
        com_alternate_number='" .$alternate_number . "',com_special_notes='" .$special_notes . "',com_city_id=" .$city_id . ",com_pincode='" .$pincode . "'
        WHERE com_order_id = 't_" .$contact . "'  AND com_order_rest_id = " . $rest_id . " and com_order_status = 'T'");
        
        $lastdata = DB::SELECT("select com_order_id as order_number from cat_order_master where com_customer_id = '$customer_id' ORDER BY com_order_date DESC limit 1");
        $neworderno= TRIM($lastdata[0]->order_number);
        $lastdata = DB::SELECT("DELETE FROM `cat_order_details` WHERE `cod_menu_selected` = 'N' AND `cod_order_id` =  '$neworderno'");
        
        $general= GeneralSetting::where('id','1')->select('restaurant_from_time','restaurant_to_time')->first();
           
        

        if((strtotime($datetime) >= strtotime($general->restaurant_from_time)) && (strtotime($datetime) <= strtotime($general->restaurant_to_time)))
        {
            $contact_time = 'Soon';
        }
        else
        { 
           // $frmtime->diff($totime)->format('%H');
            //$hours = (strtotime($datetime))->diff(strtotime($general->restaurant_from_time))->format('%H');
            $contact_time  = 'Within '.'5'.' Hours';
        }

        
        $msg = "Success";

        

		return response::json(['msg' => $msg, 'order_no'=> $neworderno, 'contact_time'=> $contact_time]);
    
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/******************** For Restaurant Reviews Done by Nihal Jabeen k **********************/
    public function cat_rest_review(Request $request)
    {  
	
		$rest_id = $request['rest_id'];


        $review = DB::SELECT("SELECT cl.name,com.com_review_star as review_star,com.com_review_text->>'$.review' as review_text,	com.com_review_date as review_date FROM `cat_order_master` com  left join customer_list cl ON com.com_customer_id = cl.id where com_order_rest_id= '" . trim($rest_id) . "' order by com.com_review_date DESC"); 

        $rest_details= DB::SELECT("SELECT cr_name as rest_name,cr_address as rest_address,JSON_UNQUOTE(cr_avg_rating->'$.count') as review_count,JSON_UNQUOTE(cr_avg_rating->'$.value') as review_value FROM cat_restaurants WHERE cr_id= '" . trim($rest_id) . "'");
        $rest_name = $rest_details[0]->rest_name;
        $rest_address = $rest_details[0]->rest_address;
        $review_count = $rest_details[0]->review_count;
        $review_value = $rest_details[0]->review_value;


          if(count($rest_details)>0)
        {
            $msg = "Exist";
        }
        else
        {
            $msg = 'Not Exist';
        }
		
		return response::json(['msg' => $msg,
			'review_data' => $review,
			'rest_name' =>$rest_name,
			'rest_address' =>$rest_address,
			'review_value' =>$review_value,
			'review_count' =>$review_count]);

    
    }



	/******************** For Catering Order History Done by Nihal Jabeen k **********************/

    public function cat_order_history(Request $request)
    {  
	
		$cst_id = $request['cst_id'];



       

         $sql_order_history = DB::SELECT("SELECT com_order_id as ordernum,com_order_date as orderdate,com_menu_type_name as typename,com_menu_type_desc as description,com_pax as pax,com_final_rate as final_rate,com_order_status as status,com_review_star as review_star FROM `cat_order_master` where com_customer_id='".trim($cst_id)."' and com_order_status!='T' order by com_order_date DESC"); 

       


          if(count($sql_order_history)>0)
        {
            $msg = "Exist";
        }
        else
        {
            $msg = 'Not Exist';
        }
		
		return response::json(['msg' => $msg,
			'orderhistory_data' => $sql_order_history]);

    
    }

	/******************** For Catering Order Review Done by Nihal Jabeen k **********************/

    public function cat_review_in(Request $request)
    {  
	
		$ordernum = $request['ordernum'];
		$review_star = $request['review_star'];
		$review_text = $request['review_text'];

		//com_review_text= JSON_OBJECT("review","'.$review_text.'")

		DB::UPDATE('update cat_order_master set com_review_star = "'.$review_star.'",com_review_text= JSON_OBJECT("review","'.$review_text.'") where com_order_id = "'.$ordernum.'"');
       $msg = 'Review Updated Successfully';


        
		
		return response::json(['msg' => $msg]);

    
    }

    /******************** For Catering Order Review getting Done by Nihal Jabeen k **********************/

    public function cat_order_review(Request $request)
    {  
	
		$ordernum = $request['ordernum'];
		$review_star = 0;
        $review_text = '';

		$sql_check_ordernum= DB::SELECT("SELECT com.com_order_id FROM `cat_order_master` com where com_order_id= '" . trim($ordernum) . "'");
		if (count($sql_check_ordernum)>0) {


		

       

        $sql_orderReviewDetails= DB::SELECT("SELECT com.com_review_star as review_star,com.com_review_text->>'$.review' as review_text FROM `cat_order_master` com   where com_order_id= '" . trim($ordernum) . "'");
        $review_star = $sql_orderReviewDetails[0]->review_star;
        $review_text = $sql_orderReviewDetails[0]->review_text;
        
		if ($review_text==null) {

			$review_text ='';
		}

          if(count($sql_orderReviewDetails)>0)
        {
            $msg = "Exist";
        }
        else
        {
            $msg = 'Not Exist';
        }
		
		return response::json(['msg' => $msg,
			'review_star' => $review_star,
			'review_text' =>$review_text]);
		}else{
			 $msg = 'Not Exist';
			return response::json(['msg' => $msg,
			'review_star' => $review_star,
			'review_text' =>$review_text]);
		}
    
    }




    public function cat_order_history_details(Request $request)
    {  
	    $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d h:i A');
        $ordernum = $request['ordernum'];
           
        
        
        $order_history = DB::SELECT("SELECT o.com_order_id as ordernum,o.com_pax as pax,o.com_final_rate as final_rate,o.com_order_date as orderdate,o.com_scheduled_date as del_schedule_date, o.com_scheduled_time as del_schedule_time,
         IFNULL(o.com_delivery_location,'') as del_location,IFNULL(o.com_pincode,'') as del_pincode,IFNULL(o.com_special_notes,'') as special_notes, r.cr_name as restaurant_name, o.com_menu_type_name as menu_type_name,o.com_single_rate as single_rate, IFNULL(o.com_delivered_date,'') as delivered_date
        FROM cat_order_master o left join cat_restaurants r on r.cr_id =o.com_order_rest_id  where com_order_id='".trim($ordernum)."'"); 


        if(count($order_history)>0)
        {
            $allcategory = DB::SELECT("SELECT DISTINCT c.cod_menu_cat_id AS category_id, c.cod_menu_cat_name AS category_name FROM cat_order_details c WHERE c.cod_order_id = '" .$ordernum . "'");
		
		foreach($allcategory as $key=>$item)
		{
            $category_id = $item->category_id;
			$category_name = $item->category_name;
			$menudetails = DB::SELECT("SELECT  c.cod_menu_name AS menu_name, cod_diet as diet FROM cat_order_details c WHERE c.cod_order_id = '" .$ordernum . "' and  cod_menu_cat_id = $category_id");
			if(count($menudetails)>0) 
			{
				$arr[] = (object) array('type'=>'category','name'=>$category_name,'diet'=>'');
				foreach ($menudetails as $value) 
				{
                    $arr[] = (object)array('type' => 'menu', 'name' => $value->menu_name,'diet' => $value->diet);
			
				}
			}



		}
            return response::json(['msg'=>'Exist',
            'ordernum'=>$order_history[0]->ordernum,
            'restaurant_name'=>$order_history[0]->restaurant_name,
            'menu_type_name'=>$order_history[0]->menu_type_name,
            'single_rate'=>(string)$order_history[0]->single_rate,
            'pax'=>(string)$order_history[0]->pax,
            'sub_total'=>(string)0,
            'tax'=>(string)0,
            'extra_charge_dtls'=>'Packing Charge = 50 +  Transporation Charge = 60',
            'final_rate'=>(string)$order_history[0]->final_rate,
            'orderdate'=>$order_history[0]->orderdate,
            'del_schedule_date'=>$order_history[0]->del_schedule_date,
            'del_schedule_time'=>$order_history[0]->del_schedule_time,
            'del_location'=>$order_history[0]->del_location,
            'del_pincode'=>$order_history[0]->del_pincode,
            'special_notes'=>$order_history[0]->special_notes,
            'delivered_date'=>$order_history[0]->delivered_date,
            'menu_data' =>$arr]); 
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg'=>$msg,'order_data' => '','menu_data' =>'']); 
        }


	    
		
    
    }
	
	
	
	
	
	
	
}
