<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use App\NotificationGroup;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Session;

class NotificationGroupController extends Controller
{
    public function notificationgroup_view(Request $request)
    {
		$staffid = Session::get('staffid');
        if(!$staffid){return redirect('');}
       $rows = DB::SELECT('SELECT g_id,g_name,g_query,g_active FROM `notification_group` WHERE `g_active` = "Y"' );
       return view('notifications.notification_group',compact('rows'));
    }
    
     public function add_notification(Request $request)
    {
        
        $types =$request['gname'];
        $user = $request['gquery'];
        $group = NotificationGroup::where('g_name',$request['gname'])
                ->get();
        if(count($group)>0)
        {
            $msg = 'already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $group= new NotificationGroup();
            $group->g_name = $request['gname'];
            $group->g_query = $request['gquery'];
            $group->save();
            $msg = 'success';
            return response::json(compact('msg'));
        }
        return redirect('notification_group');
    }
    public function group_delete(Request $request)
    {
        DB::select('delete from notification_group where g_id="'.$request['id'].'"');
        $msg = 'deleted';
        return response::json(['msg' => $msg]);
    }
    public function update_notification(Request $request)
    {
//        $group = NotificationGroup::where('g_name',$request['gname'])
//                ->get();
//        if(count($group)>0)
//        {
//            $msg = 'exist';
//            return response::json(compact('msg'));
//        }
//        else 
//        {
            DB::UPDATE("UPDATE notification_group SET g_name='".$request['gname']."',g_query=\"".$request['gquery']."\" WHERE g_id='".$request['gid']."' ");
            $msg = 'done';
            return response::json(compact('msg'));
//        }
        return redirect('manage_designation');
    }
    public function customer_list(Request $request)
    {
       $id = $request['id'];
        $search = '';

        if(isset($request['type']) && $request['type']!= '')
        {
            $type = $request['type'];

        if($type == 'ORDERED AMOUNT ABOVE')
        {
            if(isset($request['amount']) && $request['amount']!= '')
            {
                $amount = $request['amount'];
                $search .=  ' group by id,name,lname,mobile_contact HAVING SUM(om.final_total) > '.$amount;
            }
        }
        else if($type == 'RESTAURANTS')
        {
            if(isset($request['restaurantid']) && $request['restaurantid']!= '')
            {
                $restauarnt_id = $request['restaurantid'];
                $search .=  ' where om.rest_id = '.$restauarnt_id;
            }
        }
        else if($type == 'ORDER NUMBERS ABOVE')
        {
            if(isset($request['order_no']) && $request['order_no']!= '')
            {
                $orderno = $request['order_no'];
                $search .=  ' group by id having count(order_number) >= '.$orderno;
            }
        }
        }
        $details = DB::SELECT('SELECT g_query FROM `notification_group` where g_id = "'.$id.'"');
//        return $details[0]->g_query;
        $detai = DB::SELECT($details[0]->g_query.$search);
       $count = count($detai);
       $append     = "";
        $append .=  '<table id="tableexample" class="table table-bordered dataTable no-footer">';
        $append .=    '<thead>';
        $append .=    '<tr>';
        $append .=        '<th>Name</th>';
        $append .=        '<th>Last Name</th>';
        $append .=        '<th>Mobile</th>';
        $append .=    '</tr>';
        $append .=    '</thead>';
        $append .=    '<tbody>';
       if(count($detai)>0)
       {
                 $i=0; 
                    foreach($detai as $customer){
                     $i++;  
            $append .=        '<tr>';
            $append .=            '<td>'.title_case($customer->name).'</td>';
            $append .=            '<td>'.title_case($customer->lname).'</td>';
            $append .=            '<td>'.$customer->mobile_contact.'</td>';
            $append .=        '</tr>';
            
            $append .=        '</table>';
            $append .=    '</tr>';
                    }
           }
        else{
            $append .=        '<tr>';
            $append .=            '<td style="border: none;"></td>';
            $append .=            '<td  style="border: none;text-align: center;">Empty</td>';
            $append .=            '<td  style="border: none;"></td>';
            $append .=        '</tr>';

            $append .=        '</table>';
            $append .=    '</tr>';
        }
        $append .=    '</tbody>';
        $append .=    '</table>';
        return array($append,$count);
    }

    public function staff_list()
    {

    }

