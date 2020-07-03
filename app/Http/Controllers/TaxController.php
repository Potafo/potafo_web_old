<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TaxMaster;
use Helpers\Datasource;
use App\Http\Requests;
use Response;
use DB;

class TaxController extends Controller
{
    public function menu_tax($id)
    {
        $resid = $id;
        $restaurant_name = DB::SELECT('SELECT name_tagline->>"$.name" as name FROM `restaurant_master` WHERE `id` = "'.$resid.'"' );
        $rows=TaxMaster::select('restaurant_id','t_slno','t_name','t_value','t_status')
                ->where('restaurant_id','=',$id)
              ->orderBy('t_slno','asc')
              ->paginate(25);
        return view('tax.taxpercentage',compact('rows','resid','restaurant_name'));
    }
    
    public function add_tax(Request $request)
    {
        $type =$request['type']; 
        $rid = $request['res_id'];

        if($type == 'insert')
        {
            
        $tax = TaxMaster::where('t_name',$request['tax'])
              ->where('restaurant_id','=',$rid)
            ->get();
        if(count($tax)>0)
        {
            $msg = 'already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $tax= new TaxMaster();
            $tax->t_name = strtoupper($request['tax']);
            $tax->t_value = $request['value'];
            $tax->restaurant_id = $request['res_id'];
            $tax->save();
            $msg = 'success';
            return response::json(compact('msg'));
        }
       
//       return view('tax.taxpercentage',compact('msg'));
        }

        else if($type == 'update')
        {
            
            TaxMaster::where('restaurant_id', $request['res_id'])
                    ->where('t_slno',$request['slno'])->update(
                    [
                     't_value' => $request['value'],
                     't_status' => $request['status']
                    ]);

            $msg = 'done';
            return response::json(compact('msg'));
     
        }
    }
    
    public function tax_status(Request $request)
    {
        $tax = TaxMaster::where('restaurant_id','=',$request['rid'])
                       ->where('t_slno','=',$request['slno'])
                      ->where('t_status','=','Y')
                      ->get();
        if(count($tax)>0)
        {
            TaxMaster::where('restaurant_id','=',$request['rid'])
                       ->where('t_slno','=',$request['slno'])->update(
                [
                    't_status' => 'N'
                ]);
        }
        else
        {
            TaxMaster::where('restaurant_id','=',$request['rid'])
                       ->where('t_slno','=',$request['slno'])->update(
                [
                    't_status' => 'Y'
                ]);
        }

    }
    
    public function get_restraurent_category(Request $request)
    {
        $res_id         = $request['id'];
        $select_details = DB::SELECT("SELECT `restaurant_id`,`name` FROM `category` WHERE `restaurant_id`='$res_id' AND`status`='Y'");
        return $select_details;
    }
    
    public function update_restraurent_category_taxes_values(Request $request){
       
        $slno = $request['t_slno'];
        $category = $request['category'];
        $t_name   = $request['t_name'];
        $rest_id  = $request['rest_id'];
        
        $t_name_for_check   = '"'.$request['t_name'].'"';
        $category_for_check   = '"'.$request['category'].'"';
        if($request['category'] == 'all'){
        
            $cat_string = "";
        }else{
            $category_for_check   = '"'.$request['category'].'"';
            $cat_string = "and json_contains(m_category, '[$category_for_check]')";
        }
        $check_tax_exist  = DB::SELECT("SELECT m_rest_id,m_tax  FROM `restaurant_menu` WHERE m_rest_id='".$rest_id."' and json_contains(m_tax, '[$t_name_for_check]') $cat_string");
        if(count($check_tax_exist) == 0)
        {
                $tax_values = DB::SELECT("SELECT * FROM `tax_master` WHERE `t_slno`='$slno'");
                $tax = $tax_values[0]->t_value;
               $sel_all_category  = DB::SELECT("SELECT m_rest_id,m_tax,m_menu_id,json_length(m_por_rate) as len,m_menu_id  FROM `restaurant_menu` WHERE m_rest_id='".$rest_id."'  $cat_string");    
               
               
               foreach ($sel_all_category as $value) {
                  $len       = $value->len;
                  $m_menu_id = $value->m_menu_id;
                  $res_id    = $rest_id;
                  $i=0;
                  for($k = 0; $k<$len;$k++)
                  {
                      $i++;
                      
                      $update2            = DB::UPDATE("UPDATE `restaurant_menu` SET `m_por_rate` = JSON_SET(`m_por_rate`,'$.portion$i.inc_rate',((`m_por_rate`->>'$.portion$i.exc_rate' * $tax) / 100) + `m_por_rate`->>'$.portion$i.inc_rate') WHERE `m_rest_id`='$res_id' AND `m_menu_id`='$m_menu_id'");
                      $add_percentage_val =  DB::UPDATE("UPDATE `restaurant_menu` SET `temp_value`= (`m_por_rate`->>'$.portion$i.exc_rate' * $tax) / 100 WHERE `m_rest_id`='$res_id' AND `m_menu_id`='$m_menu_id'");
                      $get_percentage     =  DB::SELECT("SELECT `temp_value` FROM `restaurant_menu` WHERE `m_rest_id`='$res_id' AND `m_menu_id`='$m_menu_id'");
                      $percent_val = $get_percentage[0]->temp_value;
                      $update3 = DB::UPDATE("UPDATE `restaurant_menu` SET `m_por_rate` = JSON_SET(`m_por_rate`,'$.portion$i.final_rate',(`m_por_rate`->>'$.portion$i.final_rate' + $percent_val)) WHERE `m_rest_id`='$res_id' AND `m_menu_id`='$m_menu_id'");
                  }
               }
               $update = DB::UPDATE("UPDATE `restaurant_menu` SET `m_tax`= JSON_ARRAY_APPEND(m_tax, '$','$t_name') WHERE   not json_contains(m_tax, '[$t_name_for_check]') and m_rest_id='$rest_id' $cat_string");
            }
        else
        {
            return 'exist';
        }
    }
}
