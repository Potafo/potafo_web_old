<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Category;
class CategoryController extends Controller
{
    public function category_list($id)
    {
        $filterarr = array();
        $restaurant_id = $id;
        $restaurant_name = DB::SELECT('SELECT name_tagline->>"$.name" as name FROM `restaurant_master` WHERE `id` = "'.$restaurant_id.'"' );
        $details = DB::SELECT('SELECT restaurant_id,slno,name,status,image_view,order_no FROM `category` WHERE `category`.`restaurant_id` = "'.$restaurant_id.'" ORDER BY slno');
        return view('menu.category_list',compact('details','restaurant_id','restaurant_name'));
    }
    
  
    public function category_imgview(Request $request)
    {
        $operation = $request['optn'];
        if($operation=='image') {
                        Category::where('restaurant_id', $request['ids'])
                    ->where('slno','=',$request['slno'])->update(
                [
                    'image_view' => $request['imgstatus']
                ]);
        }
        if($operation=='status') {
                        Category::where('restaurant_id', $request['ids'])
                    ->where('slno','=',$request['slno'])->update(
                [
                    'status' => $request['catstatus']
                ]);
        }


    }
    
    //Category order edit
    public function category_order($id,$slno,$val)
    {
        Category::where('restaurant_id',trim($id))
                ->where('slno',trim($slno))
                ->update(['order_no' =>$val]);
        $details = $this->category_list($id);
        $msg = 'editted';
        return response::json(['msg' => $msg,'details' => $details]);
    }

}