    public function filter_customer(Request $request)
    {
        $search = '';
        if(isset($request['selecttype']) && $request['selecttype'] != '')
        {
            $type =  $request['selecttype'];
        }
        $name = $request['cstname'];
        $phone = $request['cstmob'];
        $arr = explode('  ',$name);
        $det = DB::SELECT('SELECT g_query FROM `notification_group` where g_id = "'.$request['q_id'].'"');
        $check = stristr($det[0]->g_query, 'where');
        if(isset($name) && $name != '')
        {
            if($search == "")
            {
                $search.="  LOWER(name) LIKE '".strtolower($name)."%'";
            }
            else
            {
                $search.=" and  LOWER(name) LIKE '".strtolower($name)."%'";
            }
        }
        if(isset($phone) && $phone != '') {
            if($search == "")
            {
                $search.="  LOWER(name) LIKE '".strtolower($name)."%'";
            }
            else
            {
                $search.=" and  LOWER(name) LIKE '".strtolower($name)."%'";
            }
        }
        if(isset($phone) && $phone != '')
        {
            if($search == "")
            {
                $search.="  mobile_contact LIKE '".$phone."%'";
            }
            else
            {
                $search.=" and  mobile_contact LIKE '".$phone."%'";
            }
        }
        if(isset($type) && $type != '')
        {
            if($type == 'ORDERED AMOUNT ABOVE')
            {
                if(isset($request['order_amount']) && $request['order_amount'] != '')
                {
                        $search .= ' group by id,name,lname,mobile_contact HAVING SUM(om.final_total) > ' . $request['order_amount'];
                }
            }
            if($type == 'RESTAURANTS')
            {
                if(isset($request['restaurantid']) && $request['restaurantid']!= '')
                {
                    $restauarnt_id = $request['restaurantid'];
                    if($search == "")
                    {
                        $search.=" om.rest_id = ".$restauarnt_id;
                    }
                    else
                    {
                        $search.=" and  om.rest_id = ".$restauarnt_id;
                    }
                }
            }
            else if($type == 'ORDER NUMBERS ABOVE')
            {
                if(isset($request['orderno']) && $request['orderno']!= '')
                {
                    $orderno = $request['orderno'];
                    $search .=  ' GROUP BY id having count(order_number) >= '.$orderno;
                }
            }
        }
        if($check === FALSE)
        {
            if($search!="")
          {
              $search="where $search ";
          }
         else
         {
            $search ="where ";
         }
        }
        else
        {
           if($search!="")
          {
              $search="and $search ";
          }
         else
         {
            $search ="and ";
         }

        }
        $details = DB::SELECT('SELECT g_query FROM `notification_group` where g_id = "'.$request['q_id'].'"');
        $detai = DB::SELECT($details[0]->g_query.' '.$search);
        $append     = "";
       if(count($detai)>0)
       {
                        $append .=  '<table id="tableexample" class="table table-bordered dataTable no-footer">';
            $append .=    '<thead>';
            $append .=    '<tr>';
            $append .=        '<th>Name</th>';
            $append .=        '<th>Last Name</th>';
            $append .=        '<th>Mobile</th>';
            $append .=    '</tr>';
            $append .=    '</thead>';
            $append .=    '<tbody>';
           if(count($detai)>0) {
               $i = 0;
               foreach ($detai as $customer) {
                   $i++;
                   $append .= '<tr>';
                   $append .= '<td>' . title_case($customer->name) . '</td>';
                   $append .= '<td>' . title_case($customer->lname) . '</td>';
                   $append .= '<td>' . $customer->mobile_contact . '</td>';
                   $append .= '</tr>';

                   $append .= '</table>';
                   $append .= '</tr>';
               }
               $append .= '</tbody>';
               $append .= '</table>';
           }
           else{
               $append .=        '<tr>';
               $append .=            '<td style="border: none;"></td>';
               $append .=            '<td  style="border: none;text-align: center;">Empty</td>';
               $append .=            '<td  style="border: none;"></td>';
               $append .=        '</tr>';

               $append .=        '</table>';
               $append .=    '</tr>';
           }
               $append .=    '</tbody>';
               $append .=    '</table>';
           $count = count($detai);
           return array($append,$count);
       }
    }

    public function restaurantlist(Request $request)
    {
        $staff_id = $request['staff_id'];
        $restaurants = DB::SELECT("SELECT id,name_tagline->>'$.name' as name from restaurant_master r where r.city in (SELECT  a.area_id from users u, internal_staffs s, internal_staffs_area a where u.staffid =s.id and s.id = a.staff_id and a.staff_id = '$staff_id')");
//      $restaurants = DB::SELECT("SELECT  id,name_tagline->>'$.name' as name FROM  restaurant_master");
        return $restaurants;
    }
}

