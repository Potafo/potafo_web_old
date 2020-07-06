<?php

namespace App\Http\Controllers;

use App\City;
use Illuminate\Http\Request;
use App\Http\Requests;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
use App\Staff;
use App\GeneralSetting;
use App\InternalStaffArea;
use App\DeliveryStaffAttendance;
use App\OrderDetails;
use Helpers\Commonsource;
use App\OrderMaster;
use DateTime;
use Session;
use DateTimeZone;
use App\StaffAttendance;
class StaffController extends Controller
{
    public function __construct()
    { // add this construct to firebase using pages and also 3 use Kreait\
        $this->jsonfile     = config('firebase.fb_jsonfile');
        $this->db_uri     = config('firebase.fb_dburi');
        $serviceAccount = ServiceAccount::fromJsonFile($this->jsonfile);
        $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri($this->db_uri)
        ->create();
        $this->firebase_db = $firebase->getDatabase();
    }


   
     public function view_staff(Request $request)
    {
		$staffid = Session::get('staffid');
        if(!$staffid){return redirect('');}
        $filterarr = array();
        $itemsarr = array();$i=0;
        $autharr = array();
//        $rows =Staff::select('id','first_name','last_name','mobile','designation','active','emergency_number','authcode','confirm_permission','cancel_permission')
//              ->orderBy('id','desc')
//              ->get();
//        $encr_method = Datasource::encr_method();
//
//        foreach($rows as $data)
//        {
//            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
//            $key1 = hash('sha256', $rowkey[0]->explore);
//            $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
//            $key = hash('sha256', $key1);
//            $iv = substr(hash('sha256', $iv1), 0, 16);
//            $authcode = openssl_decrypt(base64_decode($data->authcode), $encr_method, $key, 0, $iv);
//            $id = $data->id;
//            $first_name = $data->first_name;
//            $last_name = $data->last_name;
//            $mobile = $data->mobile;
//            $designation = $data->designation;
//            $active = $data->active;
//            $confirm_permission = $data->confirm_permission;
//            $cancel_permission = $data->cancel_permission;
//            $emergency_number = $data->emergency_number;
//            $itemsarr[$i] = ['id'=>$id,'first_name'=>$first_name,'last_name'=>$last_name,'mobile'=>$mobile,'designation'=>$designation,'active'=>$active,'emergency_number'=>$emergency_number,'authcode'=>$authcode,'confirm_permission'=>$confirm_permission,'cancel_permission'=>$cancel_permission];
//            $i++;
//       }
        return view('staff.manage_staff',compact('rows','filterarr','itemsarr','autharr'));
    }

