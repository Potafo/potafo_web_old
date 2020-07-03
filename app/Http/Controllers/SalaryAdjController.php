<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Helpers\Datasource;
use Session;
class SalaryAdjController extends Controller
{
    //INSERT INTO `module_master` (`m_id`, `module_name`, `sub_module`, `page_link`, `module_for`, `display_order`) VALUES (NULL, 'staff', 'Salary Adj.', 'manage_salaryadj', 'C', '4');
    //UPDATE `users_modules` SET `active` = 'Y' WHERE `users_modules`.`user_id` = 1 AND `users_modules`.`module_id` = 23;
    public function view_salaryadj(Request $request)
    {
        return view('staff.manage_salary_adj');
    }
    public function salary_adj_submit(Request $request)
    {
             
        $sal_date=date("Y-m-d", strtotime($request['sal_date']));
       // date('Y-m-d',strtotime($request['sal_date']));
        $sal_staff= $request['sal_staff'];
        $sal_amount =isset($request['sal_amount'])?$request['sal_amount']:0;
        $sal_mode = $request['sal_mode'];
        $sal_reason= $request['sal_reason'];
        $type=$request['type'];
        if(!is_numeric($request['sal_amount']))
        {
            return response::json(['msg' => 'Amount Not Defined']);
        }
      if($type=="insert")
        {
            DB::INSERT("INSERT INTO `internal_staffs_sal_adj`(`is_staff_id`, `is_staff_date`, `is_staff_amount`, `is_mode`, `is_reason`) VALUES "
                    . "('".$sal_staff."','".$sal_date."','".$sal_amount."','".$sal_mode."','".$sal_reason."')");
		
	$msg = 'success';
           
        }
        else if($type=="update"){
            
            $sal_slno= $request['sal_slno'];
            $msg = 'update';//
            DB::UPDATE("UPDATE `internal_staffs_sal_adj` SET `is_staff_amount`='".$sal_amount."',`is_mode`='".$sal_mode."',`is_reason`='".$sal_reason."'  WHERE `is_slno`='".$sal_slno."' and `is_staff_id`='".$sal_staff."'");
           // DB::UPDATE("UPDATE internal_staffs_credits SET status = 'Paid' where staff_id ='" . trim($staffid) . "'  and status = 'Credit'");
        }
        return response::json(['msg' => $msg]);
    }
    public function filter_salaryadj_list(Request $request)
    {
        $search = '';
        $itemsarr = array();
        $i=0;
        $staffid = $request['flt_staff'];
        //$flt_name = $request['flt_name'];
        $flt_from = $request['flt_from'];
        $flt_to = $request['flt_to'];
        $flt_mode = $request['flt_mode'];

        if($staffid!='')
        {
            $search.=" AND is_staff_id = '".($staffid)."'";
        }
        if($flt_mode!='')
        {
            $search.=" AND is_mode LIKE '".($flt_mode)."%' ";
        }
        if($flt_from!='' && $flt_to !=''){
            $search.=" and  DATE(is_staff_date)  BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
        }
        else if($flt_from!='' && $flt_to =='')
        {
            $search.=" and  DATE(is_staff_date) = '".date('Y-m-d', strtotime(($flt_from)))."' ";
        }
        else if($flt_from=='' && $flt_to !='')
        {
            $search.=" and  DATE(is_staff_date)='".date('Y-m-d', strtotime(($flt_to)))."'";
        }
        else
        {
            $search.=" and  DATE(is_staff_date)=DATE(now()) ";
        }
        $pointing = $request['current_count'];
        if($pointing=='')
        {
            $pointing=1;
        }
        $startlimit = ($pointing-1)*20;
        $endlimit = ($pointing)*20;
        $totaldetails = DB::SELECT("select count(*)  as totalstaff from  internal_staffs_sal_adj s WHERE s.`is_staff_id` != ''");
        $details = array();
        $areaarr = array();
        $append='';
        $customer_totaldetails = DB::SELECT("select count(*)  as totalstaff from  internal_staffs_sal_adj s left join `internal_staffs` as i on s.is_staff_id	 = i.id WHERE  s.`is_staff_id` != '' $search");
        $count = $customer_totaldetails[0]->totalstaff;
        $customer_res = round($customer_totaldetails[0]->totalstaff/20,0);
        $customer_mode = ($customer_totaldetails[0]->totalstaff)%(20);
//        if($customer_mode!=0){$customer_res = $customer_res+1;}
        $total_cutomers=$customer_res;
        $rows = DB::SELECT("SELECT * FROM `internal_staffs_sal_adj` as s left join `internal_staffs` as i on s.is_staff_id= i.id WHERE s.is_staff_id	 != '' $search ORDER BY s.is_staff_date	 DESC LIMIT $startlimit,20");
        $appends = '';
        foreach($rows as $data)
        {////`is_staff_id`, `is_staff_date`, `is_slno`, `is_staff_amount`, `is_mode`, `is_reason` FROM `internal_staffs_sal_adj
            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $id = $data->is_staff_id;
            $first_name = $data->first_name;
            $last_name = $data->last_name;
            $sal_date = $data->is_staff_date;
            $sal_slno = $data->is_slno;
            $sal_amount = $data->is_staff_amount;
            $sal_mode =  $data->is_mode;
            $sal_reason =  $data->is_reason;
                       
            $itemsarr[$i] = ['id'=>$id,'first_name'=>$first_name,'last_name'=>$last_name,'sal_date'=>$sal_date,'sal_slno'=>$sal_slno,'sal_amount'=>$sal_amount,'sal_mode'=>$sal_mode,'sal_reason'=>$sal_reason];
            $i++;
        }
        $m=$startlimit;
        $append .= '<table id="example1"  class="table table-striped table-bordered">';
        $append .='<thead>';
        $append .='<tr>';
        $append .='<th style="min-width:80px">Action</th>';
        $append .='<th style="min-width:80px">Date</th>';
        $append .='<th style="min-width:30px">Staff</th>';
       // $append .='<th style="min-width:100px">Slno</th>';
        $append .='<th style="min-width:80px">Mode </th>';
        $append .='<th style="min-width:15px">Amount</th>';
        $append .='<th style="min-width:15px">Reason</th>';
                
        $append .='</tr>';
        $append .='</thead>';
        $append .='<tbody >';
        foreach($itemsarr as $i)
        {
            $id = $i['id'];
            
            $first_name = $i['first_name'];
            $last_name = $i['last_name'];
            $sal_date =date('d-m-Y', strtotime(($i['sal_date']))) ;
            $sal_slno = $i['sal_slno'];
            $sal_amount = $i['sal_amount'];
            $sal_mode =  $i['sal_mode'];
            $sal_reason =  $i['sal_reason'];
            $m++;
            
            $append .= '<tr><td style="min-width:10px;"><a href="#" onclick="return salaryadj_edit('.$id.','.$sal_slno.',\''.$sal_date.'\',\''.$sal_mode.'\',\''.$sal_amount.'\',\''.$sal_reason.'\')" class="table-action-btn button_table table_edit"><i class="md md-edit"></i></a>'.
                         '<a href="#" onclick="return del_saladj('.$id.','.$sal_slno.',\''.$sal_date.'\')" class="table-action-btn button_table"><i class="fa fa-trash"></i></a></td>';
            $append .= '<td style="min-width:10px;">'.date('d-m-Y',strtotime($sal_date)).'</td>';            
            $append .= '<td style="min-width:100px;">'.$first_name.' '.$last_name.'</td>';
           // $append .= '<td style="min-width:30px;">'.$sal_slno.'</td>';
            $append .= '<td style="min-width:100px;">'.$sal_mode.'</td>';
            $append .= '<td style="min-width:10px;">'.$sal_amount.'</td>';
            $append .= '<td style="min-width:80px;">'.$sal_reason.'</td>';
            
            
            
            
            

            $append .= '</tr>';
        }
        $append .='</tbody>';
        $append .='</table>';
        return response::json(['filter_data'=>$append,'data_count'=>$total_cutomers,'count' =>$totaldetails[0]->totalstaff,'searchcount' =>$customer_totaldetails[0]->totalstaff]);

    }
    function remove_salary_adj(Request $request)
    {//`internal_staffs_sal_adj`(`is_staff_id`, `is_staff_date`, `is_slno`, `is_staff_amount`, `is_mode`, `is_reason`)
        $staff = $request['staff'];
        $slno = $request['slno'];
        $date_sal = $request['date_sal'];
        DB::SELECT("DELETE FROM `internal_staffs_sal_adj`  WHERE is_staff_id= '".$staff."' and is_staff_date='".date('Y-m-d', strtotime(($date_sal)))."' and is_slno='".$slno."'");
        $msg="deleted";
        return response::json(['msg' => $msg]);
    }
     public function sal_autocomplete_reason(Request $request) {
        $append ='';$i=1;
        $reasonlist = DB::SELECT("SELECT DISTINCT(`is_reason` ) as reason  FROM `internal_staffs_sal_adj` WHERE is_reason LIKE '%".$request['reason']."%' ");
        foreach($reasonlist as $list){
            $append .= '<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="searchreason_'.$i.'" onclick=\'selectreasonexist("'.$list->reason.'")\'><a href="javascript:vojavasid(0);"><span>'.$list->reason.'</span></a></div></br>';
$i++;
        }
        return $append;
    }
}
