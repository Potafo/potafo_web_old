<?php

namespace App\Http\Controllers;

use App\RestaurantMenu;
use App\TempRestaurantMenus;
use App\TaxMaster;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Helpers\Datasource;
use App\Group;
use App\Restaurant_Master;
use Illuminate\Support\Facades\Input;
use PHPExcel_Style_NumberFormat;
use Image;
use Excel;
use App\Category;
use App\SubCategory;
use DateTime;
use DateTimeZone;
use Helpers\Commonsource;
class MenuController extends Controller
{
//menu list page view
    public function menu_list($id)
    {
        $filterarr = array();
        $restaurant_id = $id;
        $restaurant_name = DB::SELECT('SELECT name_tagline->>"$.name" as name FROM `restaurant_master` WHERE `id` = "'.$restaurant_id.'"' );
        $details = DB::SELECT('SELECT m_name_type->>"$.name" as name,m_menu_id as menu_id,m_rest_id as rest_id,m_name_type->>"$.type" as type,JSON_UNQUOTE(m_category) as category,JSON_UNQUOTE(m_subcategory) as subcategory,JSON_UNQUOTE(m_days) as days,m_menu_id as menuid,m_status as status,JSON_UNQUOTE(m_image->"$.img1") as img,JSON_UNQUOTE(m_time->"$.from") as from_time,JSON_UNQUOTE(m_time->"$.to") as to_time,m_most_selling  FROM `restaurant_menu` WHERE `restaurant_menu`.`m_rest_id` = "'.$restaurant_id.'" ORDER BY m_menu_id');
        return view('menu.menu_list',compact('details','restaurant_id','restaurant_name'));
    }
    //view menu add page
    public function menu_add($id)
    {
        $filterarr = array();
        $resid = $id;
        $category = Datasource::restaurantcategory($resid,'');
        $subcategory = Datasource::restaurantsubcategory($resid,'');
        $taxlist = Datasource::restauranttax($resid,'');
        $restaurant_dtl = Restaurant_Master::where('id',$id)->select('extra_rate_percent')->first();
        $extra_percent = $restaurant_dtl['extra_rate_percent'];
        return view('menu.menu_add',compact('category','subcategory','taxlist','resid','extra_percent'));
    }