     //Filtering of Staff List
    public function filter_staff_list(Request $request)
    {
        $search = '';
        $itemsarr = array();$i=0;
        $staffid = $request['staff_id'];
        $flt_status = $request['flt_status'];
        $flt_name = $request['flt_name'];
        $flt_designation = $request['flt_designation'];
        if($flt_status!='')
        {
           $search.=" AND s.active = '".$flt_status."'";
        }
       if($flt_name!='')
       {
          $search.=" AND LOWER(s.first_name) LIKE '%".strtolower($flt_name)."%'";
       }
       if($flt_designation!='Select')
       {
          $search.=" AND s.designation = '".($flt_designation)."'";
       }
       $pointing = $request['current_count'];
       if($pointing=='')
       {
          $pointing=1;
       }
        $startlimit = ($pointing-1)*20;
        $endlimit = ($pointing)*20;
      //$totaldetails = DB::SELECT("SELECT count(id) as totalstaff FROM `internal_staffs`");
        $totaldetails = DB::SELECT("select count(distinct(id))  as totalstaff from internal_staffs s,internal_staffs_area a WHERE s.`id` = a.staff_id and a.area_id in ( SELECT  a1.area_id from users u1, internal_staffs s1, internal_staffs_area a1 where u1.staffid =s1.id and s1.id = a1.staff_id and a1.staff_id = '".$staffid."')and id != ''");
        $details = array();
        $areaarr = array();
        $append='';
        $customer_totaldetails = DB::SELECT("select count(distinct(id))  as totalstaff from internal_staffs s,internal_staffs_area a WHERE s.`id` = a.staff_id and a.area_id in ( SELECT  a1.area_id from users u1, internal_staffs s1, internal_staffs_area a1 where u1.staffid =s1.id and s1.id = a1.staff_id and a1.staff_id = '".$staffid."') $search");
//      $customer_totaldetails = DB::SELECT("SELECT count(id) as totalstaff FROM `internal_staffs` WHERE id != '' $search");
        $count = $customer_totaldetails[0]->totalstaff;
        $customer_res = round($customer_totaldetails[0]->totalstaff/20,0);
        $customer_mode = ($customer_totaldetails[0]->totalstaff)%(20);
        if($customer_mode!=0){$customer_res = $customer_res+1;}
        $total_cutomers=$customer_res;
        $rows = DB::SELECT("SELECT distinct(s.id) as id,s.first_name,s.last_name,s.mobile,s.designation,s.active,s.emergency_number,s.authcode,s.confirm_permission,s.staff_max_credit,s.cancel_permission,s.order_list_cat,s.complaint_status_change from internal_staffs s,internal_staffs_area a, designation_master d WHERE s.`id` = a.staff_id and  trim(s.designation) = trim(d.designation) and a.area_id in ( SELECT  a1.area_id from users u1, internal_staffs s1, internal_staffs_area a1 where u1.staffid =s1.id and s1.id = a1.staff_id and a1.staff_id =  '".$staffid."')
                and s.id != '' and d.hierarchy_order >= (SELECT d1.hierarchy_order from designation_master d1, internal_staffs s2 where d1.designation = s2.designation and s2.id =  '".$staffid."') $search ORDER BY s.id DESC LIMIT $startlimit,20");
//        $rows = DB::SELECT("SELECT distinct(id)  as id,first_name,last_name,mobile,designation,active,emergency_number,authcode,confirm_permission,cancel_permission from internal_staffs s,internal_staffs_area a WHERE s.`id` = a.staff_id and a.area_id in ( SELECT  a1.area_id from users u1, internal_staffs s1, internal_staffs_area a1 where u1.staffid =s1.id and s1.id = a1.staff_id and a1.staff_id = '".$staffid."')and id != '' $search ORDER BY id DESC LIMIT $startlimit,20");
        $encr_method = Datasource::encr_method();
        $appends = '';
        foreach($rows as $data)
        {
            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $key1 = hash('sha256', $rowkey[0]->explore);
            $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
            $key = hash('sha256', $key1);
            $iv = substr(hash('sha256', $iv1), 0, 16);
            $authcode = openssl_decrypt(base64_decode($data->authcode), $encr_method, $key, 0, $iv);
            $id = $data->id;
            $first_name = $data->first_name;
            $last_name = $data->last_name; 
            $mobile = $data->mobile;
            $designation = $data->designation;
            $active = $data->active;
            $confirm_permission = $data->confirm_permission;
            $staff_max_credit = $data->staff_max_credit;
            $cancel_permission = $data->cancel_permission;
            $emergency_number = $data->emergency_number;
            $category=$data->order_list_cat;
            $complaint_status_change=$data->complaint_status_change;
            $itemsarr[$i] = ['id'=>$id,'first_name'=>$first_name,'last_name'=>$last_name,'mobile'=>$mobile,'designation'=>$designation,'active'=>$active,'emergency_number'=>$emergency_number,'authcode'=>$authcode,'confirm_permission'=>$confirm_permission,'cancel_permission'=>$cancel_permission,'staff_max_credit'=>$staff_max_credit,'order_list_cat'=>$category,'complaint_status_change'=>$complaint_status_change];
            $i++;
       }
        $m=$startlimit;
        $append .= '<table id="example1"  class="table table-striped table-bordered">';
        $append .='<thead>';
        $append .='<tr>';
        $append .='<th style="min-width:30px">ID</th>';
        $append .='<th style="min-width:50px">Action</th>';
        $append .='<th style="min-width:100px">Name</th>';
        $append .='<th style="min-width:100px">Last Name</th>';
        $append .='<th style="min-width:80px">Mobile </th>';
        $append .='<th style="min-width:10px">Auth Code</th>';
        $append .='<th style="min-width:80px">Designation</th>';
        $append .='<th style="min-width:15px">Status</th>';
        $append .='</tr>';
        $append .='</thead>';
        $append .='<tbody >';
        foreach($itemsarr as $i)
        {
            $id = $i['id'];
            $name = $i['first_name'];
            $lname = $i['last_name'];
            $mobile = $i['mobile'];
            $authcode = $i['authcode'];
            $designation = $i['designation'];
            $status = $i['active'];
            $em_number = $i['emergency_number'];
            $con_per = $i['confirm_permission'];
            $stff_mx_credit = $i['staff_max_credit'];
            $can_per = $i['cancel_permission'];
            $category=$i['order_list_cat'];
            $complaint_status_change=$i['complaint_status_change'];
            $m++;
                $append .= '<tr><td style="min-width:30px;">'.$id.'</td>';
                          $append .=  "<td style='text-align: left;width:11%'>"
                                  . "<a onclick=\"return staffedit('$id','$name','$lname','$mobile','$em_number','$designation','$status','$authcode','$con_per','$can_per','$stff_mx_credit','$category','$complaint_status_change')\"; class='btn button_table'><i class='fa fa-edit'></i></a>"
                                  . "<a  href='staff_permission/".$id."' class='btn button_table'>"
                                  . "<i class='fa fa-user'></i>"
                                  . "</a>"
                                  . "<a onclick=\"return staffareaadd('$id')\"; class='btn button_table'>"
                                  . "<i class='fa fa-building-o'></i>"
                                  . "</a>"
                                  . "</td>";
                          $append .= '<td style="min-width:100px;">'.$name.'</td>';
                          $append .= '<td style="min-width:100px;">'.$lname.'</td>';
                          $append .= '<td style="min-width:80px;">'.$mobile.'</td>';
                          $append .= '<td style="min-width:10px;">'.$authcode.'</td>';
                          $append .= '<td style="min-width:80px;">'.$designation.'</td>';
                          if($status == 'Y')
                          { 
                              $append .= '<td style="min-width:15px;">Active</td>';
                          }
                          else
                          {
                              $append .= '<td style="min-width:15px;">Inactive</td>';
                          }
                          $append .= '</tr>';
        }
        $append .='</tbody>';
        $append .='</table>';
        return response::json(['filter_data'=>$append,'data_count'=>$total_cutomers,'count' =>$totaldetails[0]->totalstaff,'searchcount' =>$customer_totaldetails[0]->totalstaff]);
    }

    public function staffarea_list(Request $request)
    {
        $id = $request['id'];
        $appends= '';
        $list = DB::SELECT('SELECT area_id,name FROM  internal_staffs_area isa left join city cy on isa.area_id = cy.id WHERE isa.staff_id = "'.$id.'"');
        if(count($list)>0)
        {
            foreach($list as $item)
            {
                $appends .= "<tr>";
                $appends .= "<td style='width:90px'>" . $item->name . "</td>";
                $appends .= "<td style='width:40px'> <a class='btn button_table' onclick=\"area_delete('$id','".$item->area_id."')\";><i class='fa fa-trash'></i></a></td>";
                $appends .= "</tr>";
            }
        }
        else
        {
            $appends .= "<tr>";
            $appends .= "<td style='width:90px;text-align: right;border-right-color: transparent;color: lightgrey;'>No Items</td>";
            $appends .= "<td style='width:40px;'></td>";
            $appends .= "</tr>";
        }
        return $appends;

    }

    public function staff_area_delete(Request $request)
    {
        $area = $request['area'];
        $id = $request['id'];
        DB::SELECT('delete from internal_staffs_area where staff_id = "'.$id.'" and area_id = "'.$area.'"');
        return response::json(['msg' => 'success']);
    }

    public function add_staff(Request $request)
    {
        $staffid     = $request['staffid'];
        $types       = $request['types'];
        $user        = $request['fname'];
        $psw         = mt_rand(1000,9999);//generate 4 digit random authcode
//      $psw         = $request['auth_code'];
        $encr_method = Datasource::encr_method();
        $rowkey      = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1        = hash('sha256', $rowkey[0]->explore);
        $iv1         = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key         = hash('sha256', $key1);
        $iv          = substr(hash('sha256', $iv1), 0, 16);
        $password    = openssl_encrypt($psw, $encr_method, $key, 0, $iv);
        $password    = base64_encode($password);
        $staff       = Staff::where('authcode',$password)
                       ->first();
         if(count($staff)>0)
         {
            $psw         = mt_rand(1000,9999);//generate 4 digit random authcode
            $encr_method = Datasource::encr_method();
            $rowkey      = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $key1        = hash('sha256', $rowkey[0]->explore);
            $iv1         = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
            $key         = hash('sha256', $key1);
            $iv          = substr(hash('sha256', $iv1), 0, 16);
            $password    = openssl_encrypt($psw, $encr_method, $key, 0, $iv);
            $password    = base64_encode($password);

         }
         else
         {
             $password   = $password;
         }

        if($types == 'insert')
        {
        $staff           = Staff::where('mobile',$request['mobile_number'])
                           ->get();
        $code            = Staff::where('authcode',$password)
                           ->get();
        
        if(count($staff)>0 || count($code)>0)
        {

            $msg = 'already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $staff= new Staff();
            $staff->first_name = $request['fname'];
            $staff->last_name = $request['lastname'];
            $staff->mobile = $request['mobile_number'];
            $staff->emergency_number = $request['alternate_number'];
            $staff->staff_max_credit = $request['staff_credit_limit'];
            $staff->designation = $request['designation'];
            $staff->authcode = $password;
            $staff->order_list_cat = $request['category'];
            $staff->complaint_status_change= $request['changefs'];
            $staff->save();
            $stafflist = DB::SELECT("SELECT  a1.area_id as area  from users u1, internal_staffs s1, internal_staffs_area a1 where u1.staffid =s1.id and s1.id = a1.staff_id and a1.staff_id = '$staffid' limit 0,1");
            if(count($stafflist)>0)
            {
               $staffarea  = new InternalStaffArea();
               $staffarea->staff_id = $staff->id;
               $staffarea->area_id = $stafflist[0]->area;
               $staffarea->save();
            }
            $msg = 'success';
            return response::json(compact('msg'));
        }
        return redirect('manage_staff');
        }
        
        else if($types == 'update')
        {
            $pswd = $request['auth_code'];
            $encr_method = Datasource::encr_method();
            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $key1 = hash('sha256', $rowkey[0]->explore);
            $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
            $key = hash('sha256', $key1);
            $iv = substr(hash('sha256', $iv1), 0, 16);
            $passwrd = openssl_encrypt($pswd, $encr_method, $key, 0, $iv);
            $passwrd = base64_encode($passwrd);
            $staff = Staff::where('mobile',$request['mobile_number'])
            ->where('id','!=',$request['userid'])
            ->first();
            $code = Staff::where('authcode',$passwrd)
                ->where('id','!=',$request['userid'])
                ->get();
        if(count($staff)>0 || count($code)>0)
        {
            $msg = 'exist';
            return response::json(compact('msg'));
        }
        else {
            Staff::where('id', $request['userid'])->update(
                ['first_name' => $request['fname'],
                    'last_name' => $request['lastname'],
                    'mobile' => $request['mobile_number'],
                    'emergency_number' => $request['alternate_number'],
                    'designation' => $request['designation'],
                    'staff_max_credit' => $request['staff_credit_limit'],
                    'authcode' => $passwrd,
                    'active' => $request['status'],
                    'confirm_permission' => $request['permission'],
                    'cancel_permission' => $request['can_permission'],
                    'order_list_cat' => $request['category'],
                    'complaint_status_change'=> $request['changefs']
                ]);
            $msg = 'done';
			
			$staff_credit = DB::SELECT("UPDATE  internal_staffs_credits set staff_number='".$request['mobile_number']."' where staff_id='".$request['userid']."'");
            $staff_credit_pay = DB::SELECT("UPDATE  internal_staffs_credits_pay set scp_mobile='".$request['mobile_number']."' where scp_staff_id='".$request['userid']."'");
            return response::json(compact('msg'));
        }
        return redirect('manage_staff');
        }
    }
     public function deliverystaff_login(Request $request)//API to lists the staff name and number if code matches
    {
         $code = $request['code'];
         $ftoken = $request['ftoken'];
         $id = substr($code, 0, 2);
         $authcode = substr($code, 2, 6);
         $encr_method = Datasource::encr_method();
         $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
         $key1 = hash('sha256', $rowkey[0]->explore);
         $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
         $key = hash('sha256', $key1);
         $iv = substr(hash('sha256', $iv1), 0, 16);
         $password = openssl_encrypt($authcode, $encr_method, $key, 0, $iv);
         $password = base64_encode($password);
         $array = array();
        $list = DB::SELECT("SELECT id,first_name,mobile FROM `internal_staffs` WHERE RIGHT(`id`,2) =$id AND  authcode='".$password."' ");
         if(count($list)>0)
         {
            $fullid=$list[0]->id;
            $name=strtoupper($list[0]->first_name);
            $mobile=$list[0]->mobile;
            $array['id'] = $fullid;
            $array['name'] = $name;
            $array['mobile'] = $mobile;
            $msg = 'Exist';
           // return "UPDATE internal_staffs SET ftoken='".trim($ftoken)."' WHERE RIGHT(`id`,2) =$id AND  authcode='".$password."'" ;
            DB::UPDATE("UPDATE internal_staffs SET ftoken='".trim($ftoken)."' WHERE RIGHT(`id`,2) =$id AND  authcode='".$password."'  ");
            return response::json(['msg' => $msg,'staff' => $array]);
         }
         else
         {
             $msg = 'Not Exist';
             return response::json(['msg' => $msg]);
         }

    }
    
    public function deliverystaff_details($id)
    {
        $decimal_point = Commonsource::generalsettings();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $date1 = $date->format('Y-m-d');
        $details = DB::SELECT('select staff_id,entry_date,in_time,out_time FROM delivery_staff_attendence WHERE out_time IS NULL and staff_id="'.$id.'"');
        $todaydelivered = DB::SELECT('select count(order_number) as todaycount from order_master where delivery_assigned_to = "'.$id.'" and date(order_date) = "'.$date1.'" and current_status ="D"');
        foreach($todaydelivered as $key=>$list)
            {
               $counttoady = $list->todaycount;
            }
        $totaldelivered = DB::SELECT('select count(order_number) as totalcount from order_master where delivery_assigned_to = "'.$id.'" and current_status ="D"');
        foreach($totaldelivered as $key=>$list)
            {
               $counttotal = $list->totalcount;
            }
        if(count($details)>0)
        {
            $list = DB::SELECT('select entry_date,cast(in_time as time(0)) as in_time,"'.$counttoady.'" as today_delivered,"'.$counttotal.'" as total_delivered,"Open" as status FROM delivery_staff_attendence WHERE out_time IS NULL and staff_id="'.$id.'"');
        }
        else
        {
            $list = DB::SELECT('select entry_date,cast(in_time as time(0)) as in_time,cast(out_time as time(0)) as out_time,"'.$counttoady.'" as today_delivered,"'.$counttotal.'" as total_delivered,"Closed" as status FROM delivery_staff_attendence WHERE out_time IS NOT NULL and staff_id="'.$id.'"');
        }
        $details = DB::SELECT('select staff_max_credit  FROM internal_staffs WHERE id="'.$id.'"');
        $check = DB::SELECT("SELECT sum(internal_staffs_credits.final_total) as total FROM `internal_staffs_credits` WHERE internal_staffs_credits.staff_id='".trim($id)."' AND internal_staffs_credits.status ='Credit'");
        $area_list = DB::SELECT("SELECT a.id as area_id,a.name as area_name  FROM internal_staffs_area s left join city a on a.id = s.area_id WHERE s.staff_id = ".$id."");
        $notifylimit = Commonsource::notifylimit();
        $limit = $details[0]->staff_max_credit - $notifylimit;
        if(count($check)>0)
        {
            $totalamount = $check[0]->total;
        }
        else{
            $totalamount = '0';
        }
        if(count($list)>0)
        {
          $msg = 'Exist';
          return response::json(['msg' => $msg,'staffdetails' => $list[0],'staff_credit_limit' => round($details[0]->staff_max_credit,$decimal_point),'total_credit' =>round($totalamount,$decimal_point),'limit' =>round($limit,$decimal_point),'area'=> $area_list]);
        }
        else
        {
            $list = DB::SELECT('select "Closed" as status FROM delivery_staff_attendence');
            $msg = 'Exist';
            return response::json(['msg' => $msg,'staffdetails' => $list[0],'staffdetails' => $list[0],'staff_credit_limit' => round($details[0]->staff_max_credit,$decimal_point),'total_credit' =>round($totalamount,$decimal_point),'limit' =>round($limit,$decimal_point),'area'=> $area_list]);
        }
    }
    
    public function deliverystaff_addtime($id)
    {
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $array = array();
        $list = DB::SELECT("SELECT  first_name as fname,RIGHT(`id`,2) as id ,authcode,mobile as username FROM `internal_staffs` WHERE id =  trim(".$id.")");
        if(count($list)>0)
        {
            $id=$list[0]->id;
            $password = openssl_decrypt(base64_decode($list[0]->authcode), $encr_method, $key, 0, $iv);
            $fname=$list[0]->fname;
            $sendpassword = $id.$password;
            $username =$list[0]->username;
            $message = "Hi $fname,Please Update the application from Play Store. Login with your Username as  $username and Password as $sendpassword .For any concerns, conatct our Delivery Helpline no: 9567999142.";
            $sendmsg = urlencode($message);
            $smsurl = Datasource::smsurl($username,$sendmsg);
            $data = file_get_contents($smsurl);

            $msg = 'Please Update the application from Play Store. Check SMS for Login Details';
            $current_status = 'Closed';
            return response::json(['msg' => $msg,'current_status' => $current_status]);
        }
        else
        {
            $msg = 'Please Update the application from Play Store. Check SMS for Login Details';
            $current_status = 'Closed';
            return response::json(['msg' => $msg,'current_status' => $current_status]);
        }
        
    }

    public function deliverycount_list($id,$frmdate,$todate)
    {
        $arr =[];
        $decimal_point = Commonsource::generalsettings();
        $list = DB::SELECT('select date(order_date) as date,count(order_number) as count FROM order_master WHERE delivery_assigned_to = "'.$id.'" and current_status ="D" and date(order_date) >= "'.$frmdate.'" and date(order_date) <= "'.$todate.'" GROUP BY date(order_date)');

        if(count($list)>0)
        {
            foreach($list as $key=>$value)
            {
                $arr['date'] = $value->date;
                $arr['count'] = $value->count;
                $creditcountdetails = DB::SELECT('SELECT count(*) as order_count,sum(final_total) as total FROM `internal_staffs_credits` WHERE status = "Credit" and staff_id = "'.trim($id).'"  and date(order_date) <= "'.$value->date.'" GROUP BY date(order_date)');
                if(count($creditcountdetails)>0)
                {
                    $credit_count = $creditcountdetails[0]->order_count;
                    $cashonhand= isset($creditcountdetails[0]->total)?round($creditcountdetails[0]->total,$decimal_point):0;
                }
                else {
                    $credit_count = 0;
                    $cashonhand = 0;
                }
                $arr['cod'] = $credit_count;
                $arr['cashonhand'] = $cashonhand;
                $listarr[] = $arr;
            }
          $msg = 'Exist';
          return response::json(['msg' => $msg,'deliverycount' => $listarr]);
        }
        else
        {
          $msg = 'Not Exist';
          return response::json(['msg' => $msg]);
        }
    }
    public function delivery_orders($staffid)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d');
        $orders = DB::SELECT("select o.order_number as order_number FROM order_master o left join status_master s on s.id = o.current_status WHERE o.delivery_assigned_to='$staffid' and (o.current_status IN ('C','OP') or (o.current_status IN ('D') and date(o.order_date)='$datetime')) order by s.order asc");
        $detailsarr=array();
        foreach($orders as $key=>$list)
        {       
            $total =   OrderDetails::where('order_number',$list->order_number)->select("sl_no")->count();
            $details = DB::SELECT("select order_number,rs.name_tagline->>'$.name' as rest_name,customer_details->>'$.name' as cust_name,customer_details->>'$.mobile' as phone,'$total' as totalmenu,final_total as final_total,status_details->>'$.C' as time,customer_details->>'$.addresstype' as addresstype,customer_details->>'$.addressline1' as addressline1,customer_details->>'$.addressline2' as addressline2,customer_details->>'$.landmark' as landmark,customer_details->>'$.pincode' as pincode,current_status,IFNULL(rs.address,0) as restaurant_address,IFNULL(delivery_assigned_details->>'$.note',0) as note,IFNULL(customer_details->>'$.longitude',0) as longitude,IFNULL(customer_details->>'$.latitude',0) as latitude,no_contact_del,IFNULL(rs.category,'Restaurant') as order_category FROM order_master LEFT JOIN restaurant_master rs ON order_master.rest_id=rs.id WHERE delivery_assigned_to = '$staffid' and current_status IN ('C','OP','D') and order_number= '$list->order_number'");
            $detailsarr[]=$details[0];
        }
        if(count($detailsarr)>0)
        {
            $msg = 'Exist';
            return response::json(['msg' => $msg,'deliveryorders' => $detailsarr]);
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }
    
     public function delivery_order_details($order_number)
    {
         $decimal_points = commonSource::generalsettings();
         $array=array();
         $rest = DB::SELECT("select rest_id from order_master where order_number = '$order_number'");
        if(count($rest)>0) {
            foreach ($rest as $key => $item) {
                $rest_id = $item->rest_id;
            }

            $total = OrderDetails::where('order_number', $order_number)->select("sl_no")->count();
            $order_details = DB::SELECT("select om.order_number,om.customer_details->>'$.name' as cust_name,om.customer_details->>'$.mobile' as cust_phone,om.customer_details->>'$.addresstype' as cust_addresstype,om.customer_details->>'$.addressline1' as cust_addressline1,om.customer_details->>'$.addressline2' as cust_addressline2,om.customer_details->>'$.landmark' as cust_landmark,om.customer_details->>'$.pincode' as cust_pincode,om.customer_details->>'$.latitude' as cust_latitude,om.customer_details->>'$.longitude' as cust_longitude,rs.name_tagline->>'$.name' as rest_name,rs.address as rest_address,rs.phone as rest_phone,rs.mobile->>'$.ind' as rest_code,rs.mobile->>'$.mobile' as rest_mobile,od.menu_details->>'$.menu_name' as menu,od.qty as qty,od.menu_details->>'$.single_rate' as single_rate,od.final_rate as final_rate,'$total' as totalmenu,om.final_total as final_total,om.sub_total as sub_total,om.current_status,om.total_details as totals,om.payment_method as paymode,IFNULL(JSON_UNQUOTE(coupon_details->>'$.coupon_amount'),0) AS coupon_amount,om.no_contact_del as no_contact_del, IFNULL(rs.category,'Restaurant') as order_category FROM order_master as om LEFT JOIN restaurant_master rs ON om.rest_id=rs.id LEFT JOIN order_details od ON om.order_number=od.order_number WHERE om.order_number= '$order_number'");
            $details = DB::SELECT("select menu_details->>'$.menu_name' as menu,rm.m_diet as diet,menu_details->>'$.single_rate' as single_rate,qty,final_rate FROM order_details as od LEFT JOIN restaurant_menu rm ON od.menu_id=rm.m_menu_id WHERE order_number='$order_number' and m_rest_id='$rest_id'");
            $menuarr = array();
            foreach ($order_details as $key => $items) {
                $array['cust_name'] = $items->cust_name;
                $array['cust_phone'] = $items->cust_phone;
                $array['cust_addresstype'] = $items->cust_addresstype;
                $array['cust_addressline1'] = $items->cust_addressline1;
                $array['cust_addressline2'] = $items->cust_addressline2;
                $array['cust_landmark'] = $items->cust_landmark;
                $array['cust_pincode'] = $items->cust_pincode;
                $array['cust_latitude'] = $items->cust_latitude;
                $array['cust_longitude'] = $items->cust_longitude;
                $array['rest_name'] = $items->rest_name;
                $array['rest_address'] = $items->rest_address;
                $array['rest_phone'] = $items->rest_phone;
                $array['rest_code'] = $items->rest_code;
                $array['rest_mobile'] = $items->rest_mobile;
                $array['paymode'] = $items->paymode;
                $array['coupon_amount'] = (string)round($items->coupon_amount, $decimal_points);
                $array['totalmenu'] = $total;
                $array['final_total'] = (string)round($items->final_total, $decimal_points);
                $array['sub_total'] = (string)round($items->sub_total, $decimal_points);
                $array['packing_charge'] = (string)round(json_decode($items->totals, true)['packing_charge'], $decimal_points);
                $array['delivery_charge'] = (string)round(json_decode($items->totals, true)['delivery_charge'], $decimal_points);
                $array['discount_amount'] = (string)round(json_decode($items->totals, true)['discount_amount'], $decimal_points);
                $statusname = $items->current_status;
                if ($statusname == 'P') {
                    $array['status'] = 'Placed';
                } else if ($statusname == 'C') {
                    $array['status'] = 'Confirmed';
                } else if ($statusname == 'OP') {
                    $array['status'] = 'Picked';
                } else if ($statusname == 'D') {
                    $array['status'] = 'Delivered';
                } else if ($statusname == 'CA') {
                    $array['status'] = 'Cancelled';
                }
                $array['no_contact_del'] = $items->no_contact_del;
                $array['order_category'] = $items->order_category;
            }
            $msg = 'Exist';
            return response::json(['msg' => $msg, 'deliveryorder_details' => $array, 'menu' => $details]);
        }
        else{
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }
    
    public function order_status($order_number,$status)
    {
       		
		$arr = array();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $time = strtoupper($date->format('h:i a'));
        $decimal_point = Commonsource::generalsettings();
       

		$orderstatus_current = DB::SELECT("SELECT current_status,delivery_assigned_to,rest_details->>'$.name' as restname,ROUND(final_total,$decimal_point) as omfinal_total from `order_master` WHERE order_number =$order_number");
        
        if(count($orderstatus_current)>0)
		{
            $currentstatus = $orderstatus_current[0]->current_status;
            $assignedstaff = $orderstatus_current[0]->delivery_assigned_to;
            $restname = $orderstatus_current[0]->restname;

        }

        if($currentstatus == 'C')
        {

            $statustoupdate = 'OP';
            $title =  'Order Picked';
            $message = "Your Order,Order Number - $order_number  has been collected from $restname and is on the way to your location.";
        }
        else if($currentstatus == 'OP')
        {

            $statustoupdate = 'D';
            $title = 'Order Delivered';
            $message = "Your Order,Order Number - $order_number from $restname has been successfully delivered by our staff. Request to please rate us for bettering our services.";
        }
        else
        {
            $msg = 'Successful';
            return response::json(['msg' => $msg]);
        }
        DB::SELECT("UPDATE order_master SET current_status = '$statustoupdate',readytopick= 'Y',status_details=JSON_INSERT(status_details,'$.$statustoupdate','$time') WHERE order_number='$order_number'");
          
        $is_exist= DB::SELECT("SELECT ftoken,om.customer_id,name FROM order_master om join customer_list cm on om.customer_id = cm.id  join ftoken_master fm on fm.customer_id = cm.id WHERE order_number =$order_number");
        if(count($is_exist)>0)
        {
            foreach($is_exist as $item)
            {

                    $arr['to'] = $item->ftoken;
                    $arr['title'] = $title;
                    $arr['message'] = $message;
                    $arr['image'] = 'null';
                    $arr['action'] = 'orderhistory';
                    $arr['action_destination'] = 'null';
                    $arr['app_type'] = 'customerapp';
                    $result = Commonsource::notification($arr);
            }
        }
        if($statustoupdate == 'D')
        {
             DB::SELECT("UPDATE  internal_staffs_credits SET status = 'Credit',final_total = " . $orderstatus_current[0]->omfinal_total . "   where order_number = '$order_number'");
        }

        $msg = 'Successful';
        return response::json(['msg' => $msg]);
		

    }
    public function new_order_check($staffid)
    {
       
        $detail = DB::SELECT("SELECT count(order_number) as count from order_master where staff_notified = 'N' and delivery_assigned_to = '$staffid' and current_status='C'");
        $count = $detail[0]->count;
        if(count($detail)>0)
        {
            $msg = 'New order';
            return response::json(['msg' => $msg,'count' => $count]);
        }
        else
        {
            $msg = 'No order';
            return response::json(['msg' => $msg]);
        }
    }
     public function new_order_status($staffid)
    {
         $timezone = 'ASIA/KOLKATA';
         $date = new DateTime('now', new DateTimeZone($timezone));
         $time = strtoupper($date->format('h:i a'));
         DB::SELECT("UPDATE `order_master` SET `staff_notified` ='Y'  WHERE staff_notified = 'N' and delivery_assigned_to = '$staffid' and status_details->>'$.C' <='$time'");
         $msg = 'Successful';
         return response::json(['msg' => $msg]);
    }

    public function delivery_range_check(Request $request)
    {
        $dest_distance = '0';
        $ardius = '0';
        $id        = $request['user_id'];
        $line1     = $request['line1'];
        if(!isset($request['latitude']) && $request['latitude'] == 'null' || $request['latitude'] == '')
        {
            $latitude  = '0';
        }
        else
        {
            $latitude  = $request['latitude'];
        }
        if(!isset($request['latitude']) && $request['longitude'] == 'null' || $request['longitude'] == '')
        {
            $longitude  = '0';
        }
        else
        {
            $longitude  = $request['longitude'];
        }
        $rest = DB::SELECT("select rest_id,rs.google_location as rest_location,rs.geo_cordinates as cordinates,rs.delivery_range_unit->>'$.range' as range_unit from order_master om LEFT JOIN restaurant_master rs ON om.rest_id=rs.id where order_number = 't_$id'");
       if (count($rest) > 0)
       {
           $radius = $rest[0]->range_unit;
           if (!isset($rest[0]->cordinates) && $rest[0]->cordinates == '' || $rest[0]->cordinates == 'null' || $rest[0]->cordinates == ' ') {
               if (!isset($rest[0]->rest_location) && ($rest[0]->rest_location == 'null' || $rest[0]->rest_location == '' || $rest[0]->rest_location == ' ')) {
                   return response::json(['msg' => 'Service Unavailable At This Area']);
               }
               else
               {
                   $cordnt = Commonsource::latitude_longitude($rest[0]->rest_location);
                   if ($cordnt[2] == '0')
                   {
                       return response::json(['msg' => 'Service Unavailable At This Area']);
                   }
                   else
                   {
                       $cordinates = $cordnt;
                   }
               }
           }
           else
           {
               $crdarr = explode(',', $rest[0]->cordinates);
               $cordinates = $crdarr;
           }

           if ($latitude != '0' || $longitude != '0')
           {
               $dest_distance = Commonsource::distance_calculate($latitude, $cordinates[0], $longitude, $cordinates[1]);
               if ($dest_distance <= $radius)
               {
                   $msg = 'success';
               }
               else
               {
                   $msg = 'Service Unavailable At This Area';
               }

           }
           else if ($latitude == '0' || $longitude == '0')
           {
               if ($line1 == '' || $line1 == ' ' || $line1 == 'null') {
                   return response::json(['msg' => 'Service Unavailable At This Area']);
               }
               else
               {
                   $cordnt = Commonsource::latitude_longitude($line1);
                   if ($cordnt[2] == '0')
                   {
                       return response::json(['msg' => 'Service Unavailable At This Area']);
                   }
                   else
                   {
                       $cordt = $cordnt;
                   }
                   if(count($cordt)>0 || ($cordnt[0] != '' || $cordnt[0] != 'NULL' || $cordnt[0] != '0' ||$cordnt[0] != ' ' || $cordnt[1] != ''|| $cordnt[1] != 'NULL'|| $cordnt[1] != '0'|| $cordnt[1] != ' '))
                   {
                       $dest_distance = Commonsource::distance_calculate($cordnt[0], $cordinates[0], $cordnt[1], $cordinates[1]);
                       if ($dest_distance <= $radius)
                       {
                           $msg = 'success';
                       }
                       else
                       {
                           $msg = 'Service Unavailable At This Areas';
                       }
                   }
                   else
                   {
                       $msg = 'Service Unavailable At This Area';
                   }
               }
           }
       }
       else
       {
           return response::json(['msg' => 'Service Unavailable At This Area']);
       }
        return response::json(['msg' => $msg]);
      /*  if($line1 != '' || $line1 != ' ' || $line1 != 'null')
        {
            $googlekey = Commonsource::googleapikey();
            $arr = array();
            $rest = DB::SELECT("select rest_id,rs.google_location as rest_location,rs.delivery_range_unit->>'$.range' as range_unit from order_master om LEFT JOIN restaurant_master rs ON om.rest_id=rs.id where order_number = 't_$id'");
            if (count($rest) > 0)
            {
                if ($rest[0]->rest_location != '')
                {
                    $origin = $rest[0]->rest_location;
                    $radius = $rest[0]->range_unit;
                    $snw = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=' . urlencode($origin) . '&destinations=' . urlencode($line1) . '&key=' . $googlekey;
                    $menus = file_get_contents($snw);
                    $menulist = json_decode($menus, true);
                    $distance = $menulist['rows'][0]['elements'][0]['distance']['text'];
                    $distance_unit = explode(' ', $distance)[1];
                    $dest_distance = explode(' ', $distance)[0];
                    if (strtoupper($distance_unit) == 'KM') {
                        if ($radius > 0) {
                            if ($dest_distance <= $radius) {
                                $msg = 'success';

                            } else {
                                $msg = 'not success';
                            }
                        }
                        else
                        {
                            $msg = 'Restaurant Distance Radius Not Set';
                        }
                    }
                    else if (strtoupper($distance_unit) == 'M')
                    {
                        $msg = 'success';
                    }
                    return response::json(['msg' => $msg]);
                }
                else
                {
                    $msg = 'Restuarant Location not Set';
                    return response::json(['msg' => $msg]);
                }
            } else {
                return response::json(['msg' => 'Error']);
            }
        }
        else {
            return response::json(['msg' => 'Error']);
        }*/
    }
//    public function minimumcartcheck($userid)
//    {
//        $decimal_points = commonSource::generalsettings();
//        $order = DB::SELECT("select rest_id,IFNULL(sub_total,0) as sub_total,IFNULL(min_cart_value,0) as min_cart FROM order_master LEFT JOIN restaurant_master ON restaurant_master.id=order_master.rest_id where order_number = 't_$userid' ORDER BY order_date desc limit 1");
//        if(count($order) >0) {
//            $subtotal = $order[0]->sub_total;
//            $min_cart = $order[0]->min_cart;
//            if ($subtotal >= $min_cart)
//            {
//                $msg = 'success';
//            }
//            else
//            {
//                $msg = 'Sorry! Minimum cart amount should be Rs '.(string)round($min_cart, $decimal_points).' for this restaurant.';
//            }
//        }
//        else
//        {
//            $msg = 'Error';
//        }
//        return response::json(['msg' => $msg]);
//
//    }

    public function minimumcartcheck($userid)
    {
        $decimal_points = commonSource::generalsettings();
        $order = DB::SELECT("select rest_id,IFNULL(sub_total,0) as sub_total,IFNULL(min_cart_value,0) as min_cart FROM order_master LEFT JOIN restaurant_master ON restaurant_master.id=order_master.rest_id where order_number = 't_$userid' ORDER BY order_date desc limit 1");
        if(count($order) >0) {
            $rest_id = $order[0]->rest_id;
            $timezone = 'ASIA/KOLKATA';
            $date = new DateTime('now', new DateTimeZone($timezone));
            $datetime = $date->format('Y m d h:i:s a');
            $time = strtoupper($date->format('h:i a'));
            $operationtime =  strtoupper($date->format('H:i:s'));
            $day = strtoupper($date->format('l'));
            $restlist = DB::SELECT("select rt_rest_id as rest_id,rt_from_time as from_time,rt_to_time as to_time from restaurant_timings left join order_master on restaurant_timings.rt_rest_id=order_master.rest_id join restaurant_master on restaurant_master.id = restaurant_timings.rt_rest_id where order_number='t_$userid' AND rest_id='$rest_id'  AND busy='N' AND force_close = 'N' AND status='Y' and rt_day = '$day' and time(rt_from_time) <= '$operationtime' and time(rt_to_time) >='$operationtime'");
//          $restlist = DB::SELECT("SELECT rest_id,operational_time,json_length(operational_time->'$." . $day . "')  as count FROM restaurant_master LEFT JOIN order_master ON restaurant_master.id=order_master.rest_id WHERE order_number='t_$userid' AND rest_id='$rest_id'  AND busy='N' AND status='Y' ");
            if(count($restlist)!=0) {
                $c=0;
//                foreach($restlist as $key=>$value)
//                {
//                    $open      = strtoupper($value->from_time);
//                    $close     = strtoupper($value->to_time);
//                    if (strtotime($time) >= strtotime($open) && strtotime($time) <= strtotime($close)  )
//                    {
//                        $c=0;
//                        break;
//                    }
//                    else
//                    {
//                        $c=1;
//                    }
//                }
                    if($c==0) {
            $order_details = DB::SELECT("SELECT a.rest_id,a.menu_id,b.m_name_type->>'$.name' as menuname,a.order_number FROM order_details a left join restaurant_menu b on a.menu_id=b.m_menu_id AND a.rest_id=b.m_rest_id WHERE a.order_number='t_$userid' and a.final_rate >0 ");
                        $inactive_menlist = "";
                        $i=0;
            foreach($order_details as $menu){
                            $dayname = ucwords($date->format('D'));
                             $dayname = '"'.$dayname.'"';
                             $menuname = $menu->menuname;
                            $menuid= $menu->menu_id;
                            $rest_id= $menu->rest_id;
                            $menu_info = DB::SELECT("SELECT m_menu_id,m_time,m_name_type->>'$.name' as menuname FROM restaurant_menu WHERE m_rest_id='$rest_id' and m_menu_id='$menuid' and json_contains(m_days,'[$dayname]') AND DATE_FORMAT(NOW(),'%H:%i') between m_time->>'$.from' AND m_time->>'$.to' AND m_status='Y' ");
                            if(count($menu_info)==0 &&  $i==0){
                                $inactive_menlist .= "$menuname";
                            }
                            if(count($menu_info)==0 &&  $i!=0){
                                $inactive_menlist .= ", $menuname";
                            }
                            $i++;
                        }
                        if($inactive_menlist!='') {
                          return response::json(['msg' =>"$inactive_menlist - Not Available Now"]);
                        }
                        $subtotal = $order[0]->sub_total;
                        $min_cart = $order[0]->min_cart;
                        if ($subtotal >= $min_cart)
                        {
                            $msg = 'success';
                        }
                        else
                        {
                            $msg = 'Sorry! Minimum cart amount should be Rs '.(string)round($min_cart, $decimal_points).'';
                        }
                    } else{
                        $msg = 'Sorry this restaurant is Currently Not Available.';
                    }
            } else {
                $msg = 'Sorry this restaurant is Currently Not Available.';
            }
            
        }
        else
        {
            $msg = 'Error';
        }
        return response::json(['msg' => $msg]);

    }
    public function testing()
    {
        $tokenarrs = ["1","2","3","4","5","6"];
        $chunked_arr = array_chunk($tokenarrs,3);
        return $chunked_arr[1];
       /*$lon2  = "75.7842173";//11.246987,75.7842173,17
         $lon1  = "75.8744252";//11.3054724,75.8744252
         $lat2  = "11.246987";
         $lat1  = "11.3054724";
         $theta = $lon1 - $lon2;
         $dist  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
         $dist  = acos($dist);
         $dist  = rad2deg($dist);
         $miles = $dist * 60 * 1.1515;
         return ($miles * 1.609344);*/

//     return (SQRT((SIN(($lat2*(3.14159/180)-$lat1*(3.14159/180))/2))^2+COS($lat2*(3.14159/180))*COS($lat1*(3.14159/180))*SIN((($lon2*(3.14159/180)-$lon1*(3.14159/180))/2))^2));
       /* $restaurant = DB::SELECT('SELECT rt.id as res_id,rt.google_location as location FROM `restaurant_master` rt');
        foreach($restaurant as $item)
        {
            $cordinates = Commonsource::latitude_longitude($item->location);
            $cord = $cordinates[0].",".$cordinates[1];
            DB::UPDATE('UPDATE `restaurant_master` set geo_cordinates= "'.$cord.'" where id  = "'.$item->res_id.'"');


        }*/
    }
    public function ftoken_staff_check(Request $request) {
        $staffid= $request['code'];
        $ftoken = $request['ftoken'];
        $check = DB::SELECT("SELECT ftoken FROM internal_staffs WHERE id='".$staffid."' AND trim(ftoken)='".trim($ftoken)."' ");
        if(count($check)==0){
            DB::UPDATE("UPDATE internal_staffs SET ftoken='".trim($ftoken)."' WHERE id='".$staffid."' ");
             $msg = 'updated';
        }
        else{
            $msg = 'nochanges';
        }
         return response::json(['msg' => $msg]);
    }

    public function notificationsubmit(Request $request)
    {
        
        $userarr = array();
        $tokenarr = array();
        $arr =   array();
        $post = $request->all();
        $title = $post['title'];
        $group = $post['group'];
        $message = $post['message'];
        $expiry_date = date('Y-m-d',strtotime($post['expiry_date']));
        if(strtolower($group) == 'all')
        {
            DB::select("INSERT INTO `notifications`(`is_all`, `title`, `message`, `entry_date`, `expiry`) VALUES ('Y','".$title."','".$message."',now(),'".$expiry_date."')");
            $arr['to'] = '/topics/potafo';
            $arr['title'] = trim($title);
            $arr['message'] = trim($message);
            $arr['image'] = 'null';
            $arr['action'] = 'notification';
            $arr['action_destination'] = 'null';
            $arr['app_type'] = 'customerapp';
            $result = Commonsource::notification($arr);
        }
        else
        {
            $search = '';
            if(isset($post['type']) && $post['type']  != '')
            {
                $type = $post['type'];

                if($type == 'ORDERED AMOUNT ABOVE')
                {
                    if(isset($request['amount']) && $request['amount'] != '')
                    {
                        $search .= ' group by id,name,lname,mobile_contact HAVING SUM(om.final_total) > ' . $request['amount'];
                    }
                }
                if($type == 'ORDER NUMBERS ABOVE')
                {
                    if(isset($request['order_no']) && $request['order_no'] != '')
                    {
                        $search .= ' GROUP BY id HAVING COUNT(order_number) >=' .$request['order_no'];
                    }
                }
                if($type == 'RESTAURANTS')
                {
                    if(isset($request['restaurantid']) && $request['restaurantid']!= '')
                    {
                        $restauarnt_id = $request['restaurantid'];
                        $search.=" and  om.rest_id = ".$restauarnt_id;
                    }
                }
            }
           $result = DB::SELECT('SELECT g_query FROM  notification_group WHERE g_id = "'.trim($group).'" ');
           $detail = DB::SELECT($result[0]->g_query.$search);
           foreach($detail as $item)
           {
               if(!in_array($item->id,$userarr))
               {
                   if(isset($post['type']) && $post['type'] == 'DELIVERY STAFF') {
					   $notify_app = 'DELIVERY_STAFF';
                       $userarr[] = $item->id;
                       if(!in_array($item->ftoken,$tokenarr))
                       {
                           $tokenarr[] = $item->ftoken;
                       }
                   }
				   else if(isset($post['type']) && $post['type'] == 'PARTNERS') {
					    $notify_app = 'PARTNERS';
                       $userarr[] = $item->id;
                       if(!in_array($item->ftoken,$tokenarr))
                       {
                           $tokenarr[] = $item->ftoken;
                       }
                   }
                   else
                   {
					   $notify_app = 'CUSTOMER';
                       $userarr[] = $item->id;
                       $data    = DB::SELECT("SELECT * FROM ftoken_master WHERE customer_id ='".trim($item->id)."'");
                       foreach($data as $key)
                       {
                           if(isset( $key->ftoken) && !in_array($key->ftoken,$tokenarr))
                           {
                               $tokenarr[] = $key->ftoken;
                           }
                       }
                   }
               }
           }
            $userval = "'".implode ( "','", $userarr )."'";
            DB::INSERT("INSERT INTO `notifications`(`is_all`,`groupid`,`title`, `message`, `entry_date`, `expiry`,`user_list`) VALUES ('N','".$group."','".$title."','".$message."',now(),'".$expiry_date."',JSON_ARRAY($userval))");
            $chunked_arr = array_chunk($tokenarr,1000);
            foreach($chunked_arr as $index=>$item)
            {
                 $arr['tokens'] =$item;
                 $arr['title'] = trim($title);
                 $arr['message'] = trim($message);
                 $arr['image'] = 'null';
                 $arr['action'] = 'notification';
                 $arr['action_destination'] = 'null';
				 if($notify_app == 'DELIVERY_STAFF') 
				 {
					$arr['app_type'] = 'deliveryapp';
				 }
				 if($notify_app == 'PARTNERS') 
				 {
					$arr['app_type'] = 'partnerapp';
				 }
				 if($notify_app == 'CUSTOMER') 
				 {
					$arr['app_type'] = 'customerapp';
				 }
				 $result = Commonsource::group_notification($arr);
			}	 
        }
        return 'success';
    }

    public function staff_area(Request $request)
    {
          $append = '';
          $id = trim($request['id']);
          $area = trim($request['area']);
          $list_qry = InternalStaffArea::where('staff_id',$id)->where('area_id',$area);
          $list = $list_qry->select('staff_id','area_id')->first();
          if(count($list)<=0)
          {
              DB::INSERT('INSERT INTO  `internal_staffs_area`(`staff_id`, `area_id`) VALUES ("'.$id.'","'.$area.'")');
              $msg = 'success';
           }
           else
           {
               $msg = 'Already Exist';
           }
        return response::json(['msg' => $msg]);

    }

    public function get_staff_credit_limit()
    {
        $general = GeneralSetting::where('id','!=','')->select('staff_credit_limit')->first();
        if(count( $general)>0)
        {
            $msg    = 'Exist';
            $credit = $general['staff_credit_limit'];
        }
        else
        {
            $msg = 'Not Exist';
        }
        return response::json(['msg' => $msg,'credit' =>$credit]);
    }

    public function get_staff_credit_sum(Request $request)
    {
        $decimal_points = commonSource::generalsettings();
        $data = [];
        $mobile= $request['mobile'];
		
		$staffdetails = DB::SELECT("SELECT first_name,last_name,mobile FROM `internal_staffs` WHERE mobile ='".trim($mobile)."'");
		if(count($staffdetails)>0)
        {
			$check = DB::SELECT("SELECT sum(internal_staffs_credits.final_total) as total,internal_staffs.first_name,internal_staffs.last_name,internal_staffs.mobile
			FROM `internal_staffs_credits` LEFT JOIN `internal_staffs`  ON internal_staffs_credits.staff_id=internal_staffs.id   WHERE internal_staffs_credits.staff_number='".trim($mobile)."' AND internal_staffs_credits.status ='Credit'");
			if(count($check)>0 && isset($check[0]->first_name))
			{
				$data['total'] = (string) round($check[0]->total,$decimal_points);
				$data['first_name'] = $check[0]->first_name;
				$data['last_name'] = $check[0]->last_name;
				$data['mobile'] = $check[0]->mobile;
				$msg = 'exist';
				return response::json(['msg' => $msg,'data' => $data]);
			}
			else
			{
				$data['total'] = 0.00;
				$data['first_name'] = $staffdetails[0]->first_name;
				$data['last_name'] = $staffdetails[0]->last_name;
				$data['mobile'] = $staffdetails[0]->mobile;
				$msg = 'exist';
				return response::json(['msg' => $msg,'data' => $data]);
			}
		}
		else
		{
			$msg = 'notexist';
            return response::json(['msg' => $msg]);
			
		}
    }

    public function credit_amount(Request $request)
    {
        $arr = [];
        $decimal_points = commonSource::generalsettings();
        $data = [];
        $staff_id= $request['staff_id'];
        $check = DB::SELECT("Select order_date,order_number,final_total from internal_staffs_credits WHERE internal_staffs_credits.staff_id='".trim($staff_id)."' AND internal_staffs_credits.status ='Credit'");
        if(count($check)>0)
        {
             Foreach($check as $key=>$val){
            $data['total'] = (string) round($val->final_total,$decimal_points);
            $data['order_date'] = $val->order_date;
            $data['order_no'] = $val->order_number;
            $arr[] =$data;
}
            $msg = 'exist';
            return response::json(['msg' => $msg,'data' => $arr]);
        }
        else{
            $msg = 'notexist';
        }
        return response::json(['msg' => $msg]);
    }

    public function staff_credit_pay(Request $request)
    {
        $decimal_points = commonSource::generalsettings();
        $staff_id= $request['staff_id'];
        $from_date= $request['from_date'];
        $to_date= $request['to_date'];
        $check = DB::SELECT("Select date(scp_trns_date) as transaction_date,sum(scp_amount) as transaction_amount,scp_payment_reference_number as payment_referencenumber from internal_staffs_credits_pay  WHERE internal_staffs_credits_pay.scp_staff_id='".trim($staff_id)."' AND  date(scp_trns_date) >= '".$from_date."' and date(scp_trns_date) <= '".$to_date."' group by scp_trns_date");
        if(count($check)>0)
        {
            $msg = 'exist';
            return response::json(['msg' => $msg,'data' => $check]);
        }
        else{
            $msg = 'notexist';
        }
        return response::json(['msg' => $msg]);
    }


    public function credit_pay_post(Request $request)
    {
        $data = [];
        if(isset($request['mobile']))
        {
            if(!validate_mobile($request['mobile']))
            {
                return response::json(['msg' => 'Invalid Mobile Number']);
            }
            else{
                $mobile= $request['mobile'];
            }
        }
        else
        {
            return response::json(['msg' => 'Mobile Number Parameter Missing']);
        }
        if(isset($request['depositor_mobile']))
        {
            if (!validate_mobile($request['depositor_mobile']))
            {
                return response::json(['msg' => 'Invalid Depositor Mobile Number']);
            }
            else{
                $depositor_mobile= $request['depositor_mobile'];
            }
        }
        else{
            $depositor_mobile= null;
        }
        $product_description= $request['product_description'];
        $customer_name= $request['customer_name'];
        $PNBTransactionID =$request['PNBTransactionID'];
        $transactionsource = $request['transactionSource'];
        $amount= isset($request['amount'])?$request['amount']:0;
        if(!is_numeric($request['amount']))
        {
            return response::json(['msg' => 'Amount Not Defined']);
        }
        $paymode=   isset($request['paymode'])?$request['paymode']:NULL;
        if(strtoupper($paymode)!= 'CASH')
        {
            return response::json(['msg' => 'Payment Mode Not Defined']);
        }
        $transfer_date=  isset($request['transfer_date'])?date('Y-m-d H:i:s',strtotime($request['transfer_date'])):null;
       
        //Link exist checking starts
        if(strpos($product_description, 'http') !== false || strpos($product_description, 'https') !== false ||strpos($product_description, 'www.') !== false){
            return response::json(['msg' => 'Product Description Not Defined']);
        }
       
        if(strpos($customer_name, 'http') !== false || strpos($customer_name, 'https') !== false ||strpos($customer_name, 'www.') !== false){
            return response::json(['msg' => 'Customer Name Not Defined']);
        }

        if(strpos($PNBTransactionID, 'http') !== false || strpos($PNBTransactionID, 'https') !== false ||strpos($PNBTransactionID, 'www.') !== false){
            return response::json(['msg' => 'PNBTransactionID Not Defined']);
        }

        if(strpos($transactionsource, 'http') !== false || strpos($transactionsource, 'https') !== false ||strpos($transactionsource, 'www.') !== false){
            return response::json(['msg' => 'Transaction Source Not Defined']);
        }
        //Link exist checking ends

        $check = DB::SELECT("SELECT id from internal_staffs where internal_staffs.mobile ='$mobile'");
        if(count($check)>0)
        {
            $refrencestart = Commonsource::credits_pay_reference();
/*             if(count($lastcredit)<=0)
             {
                 $reference = 'PAY'.$refrencestart;
             }
             else{
                $reference = 'PAY'.(substr($lastcredit[0]->scp_payment_reference_number,3)+1);
             }*/
            $reference =$this->referencekeyfenerate($mobile);
            $exist = DB::SELECT('SELECT scp_payment_reference_number FROM `internal_staffs_credits_pay` where scp_payment_reference_number = "'.$reference.'" order by scp_slno desc');
            if(count($exist)>0)
            {
                $reference =$this->referencekeyfenerate($mobile);
            }
            $staffid = $check[0]->id;
            DB::INSERT("INSERT INTO `internal_staffs_credits_pay`(`scp_staff_id`, `scp_trns_date`, `scp_slno`, `scp_mobile`, `scp_amount`, `scp_product_description`, `scp_depositormobile_no`, `scp_paymode`, `scp_errors`,`scp_transaction_source`,`scp_PNBTransactionID`,`scp_payment_reference_number`)
            VALUES ('" . trim($staffid) . "','".$transfer_date."','0','" .$mobile. "','" .$amount. "','".$product_description."','" .$depositor_mobile. "','" .$paymode. "',NULL,'".$transactionsource."','".$PNBTransactionID."','".$reference."')");
            
			DB::UPDATE("UPDATE internal_staffs_credits SET status = 'Paid' where staff_id ='" . trim($staffid) . "'  and status = 'Credit'");
			$msg = 'success';
            return response::json(['msg' => $msg,'PaymentReferenceNumber'=>(string)$reference,'txnDate'=>$transfer_date,'PNBTransactionID'=>$PNBTransactionID,'transactionAmount' =>$amount]);
        }
        else{
            $msg = 'notexist';
        }
        return response::json(['msg' => $msg]);
    }

    public function referencekeyfenerate($mobile)
    {
        $encr_method = Datasource::encr_method();
        $string = date('Y-m-d H:i:s').''.$mobile;
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $referencekey = openssl_encrypt($string, $encr_method, $key, 0, $iv);
        $referencekey = base64_encode($referencekey);
        $reference = 'PTF'.substr($referencekey,0,30);
        return $reference;
    }

    public function dlv_staff_notification(Request $request)
    {
       $arr = array();
        $data = [];
        $staff_id= $request['staff_id'];
        $detail = DB::SELECT("select id,message,title,DATE_FORMAT(notifications.entry_date, '%Y-%m-%d %h:%i:%s %p') as entry_date from notifications where is_all = 'N' AND groupid = (select g_id from notification_group ng where ng.g_name = 'DELIVERY STAFF') and now()< expiry and  json_contains(user_list,'[\"$staff_id\"]')");
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
        else{
            $msg = 'notexist';
            return response::json(['msg' => $msg]);
        }
    }
	
	public function dlv_app_forceupdate(Request $request)
    {
   
		$setting = GeneralSetting::where('id','1')->select('dlv_app_currnt_version','dlv_app_custom_msg','dlv_force_update')->first();
        if(count($setting)>0)
        {
            if(strtoupper($setting['dlv_force_update']) == 'Y')
            {
                $msg = 'Exist';
                return response::json(['msg' => $msg,'version' => $setting['dlv_app_currnt_version'],'message' => $setting['dlv_app_custom_msg'],'clear_data' => 'N']);
            }
            else if(strtoupper($setting['dlv_force_update']) == 'N')
            {
                $msg = 'no_force_update';
                return response::json(['msg' => $msg,'version' => $setting['dlv_app_currnt_version'],'message' => $setting['dlv_app_custom_msg'],'clear_data' => 'N']);
            }
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }

    public function staff_mobile($id)
    {
        $check = DB::SELECT("SELECT mobile from internal_staffs where internal_staffs.id ='$id'");
        return response::json(['msg' => "success","mobile" =>$check[0]->mobile]);


    }


    public function staff_sal_amt_details(Request $request)
    {
        $arr = array();
        $data = [];
        $staff_id  =   $request['staff_id'];
        $date      =   date('Y-m-d',  strtotime($request['date'])); 
        $mode      =   $request['mode'];
        $detail = DB::SELECT("SELECT is_slno as slno ,is_staff_amount as amount,is_reason as reason FROM internal_staffs_sal_adj WHERE is_staff_id = '$staff_id' AND is_staff_date = '$date' AND is_mode = '$mode'");
        if(count($detail)>0)
        {
           foreach($detail as $key=>$val)
           {
               $data['id'] = (string)$val->slno;
               $data['amount'] = $val->amount;
               $data['reason'] = $val->reason;
               $arr[]=$data;
           }
           $msg = 'success';
           return response::json(['msg' => $msg,'data'=>$arr]);
       }
        else{
            $msg = 'notexist';
            return response::json(['msg' => $msg]);
        }
    }

    public function staff_login_credentials(Request $request)//API to lists the staff name and number if code matches
    {
         $username = $request['username'];
         $code = $request['code'];
         $ftoken = $request['ftoken'];
         $id = substr($code, 0, 2);
         $authcode = substr($code, 2, 6);
         $encr_method = Datasource::encr_method();
         $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
         $key1 = hash('sha256', $rowkey[0]->explore);
         $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
         $key = hash('sha256', $key1);
         $iv = substr(hash('sha256', $iv1), 0, 16);
         $password = openssl_encrypt($authcode, $encr_method, $key, 0, $iv);
         $password = base64_encode($password);
         $array = array();
         $list = DB::SELECT("SELECT id,first_name,mobile FROM `internal_staffs` WHERE RIGHT(`id`,2) =$id AND  authcode='".$password."' and mobile = trim(".$username.")");
         if(count($list)>0)
         {
            $fullid=$list[0]->id;
            $name=strtoupper($list[0]->first_name);
            $mobile=$list[0]->mobile;
            $array['id'] = $fullid;
            $array['name'] = $name;
            $array['mobile'] = $mobile;
            $msg = 'Exist';
            $area_list = DB::SELECT("SELECT a.id as area_id,a.name as area_name  FROM internal_staffs_area s left join city a on a.id = s.area_id WHERE s.staff_id = ".$fullid."");
        
           // return "UPDATE internal_staffs SET ftoken='".trim($ftoken)."' WHERE RIGHT(`id`,2) =$id AND  authcode='".$password."'" ;
            DB::UPDATE("UPDATE internal_staffs SET ftoken='".trim($ftoken)."' WHERE id=".$fullid." and mobile = trim(".$username.")");
            return response::json(['msg' => $msg,'staff' => $array, 'area'=> $area_list]);
         }
         else
         {
             $msg = 'Invalid Credentials';
             return response::json(['msg' => $msg]);
         }

    }




    public function order_status_change(Request $request)
    {
        $arr = array();
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $time = strtoupper($date->format('h:i a'));
        $decimal_point = Commonsource::generalsettings();
        $order_number= $request['order_number'];
        $status= strtoupper($request['status']);
        $staff_id= $request['staff_id'];
        $dlv_cust_rating= $request['dlv_cust_rating'];
        $dlv_cust_remarks= $request['dlv_cust_remarks'];
        $database = $this->firebase_db;

		$orderstatus_current = DB::SELECT("SELECT current_status,delivery_assigned_to,rest_details->>'$.name' as restname,ROUND(final_total,$decimal_point) as omfinal_total from `order_master` WHERE order_number =$order_number");
        
        if(count($orderstatus_current)>0)
		{
            $currentstatus = $orderstatus_current[0]->current_status;
            $assignedstaff = $orderstatus_current[0]->delivery_assigned_to;
            $restname = $orderstatus_current[0]->restname;

        }
        if(trim($assignedstaff) != trim($staff_id))
        {
            $msg = 'Staff has been Changed !! Please refersh your the order list.';
            return response::json(['msg' => $msg]);
            
        }
        if($currentstatus == 'C')
        {

            $statustoupdate = 'OP';
            $title =  'Order Picked';
            $message = "Your Order,Order Number - $order_number  has been collected from $restname and is on the way to your location.";
        }
        else if($currentstatus == 'OP')
        {
            if($dlv_cust_rating<1)
            {
                $msg = 'Successful';
                return response::json(['msg' => $msg]);
            }
            
            $statustoupdate = 'D';
            $title = 'Order Delivered';
            $message = "Your Order,Order Number - $order_number from $restname has been successfully delivered by our  staff. Request to please rate us for bettering our services.";
        }
        else
        {
            $msg = 'Successful';
            return response::json(['msg' => $msg]);
        }
        DB::SELECT("UPDATE order_master SET current_status = '$statustoupdate',readytopick= 'Y',status_details=JSON_INSERT(status_details,'$.$statustoupdate','$time') WHERE order_number='$order_number'");
        
        $is_exist= DB::SELECT("SELECT ftoken,om.customer_id,name FROM order_master om join customer_list cm on om.customer_id = cm.id  join ftoken_master fm on fm.customer_id = cm.id WHERE order_number =$order_number");
        if(count($is_exist)>0)
        {
            foreach($is_exist as $item)
            {

                    $arr['to'] = $item->ftoken;
                    $arr['title'] = $title;
                    $arr['message'] = $message;
                    $arr['image'] = 'null';
                    $arr['action'] = 'orderhistory';
                    $arr['action_destination'] = 'null';
                    $arr['app_type'] = 'customerapp';
                    $result = Commonsource::notification($arr);
            }
        }
        if($statustoupdate == 'D')
        {
            $staff_current_order_count = DB::SELECT("SELECT count(order_number) as current_count FROM order_master WHERE delivery_assigned_to = '$staff_id' AND current_status in ('C','OP')");
            $order_val = $staff_current_order_count[0]->current_count;
            
            DB::SELECT("UPDATE order_master SET dlv_review_details= JSON_OBJECT('dlv_cust_rating','$dlv_cust_rating','dlv_cust_remarks','$dlv_cust_remarks') WHERE order_number='$order_number'");
            DB::SELECT("UPDATE  internal_staffs_credits SET status = 'Credit',final_total = " . $orderstatus_current[0]->omfinal_total . "   where order_number = '$order_number'");
            DB::SELECT("UPDATE internal_staffs SET current_confirmed_count = $order_val, auto_assign_next_order_2 = NULL ,auto_assign_after = DATE_ADD(NOW(), INTERVAL 20 SECOND) where id = $staff_id");
            DB::SELECT("DELETE FROM internal_staffs_current_orders WHERE isco_staff_id = $staff_id and isco_order_no = $order_number");
        //decrease current order count of the delivery staff 
        //$count_orderno = $database->getReference('location')->getChild($staff_id)->getChild("current_order")->getChild("order_count")->getValue();          
        //$count_orderno_ct['order_count']= $count_orderno - 1;
         //$database->getReference('location')->getChild($staff_id)->getChild("current_order")->update($count_orderno_ct);          
        
           // remove th specified order number from this delivery staff    
        //$database->getReference('location')->getChild($staff_id)->getChild("orders")->getChild($order_number)->remove();         
        
        
        }

        $msg = 'Successful';
        return response::json(['msg' => $msg]);
    }


    public function staff_forgot_credentials(Request $request)//API to lists the staff name and number if code matches
    {
        $username = $request['username'];
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $array = array();
        $list = DB::SELECT("SELECT  first_name as fname,RIGHT(`id`,2) as id ,authcode FROM `internal_staffs` WHERE mobile = trim(".$username.")");
        if(count($list)>0)
        {
            $id=$list[0]->id;
            $password = openssl_decrypt(base64_decode($list[0]->authcode), $encr_method, $key, 0, $iv);
            $fname=$list[0]->fname;
            $sendpassword = $id.$password;

            $message = "Hi $fname, As requested please note your Username is $username and Password is $sendpassword .For any concerns, conatct our Delivery Helpline no: 9567999142.";
            $sendmsg = urlencode($message);
            $smsurl = Datasource::smsurl($username,$sendmsg);
            $data = file_get_contents($smsurl); 

            $msg = 'Password Sent To Your Registered Mobile';
            return response::json(['msg' => $msg]);
        }
        else
        {
            $msg = 'Invalid Mobile';
            return response::json(['msg' => $msg]);
        }

    }


    public function deliverystaff_attendance(Request $request, $staff_id)
    {
        $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d H:i:s');
        $today = $date->format('Y-m-d');
        $time = $date->format('H:i:s');
        $date1 = $date->format('Y-m-d');
        $details = DB::SELECT('select id FROM internal_staffs WHERE id="'.$staff_id.'" and active ="Y"');
        if(count($details)>0)
        {
            $detail = DB::SELECT('select staff_id,entry_date,in_time,out_time FROM delivery_staff_attendence WHERE out_time IS NULL and staff_id="'.$staff_id.'"');  
            if(count($detail)>0)
            {
                $checkstatus = DB::SELECT('select order_number FROM order_master WHERE delivery_assigned_to = "'.$staff_id.'" and current_status != "D" and current_status !="T" and current_status !="CA"');
                if(count($checkstatus)>0)
                {
                    $msg = 'Order Pending To Be Delivered.';
                    $current_status = 'Started';
                }
                else
                {
                   DB::SELECT("UPDATE delivery_staff_attendence SET out_time = '$datetime' WHERE staff_id='$staff_id' and out_time IS NULL ");
                   DB::SELECT("UPDATE internal_staffs SET current_confirmed_count= 0, dlv_area = NULL, auto_assign_next_order_2 = NULL where id = $staff_id");

                   $attendance = StaffAttendance::where('staff_id', $staff_id)->where('checkout_time', Null)->orderBy('id', 'desc')->first();
                   $attendance->checkout_time = $datetime;

                   if($request->post('by_admin') == 1 && $attendance && Auth::user()) {
                        $attendance->checkout_by = 0;
                        $attendance->done_by_userid = Auth::user()->id;
                   } elseif($attendance) {
                        $attendance->checkout_by = 1;
                   }

                   $attendance->save();
        
                   $current_status = 'Closed';
                   $msg = 'Successful';
                }
                return response::json(['msg' => $msg,'current_status' => $current_status]);
            }
            else
            {
                DB::INSERT("INSERT INTO `delivery_staff_attendence`(`staff_id`, `entry_date`, `slno`, `in_time`) VALUES ('" . trim($staff_id) . "','".$date1."','0','" .$datetime. "')");

                $todays_attendance_count = StaffAttendance::where('staff_id', $staff_id)
                                                            ->where('created_at', 'like', "$today%")->count();

                StaffAttendance::create([
                    'staff_id' => $staff_id,
                    'checkin_serial' => ++$todays_attendance_count,
                    'checkin_time' => $time,
                ]);

                DB::SELECT("UPDATE internal_staffs SET auto_assign_after = current_time() where id = $staff_id");
              $msg = 'Successful';
              $current_status = 'Started';
              return response::json(['msg' => $msg,'current_status' => $current_status]);
               
            }
        }
        else
        {
            $msg = 'No Match Found';
            $current_status = 'Closed';
            return response::json(['msg' => $msg,'current_status' => $current_status]);
        }        
    }

}


