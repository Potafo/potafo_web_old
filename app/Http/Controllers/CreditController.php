<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Helpers\Datasource;
use Excel;
use Session;
class CreditController extends Controller
{

    public function view_credits(Request $request)
    {
        $staffid = Session::get('staffid');
        if(!$staffid){return redirect('');}
        $filterarr = array();
        $itemsarr = array();$i=0;
        $autharr = array();
        return view('staff.manage_credits',compact('rows','filterarr','itemsarr','autharr'));
    }
public function excel_download_credit(Request $request) {
         $search = '';
        
        $flt_status = $request['flt_status_xl'];
        $flt_name = $request['flt_name_xl'];
        $flt_from = $request['flt_from_xl'];
        //$flt_to   = $request['flt_to_xl'];
       if($flt_status!=''){
          $search.=" AND s.status = '".$flt_status."'";
       }
       if($flt_name!=''){
           $search .= " AND LOWER(i.id) LIKE '%" . strtolower($flt_name) . "%'";
       }
       if($flt_from!='' ){
           $search.=" AND DATE(order_date) = '".date('Y-m-d', strtotime(($flt_from)))."'";
       }
//       if($flt_from!=''  && $flt_phone=='' && $flt_name==''){
//           $search.=" and  DATE(order_date) = '".date('Y-m-d', strtotime(($flt_from)))."' ";
//       }
//       if($flt_phone =='' && $flt_name=='' && $flt_from=='' ){
//           $search.=" AND DATE(order_date) = '".date('Y-m-d', strtotime(($flt_from)))."' ";
//       }
        $details = array();
        $j=0;$m=0;
        $customer_details = DB::SELECT("SELECT first_name,i.id,last_name,mobile,sum(final_total) as final_total FROM `internal_staffs_credits` as s left join `internal_staffs` as i on s.staff_id = i.id WHERE s.staff_id != '' $search group by s.staff_id ORDER BY s.order_date DESC ");
       
        foreach($customer_details as $i)
        {
            $id = $i->id;
            $fname = $i->first_name;
            $lname = $i->last_name;
            $mobile = (int)$i->mobile;
            $final_total = (float)$i->final_total;
            $order_date = $flt_from;	
                        

            $name = $fname." ".$lname;
            $data[$m] = ['ID'  => $id,
                        'Date'  => $order_date,
                         'Name'   => $name,
                          'Mobile'    => $mobile,                         
                          'Total'  => $final_total,
                          
                     ];
               
                $m++;
               
        }
       $dateadd=date('dmy', strtotime(($flt_from)));
        //return $data;
          Excel::create('Credits_'.$dateadd, function($excel) use ($data) {
           
            $excel->sheet('Credits', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
                $sheet->getStyle('A1:AN1')->getFont()->setBold(true);
              
            });
            
        })->download('xlsx');
       
     }
    public function filter_credits_list(Request $request)
    {
        $search = '';
        $itemsarr = array();
        $i=0;
        $staffid = $request['staff_id'];
        $flt_status = $request['flt_status'];
        $flt_name = $request['flt_name'];
        $flt_from = $request['flt_from'];

        if($flt_status!='')
        {
            $search.=" AND s.status = '".$flt_status."'";
        }
        if($flt_name!='') {
            $search .= " AND LOWER(i.id) LIKE '%" . strtolower($flt_name) . "%'";
        }
        if($flt_from!='')
        {
            $search.=" and  DATE(order_date) = '".date('Y-m-d', strtotime(($flt_from)))."' ";
        }
        $pointing = $request['current_count'];
        if($pointing=='')
        {
            $pointing=1;
        }
        $startlimit = ($pointing-1)*20;
        $endlimit = ($pointing)*20;
        $totaldetails = DB::SELECT("select count(*)  as totalstaff from internal_staffs_credits s WHERE s.`staff_id` != ''");
        $details = array();
        $areaarr = array();
        $append='';
        $customer_totaldetails = DB::SELECT("select count(*)  as totalstaff from internal_staffs_credits s left join `internal_staffs` as i on s.staff_id = i.id WHERE  s.`staff_id` != '' $search");
        $count = $customer_totaldetails[0]->totalstaff;
        $customer_res = round($customer_totaldetails[0]->totalstaff/20,0);
        $customer_mode = ($customer_totaldetails[0]->totalstaff)%(20);
//        if($customer_mode!=0){$customer_res = $customer_res+1;}
        $total_cutomers=$customer_res;
        $rows = DB::SELECT("SELECT first_name,i.id,last_name,mobile,sum(final_total) as final_total FROM `internal_staffs_credits` as s left join `internal_staffs` as i on s.staff_id = i.id WHERE s.staff_id != '' $search group by s.staff_id ORDER BY s.order_date DESC   LIMIT $startlimit,20");
        $appends = '';
        foreach($rows as $data)
        {
            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $id = $data->id;
            $first_name = $data->first_name;
            $last_name = $data->last_name;
            $mobile = $data->mobile;
            $order_date = $flt_from;
            $final_total =  $data->final_total;
            $itemsarr[$i] = ['id'=>$id,'first_name'=>$first_name,'last_name'=>$last_name,'final_total'=>$final_total,'order_date'=>$order_date,'mobile'=>$mobile];
            $i++;
        }
        $m=$startlimit;
        $append .= '<table id="example1"  class="table table-striped table-bordered">';
        $append .='<thead>';
        $append .='<tr>';
        $append .='<th style="min-width:30px">ID</th>';
        $append .='<th style="min-width:80px"> Date</th>';
        $append .='<th style="min-width:100px">Name</th>';
        $append .='<th style="min-width:80px">Mobile </th>';
        $append .='<th style="min-width:15px">Final Total</th>';
        $append .='</tr>';
        $append .='</thead>';
        $append .='<tbody >';
        foreach($itemsarr as $i)
        {
            $id = $i['id'];
            $name = $i['first_name'];
            $lname = $i['last_name'];
            $mobile = $i['mobile'];
            $order_date =$flt_from;
            $final_total = $i['final_total'];
            $m++;
            $append .= '<tr><td style="min-width:30px;">'.$id.'</td>';
            $append .= '<td style="min-width:10px;">'.date('Y-m-d H:i:s',strtotime($order_date)).'</td>';
            $append .= '<td style="min-width:100px;">'.$name.' '.$lname.'</td>';
            $append .= '<td style="min-width:100px;">'.$mobile.'</td>';
            $append .= '<td style="min-width:10px;">'.$final_total.'</td>';
            $append .= '</tr>';
        }
        $append .='</tbody>';
        $append .='</table>';
        return response::json(['filter_data'=>$append,'data_count'=>$total_cutomers,'count' =>$totaldetails[0]->totalstaff,'searchcount' =>$customer_totaldetails[0]->totalstaff]);

    }
}