    //menu add function
    public function submit_menu(Request $request)
    {
        $string = '';
        $post = $request->all();
        $restaurantid = trim($post['res_id']);
        $type = trim($post['menu_type']);
        $diet = $post['diet'];
        $most = $post['most_selling'];
        $menu_name = trim($post['menu_name']);
        $tax = isset($post['tax'])?$post['tax']:[""];
        $category = isset($post['category'])?$post['category']:[""];
        $subcategory = isset($post['subcategory'])?$post['subcategory']:[""];
        $pack_rate= isset($post['pack_rate'])?$post['pack_rate']:'0.00';
        $days= isset($post['days'])?$post['days']:[""];
        $portion_count= $post['portion_count'];
        $total_portion= $post['tot_count'];
        $from_time= isset($post['from_time'])?date("H:i:s", strtotime(trim($post['from_time']))):'';
        $to_time= isset($post['to_time'])?date("H:i:s", strtotime(trim($post['to_time']))):'';
        $description = trim($post['description']);
        $img = Input::file('menu_image');
        $timeDate = date("jmYhis") . rand(991, 9999);
        if(isset($img) && $img !='')
        {
            $uploadfile = $timeDate . '.' .strtolower($img->getClientOriginalExtension());
            Image::make($img)->resize(400, 240)->save(base_path() . '/uploads/menus/' . $uploadfile);
            $image_url = 'uploads/menus/' . $uploadfile;
        }
        else
        {
            $image_url = '';
        }
        $menus = DB::select("SELECT m_name_type->>'$.name' FROM restaurant_menu where m_name_type->>'$.name' = '".trim($menu_name)."' and m_rest_id = '".$restaurantid."'");
        if(count($menus)>0)
        {
            $msg = 'Already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $count = $portion_count;
            $n =0;
            for($i=1;$i<=$total_portion;$i++)
            {
                if($post['portion'.$i] != '')
                {
                    $n = $n + 1;
                    $string .= '"portion' . $n . '":{"portion":"' . $post['portion' . $i] . '","exc_rate":"' . $post['exc_rate' . $i] . '","inc_rate":"' . $post['inc_rate' . $i] . '","extra_percent":"' . $post['extra_rate' . $i] . '","extra_val":"' . $post['extra_val' . $i] . '","final_rate":"' . $post['final_rate' . $i] . '"},';
                }
            }
            $string = rtrim($string, ',');
            $abc = '{'.$string.'}';
            DB::INSERT("INSERT INTO `restaurant_menu`(`m_rest_id`,`m_menu_id`,`m_por_rate`,`m_pack_rate`,`m_name_type`,`m_diet`, `m_days`,`m_category`, `m_subcategory`, `m_description`, `m_most_selling`,`m_time`, `m_image`,`m_tax`)
            VALUES('" . $restaurantid . "','0','".$abc."','".$pack_rate."',json_object('name','" . title_case($menu_name) . "','type','" . title_case($type) . "'),'".$diet."','" . json_encode($days) . "','" . json_encode($category) . "','" . json_encode($subcategory) . "','" . $description . "','".$most."',json_object('from','" .$from_time. "','to','" .$to_time. "'),json_object('img1','" . $image_url . "'),'" . json_encode($tax) . "')");
            $msg = "success";
            return response::json(compact('msg'));
        }
    }

    //category of particular restaurant
    public function category($id)
    {
        $category = Datasource::restaurantcategory($id,'all');
        return response::json(compact('category'));
    }

   //subcategory of particular restaurant
    public function subcategory($id)
    {
        $subcategory = Datasource::restaurantsubcategory($id,'all');
        return response::json(compact('subcategory'));
    }

    //add category
    public function category_add(Request $request)
    {
        $post = $request->all();
        $details = Category::where('restaurant_id',trim($post['res_id']))
                   ->where('name',trim(title_case($post['category'])))
                   ->get();
        if(count($details) >0)
        {
            $msg = 'Already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $category = new Category();
            $category->restaurant_id = trim($post['res_id']);
            $category->name = trim(title_case($post['category']));

            if($post['status'] == 'true')
            {
                $category->status = 'Y';
            }
            else
            {
                $category->status = 'N';
            }

            $category->save();
            $msg = "success";
            $category =   Datasource::restaurantcategory(trim($post['res_id']),' ');
            return response::json(['msg'=>$msg,'category' =>$category]);
        }
    }

    //add sub category
    public function subcategory_add(Request $request)
    {
        $post = $request->all();
        $details = SubCategory::where('restaurant_id',trim($post['res_id']))
                   ->where('name',trim(title_case($post['subcategory'])))
                   ->get();

        if(count($details) >0)
        {
            $msg = 'Already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $subcat = new SubCategory();
            $subcat->restaurant_id = trim($post['res_id']);
            $subcat->name = trim(title_case($post['subcategory']));
            if($post['status'] == 'true')
            {
                $subcat->status = 'Y';
            }
            else
            {
                $subcat->status = 'N';
            }
            $subcat->save();
            $msg = "success";
            $category =   Datasource::restaurantsubcategory(trim($post['res_id']),' ');
            return response::json(['msg'=>$msg,'category' =>$category]);
        }
    }

    //filter menu list
    public function filter_menu(Request $request)
    {
        $search = '';
        $menu_name = $request['menuname'];
        $category= $request['menu_categry'];
        $subcategory= $request['menu_subcat'];
        $resid= $request['resid'];

        if(isset($menu_name) && $menu_name != '')
        {
            if($search == "")
            {
                $search.=" LOWER(m_name_type->>'$.name') LIKE '%".strtolower($menu_name)."%'";
            }
            else
            {
                $search.=" and LOWER(m_name_type->>'$.name')  LIKE '%".strtolower($menu_name)."%'";
            }
        }
        if(isset($category) && $category != '')
        {
            if($search == "")
            {
                $search.="  json_search(UPPER(m_category), 'all', UPPER('%".title_case($category)."%')) is not null";
             // $search.="  JSON_CONTAINS(m_category, '[\"".title_case($category)."\"]')";
            }
            else
            {
                $search.=" and json_search(UPPER(m_category), 'all', UPPER('%".title_case($category)."%')) is not null";
             // $search.=" and  JSON_CONTAINS(m_category, '[\"".title_case($category)."\"]')";
            }
        }

        if(isset($subcategory) && $subcategory != '')
        {
            if($search == "")
            {
                $search.="  json_search(UPPER(m_subcategory), 'all', UPPER('".title_case($subcategory)."%')) is not null";
            }
            else
            {
                $search.=" and  json_search(UPPER(m_subcategory), 'all', UPPER('".title_case($subcategory)."%')) is not null";
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
        $details = DB::SELECT('SELECT m_rest_id,m_most_selling,m_name_type->>"$.name" as name,m_name_type->>"$.type" as type,JSON_UNQUOTE(m_category) as category,JSON_UNQUOTE(m_subcategory) as subcategory,JSON_UNQUOTE(m_days) as days,m_menu_id as menuid,m_status as status,m_image->"$.img1",JSON_UNQUOTE(m_time->"$.from") as from_time,JSON_UNQUOTE(m_time->"$.to") as to_time FROM `restaurant_menu` '.$search.' `restaurant_menu`.`m_menu_id` != " "  and `restaurant_menu`.m_rest_id = "'.$resid.'" ORDER BY m_menu_id');
        return $details;
    }

   //get tax value for particular tax
    public function get_taxvalue(Request $request)
    {
        $taxdetail = TaxMaster::where('t_name',trim($request['tax']))->where('restaurant_id',trim($request['id']))->select('t_value')->first();
        $taxvalue = $taxdetail['t_value'];
        return response::json(compact('taxvalue'));
    }

    //view menu edit page
    public function menu_edit($resid,$menuid,Request $request)
    {

        $details = DB::SELECT('SELECT m_name_type->>"$.name" as name,m_menu_id as menuid,JSON_LENGTH(`m_por_rate`) as count,m_name_type->>"$.type" as type,JSON_UNQUOTE(m_category) as category,JSON_UNQUOTE(m_subcategory) as subcategory,JSON_UNQUOTE(m_days) as days,m_menu_id as menuid,m_status as status,JSON_UNQUOTE(m_image->"$.img1") as img,
        JSON_UNQUOTE(m_time->"$.from") as from_time,JSON_UNQUOTE(m_time->"$.to") as to_time,m_description as description,m_pack_rate as  pack_rate ,m_tax as tax,JSON_UNQUOTE(`m_por_rate`->>"$.portion1.portion")  as portion1,JSON_UNQUOTE(`m_por_rate`->>"$.portion1.exc_rate")  as exc_rate1,
        JSON_UNQUOTE(`m_por_rate`->>"$.portion1.inc_rate")  as inc_rate1,JSON_UNQUOTE(`m_por_rate`->>"$.portion1.extra_val")  as extra_val1,JSON_UNQUOTE(`m_por_rate`->>"$.portion1.final_rate")  as final_rate1,JSON_UNQUOTE(`m_por_rate`->>"$.portion1.extra_percent")  as extra_rate1,JSON_UNQUOTE(`m_por_rate`->>"$.portion2.portion")  as portion2,JSON_UNQUOTE(`m_por_rate`->>"$.portion2.exc_rate")  as exc_rate2,
        JSON_UNQUOTE(`m_por_rate`->>"$.portion2.inc_rate")  as inc_rate2,JSON_UNQUOTE(`m_por_rate`->>"$.portion2.extra_val")  as extra_val2,JSON_UNQUOTE(`m_por_rate`->>"$.portion2.final_rate")  as final_rate2,JSON_UNQUOTE(`m_por_rate`->>"$.portion2.extra_percent")  as extra_rate2,JSON_UNQUOTE(`m_por_rate`->>"$.portion3.portion")  as portion3,JSON_UNQUOTE(`m_por_rate`->>"$.portion3.exc_rate")  as exc_rate3,
        JSON_UNQUOTE(`m_por_rate`->>"$.portion3.inc_rate")  as inc_rate3,JSON_UNQUOTE(`m_por_rate`->>"$.portion3.extra_val")  as extra_val3,JSON_UNQUOTE(`m_por_rate`->>"$.portion3.final_rate")  as final_rate3,JSON_UNQUOTE(`m_por_rate`->>"$.portion3.extra_percent")  as extra_rate3,JSON_UNQUOTE(`m_por_rate`->>"$.portion4.portion")  as portion4,JSON_UNQUOTE(`m_por_rate`->>"$.portion4.exc_rate")  as exc_rate4,
        JSON_UNQUOTE(`m_por_rate`->>"$.portion4.inc_rate")  as inc_rate4,JSON_UNQUOTE(`m_por_rate`->>"$.portion4.extra_val")  as extra_val4,JSON_UNQUOTE(`m_por_rate`->>"$.portion4.final_rate")  as final_rate4,JSON_UNQUOTE(`m_por_rate`->>"$.portion4.extra_percent")  as extra_rate4,JSON_UNQUOTE(`m_por_rate`->>"$.portion5.portion")  as portion5,JSON_UNQUOTE(`m_por_rate`->>"$.portion5.exc_rate")  as exc_rate5,
        JSON_UNQUOTE(`m_por_rate`->>"$.portion5.inc_rate")  as inc_rate5,JSON_UNQUOTE(`m_por_rate`->>"$.portion5.extra_val")  as extra_val5,JSON_UNQUOTE(`m_por_rate`->>"$.portion5.final_rate")  as final_rate5,JSON_UNQUOTE(`m_por_rate`->>"$.portion5.extra_percent")  as extra_rate5,JSON_UNQUOTE(`m_por_rate`->>"$.portion6.portion")  as portion6,JSON_UNQUOTE(`m_por_rate`->>"$.portion6.exc_rate")  as exc_rate6,
        JSON_UNQUOTE(`m_por_rate`->>"$.portion6.inc_rate")  as inc_rate6,JSON_UNQUOTE(`m_por_rate`->>"$.portion6.extra_val")  as extra_val6,JSON_UNQUOTE(`m_por_rate`->>"$.portion6.final_rate")  as final_rate6,JSON_UNQUOTE(`m_por_rate`->>"$.portion6.extra_percent")  as extra_rate6,JSON_UNQUOTE(`m_por_rate`->>"$.portion7.portion")  as portion7,JSON_UNQUOTE(`m_por_rate`->>"$.portion7.exc_rate")  as exc_rate7,
        JSON_UNQUOTE(`m_por_rate`->>"$.portion7.inc_rate")  as inc_rate7,JSON_UNQUOTE(`m_por_rate`->>"$.portion7.extra_val")  as extra_val7,JSON_UNQUOTE(`m_por_rate`->>"$.portion7.final_rate")  as final_rate7,JSON_UNQUOTE(`m_por_rate`->>"$.portion7.extra_percent")  as extra_rate7,`m_diet` as diet,`m_most_selling` FROM `restaurant_menu` WHERE `restaurant_menu`.`m_rest_id` = "'.$resid.'" and `restaurant_menu`.`m_menu_id` = "'.$menuid.'" ORDER BY m_menu_id');
        $category = Datasource::restaurantcategory($resid,'');
        $subcategory = Datasource::restaurantsubcategory($resid,'');
        $taxlist = Datasource::restauranttax($resid,'');
        $restaurant_dtl = Restaurant_Master::where('id',$resid)->select('extra_rate_percent')->first();
        $extra_percent = $restaurant_dtl['extra_rate_percent'];
        return view('menu.menu_edit',compact('details','category','portion','count','subcategory','taxlist','resid','menuid','extra_percent'));
    }

    //Edit Menu
    public function menu_editsubmit(Request $request)
    {
        $string = '';
        $post = $request->all();
        $portionarr = array();
        $excratearr = array();
        $post = $request->all();
        $restaurantid = trim($post['res_id']);
        $menuid = $post['menuid'];
        $type = trim($post['menu_type']);
        $menu_name = trim($post['menu_name']);
        $tax = isset($post['tax'])?$post['tax']:[""];
        $old_img = $post['img1'];
        $diet = trim($post['diet']);
        $most = trim($post['most_selling']);
        $status = trim($post['status']);
        $category = isset($post['category'])?$post['category']:[""];
        $subcategory = isset($post['subcategory'])?$post['subcategory']:[""];
        $pack_rate= $post['pack_rate'];
        $days= isset($post['days'])?$post['days']:[""];
        $portion_count= $post['portion_count'];
        $total_portion= $post['tot_count'];
        $from_time= isset($post['from_time'])?date("H:i:s", strtotime(trim($post['from_time']))):null;
        $to_time= isset($post['to_time'])?date("H:i:s", strtotime(trim($post['to_time']))):null;
        $description = trim($post['description']);
        $img = Input::file('menu_image');
        $timeDate = date("jmYhis") . rand(991, 9999);

        if(isset($img) && $img !='')
        {
            $uploadfile = $timeDate . '.' .strtolower($img->getClientOriginalExtension());
            Image::make($img)->resize(400, 240)->save(base_path() . '/uploads/menus/' . $uploadfile);
            $image_url = 'uploads/menus/' . $uploadfile;
        }
        else
        {
            if($old_img != ' ')
            {
                $image_url = $old_img;
            }
            else
            {
                $image_url = '';
            }
        }

        $menus = DB::select("SELECT m_name_type->>'$.name' FROM restaurant_menu where m_name_type->>'$.name' = '".trim($menu_name)."' and m_rest_id = '".$restaurantid."' and m_menu_id != '".$menuid."'");

        if(count($menus)>0)
        {
            $msg = 'Already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $count = $portion_count;
            $n =0;
            for($i=1;$i<=$total_portion;$i++)
            {
                if($post['portion'.$i] != '') {
                    $n = $n + 1;
                    $string .= '"portion' . $n . '":{"portion":"' . $post['portion' . $i] . '","exc_rate":"' . $post['exc_rate' . $i] . '","inc_rate":"' . $post['inc_rate' . $i] . '","extra_percent":"' . $post['extra_rate' . $i] . '","extra_val":"' . $post['extra_val' . $i] . '","final_rate":"' . $post['final_rate' . $i] . '","final_rate":"' . $post['final_rate' . $i] . '"},';
                }
            }
            $string = rtrim($string, ',');
            $abc = '{'.$string.'}';
            DB::UPDATE('UPDATE `restaurant_menu` set m_name_type = JSON_SET(m_name_type, "$.name","'.$menu_name.'"),m_name_type = JSON_SET(m_name_type, "$.type","'.$type.'"),m_time = JSON_SET(m_time, "$.from","'.$from_time.'"),m_time = JSON_SET(m_time, "$.to","'.$to_time.'"),
            m_category = \'' . json_encode($category) . '\',m_subcategory = \'' . json_encode($subcategory) . '\',m_pack_rate = "'.$pack_rate.'",m_days = \'' . json_encode($days) . '\',m_image = json_object("img1",\'' . $image_url . '\'),m_description = "'.$description.'",m_diet = "'.$diet.'",m_tax = \''.json_encode($tax).'\',
            m_por_rate = \'' .$abc. '\',m_status = \'' .$status. '\',m_most_selling = "'.$most.'" where m_rest_id = "'.$restaurantid.'" and m_menu_id = "'.$menuid.'"');
            $msg = 'success';
            return response::json(compact('msg'));
        }
        }

    //Menu Excel Download
    public function menu_excel_download($id)
    {
      /*  $columns = DB::select('SELECT column_name from information_schema.columns where table_schema = "potafo" and table_name = "temp_restaurantmenus"');
        Excel::create('Restaurant Menus', function($excel)use ($columns)
        {
            $excel->sheet('Sheet1', function($sheet)use ($columns)
            {
                $sheet->row(2,[
                    'Menu','Chicken Biriyani','Biriyani','Special','Non Veg','Full,Quarter','180,150','10','Sun,Mon,Tue,Wed,Thur,Fri,sat','05:00 am','12:00 am','Spicy'
                ]);
                $sheet->setColumnFormat(array('F' => '@','G' => '@','J' => '@','K' => '@'));
                $sheet->setAutoSize(true);
                $sheet->loadView('menu.excel-download',compact('columns'));
            });
        })->download('xls');*/
///ALTER TABLE `temp_restaurantmenus` ADD `Status` VARCHAR(1) NOT NULL AFTER `Description`;
        $databasename = Commonsource::getDatabaseName();   
        $arr = array();
        $brandarr = array();
        $pbd = '';
        $columns = DB::select('SELECT column_name from information_schema.columns where table_schema = "'.$databasename.'" and table_name = "temp_restaurantmenus"');
        $menus = DB::SELECT('SELECT m_menu_id as menuid,m_name_type->>"$.type" as type,m_name_type->>"$.name" as name,JSON_UNQUOTE(m_category) as category,JSON_UNQUOTE(m_subcategory) as subcategory,m_diet as diet,JSON_UNQUOTE(m_por_rate->"$.portion1.portion") as portion1 ,JSON_UNQUOTE(m_por_rate->"$.portion2.portion") as portion2,JSON_UNQUOTE(m_por_rate->"$.portion1.exc_rate") as portion1rate,JSON_UNQUOTE(m_por_rate->"$.portion2.exc_rate") as portion2rate,JSON_UNQUOTE(m_days) as days,JSON_UNQUOTE(m_time->"$.from") as from_time,JSON_UNQUOTE(m_time->"$.to") as to_time,m_pack_rate as packing_rate, m_description as description,m_status as status, m_image as images,m_most_selling as most_selling FROM `restaurant_menu` WHERE `restaurant_menu`.`m_menu_id` != " " and `restaurant_menu`.m_rest_id = "'.$id.'" ORDER BY m_menu_id');
        $restaurant_name = DB::SELECT('SELECT name_tagline->>"$.name" as restname FROM `restaurant_master` WHERE `id` = "'.$id.'"' );
        $restname = $restaurant_name[0]->restname;
        $menulist = array();$i=0;
        foreach($menus as $key=>$val)
        {$i++;
            //$menulist['Id'] = "123";
            $menulist[$i]['Type'] = $val->type;
            $menulist[$i]['Name'] = $val->name;
            $category=explode("\"",$val->category);
            $menulist[$i]['Category'] = $category[1];
            $subcategory=explode("\"",$val->subcategory);
            $menulist[$i]['Sub_category'] = $subcategory[1];
            $menulist[$i]['Diet'] = $val->diet;
            $portion=$val->portion1;
            if($val->portion2 != "") $portion=$val->portion1.",".$val->portion2;
            $menulist[$i]['Portion'] = $portion;
            $rate=$val->portion1rate;
            if($val->portion2rate != "") $rate=$val->portion1rate.",".$val->portion2rate;
            $menulist[$i]['Rate'] = $rate;
            $menulist[$i]['Packing_Charge'] = $val->packing_rate;
            $days=str_replace("\"","",$val->days);
            $days_f=explode("[",$days);
            $days_l=str_replace("]","",$days_f[1]);
            $days_cal=str_replace(" ","",$days_l);
            $menulist[$i]['Day'] = $days_cal;
            $menulist[$i]['From_Time'] = date('h:i a', strtotime($val->from_time));
            $menulist[$i]['To_Time'] = date('h:i a', strtotime($val->to_time));
            $menulist[$i]['Description'] = $val->description;
         $menulist[$i]['Status'] = $val->status;
         $menulist[$i]['Images'] = $val->images;
         $menulist[$i]['Most_Selling'] = $val->most_selling;
            
        }
        /*
         * $i=2;
               foreach($menulist as $ky=>$val)
                {
              $sheet->row($i,[
                    $menulist['Type'] ,$menulist['Name'],$menulist['Category'] ,$menulist['Sub_category'],$menulist['Diet'],$menulist['Portion'] ,$menulist['Rate'],$menulist['Packing_Charge'],$menulist['Day'],$menulist['From_Time'],$menulist['To_Time'], $menulist['Description'],$menulist['Status']
                ]);
              $i++;
                }
         */
        Excel::create($restname, function($excel) use ($columns,$menulist)
        {
          $excel->sheet('Sheet1', function($sheet)use ($menulist,$columns)
          {
              $i=1;$j=2;
               foreach($menulist as $ky=>$val)
                {
              $sheet->row($j,[
                    $menulist[$i]['Type'] ,$menulist[$i]['Name'],$menulist[$i]['Category'] ,$menulist[$i]['Sub_category'],$menulist[$i]['Diet'],$menulist[$i]['Portion'] ,$menulist[$i]['Rate'],$menulist[$i]['Packing_Charge'],$menulist[$i]['Day'],$menulist[$i]['From_Time'],$menulist[$i]['To_Time'],$menulist[$i]['Description'],$menulist[$i]['Status'],$menulist[$i]['Images'],$menulist[$i]['Most_Selling']
                ]);
              $j++;$i++;
                }
              $sheet->setColumnFormat(array('F' => '@','G' => '@','J' => '@','K' => '@'));
              $sheet->setAutoSize(true);
              $sheet->loadView('menu.excel-download',compact('columns','menulist'));
          });
        })->download('xls');
}

//Menu Excel Upload
public function menu_upload(Request $request)
{
 $restid = $request['resid'];
 $dietarr = ['NON VEG','VEG','GENERAL'];
 DB::SELECT('TRUNCATE temp_restaurantmenus');
 if (Input::hasFile('upld_file')) {
     $path = Input::file('upld_file')->getRealPath();
	 DB::SELECT('DELETE FROM restaurant_offers where rest_id ="'.$restid.'"');
	 DB::SELECT('DELETE FROM restaurant_menu where m_rest_id ="'.$restid.'"');
	 DB::SELECT('DELETE FROM sub_category WHERE restaurant_id = "'.$restid.'"'); 
	 DB::SELECT('DELETE FROM category WHERE restaurant_id = "'.$restid.'"');
    
     $data = Excel::load($path, function ($reader)
     {
         $reader->ignoreEmpty();
     })->get();//->skipRows(2)
     if (!empty($data) && $data->count() > 0)
     {
         foreach ($data as $row) {
             if (($row->type) != null || ($row->type) != '' || (($row->name) != null || ($row->name) != '') || (($row->category) != null || ($row->category) != '')) {
                 if (($row->type) == null || ($row->type) == '') {
                     $msg = 'error';
                     $data = 'Enter Menu Type'.$row;
                     return response::json(compact('msg', 'data'));
                 }
                 if (($row->name) == null || ($row->name) == '') {
                     $msg = 'error';
                     $data = 'Enter Menu Name';
                     return response::json(compact('msg', 'data'));
                 }
                 if (($row->category) == null || ($row->category) == '') {
                     $msg = 'error';
                     $data = 'Enter Category for Menu ' . strtoupper($row->name);
                     return response::json(compact('msg', 'data'));
                 }
                 if (($row->diet) == null || ($row->diet) == '' || !in_array(strtoupper($row->diet), $dietarr)) {
                     $msg = 'error';
                     $data = 'Invalid Diet for ' . strtoupper($row->name) . '';
                     return response::json(compact('msg', 'data'));
                 }
                 if (($row->portion) == null || ($row->portion) == '') {
                     $msg = 'error';
                     $data = 'Enter Portion for Menu ' . strtoupper($row->portion);
                     return response::json(compact('msg', 'data'));
                 }

                 $temp_qry = TempRestaurantMenus::where('Name', $row->name);
                 if (isset($row->type) && $row->type != '' || $row->type != null) {
                     $temp_qry->where('Type', $row->type);
                 }
                 if (isset($row->category) && $row->category != '' || $row->category != null) {
                     $temp_qry->where('Category', $row->category);
                 }
                 if (isset($row->sub_category) && $row->sub_category != '' || $row->sub_category != null) {
                     $temp_qry->where('Sub_category', $row->sub_category);
                 }
                 if (isset($row->diet) && $row->diet != '' || $row->diet != null) {
                     $temp_qry->where('Diet', $row->diet);
                 }
                 if (isset($row->portion) && $row->portion != '' || $row->portion != null) {
                     $temp_qry->where('Portion', $row->portion);
                 }
                 if (isset($row->rate) && $row->rate != '' || $row->rate != null) {
                     $temp_qry->where('Rate', $row->rate);
                 }
                 if (isset($row->packing_charge) && $row->packing_charge != '' || $row->packing_charge != null) {
                     $temp_qry->where('Packing_Charge', $row->packing_charge);
                 }
                 if (isset($row->day) && $row->day != '' || $row->day != null) {
                     $temp_qry->where('Day', $row->day);
                 }
                 if (isset($row->from_time) && $row->from_time != '' || $row->from_time != null) {
                     $temp_qry->where('From_Time', $row->from_time);
                 }
                 if (isset($row->to_time) && $row->to_time != '' || $row->to_time != null) {
                     $temp_qry->where('To_Time', $row->to_time);
                 }
                 if (isset($row->description) && $row->description != '' || $row->description != null) {
                     $temp_qry->where('Description', $row->description);
                 }
                 if (isset($row->status) && $row->status != '' || $row->status != null) {
                     $temp_qry->where('Status', $row->status);
                 }
                 if (isset($row->images) && $row->images != '' || $row->images != null) {
                     $temp_qry->where('Images', $row->images);
                 }
                 if (isset($row->most_selling) && $row->most_selling != '' || $row->most_selling != null) {
                     $temp_qry->where('Most_Selling', $row->most_selling);
                 }
                 $temp = $temp_qry->first();
                 if (count($temp) <= 0)
                 {
                     $tmp_menu = new TempRestaurantMenus();
                     $tmp_menu->Name = str_replace("'","", trim($row->name));
                     $tmp_menu->Type = $row->type;
                     $tmp_menu->Category =  str_replace("'","", trim($row->category));
                     $tmp_menu->Sub_category = str_replace("'","", trim($row->sub_category));
                     $tmp_menu->Portion = trim($row->portion);
                     $tmp_menu->Rate = trim($row->rate);
                     $tmp_menu->Packing_Charge = isset($row->packing_charge)?$row->packing_charge:'0';
                     $tmp_menu->Day = $row->day;
                     $tmp_menu->Diet = trim($row->diet);
                     $tmp_menu->From_Time = date('H:i:s', strtotime(trim($row->from_time)));
                     $tmp_menu->To_Time = date('H:i:s', strtotime(trim($row->to_time)));
                     $tmp_menu->Description =  str_replace("'","", trim($row->description));
                     $tmp_menu->Status = trim($row->status);
                     $tmp_menu->Images = trim($row->images);
                     $tmp_menu->Most_Selling = trim($row->most_selling);
                     $tmp_menu->save();
                 }
             }
         }
     }
 }
 $this->menucategorygenerate($restid);  //Insert category newly added
 $this->menusubcategorygenerate($restid); //insert sub category newly added
 $this->restaurantmenugenerate($restid); //insert sub category newly added
 $msg = 'success';
 return response::json(compact('msg'));
}
//add category not existing in master
public function menucategorygenerate($restid)
{
 $temp_menus = TempRestaurantMenus::where('id','!=','')->select('Category')->get();
 foreach($temp_menus as $temp)
 {
       $categoryarr= explode(',',$temp->Category);
       for($i =0;$i<count($categoryarr);$i++)
       {
         $exit_status = Category::where('name',trim($categoryarr[$i]))
                        ->where('restaurant_id',trim($restid))->get();
         if(count($exit_status)<=0)
         {
         $category = new Category();
         if (trim($categoryarr[$i]) != '')
         {
             $category->name = ucwords(trim($categoryarr[$i]));
             if ($restid != '')
             {
                 $category->restaurant_id = trim($restid);
             }
             $category->save();
         }
       }
     }
 }
}

//add sub category not existing in master
public function menusubcategorygenerate($restid)
{
 $temp_menus = TempRestaurantMenus::where('id','!=','')->select('Sub_category')->get();
 foreach($temp_menus as $temp)
 {
     $subcategoryarr= explode(',',$temp->Sub_category);
     for($i =0;$i<count($subcategoryarr);$i++) {
         $exit_status = SubCategory::where('name', $subcategoryarr[$i])
             ->where('restaurant_id', trim($restid))
             ->get();
         if (count($exit_status) <= 0) {
             $subcategory = new SubCategory();
             if ($subcategoryarr[$i] != '') {
                 $subcategory->name = ucwords($subcategoryarr[$i]);
                 if ($restid != '')
                 {
                     $subcategory->restaurant_id = $restid;
                 }
                 $subcategory->save();
             }
         }
     }
 }
}
//restaurant menu add
public function restaurantmenugenerate($restid)
{
    $tax = array();
    $menuarr = array();
    $temp_menus = TempRestaurantMenus::where('id', '!=', '')->get();
    $restaurant_dtl = Restaurant_Master::where('id',$restid)->select('extra_rate_percent')->first();
    $extra_percent = $restaurant_dtl['extra_rate_percent'];
    foreach($temp_menus as $temp)
    {
        $menuarr[] = trim(strtolower($temp->Name));
        $string = '';
        $menus = DB::select("SELECT m_name_type->>'$.name' FROM restaurant_menu where LOWER(m_name_type->>'$.name') = '".trim(strtolower($temp->Name))."' and m_rest_id = '".$restid."'");
        if(count($menus)<=0)
        {
            $category= explode(',',ucwords($temp->Category));
            $subcategory= explode(',',ucwords($temp->Sub_category));
            $days= explode(',',$temp->Day);
            $portion= explode(',',$temp->Portion);
            $rate= explode(',',$temp->Rate);
            $dayarr = array();
            for($i =0; $i <count($days);$i++)
            {
                if(in_array(strtoupper($days[$i]),['ALL DAY','ALL DAYS']))
                {
                    $dayarr[] = ['Sun','Mon','Tue','Wed','Thur','Fri','Sat'];
                }
                if(in_array(strtoupper($days[$i]),['SUNDAY','SUN']))
                {
                    $dayarr[] = 'Sun';
                }
                if(in_array(strtoupper($days[$i]),['MONDAY','MON']))
                {
                    $dayarr[] = 'Mon';
                }
                if(in_array(strtoupper($days[$i]),['TUESDAY','TUE','TUES']))
                {
                    $dayarr[] = 'Tue';
                }
                if(in_array(strtoupper($days[$i]),['WEDNESDAY','WED']))
                {
                    $dayarr[] = 'Wed';
                }
                if(in_array(strtoupper($days[$i]),['THURSDAY','THUR','THU','THURS']))
                {
                    $dayarr[] = 'Thu';

                } if(in_array(strtoupper($days[$i]),['FRIDAY','FRI']))
            {
                $dayarr[] = 'Fri';

            } if(in_array(strtoupper($days[$i]),['SATURDAY','SAT']))
            {
                $dayarr[] = 'Sat';
            }
            }
            for($n = 0;$n < count($portion);$n++)
            {
                $j = 0;
                if($portion[$n] != '')
                {
                    $j = $n + 1;
                    if(isset($rate[$n]))
                    {
                        $taxvalue = '0';
                        $taxarr = array();
                        $check_tax_exist  =  DB::SELECT("SELECT t_value,t_name FROM `tax_master` tx left Join `restaurant_master` rm on tx.restaurant_id = rm.id WHERE restaurant_id ='".$restid."' and t_status = 'Y'");
                        if(count($check_tax_exist) >0)
                        {
                            foreach($check_tax_exist as $item)
                            {
                                $taxvalue = $taxvalue +  $item->t_value;
                                $taxarr[] =$item->t_name;
                            }
                            $taxval = $taxvalue;
                        }
                        else
                        {
                            $taxarr = [""];
                            $taxval = '0';
                        }
                        $exc_rate = $rate[$n];
                        $inc_rate = ($rate[$n]*$taxval)/100 + $rate[$n];
                        $extraval = (($rate[$n]*$extra_percent)/100);
                        $final_rate = (($rate[$n]*$extra_percent)/100)+$inc_rate;
                    }
                    else
                    {
                        $exc_rate = '0';
                        $inc_rate = '0';
                        $extraval = '0';
                        $taxarr = [""];
                        $final_rate = (isset($rate[$n])&& $rate[$n] != '0.00')?$rate[$n] : '0.00';
                    }
                    $string .= '"portion'.$j.'":{"portion":"' . $portion[$n] . '","exc_rate":"'.$exc_rate.'","inc_rate":"'.$inc_rate.'","extra_percent":"'.$extra_percent.'","extra_val":"'.$extraval.'","final_rate":"'.$final_rate.'"},';
                }
            }
            $pckchrge = isset($temp->Packing_Charge)?$temp->Packing_Charge:0;
            $string   = rtrim($string, ',');
            $abc      = '{'.$string.'}';
            $tax      = $taxarr;
            $newimage = !empty($temp->Images) ? "'$temp->Images'" : "NULL";
            DB::INSERT("INSERT INTO `restaurant_menu`(`m_rest_id`,`m_menu_id`,`m_pack_rate`,`m_name_type`,`m_diet`,`m_category`, `m_subcategory`, `m_description`, `m_most_selling`,`m_time`,`m_days`,`m_por_rate`,`m_tax`,m_status,m_image)
         VALUES('" . $restid . "','0','".$pckchrge."',json_object('name','" . title_case($temp->Name) . "','type','" . title_case($temp->Type) . "'),'".$temp->Diet."','" . json_encode($category) . "','" . json_encode($subcategory) . "','" . $temp->Description . "','".$temp->Most_Selling."',json_object('from','" .$temp->From_Time. "','to','" .$temp->To_Time. "'),'" . json_encode(array_unique($dayarr)) . "','".$abc."','" . json_encode($tax) . "','".$temp->Status."',".$newimage.")");
        }
        else
        {
            $portion  = explode(',',$temp->Portion);
            $rate     = explode(',',$temp->Rate);
            for($n = 0;$n < count($portion);$n++)
            {
                $j = 0;
                if($portion[$n] != '')
                {
                    $j = $n + 1;
                    if(isset($rate[$n]))
                    {
                        $taxvalue = '0';
                        $taxarr = array();
                        $check_tax_exist  =  DB::SELECT("SELECT t_value,t_name FROM `tax_master` tx left Join `restaurant_master` rm on tx.restaurant_id = rm.id WHERE restaurant_id ='".$restid."' and t_status = 'Y'");
                        if(count($check_tax_exist) >0)
                        {
                            foreach($check_tax_exist as $item)
                            {
                                $taxvalue = $taxvalue +  $item->t_value;
                                $taxarr[] =$item->t_name;
                            }
                            $taxval = $taxvalue;
                        }
                        else
                        {
                            $taxarr = [""];
                            $taxval = '0';
                        }
                        $exc_rate = $rate[$n];
                        $inc_rate = ($rate[$n]*$taxval)/100 + $rate[$n];
                        $extraval =  (($rate[$n]*$extra_percent)/100);
                        $final_rate = (($rate[$n]*$extra_percent)/100)+$inc_rate;
                    }
                    else
                    {
                        $exc_rate = '0';
                        $inc_rate = '0';
                        $taxarr = [""];
                        $extraval = '0';
                        $final_rate = (isset($rate[$n])&& $rate[$n] != '0.00')?$rate[$n] : '0.00';
                    }
                    $string .= '"portion'.$j.'":{"portion":"' . $portion[$n] . '","exc_rate":"'.$exc_rate.'","inc_rate":"'.$inc_rate.'","extra_percent":"'.$extra_percent.'","extra_val":"'.$extraval.'","final_rate":"'.$final_rate.'"},';
                }
            }
            $pckchrge =  isset($temp->Packing_Charge)?$temp->Packing_Charge:0;
            $string   =  rtrim($string, ',');
            $abc      = '{'.$string.'}';
            $tax      =  $taxarr;
            $update3 = DB::UPDATE("UPDATE `restaurant_menu` SET `m_por_rate` = '".$abc."',m_tax ='".json_encode($tax)."',m_time = json_object('from','" .  $temp->From_Time . "','to','" .  $temp->To_Time . "') WHERE LOWER(m_name_type->>'$.name') = '".trim(strtolower($temp->Name))."' and m_rest_id = '".$restid."'");
        }
    }
    DB::SELECT("UPDATE `restaurant_menu` SET `m_status` = 'N' WHERE LOWER(m_name_type->>'$.name') NOT IN ( '" . implode( "', '" , $menuarr ) . "' ) and m_rest_id = '".$restid."'");
}
public function menulist($restid,$userid)
{
 
 $resultarr = array();
 $array = array();
$cat = "General";
         $mainarr['category']= $cat;
        $arrlist['id'] = 1;
                 $arrlist['menu'] = "Please Update App From Store";
                 $arrlist['type'] = "Menu";
                 $arrlist['m_subcategory'] = "General";
                 $arrlist['subcategory_status'] = "General";
                 $arrlist['m_description'] = "Please update the Application from store for new version";
                 $arrlist['m_tax'] = [
                     ""
                 ];
                 $arrlist['m_image'] = "0";
                 $arrlist['m_diet'] = "General";
                 $arrlist['portion'] = "Single";
                 $arrlist['rate'] = 0.00;
                 $arrlist['inv_offer_rate'] = 0;
                 $arrlist['open'] = "00:01:00";
                
                     $arrlist['order_qty'] = "0";
                
                 $arrlist['close'] = "23:58:00";
                 $arrlist['most_selling'] = "Y";
                 $arrlist['menu_status'] = "N";
                 $arrlist['image_status'] = "N";
                 $array[$cat]['menulist'][] = $arrlist;
                 $mainarr['menulist'][] = $arrlist;
                $resultarr[] =  $mainarr;
          
        return response::json(['details' => $resultarr,'count' =>1]);
    }

    public function menulist_new(Request $request)
   {
        $restid     = $request['restid'];
        $userid     = $request['userid'];
        $menutype = $request['menu_type'];
        $condition = '';
        if($menutype=='Veg'){
            $condition = " AND m_diet in ('Veg','General')  ";
        }
        else if($menutype=='MS'){
            $condition = " AND m_most_selling = 'Y' ";
        }
 $timezone = 'ASIA/KOLKATA';
 $date = new DateTime('now', new DateTimeZone($timezone));
 $datetime = $date->format('Y m d h:i:s a');
 $dtime = $date->format('h:i a');
 $time = strtoupper($date->format('h:i a'));
 $ddday=ucwords(strtolower($date->format('l')));
 $day=substr($ddday,0,3);
 $status = '';
 $inv_offer_rate = '';
 $category = DB::SELECT('SELECT restaurant_id,name FROM `category` WHERE status = "Y" and restaurant_id = "'.$restid.'" ORDER BY order_no asc');
 $resultarr = array();
 $array = array();
 foreach($category as $ky=>$val)
 {
     $mainarr = array();
     $cat = $val->name;
     $menu = DB::SELECT("SELECT m_menu_id as res_id,JSON_UNQUOTE(m_name_type->'$.name') as menu,m_most_selling as most_selling,JSON_UNQUOTE(m_name_type->'$.type') as type,m_por_rate as portion,m_subcategory,m_description,m_tax,m_image,m_diet,JSON_UNQUOTE(m_time->'$.from') as open,JSON_UNQUOTE(m_time->'$.to') as close,m_offer_exists,JSON_UNQUOTE(m_present_offers->'$.type') as offer_type,JSON_UNQUOTE(m_present_offers->'$.offer_rate') as offer_rate,JSON_UNQUOTE(m_present_offers->'$.desc') as description,m_pack_rate FROM `restaurant_menu` WHERE  m_rest_id = '" . $val->restaurant_id . "' $condition and (CURRENT_TIME() BETWEEN m_time->>'$.from' and m_time->>'$.to') and JSON_CONTAINS(m_days, '[\"" . $day . "\"]') and m_status = 'Y' and JSON_CONTAINS(m_category, '[\"" . $cat . "\"]') order by `m_subcategory`->>'$[0]'");
     $subarr = [];
     if (count($menu) > 0)
     {
         $mainarr['category']= $cat;
         $details = DB::SELECT('SELECT image_view FROM `category` WHERE `category`.`restaurant_id` = "'. $val->restaurant_id .'"  and `category`.`name` = "'.$cat.'" ORDER BY slno');
         foreach ($menu as $key => $list)
         {
             $offer_status = $list->m_offer_exists;
             $id = $list->res_id;
             $type = $list->type;
             $m_subcategory = $list->m_subcategory;
             $m_description = $list->m_description;
             $m_tax = $list->m_tax;
             $m_image = $list->m_image;
             $m_diet = $list->m_diet;
             $offer_type = $list->offer_type;
             $description = $list->description;
             $offer_rate = $list->offer_rate;
             $pack_rate = $list->m_pack_rate;
             $port = json_decode($list->portion, true);
             $portion_count = count($port);
             $arrlist = array();
             $portion = array();
             foreach ($port as $key => $itemlist)
             {
                 $portionname = $itemlist['portion'];
                 $inv_offer_details = DB::SELECT("SELECT a.inv_offer_details->>'$.$portionname.valid_from' as valid_from,a.inv_offer_details->>'$.$portionname.valid_to' as valid_to,IFNULL(a.inv_offer_details->>'$.$portionname.offer_rate',0) as offer_rate FROM restaurant_menu a LEFT JOIN restaurant_offers b on a.m_rest_id=b.rest_id AND a.inv_offer_details->>'$.$portionname.offer_slno'=b.sl_no WHERE a.m_rest_id='" . $val->restaurant_id . "' AND a.m_menu_id='" . $list->res_id . "' AND now() BETWEEN a.inv_offer_details->>'$.$portionname.valid_from' AND inv_offer_details->>'$.$portionname.valid_to' AND b.active='Y'");
                 if(count($inv_offer_details)!=0){
                     $inv_offer_rate = $inv_offer_details[0]->offer_rate;
                 }
                 else{
                     $inv_offer_rate=0;
                 }
                 $rate = $itemlist['final_rate'] + $pack_rate;
                 $por = '{"portion": "' . $portionname . '", "final_rate": "' . $rate . '"}';
                 $portion[] = json_decode($por);

                 if($userid != 'null')
                 {
                     $cartlist = DB::SELECT("SELECT `qty` from order_details WHERE order_number = 't_$userid' and  rest_id = '" . $val->restaurant_id . "' and  menu_id = '" . $list->res_id . "' and JSON_UNQUOTE(menu_details->'$.portion') = '".$portionname."'");
                     if(count($cartlist) > 0)
                     {
                         $order_qty = $cartlist[0]->qty;
                     }
                     else
                     {
                         $order_qty = '0';
                     }
                 }
             $open = $list->open;
             $close = $list->close;
             if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close))
             {
                 $status = 'Y';
             }
             else
             {
                 $status = 'N';
             }
             if ($offer_status == 'Y')
             {
                 $subcatarr = json_decode($m_subcategory);
                 if(!in_array($subcatarr[0],$subarr))
                 {
                     $subarr[] = $subcatarr[0];
                     $subc     = $subcatarr[0];
                 }
                 else{
                     if($subcatarr[0] != null)
                     {
                         $subc = 'same subcategory';
                     }
                     else{
                         $subc =   'general';
                     }
                 }
                 if($itemlist['final_rate']>0){
                 $arrlist['id'] = $id;
                 $arrlist['menu'] = $list->menu;
                 $arrlist['type'] = $type;
                 $arrlist['m_subcategory'] = (isset($subcatarr[0])&&($subcatarr[0]!= null))?$subcatarr[0]:'General';
                 $arrlist['subcategory_status'] = $subc;
                 $arrlist['m_description'] = $m_description;
                 $arrlist['m_tax'] = json_decode($m_tax,true);
                 $arrlist['m_image'] = json_decode($m_image,true)['img1'];
                 $arrlist['m_diet'] = $m_diet;
                 $arrlist['portion'] = $portionname;
                 $arrlist['rate'] = $rate;
                 $arrlist['inv_offer_rate'] = $inv_offer_rate;
                 $arrlist['open'] = $open;
                 if($userid != 'null') {
                     $arrlist['order_qty'] = $order_qty;
                 }
                 $arrlist['close'] = $close;
                 $arrlist['most_selling'] = $list->most_selling;
                 $arrlist['menu_status'] = $status;
                 $arrlist['offer_exist'] = $offer_status;
                 $arrlist['offer_type'] = $offer_type;
                 $arrlist['offer_rate'] = $offer_rate;
                 $arrlist['image_status'] = $details[0]->image_view;
                 $array[$cat]['menulist'][] = $arrlist;
                 $mainarr['menulist'][] = $arrlist;
                 }
             }
       if ($offer_status == 'Y' && $offer_type == 'P')
             {
                 $subcatarr = json_decode($m_subcategory);
                 if(!in_array($subcatarr[0],$subarr))
                 {
                     $subarr[] = $subcatarr[0];
                     $subc     = $subcatarr[0];
                 }
                 else{
                     if($subcatarr[0] != null)
                     {
                         $subc = 'same subcategory';
                     }
                     else{
                         $subc =   'general';
                     }
                 }
                  if($itemlist['final_rate']>0){
                 $imgarr = json_decode($m_image);
                 $arrlist['id'] = $id;
                 $arrlist['menu'] = $list->menu;
                 $arrlist['type'] = $type;
                 $arrlist['m_subcategory'] = (isset($subcatarr[0])&&($subcatarr[0]!= null))?$subcatarr[0]:'General';
                 $arrlist['subcategory_status'] = $subc;
                 $arrlist['m_description'] = $m_description;
                 $arrlist['m_tax'] =  json_decode($m_tax,true);
                 $arrlist['m_image'] =json_decode($m_image,true)['img1'];
                 $arrlist['m_diet'] = $m_diet;
                 $arrlist['portion'] = $portionname;
                 $arrlist['rate'] = $rate;
                 $arrlist['inv_offer_rate'] = 0;
                 $arrlist['open'] = $open;
                 if($userid != 'null')
                 {
                     $arrlist['order_qty'] = $order_qty;
                 }
                 $arrlist['close'] = $close;
                 $arrlist['most_selling'] = $list->most_selling;
                 $arrlist['menu_status'] = $status;
                 $arrlist['offer_exist'] = $offer_status;
                 $arrlist['offer_type'] = $offer_type;
                 $arrlist['description'] = $description;
                 $arrlist['image_status'] = $details[0]->image_view;
                 $array[$cat]['menulist'][] = $arrlist;
                 $mainarr['menulist'][] = $arrlist;
                  }
             }
             else if ($offer_status == 'N')
             {
                 $subcatarr = json_decode($m_subcategory);
                 if(!in_array($subcatarr[0],$subarr))
                 {
                     $subarr[] = $subcatarr[0];
                     $subc     = $subcatarr[0];
                 }
                 else{
                   /*  if($subcatarr[0] != null)
                     {*/
                                $subc = 'same subcategory';
                           /* }
                            else{
                                $subc =   'general';
                            }*/
                        }
                         if($itemlist['final_rate']>0){
                        $arrlist['id'] = $id;
                        $arrlist['menu'] = $list->menu;
                        $arrlist['type'] = $type;
                        $arrlist['m_subcategory'] = (isset($subcatarr[0])&&($subcatarr[0]!= null))?$subcatarr[0]:'General';
                        $arrlist['subcategory_status'] = (isset($subc)&&($subc!= null))?$subc:'General';
                        $arrlist['m_description'] = $m_description;
                        $arrlist['m_tax'] =  json_decode($m_tax,true);
                        $arrlist['m_image'] = (isset(json_decode($m_image,true)['img1'])&&(json_decode($m_image,true)['img1']!= null))?json_decode($m_image,true)['img1']:'0';
                        $arrlist['m_diet'] = $m_diet;
                        $arrlist['portion'] = $portionname;
                        $arrlist['rate'] = $rate;
                        $arrlist['inv_offer_rate'] = $inv_offer_rate;
                        $arrlist['open'] = $open;
                        if($userid != 'null') {
                            $arrlist['order_qty'] = $order_qty;
                        }
                        $arrlist['close'] = $close;
                        $arrlist['most_selling'] = $list->most_selling;
                        $arrlist['menu_status'] = $status;
                        $arrlist['image_status'] = $details[0]->image_view;
                        $array[$cat]['menulist'][] = $arrlist;
                        $mainarr['menulist'][] = $arrlist;
                         }

                    }
                    }
                     }
                    if(isset($mainarr['menulist']) && count($mainarr['menulist']) >0)
                    {
                           $resultarr[] =  $mainarr;
                    }
            }
        }
        return response::json(['details' => $resultarr,'count' => count($resultarr)]);
    }
     public function most_selling(Request $request)
    {
        $type = $request['type'];
        if($type == 'MS')
        {
         $menu = RestaurantMenu::where('m_rest_id','=',$request['restid'])
                      ->where('m_menu_id','=',$request['menuid'])
                      ->where('m_most_selling','=','Y')
                      ->get();
         if(count($menu)>0)
         {
            RestaurantMenu::where('m_rest_id','=',$request['restid'])
                      ->where('m_menu_id','=',$request['menuid'])->update(
                [
                    'm_most_selling' => 'N'
                ]);
         }
         else
         {
            RestaurantMenu::where('m_rest_id','=',$request['restid'])
                      ->where('m_menu_id','=',$request['menuid'])->update(
                [
                    'm_most_selling' => 'Y'
                ]);
         }
        }
        else
        {
            $menu = RestaurantMenu::where('m_rest_id','=',$request['restid'])
                      ->where('m_menu_id','=',$request['menuid'])
                      ->where('m_status','=','Y')
                      ->get();
         if(count($menu)>0)
         {
            RestaurantMenu::where('m_rest_id','=',$request['restid'])
                      ->where('m_menu_id','=',$request['menuid'])->update(
                [
                    'm_status' => 'N'
                ]);
         }
         else
         {
            RestaurantMenu::where('m_rest_id','=',$request['restid'])
                      ->where('m_menu_id','=',$request['menuid'])->update(
                [
                    'm_status' => 'Y'
                ]);
         }
        }

    }
    public function add_menu_image(Request $request) 
            {
        //add_mobile cust_name upld_file cpl_date heading description custid
        try {
        //$post = $request->all();D:\wamp\tmp\php40DF.tmp
      $data2 = $request['upld_file'];
      $timeDate = date("jmYhis") . rand(991, 9999);
      
      
      
      $image_url='';
      $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d H:i:s');
      $cpl_date= isset($request['cpl_date'])?date("Y-m-d", strtotime(trim($request['cpl_date']))):'';
        if(isset($data2) && $data2 !='')
        {
            $extension2 = strtolower($data2->getClientOriginalExtension());
            $uploadfile = "menu-".time(). '.' .$extension2;
            
            //$path1 = 'uploads/banner/web/' . $url2;
            $image_url = 'uploads/menu/' . $uploadfile;
            move_uploaded_file($data2,$image_url);
//        $img1 = str_replace('data:image/'.$extension2.';base64,', '', $data2);
//        $img1 = str_replace(' ', '+', $img1);
//        $base_data2 = base64_decode($img1);
//        file_put_contents( base_path().'/'.$image_url, $base_data2);
            //Image::make($data2)->save(base_path() . '/uploads/complaints/' . $uploadfile);//->resize(700, 640)
           
            
         //$image_url=$path1;
        }
        else
        {
            $image_url = '';
        }
        
          $menuid  =$request['menuid'];
		  $restaurantid  =$request['restaurant_id'];
        DB::UPDATE("UPDATE `restaurant_menu` set m_image = json_object('img1', $image_url) WHERE m_menu_id=$menuid and m_rest_id=$restaurantid");
		//json_object('img1','" . $image_url . "')
        return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
//               / console.log($e->getMessage());
                 }
                   
    } 
}
