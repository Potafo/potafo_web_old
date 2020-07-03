<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Session;
use Helpers\Datasource;
use App\Http\Requests;
use Image;
use App\CategoryMenu;
use App\CategoryMenuType;
use App\MenuTypeCategory;
use App\GeneralSetting;
use Response;
use Helpers\Commonsource;
use DateTime;
use DateTimeZone;


class CategoryMenuTypeController extends Controller
{
    public function menu_types($id){
        $filterarr = array();
        $user_id =  Session::get('staffid');
        $restaurant_id = $id;
        $restaurant_name = DB::SELECT('SELECT cr_name as name FROM `cat_restaurants` WHERE `cr_id` = "'.$restaurant_id.'"' );
        $details = DB::SELECT('SELECT mt_rest_id,mt_type_name,mt_type_rate,mt_min_pax ,mt_max_pax,mt_type_id,mt_status,mt_pic,mt_display_order FROM `cat_menu_types` WHERE `cat_menu_types`.`mt_rest_id` = "'.$restaurant_id.'" ORDER BY mt_type_id');
        return view('catering.type',compact('details','restaurant_id','restaurant_name','user_id'));
    }

    public function add_type(Request $request)
    {
        $img = Input::file('type_image');
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
            Image::make($img)->resize(250, 250)->save(base_path() . '/uploads/catering/menu_types/' . $uploadfile);
            $image_url = 'uploads/catering/menu_types/' . $uploadfile;
        }
        //return $image_url."  ".$request['cat_cat'];
        if ($type == 'insert') {
            $catlist = CategoryMenuType::where('mt_type_name', $request['name'])
                ->get();
            if (count($catlist) > 0) {

                $msg = 'already exist';

                $rows = DB::SELECT('SELECT mt_rest_id,mt_type_name,mt_min_pax ,mt_max_pax ,mt_type_rate,mt_type_id,mt_status,mt_pic,mt_display_order FROM `cat_menu_types` WHERE `cat_menu_types`.`mt_rest_id` = "'.$request['res_id'].'" ORDER BY mt_type_id');
                return response::json(compact('msg', 'rows'));
            } else {
                $catgy = new CategoryMenuType();
                $catgy->mt_type_name = ucwords(strtolower($request['name']));
                $catgy->mt_rest_id=$request['res_id'];
                $catgy->mt_type_rate=$request['rate'];
                $catgy->mt_min_pax=$request['min_pax'];
                $catgy->mt_max_pax=$request['max_pax'];
                $catgy->mt_modified_by	 =$request['userid'];
                $catgy->mt_modified_on	 = date('Y-m-d H:i:s');
                $catgy->mt_pic=$image_url;
                $rows = DB::SELECT('SELECT count(*) as totalcount FROM `cat_menu_types` WHERE `cat_menu_types`.`mt_rest_id` = "'.$request['res_id'].'" ORDER BY mt_type_id');
                $catgy->mt_display_order = $rows[0]->totalcount+1;
                $catgy->save();
                $msg = 'success';
                $rows = DB::SELECT('SELECT mt_rest_id,mt_type_name,mt_type_rate,mt_min_pax ,mt_max_pax mt_type_id,mt_status,mt_pic,mt_display_order FROM `cat_menu_types` WHERE `cat_menu_types`.`mt_rest_id` = "'.$request['res_id'].'" ORDER BY mt_type_id');
                return response::json(compact('msg', 'rows'));
            }

            return redirect('menu/types'.$request['res_id']);
        }
        else if($type == 'update') {


            $ctys = CategoryMenuType::where('mt_type_name', $request['name'])
                ->where('mt_type_id', '!=', $request['type_id'])
                ->first();
            if (count($ctys) > 0) {

                $msg = 'exist';
                $rows = DB::SELECT('SELECT mt_rest_id,mt_type_name,mt_type_rate,mt_min_pax ,mt_max_pax mt_type_id,mt_status,mt_pic,mt_display_order FROM `cat_menu_types` WHERE `cat_menu_types`.`mt_rest_id` = "' . $request['res_id'] . '" ORDER BY mt_type_id');
                return response::json(compact('msg', 'rows'));
            } else {

                $arr = array();
                $arr['mt_type_name'] = ucwords(strtolower($request['name']));
                if($image_url) {
                    $arr['mt_pic'] = $image_url;
                }
                $arr['mt_status'] =        $request['status'];
                $arr['mt_type_rate'] =     $request['rate'];
                $arr['mt_min_pax'] =       $request['min_pax'];
                $arr['mt_max_pax'] =       $request['max_pax'];
                $arr['mt_modified_by']	 = $request['userid'];
                $arr['mt_modified_on']	 = date('Y-m-d H:i:s');
                CategoryMenuType::where('mt_type_id', $request['type_id'])->update($arr);
                $msg = 'done';
                $rows = DB::SELECT('SELECT mt_rest_id,mt_type_name,mt_type_rate,mt_min_pax ,mt_max_pax mt_type_id,mt_status,mt_pic,mt_display_order FROM `cat_menu_types` WHERE `cat_menu_types`.`mt_rest_id` = "' . $request['res_id'] . '" ORDER BY mt_type_id');
                return response::json(compact('msg', 'rows'));
            }
        }
    }

    public function type_order($id,$slno,$val)
    {
        CategoryMenuType::where('mt_rest_id',trim($id))
            ->where('mt_type_id',trim($slno))
            ->update(['mt_display_order' =>$val]);
        $details =DB::SELECT('SELECT mt_rest_id,mt_type_name,mt_type_rate,mt_min_pax ,mt_max_pax mt_type_id,mt_status,mt_pic,mt_display_order FROM `cat_menu_types` WHERE `cat_menu_types`.`mt_rest_id` = "' . $id . '" ORDER BY mt_type_id');
        $msg = 'editted';
        return response::json(['msg' => $msg,'details' => $details]);
    }

    public function categorytype_order($id,$typeid,$val)
    {
        MenuTypeCategory::where('mtc_id',trim($id))->where('mtc_menu_type_id',trim($typeid))
            ->update(['mtc_display_order' =>$val]);
        $details = DB::SELECT('SELECT mtc_id,mtc_rest_id,mtc_menu_type_id,mtc_name,mtc_max_item_count,mtc_status,mtc_display_order FROM `cat_menu_types_category` WHERE `cat_menu_types_category`.`mtc_menu_type_id` = "'.$typeid.'" ORDER BY mtc_menu_type_id');
        $msg = 'editted';
        return response::json(['msg' => $msg,'details' => $details]);
    }



    public function menu_category($id)
    {
        $filterarr = array();
        $user_id =  Session::get('staffid');
        $type_id = $id;
        $restaurantdetails = DB::SELECT('SELECT mt_rest_id,cr_name FROM `cat_menu_types` join cat_restaurants on cat_restaurants.cr_id =cat_menu_types.mt_rest_id  WHERE `cat_menu_types`.`mt_type_id` = "'.$type_id.'"');
        $restaurant_name = $restaurantdetails[0]->cr_name;
        $restaurant_id = $restaurantdetails[0]->mt_rest_id;
        $details = DB::SELECT('SELECT mtc_id,mtc_rest_id,mtc_menu_type_id,mtc_name,mtc_max_item_count,mtc_status,mtc_display_order FROM `cat_menu_types_category` WHERE `cat_menu_types_category`.`mtc_menu_type_id` = "'.$type_id.'" ORDER BY mtc_menu_type_id');
        return view('catering.category_type',compact('details','restaurant_id','restaurant_id','user_id','restaurant_name','type_id'));
    }

    public function add_categorytype(Request $request)
    {
        $type = $request['type'];
        $url = Datasource::geturl();
        $type_id = $request['menutype_id'];
        $timeDate = date("jmYhis") . rand(991, 9999);
        $date = date('Y-m-d');
        if ($type == 'insert')
        {
            $catlist = MenuTypeCategory::where('mtc_name', $request['name'])->get();
            if (count($catlist) > 0)
            {
                $msg = 'already exist';
                $rows = DB::SELECT('SELECT mtc_id,mtc_rest_id,mtc_menu_type_id,mtc_name,mtc_max_item_count,mtc_status,mtc_display_order FROM `cat_menu_types_category` WHERE `cat_menu_types_category`.`mtc_menu_type_id` = "' . $type_id . '" ORDER BY mtc_menu_type_id');
                return response::json(compact('msg', 'rows'));
            }
            else
            {
                $catgy = new MenuTypeCategory();
                $catgy->mtc_name = ucwords(strtolower($request['name']));
                $catgy->mtc_rest_id = $request['res_id'];
                $catgy->mtc_menu_type_id = $request['menutype_id'];
                $catgy->mtc_max_item_count = $request['count'];
                $catgy->mtc_display_order = '1';
                $catgy->save();
                $msg = 'success';
                $rows = DB::SELECT('SELECT mtc_id,mtc_rest_id,mtc_menu_type_id,mtc_name,mtc_max_item_count,mtc_status,mtc_display_order FROM `cat_menu_types_category` WHERE `cat_menu_types_category`.`mtc_menu_type_id` = "' . $type_id . '" ORDER BY mtc_menu_type_id');
                $arr = array();
                foreach ($rows as $item => $val)
                {
                    $value = $val->mtc_max_item_count . ' ' . $val->mtc_name;
                    $arr[] = $value;
                }
                $description = implode('+', $arr);
                CategoryMenuType::where('mt_type_id', trim($request['menutype_id']))
                    ->update(['mt_description' => $description]);
                return response::json(compact('msg', 'rows'));
            }
            return redirect('menu/category' . $request['res_id']);
        }
        else if ($type == 'update') {
            $ctys = MenuTypeCategory::where('mtc_name', $request['name'])
                ->where('mtc_id', '!=', $request['type_id'])
                ->first();
            if (count($ctys) > 0) {

                $msg = 'exist';
                $rows = DB::SELECT('SELECT mtc_id,mtc_rest_id,mtc_menu_type_id,mtc_name,mtc_max_item_count,mtc_status,mtc_display_order FROM `cat_menu_types_category` WHERE `cat_menu_types_category`.`mtc_menu_type_id` = "' . $type_id . '" ORDER BY mtc_menu_type_id');
                return response::json(compact('msg', 'rows'));
            } else {

                $arr = array();
                $arr['mtc_name'] = ucwords(strtolower($request['name']));
                $arr['mtc_status'] = $request['status'];
                $arr['mtc_max_item_count'] = $request['count'];
                MenuTypeCategory::where('mtc_id', $request['type_id'])->update($arr);
                $msg = 'done';
                $rows = DB::SELECT('SELECT mtc_id,mtc_rest_id,mtc_menu_type_id,mtc_name,mtc_max_item_count,mtc_status,mtc_display_order FROM `cat_menu_types_category` WHERE `cat_menu_types_category`.`mtc_menu_type_id` = "' . $type_id . '" ORDER BY mtc_menu_type_id');
                foreach ($rows as $item => $val)
                {
                    $value = $val->mtc_max_item_count . ' ' . $val->mtc_name;
                    $arr[] = $value;
                }
                $description = implode('+', $arr);
                CategoryMenuType::where('mt_type_id', trim($request['menutype_id']))
                    ->update(['mt_description' => $description]);
                return response::json(compact('msg', 'rows'));
            }
        }
    }

    public function category_type(Request $request)
    {
        $name = $request['filter_name'];
        $search = '';
        if(isset($name) && $name != '')
        {
            if($search == "")
            {
                $search.="  LOWER(mtc_name) LIKE '%".strtolower($name)."%'";
            }
            else
            {
                $search.=" and  LOWER(mtc_name)  LIKE '%".strtolower($name)."%'";
            }
        }
        if($search!="")
        {
            $search="where $search ";
        }
        $details = DB::SELECT('SELECT mtc_id,mtc_rest_id,mtc_menu_type_id,mtc_name,mtc_max_item_count,mtc_status,mtc_display_order FROM `cat_menu_types_category`  '.$search.' ORDER BY mtc_menu_type_id');
        return $details;
    }

    public function menu($id)
    {
        $filterarr = array();
        $user_id =  Session::get('staffid');
        $type_id = $id;
        $category = DB::SELECT('SELECT mtc_name,mtc_id,mtc_rest_id,mtc_status FROM `cat_menu_types_category`  WHERE `cat_menu_types_category`.`mtc_menu_type_id` = "'.$type_id.'"');
        $restaurantdetails = DB::SELECT('SELECT mt_rest_id,cr_name FROM `cat_menu_types` join cat_restaurants on cat_restaurants.cr_id =cat_menu_types.mt_rest_id  WHERE `cat_menu_types`.`mt_type_id` = "'.$type_id.'"');
        $restaurant_name = $restaurantdetails[0]->cr_name;
        $restaurant_id = $restaurantdetails[0]->mt_rest_id;
        $details = DB::SELECT('SELECT cm_type_id,cm_rest_id,cm_type_cat_id,mtc_name,cm_sl_no,cm_menu_name,cm_menu_details,cm_menu_dis_order,cm_diet FROM `cat_menu` join cat_menu_types_category on cat_menu.cm_type_cat_id =  cat_menu_types_category.mtc_id WHERE `cat_menu`.`cm_type_id` = "'.$type_id.'" ORDER BY cm_type_id');
        return view('catering.menu',compact('details','restaurant_id','restaurant_id','user_id','restaurant_name','type_id','category'));
    }

    public function add_menu(Request $request)
    {
        $type = $request['type'];
        $url = Datasource::geturl();
        $type_id = $request['menutype_id'];
        $timeDate = date("jmYhis") . rand(991, 9999);
        $date = date('Y-m-d');
        if ($type == 'insert') {
            $catlist = CategoryMenu::where('cm_type_id', $request['menutype_id'])->where('cm_menu_name', $request['name'])->get();
            if (count($catlist) > 0)
            {
                $msg = 'already exist';
                $rows = DB::SELECT('SELECT cm_type_id,cm_rest_id,cm_type_cat_id,cm_sl_no,cm_menu_name,cm_menu_details,cm_menu_dis_order,cm_diet FROM `cat_menu` WHERE `cat_menu`.`cm_type_id` = "'.$type_id.'" ORDER BY cm_type_id');
                return response::json(compact('msg', 'rows'));
            }
            else
            {
                $catgy = new CategoryMenu();
                $catgy->cm_menu_name = ucwords(strtolower($request['name']));
                $catgy->cm_rest_id = $request['res_id'];
                $catgy->cm_type_id = $request['menutype_id'];
                $catgy->cm_type_cat_id = $request['category'];
                $catgy->cm_menu_details = $request['description'];
                $catgy->cm_diet = $request['diet'];
                $catgy->cm_menu_dis_order = '1';
                $catgy->save();
                $msg = 'success';
                $rows = DB::SELECT('SELECT cm_type_id,cm_rest_id,cm_type_cat_id,cm_sl_no,cm_menu_name,cm_menu_details,cm_menu_dis_order,cm_diet FROM `cat_menu` WHERE `cat_menu`.`cm_type_id` = "'.$type_id.'" ORDER BY cm_type_id');
                return response::json(compact('msg', 'rows'));
            }
            return redirect('menu' . $request['res_id']);
        }
        else if ($type == 'update') {
            $ctys = CategoryMenu::where('cm_menu_name', $request['name'])
                ->where('cm_sl_no', '!=', $request['slno'])
                ->first();
            if (count($ctys) > 0) {

                $msg = 'exist';
                $rows = DB::SELECT('SELECT cm_type_id,cm_rest_id,cm_type_cat_id,cm_sl_no,cm_menu_name,cm_menu_details,cm_menu_dis_order,cm_diet FROM `cat_menu` WHERE `cat_menu`.`cm_type_id` = "'.$type_id.'" ORDER BY cm_type_id');
                return response::json(compact('msg', 'rows'));
            } else {
                $arr = array();
                $arr['cm_menu_name'] = ucwords(strtolower($request['name']));
                $arr['cm_rest_id'] = $request['res_id'];
                $arr['cm_type_id'] = $request['menutype_id'];
                $arr['cm_type_cat_id'] = $request['category'];
                $arr['cm_menu_details'] = $request['description'];
                $arr['cm_diet'] = $request['diet'];
                CategoryMenu::where('cm_sl_no', $request['slno'])->where('cm_type_id', $request['menutype_id'])->update($arr);
                $msg = 'done';
                $rows = DB::SELECT('SELECT cm_type_id,cm_rest_id,cm_type_cat_id,cm_sl_no,cm_menu_name,cm_menu_details,cm_menu_dis_order,cm_diet FROM `cat_menu` WHERE `cat_menu`.`cm_type_id` = "'.$type_id.'" ORDER BY cm_type_id');
                return response::json(compact('msg', 'rows'));
            }
        }
    }

    public function menu_order($typeid,$id,$slno,$val)
    {
        CategoryMenu::where('cm_type_id',trim($id))->where('cm_sl_no',trim($slno))
            ->update(['cm_menu_dis_order' =>$val]);
        $details = DB::SELECT('SELECT cm_type_id,cm_rest_id,cm_type_cat_id,cm_sl_no,cm_menu_name,cm_menu_details,cm_menu_dis_order,cm_diet FROM `cat_menu` WHERE `cat_menu`.`cm_type_id` = "'.$typeid.'" ORDER BY cm_type_id');
        $msg = 'editted';
        return response::json(['msg' => $msg,'details' => $details]);
    }

    public function menus(Request $request)
    {
        $name = $request['filter_name'];
        $category = $request['flt_category'];
        $search = '';
        if(isset($name) && $name != '')
        {
            if($search == "")
            {
                $search.="  LOWER(cm_menu_name) LIKE '%".strtolower($name)."%'";
            }
            else
            {
                $search.=" and  LOWER(cm_menu_name)  LIKE '%".strtolower($name)."%'";
            }
        }
        if(isset($category) && $category != '')
        {
            if($search == "")
            {
                $search.="  cm_type_cat_id =".$category;
            }
            else
            {
                $search.=" and  cm_type_cat_id = ".$category;
            }
        }

        if($search!="")
        {
            $search="where $search and ";
        }
        else{
            $search="where";
        }
        $details = DB::SELECT('SELECT cm_type_id,cm_rest_id,cm_type_cat_id,mtc_name,cm_sl_no,cm_menu_name,cm_menu_details,cm_menu_dis_order,cm_diet FROM `cat_menu` join cat_menu_types_category on cat_menu.cm_type_cat_id =  cat_menu_types_category.mtc_id '.$search.'  cm_menu_name is not null and cm_type_id = "'.$request['mtype_id'].'" ORDER BY cm_type_id');
        return $details;
    }
}

//INSERT INTO `module_master` (`m_id`, `module_name`, `sub_module`, `page_link`, `module_for`, `display_order`) VALUES (NULL, 'Cater.Orders', 'cm_type_cat_id', 'manage_cateringorder', 'C', '7');
//update `users_modules` set active = 'Y' WHERE `module_id` = 22

//UPDATE `module_master` SET `display_order` = '8' WHERE `module_master`.`m_id` = 7;
