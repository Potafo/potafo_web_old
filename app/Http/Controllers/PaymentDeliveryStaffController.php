<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\AuthPermission;
use App\City;
use App\DeliveryStaffPayment;
use App\Staff;
use App\AuthLogin;
use Response;
use Session;
use DateTime;
use DateTimeZone;
class PaymentDeliveryStaffController extends Controller
{
    //view staff payment page
    public function view_staff_payment(Request $request)
    {
		$staffid = Session::get('staffid');
		if(!$staffid){return redirect('');}
        $checkpermission= AuthPermission::where('userid', $staffid)
                ->where('page_status', 'Y')
                ->where('page_name', 'delivery_staff_payment')
                ->first();
                if (count($checkpermission)<=0) {
                    return redirect('dashboard');
                }
                $staffdesign= Staff::where('id', $staffid)->first();
                $designation=$staffdesign->designation;
                $paymenttype=$checkpermission->payment_type;
                $staff_to='';
               /* if($designation=="Delivery Staff")
                {
                    $staff_to=0;
                }else if($designation=="Collection Point")
                {
                    $staff_to=1;
                }else if($designation=="Cashier")
                {
                    $staff_to=2;
                }else if($designation=="Accountant")
                {
                    $staff_to=3;
                 }
                  if($designation=="Admin" || $designation=="Super_Admin")
                {
                    $rows=DeliveryStaffPayment::where('status', 0)
                    ->select('internal_staffs.first_name','internal_staffs.last_name','delivery_staff_payment.*')
                    ->join('internal_staffs', 'internal_staffs.id', '=', 'delivery_staff_payment.from_staff_id')              
                    ->get();
                }else{
                    $rows=DeliveryStaffPayment::where('status', 0)
                    ->select('internal_staffs.first_name','internal_staffs.last_name','delivery_staff_payment.*')
                    ->join('internal_staffs', 'internal_staffs.id', '=', 'delivery_staff_payment.from_staff_id')
                    ->where('transc_type', $paymenttype)
                    ->where('to_staff_type', $staff_to)
                    ->get();
                }*/
        
        /*
        staff type
        0 Delivery Staff
        1 Collection Point
        2 Cashier
        3 Accountant
        4 admin
        status 
        0 processing
        1 pending
        2 accepted
        3 rejected
        */
       // return view('payment.delivery_staff_payment',compact('rows','paymenttype','rows','designation','staffid'));
       return view('payment.delivery_staff_payment',compact('paymenttype','designation'));
    }
    public function load_paymenttable(Request $request)
    {
        $staff='';
        if(isset($request['staffname']))
        {
            $staff=$request['staffname'];
        }
        $datecheck='';
        if(isset($request['datecheck']))
        {
            $datecheck=$request['datecheck'];
        }
      $staffid = Session::get('staffid');
		//if(!$staffid){return redirect('');}
        $checkpermission= AuthPermission::where('userid', $staffid)
                ->where('page_status', 'Y')
                ->where('page_name', 'delivery_staff_payment')
                ->first();
                /*if (count($checkpermission)<=0) {
                    return redirect('index');
                }*/
                $staffdesign= Staff::where('id', $staffid)->first();
                $designation=$staffdesign->designation;
                $paymenttype=$checkpermission->payment_type;
                $staff_to='';
                if($designation=="Delivery Staff")
                {
                    $staff_to=0;
                }else if($designation=="Collection Point")
                {
                    $staff_to=1;
                }else if($designation=="Cashier")
                {
                    $staff_to=2;
                }else if($designation=="Accountant")
                {
                    $staff_to=3;
                 }
                 
                 $dd=date("Y-m-d",strtotime($datecheck));
                  if($designation=="Admin" || $designation=="Super_Admin")
                {
                    //,'internal_staffs.last_name'
                    $rows=DeliveryStaffPayment::where('status', 0)
                    ->select('internal_staffs.first_name','delivery_staff_payment.*')
                    ->join('internal_staffs', 'internal_staffs.id', '=', 'delivery_staff_payment.from_staff_id')              
                    ->where('internal_staffs.first_name','like','%'.$staff.'%')
                    ->where('delivery_staff_payment.created_at','like',$dd.'%')
                    ->get();
                    
                }else{
                    $rows=DeliveryStaffPayment::where('status', 0)
                    ->select('internal_staffs.first_name','delivery_staff_payment.*')
                    ->join('internal_staffs', 'internal_staffs.id', '=', 'delivery_staff_payment.from_staff_id')
                    ->where('transc_type', $paymenttype)
                    ->where('to_staff_type', $staff_to)
                    ->where('internal_staffs.first_name','like','%'.$staff.'%')
                    ->where('delivery_staff_payment.created_at','like',$dd.'%')
                    ->get();
                }
        
        /*
        staff type
        0 Delivery Staff
        1 Collection Point
        2 Cashier
        3 Accountant
        4 admin
        status 
        0 processing
        1 pending
        2 accepted
        3 rejected
        */
       // return view('payment.payment_table_load',compact('rows','paymenttype','rows','designation','staffid'));
$append='';
       if(count($rows)>0)
       {$sl=1;
         foreach($rows as $value)
            {
                //$date=$value['created_at'];
                $date=date("d-m-Y",strtotime($value['created_at']));
                //$dateval = strtoupper($date->format('m-d-Y'));
           $append.='<tr>';
                 
                 $append.=' <td style="text-align: left;width:4%">'.$sl++.'</td>';
           $append.=' <td style="text-align: left;width:10%">'. $value['from_staff_id'].'</td>';
           $append.=' <td style="text-align: left;width:10%">'. $value['first_name'].' '. $value['last_name'].'</td>';
           $append.=' <td style="text-align: left;width:10%">'. $value['transc_type'].'</td>';
           $append.=' <td style="text-align: left;width:10%">'.$date.'</td>';
           $append.=' <td style="text-align: left;width:10%">'. $value['amount'].'</td>';
            $append.=' <td style="text-align: left;width:10%">';
            $status="";
                  if( $value['status'] == '0')
                  $status= "Processing";
                   else if( $value['status']  == '1') 
                   $status= "Pending";
                   else if( $value['status']  == '2') 
                   $status= "Accepted";
                   else if( $value['status']  == '3') 
                   $status= "Rejected"; 
                   $append.=$status;              
            $append.='</td>';
            $append.='<td style="text-align: left;width:10%">'. $value['remarks'].'</td>';
            $append.='<td style="text-align: left;width:20%">';
                   if($paymenttype=="CASH")
                   $append.='<a  class="Location_btn_acc " onclick="accept_money('.$value['id'].','. $value['amount'].','. $value['from_staff_id'].','.$staffid.')"> Accept</a>';
                    else if($paymenttype=="UPI")
                    $append.='<a  class="Location_btn " onclick="accept_upi('.$value['id'].','. $value['amount'].','. $value['from_staff_id'].','.$staffid.')"> Accept</a>';
                    
                    $append.='<a  class="Location_btn reject" onclick=" reject_transc('.$value['id'].','. $value['from_staff_id'].','.$staffid.')"> Reject</a>';
                   
                   $append.=' </td>';
                   $append.='</tr>';
            }
       }

return $append;



    }
    public function authpermission()
    {
        $rows=AuthPermission::select(`id`, `userid`, `page_name`, `page_status`, `payment_type`, `login_status`)
            ->orderBy('id','desc')
            ->get();
        return $rows;
    }
    public function paymentlist()
    {
        $rows=DeliveryStaffPayment::select('id', 'from_staff_id', 'from_staff_type', 'to_staff_id', 'to_staff_type', 'amount', 'transc_type', 'upi_transc_id', 'account', 'status', 'remarks', 'created_at', 'updated_at')
            ->orderBy('id','desc')
            ->get();
        return $rows;
    }
    public function  insert_delivery_staff_payment(Request $request)
    {
      $paymnt= new DeliveryStaffPayment();
        $paymnt->from_staff_id =$request['from_id'];
        $paymnt->from_staff_type =$request['from_type'];
        $paymnt->to_staff_type =$request['to_type'];
        if (isset($request['transc_type'])) {
            $paymnt->transc_type =$request['transc_type'];
        }else{
            $paymnt->transc_type =NULL;
        }
        if (isset($request['upi_transc_id'])) {
            $paymnt->upi_transc_id =$request['upi_transc_id'];
        }else{
            $paymnt->upi_transc_id =NULL;
        }
        $paymnt->amount =$request['amount'];
        $paymnt->status =0;
        $paymnt->save();
        $arr = array();
        $arr[0] = 'success';
        //$msg = 'success';
        return response::json(['data' =>$arr]);
    }
    public function checkauth_payment(Request $request)
    {
        $rows=AuthLogin::where('userid',$request['staff']) 
        ->where('auth_code',$request['authcode'])            
        ->get();
        if(count($rows)>0)
        {
            return "success";
        }else{
            return "sorry";
        }
    }
    public function accept_amount(Request $request)
    {
        
        DeliveryStaffPayment::where('id', $request['fieldid'])
        ->update(
            ['account'=>$request['userid'],
                'status' => 2,
                'upi_transc_id'=>$request['transctn'],
                'updated_at'=>now()
            ]);
           
    }
    public function reject_amount(Request $request)
    {
        
        DeliveryStaffPayment::where('id', $request['fieldid'])
        ->update(
            ['account'=>$request['userid'],
                'status' => 3,
                'remarks'=>$request['remarks'],
                'updated_at'=>now()
            ]);
           
    }

