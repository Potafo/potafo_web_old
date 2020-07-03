<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Helpers\Datasource;
use Session;
use Excel;
class CreditPaysController extends Controller
{

    public function view_creditpays(Request $request)
    {
        return view('staff.manage_credit_pays');
    }
public function excel_download_creditpay(Request $request) {
         $search = '';
        
       // $flt_status = $request['flt_status_xl'];
        $flt_name = $request['flt_name_xl'];
        $flt_from = $request['flt_from_xl'];
        $flt_to   = $request['flt_to_xl'];
        if($flt_name!='')
        {
            $search.=" AND LOWER(i.id) LIKE '%".strtolower($flt_name)."%'";
        }
        if($flt_from!='' && $flt_to !=''){
            $search.=" and  DATE(scp_trns_date)  BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
        }
        else if($flt_from!='' && $flt_to =='')
        {
            $search.=" and  DATE(scp_trns_date) = '".date('Y-m-d', strtotime(($flt_from)))."' ";
        }
        else if($flt_from=='' && $flt_to !='')
        {
            $search.=" and  DATE(scp_trns_date)='".date('Y-m-d', strtotime(($flt_to)))."'";
        }
        else
        {
            $search.=" and  DATE(scp_trns_date)=DATE(now()) ";
        }
        $details = array();
        $j=0;$m=0;
        $customer_details = DB::SELECT("SELECT * FROM `internal_staffs_credits_pay` as s left join `internal_staffs` as i on s.scp_staff_id= i.id WHERE s.scp_staff_id	 != '' $search ORDER BY s.scp_trns_date	 DESC ");
       
        foreach($customer_details as $i)
        {
             $id = $i->id;
            $first_name = $i->first_name;
            $last_name = $i->last_name;
            $mobile = (int)$i->mobile;
            $scp_slno = $i->scp_slno;
            $scp_trns_date = $i->scp_trns_date;
            $scp_amount =  (float)$i->scp_amount;
            $scp_source_no =  $i->scp_transaction_source;
            $scp_paymode =  $i->scp_paymode;
            $scp_pnbtranscid =  $i->scp_PNBTransactionID;
            $scp_pay_ref =  $i->scp_payment_reference_number;
           
                        

            $name = $first_name." ".$last_name;
            $data[$m] = ['Date'  => $scp_trns_date,
                        'ID'  => $id,                       
                         'Name'   => $name,
                          'Mobile'    => $mobile,                         
                          'Amount'  => $scp_amount,
                          'Source'  => $scp_source_no,
                        'Paymode'  => $scp_paymode,
                        'PNB Transc ID'  => $scp_pnbtranscid,
                        'Pay Ref'  => $scp_pay_ref,

                     ];
               
                $m++;
               
        }
       $dateadd=date('dmy', strtotime(($flt_from)))."_".date('dmy', strtotime(($flt_to)));
        //return $data;
          Excel::create('CreditsPay_'.$dateadd, function($excel) use ($data) {
           
            $excel->sheet('CreditsPay', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
                $sheet->getStyle('A1:AN1')->getFont()->setBold(true);
              
            });
            
        })->download('xlsx');
       
     }
    public function filter_creditpays_list(Request $request)
    {
        $search = '';
        $itemsarr = array();
        $i=0;
        $staffid = $request['staff_id'];
        $flt_name = $request['flt_name'];
        $flt_from = $request['flt_from'];
        $flt_to = $request['flt_to'];

        if($flt_name!='')
        {
            $search.=" AND LOWER(i.id) LIKE '%".strtolower($flt_name)."%'";
        }
        if($flt_from!='' && $flt_to !=''){
            $search.=" and  DATE(scp_trns_date)  BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
        }
        else if($flt_from!='' && $flt_to =='')
        {
            $search.=" and  DATE(scp_trns_date) = '".date('Y-m-d', strtotime(($flt_from)))."' ";
        }
        else if($flt_from=='' && $flt_to !='')
        {
            $search.=" and  DATE(scp_trns_date)='".date('Y-m-d', strtotime(($flt_to)))."'";
        }
        else
        {
            $search.=" and  DATE(scp_trns_date)=DATE(now()) ";
        }
        $pointing = $request['current_count'];
        if($pointing=='')
        {
            $pointing=1;
        }
        $startlimit = ($pointing-1)*20;
        $endlimit = ($pointing)*20;
        $totaldetails = DB::SELECT("select count(*)  as totalstaff from  internal_staffs_credits_pay s WHERE s.`scp_staff_id` != ''");
        $details = array();
        $areaarr = array();
        $append='';
        $customer_totaldetails = DB::SELECT("select count(*)  as totalstaff from  internal_staffs_credits_pay s left join `internal_staffs` as i on s.scp_staff_id	 = i.id WHERE  s.`scp_staff_id` != '' $search");
        $count = $customer_totaldetails[0]->totalstaff;
        $customer_res = round($customer_totaldetails[0]->totalstaff/20,0);
        $customer_mode = ($customer_totaldetails[0]->totalstaff)%(20);
//        if($customer_mode!=0){$customer_res = $customer_res+1;}
        $total_cutomers=$customer_res;
        $rows = DB::SELECT("SELECT * FROM `internal_staffs_credits_pay` as s left join `internal_staffs` as i on s.scp_staff_id= i.id WHERE s.scp_staff_id	 != '' $search ORDER BY s.scp_trns_date	 DESC LIMIT $startlimit,20");
        $appends = '';
        foreach($rows as $data)
        {
            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $id = $data->id;
            $first_name = $data->first_name;
            $last_name = $data->last_name;
            $mobile = $data->mobile;
            $scp_slno = $data->scp_slno;
            $scp_trns_date = $data->scp_trns_date;
            $scp_amount =  $data->scp_amount;
            $scp_source_no =  $data->scp_transaction_source;
            $scp_paymode =  $data->scp_paymode;
            $scp_pnbtranscid =  $data->scp_PNBTransactionID;
            $scp_pay_ref =  $data->scp_payment_reference_number;
            
            $itemsarr[$i] = ['id'=>$id,'first_name'=>$first_name,'last_name'=>$last_name,'scp_amount'=>$scp_amount,'scp_source_no'=>$scp_source_no,'scp_trns_date'=>$scp_trns_date,'mobile'=>$mobile,'scp_slno'=>$scp_slno,'scp_paymode'=>$scp_paymode,'pnbtrnscid'=>$scp_pnbtranscid,'payref'=>$scp_pay_ref];
            $i++;
        }
        $m=$startlimit;
        $append .= '<table id="example1"  class="table table-striped table-bordered">';
        $append .='<thead>';
        $append .='<tr>';
        $append .='<th style="min-width:80px">Date</th>';
        $append .='<th style="min-width:30px">ID</th>';
        $append .='<th style="min-width:100px">Name</th>';
        $append .='<th style="min-width:80px">Mobile </th>';
        $append .='<th style="min-width:15px">Amount</th>';
        $append .='<th style="min-width:15px">Source</th>';
        $append .='<th style="min-width:15px">Paymode</th>';
        $append .='<th style="min-width:100px">PNB Transc ID</th>';
        $append .='<th style="min-width:100px">Pay Ref</th>';
        
        
        
        
        $append .='</tr>';
        $append .='</thead>';
        $append .='<tbody >';
        foreach($itemsarr as $i)
        {
            $id = $i['id'];
            $name = $i['first_name'];
            $lname = $i['last_name'];
            $mobile = $i['mobile'];
            $scp_slno = $i['scp_slno'];
            $scp_trns_date = $i['scp_trns_date'];
            $scp_amount = $i['scp_amount'];
            $scp_source_no = $i['scp_source_no'];
            $scp_paymode = $i['scp_paymode'];
            $scp_pnbid=$i['pnbtrnscid'];
            $scp_payref=$i['payref'];
            $m++;
            $append .= '<tr><td style="min-width:10px;">'.date('Y-m-d',strtotime($scp_trns_date)).'</td>';
            $append .= '<td style="min-width:30px;">'.$id.'</td>';
            $append .= '<td style="min-width:100px;">'.$name.' '.$lname.'</td>';
            $append .= '<td style="min-width:100px;">'.$mobile.'</td>';
            $append .= '<td style="min-width:10px;">'.$scp_amount.'</td>';
            $append .= '<td style="min-width:80px;">'.$scp_source_no.'</td>';
            $append .= '<td style="min-width:80px;">'.$scp_paymode.'</td>';
            $append .= '<td style="min-width:80px;">'.$scp_pnbid.'</td>';
            $append .= '<td style="min-width:80px;">'.$scp_payref.'</td>';
            
            
            
            

            $append .= '</tr>';
        }
        $append .='</tbody>';
        $append .='</table>';
        return response::json(['filter_data'=>$append,'data_count'=>$total_cutomers,'count' =>$totaldetails[0]->totalstaff,'searchcount' =>$customer_totaldetails[0]->totalstaff]);

    }
}
