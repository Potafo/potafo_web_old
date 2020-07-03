<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use App\User;
use App\UserMaster;
use Response;
use Illuminate\Support\Facades\Input;
use ConsoleTVs\Charts\Facades\Charts;
use Helpers\Commonsource;
use Helpers\Datasource;
use App\GeneralSetting;
use App\Restaurant_Master;

class usercontroller extends Controller
{
    public function manage_user_master(Request $request){
        $csrf_token = $request['token'];
        $users = DB::SELECT(" SELECT a.*,b.group_name FROM `tbl_user_master` a LEFT join tbl_group_master b on b.id=a.group_id ");
        return view('user.manage_user', compact('users','csrf_token','pass'));
    }
    public function manage_staff(Request $request){
        $csrf_token = $request['token'];
        $user_id = session('userid');
        $group   = session('groupid');
        $users = DB::SELECT(" SELECT a.*,b.group_name FROM `tbl_user_master` a LEFT join tbl_group_master b on b.id=a.group_id WHERE designation!='SuperAdmin' AND group_id='".$group."' ");
        return view('userpages.manage_staff', compact('users','csrf_token','pass','group'));
    }
    public function manage_report_user(Request $request) {
        
        $csrf_token = $request['token'];
        $userid = $request['user'];
        $user_details = DB::SELECT("SELECT username, group_id FROM tbl_user_master WHERE id= '".$userid."' ");
        $username  = $user_details[0]->username;
        $get_cate_report = DB::SELECT("SELECT DISTINCT a.rc_id, a.rc_category_name FROM tbl_report_category a LEFT JOIN tbl_report_master b ON b.rm_main_cat = a.rc_id LEFT JOIN tbl_report_users c ON c.ru_id = b.rm_id WHERE b.rm_active='Y' AND c.ru_userid= '".$userid."' ");
        $get_report = DB::SELECT("SELECT c.rm_name,  b.ru_access,b.ru_id FROM tbl_report_users b LEFT JOIN tbl_report_master c on c.rm_id = b.ru_id WHERE c.rm_active='Y' AND b.ru_userid='".$userid."' ");
        return view('user.manage_report_status', compact('username','userid','csrf_token','get_report','get_cate_report'));
    }
    public function add_user(Request $request){
        
      $userid     = $request['userid'];
      $token      = $request['token'];
      $group_id   = $request['group_id'];
      $user_name  = $request['user_name'];
      $reg_email  = $request['reg_email'];
      $status     = $request['status'];
      $cs_status  = $request['cs_status'];
      $onl_status = $request['onl_status'];
      $password     = $request['password'];
      $phone_number     = $request['phone_number'];
      $designation     = $request['designation'];
      $password_encryp = hash("SHA512", $password, false);
     
      $check_usr_count = DB::SELECT("SELECT `max_user_count`,curent_usr_count FROM `tbl_group_master` WHERE `id`='".$group_id."' ");
      
      $max_count = $check_usr_count[0]->max_user_count;
      $current_count = $check_usr_count[0]->curent_usr_count;
      if($userid==''){
           if($current_count==$max_count){
             return "usercountExceed" ; 
           }  
           else{
                $new_user = DB::INSERT("INSERT INTO `tbl_user_master` (`id`, `group_id`, `username`, `password`, `status`, `registered_email`,phone_number,designation,cs_active,online_active) VALUES ('0', '".$group_id."', '".$user_name."', '".$password_encryp."', '".$status."', '".$reg_email."','".$phone_number."','".$designation."','".$cs_status."','".$onl_status."')");
                $update_count = DB::UPDATE("UPDATE `tbl_group_master` SET `curent_usr_count`= curent_usr_count+1 WHERE `id`='".$group_id."' ");
                return "inserted";
           }
      }
      else{
             $update_user = DB::UPDATE("UPDATE `tbl_user_master` SET `group_id`='".$group_id."',`username`='".$user_name."',`status`='".$status."',cs_active='".$cs_status."',online_active='".$onl_status."',`registered_email`= '".$reg_email."',phone_number='".$phone_number."',designation='".$designation."' WHERE `id`='".$userid."' ");  
                return "updated";
      }
    }
    public function reset_password(Request $request){
          $token     = $request['token'];
        $userid = $request['userid_reset'];
        $username = $request['username_reset'];
        $password = $request['new_password'];
        $encrypt_password = hash("SHA512",$password,false);
             $update_user = DB::UPDATE("UPDATE `tbl_user_master` SET `username`='".$username."',password='".$encrypt_password."' WHERE `id`='".$userid."' ");  
               return redirect('manage_user_master?token='.$token.'&pass=2');

        
    }
    public function get_branch_by_user($id){
        $branch = DB::SELECT("SELECT a.`branchid`,b.branch_name,a.active FROM `tbl_user_master_branch` a LEFT join tbl_branch b on a.`branchid`=b.branch_id WHERE a.`id_usermaster`='".$id."' ");
        return $branch;
    }
    public function update_branch($id,$branchid,$status){
       
       if($status=='Y'){
           $status_new = 'N';
       }
       else{
          $status_new = 'Y'; 
       }
        $branch = DB::SELECT("UPDATE `tbl_user_master_branch` SET `active`='".$status_new."' WHERE `id_usermaster`='".$id."' and `branchid`='".$branchid."' ");
        return 'updated';
    }
    public function change_report_status(Request $request){
        $userid     = $request['userid'];
        $status     = $request['status'];
        $report_id  = $request['report_id'];
        $reset_status = DB::UPDATE("UPDATE tbl_report_users SET ru_access='".$status."' WHERE ru_id='".$report_id."' and ru_userid='".$userid."' ");
        return "updated";
    }
    public function filter_report_catrgory(Request $request){

        $userid     = $request['userid'];
        $report_id  = $request['report_id'];
        if($report_id == 'allcat'){
            $condition ='';
        }
        else{
            $condition =" and b.rm_main_cat='".$report_id."' ";
        }
$get_report = DB::SELECT("SELECT DISTINCT b.rm_name, c.ru_access, c.ru_id FROM tbl_report_category a LEFT JOIN tbl_report_master b ON b.rm_main_cat = a.rc_id LEFT JOIN tbl_report_users c ON c.ru_id = b.rm_id WHERE b.rm_active='Y' AND c.ru_userid='".$userid."' $condition");
              $append =  '';
                        if(count($get_report)!=0){
                             $i=0;
                        foreach($get_report as $report){
                             $i++;
                            

                            $append .=  '<tr>';
                                        $append .= '<td>'.$i.'</td>';
                                         $append .= '<td >';
                                             $append .=  '<a class="btn" id="edit_icon'.$report->ru_id.'" onclick="change_status('.$report->ru_id.' )"><i class="fa fa-edit"></i></a>';
                                             $append .=  '<a class="btn" style="display: none" id="save_icon'.$report->ru_id.'" onclick="save_status('.$report->ru_id.' )"><i class="fa fa-save"></i></a>';

                                         $append .=  '</td>';

                                         $append .=  '<td>'.$report->rm_name.'</td>';
                                        $append .=  ' <td>';
                                        $append .=  '<span id="show_status'.$report->ru_id.'" class=" staff-master-select form-control">';
                                                                                 if($report->ru_access=='Y')
                                                                                 {
                                                                                  $append .= 'Active';
                                                                                 }
                                                                               if($report->ru_access=='N') {
                                                                                  $append .= 'InActive';

                                                                                   }
                                                                                   $append .= ' </span>';

                                            $append .=  ' <select id="report_status'.$report->ru_id.'" style="background-color:transparent;height: 39px;display: none;" class=" staff-master-select form-control">';
                                                $append .=  '<option value="Y">Active</option>';
                                               $append .=  '  <option value="N">InActive</option>';
                                           $append .=  '  </select>';

                                        $append .=  ' </td>';
                                   $append .=  '</tr>';

                      }
                        }
                        return $append;
    }
    public function reset_all_report(Request $request) {
        
        $status = $request['status'];
        $userid = $request['userid'];
        if($status=='true'){
            $reset_active = DB::UPDATE("UPDATE tbl_report_users SET ru_access='Y' WHERE ru_userid='".$userid."'");
            return "active";
        }
        if($status=='false'){
            $reset_inactive = DB::UPDATE("UPDATE tbl_report_users SET ru_access='N' WHERE ru_userid='".$userid."'");
            return "inactive";
        }
        
    }