    public function view_staff_payment_history(Request $request)
    {
        $staffid = Session::get('staffid');
		if(!$staffid){return redirect('');}
        $checkpermission= AuthPermission::where('userid', $staffid)
                ->where('page_status', 'Y')
                ->where('page_name', 'delivery_staff_history')
                ->first();
                if (count($checkpermission)<=0) {
                    return redirect('dashboard');
                }
                $staffdesign= Staff::where('id', $staffid)->first();
                $designation=$staffdesign->designation;
                $paymenttype=$checkpermission->payment_type;
                $staff_to='';
                return view('payment.delivery_staff_history',compact('paymenttype','designation'));
    }
    public function load_paymenttable_history(Request $request)
    {
        $staff='';
        if(isset($request['staffname']))
        {
            $staff=$request['staffname'];
        }
        $datecheck='';
        if(isset($request['datecheck']))
        {
            $datecheck=$request['datecheck'];
        }
        $transctype='';
        if(isset($request['transctype']))
        {
            $transctype=$request['transctype'];
        }
      $staffid = Session::get('staffid');
		//if(!$staffid){return redirect('');}
        $checkpermission= AuthPermission::where('userid', $staffid)
                ->where('page_status', 'Y')
                ->where('page_name', 'delivery_staff_history')
                ->first();
                /*if (count($checkpermission)<=0) {
                    return redirect('index');
                }*/
                $staffdesign= Staff::where('id', $staffid)->first();
                $designation=$staffdesign->designation;
                $paymenttype=$checkpermission->payment_type;
                $staff_to='';
               /* if($designation=="Delivery Staff")
                {
                    $staff_to=0;
                }else if($designation=="Collection Point")
                {
                    $staff_to=1;
                }else if($designation=="Cashier")
                {
                    $staff_to=2;
                }else if($designation=="Accountant")
                {
                    $staff_to=3;
                 }*/
                 
                 $dd=date("Y-m-d",strtotime($datecheck));
                /*  if($designation=="Admin" || $designation=="Super_Admin")
                {*/
                    //,'internal_staffs.last_name'
                    if ($transctype=="") {
                        $rows=DeliveryStaffPayment::select('internal_staffs.first_name', 'delivery_staff_payment.*')
                    ->join('internal_staffs', 'internal_staffs.id', '=', 'delivery_staff_payment.from_staff_id')
                    ->where('internal_staffs.first_name', 'like', '%'.$staff.'%')
                    ->where('delivery_staff_payment.created_at', 'like', $dd.'%')
                    ->get();
                    }else{
                        $rows=DeliveryStaffPayment::select('internal_staffs.first_name', 'delivery_staff_payment.*')
                    ->join('internal_staffs', 'internal_staffs.id', '=', 'delivery_staff_payment.from_staff_id')
                    ->where('internal_staffs.first_name', 'like', '%'.$staff.'%')
                    ->where('delivery_staff_payment.created_at', 'like', $dd.'%')
                    ->where('transc_type', $transctype)
                    ->get();
                    }
                    
               /* }else{
                    $rows=DeliveryStaffPayment::where('status', 0)
                    ->select('internal_staffs.first_name','delivery_staff_payment.*')
                    ->join('internal_staffs', 'internal_staffs.id', '=', 'delivery_staff_payment.from_staff_id')
                    ->where('transc_type', $paymenttype)
                    ->where('to_staff_type', $staff_to)
                    ->where('internal_staffs.first_name','like','%'.$staff.'%')
                    ->where('delivery_staff_payment.created_at','like',$dd.'%')
                    ->get();
                }*/
        
        /*
        staff type
        0 Delivery Staff
        1 Collection Point
        2 Cashier
        3 Accountant
        4 admin
        status 
        0 processing
        1 pending
        2 accepted
        3 rejected
        */
       // return view('payment.payment_table_load',compact('rows','paymenttype','rows','designation','staffid'));
$append='';
       if(count($rows)>0)
       {$sl=1;
         foreach($rows as $value)
            {
                //$date=$value['created_at'];
                $date=date("d-m-Y",strtotime($value['created_at']));
                //$dateval = strtoupper($date->format('m-d-Y'));
           $append.='<tr>';
                 
                 $append.=' <td style="text-align: left;width:4%">'.$sl++.'</td>';
           $append.=' <td style="text-align: left;width:10%">'. $value['from_staff_id'].'</td>';
           $append.=' <td style="text-align: left;width:10%">'. $value['first_name'].' '. $value['last_name'].'</td>';
           $append.=' <td style="text-align: left;width:10%">'. $value['transc_type'].'</td>';
           $append.=' <td style="text-align: left;width:10%">'.$date.'</td>';
           $append.=' <td style="text-align: left;width:10%">'. $value['amount'].'</td>';
            $append.=' <td style="text-align: left;width:10%">';
            $status="";
            if( $value['status'] == '0')
            $status= "Processing";
             else if( $value['status']  == '1') 
             $status= "Pending";
             else if( $value['status']  == '2') 
             $status= "Accepted";
             else if( $value['status']  == '3') 
             $status= "Rejected"; 
             $append.=$status;                
            $append.='</td>';
            $append.='<td style="text-align: left;width:10%">'. $value['remarks'].'</td>';
           /* $append.='<td style="text-align: left;width:20%">';
                   if($paymenttype=="CASH")
                   $append.='<a  class="Location_btn " onclick="accept_money('.$value['id'].','. $value['amount'].','. $value['from_staff_id'].','.$staffid.')"> Accept</a>';
                    else if($paymenttype=="UPI")
                    $append.='<a  class="Location_btn " onclick="accept_upi('.$value['id'].','. $value['amount'].','. $value['from_staff_id'].','.$staffid.')"> Accept</a>';
                    
                    $append.='<a  class="Location_btn reject" onclick=" reject_transc('.$value['id'].','. $value['from_staff_id'].','.$staffid.')"> Reject</a>';
                   */
                   $append.=' </td>';
                   $append.='</tr>';
            }
       }

return $append;



    }
    public function dashboard()
    {
        return view('dashboard');
    }

}