    public function generalsettings(Request $request)
    {
          $restaurantid=$request['restaurantid'];
          $key = $request['key'];
          $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
         if($details == 'Exist') {
              $restaurantsettings = DB::SELECT('select force_close,busy	from `restaurant_master` where id = "'.$restaurantid.'"');
              $general= GeneralSetting::where('id','1')->select('restaurant_time_count')->first();
              $day = DB::SELECT('SELECT rt_day,rt_from_time,rt_to_time,rt_slno FROM `restaurant_timings` where rt_rest_id = "'.$restaurantid.'" and rt_day = "MONDAY"');
              $general = GeneralSetting::where('id','!=','')->select('restaurant_from_time','restaurant_to_time')->first();
              $msg = 'Exist';
              return response::json(['msg'=>$msg,
                      'force_close'=>$restaurantsettings[0]->force_close,
                      'busy'=>$restaurantsettings[0]->busy,
                      'restaurant_time_count'=> $general['restaurant_time_count'],'timings'=>$day]);
          }
          else
          {
              $msg =$details;
              return response::json(compact('msg'));
          }
    }

    public function restauranttimebyday(Request $request)
    {
        $restaurantid=$request['restaurantid'];
        $key = $request['key'];
        $day =strtoupper($request['day']);
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
        if($details == 'Exist')
        {
            $days = DB::SELECT('SELECT rt_day,rt_from_time,rt_to_time,rt_slno FROM `restaurant_timings` where rt_rest_id = "'.$restaurantid.'" and rt_day = "'.$day.'"');
            if(count($days) > 0)
            {
                $msg ='Exist';
                return response::json(compact('msg','days'));

            }
            else
            {
                $msg = 'Not Exist';
                return response::json(compact('msg'));
            }
        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
        }

    public function busyupdate(Request $request)
    {
        $restaurantid=$request['restaurantid'];
        $key = $request['key'];
        $userid =$request['userid'];
        $status = $request['status'];
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
        if($details == 'Exist') {
            $restaurant = Restaurant_Master::where('id','=',$restaurantid)
                          ->get();
            if(count($restaurant)>0)
            {
                $msg = 'Success';
                Restaurant_Master::where('id', $restaurantid)->update(
                 [
                            'busy' =>$status,'busy_by'=>$userid,
                    ]);
            }
            else{
                $msg ='Not Exist';
            }
            return response::json(compact('msg'));
        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }

    public function forcecloseupdate(Request $request)
    {
        $restaurantid=$request['restaurantid'];
        $key = $request['key'];
        $userid =$request['userid'];
        $status = $request['status'];
        $details = Commonsource::checkrestaurantvalidity($restaurantid,$key);
        if($details == 'Exist') {
            $restaurant = Restaurant_Master::where('id','=',$restaurantid)
                ->get();
            if(count($restaurant)>0)
            {
                $msg = 'Success';
                Restaurant_Master::where('id', $restaurantid)->update(
                    [
                        'force_close' =>$status,'forceclose_by'=>$userid,
                    ]);
            }
            else
            {
                $msg ='Not Exist';
            }
            return response::json(compact('msg'));
        }
        else
        {
            $msg =$details;
            return response::json(compact('msg'));
        }
    }
}

