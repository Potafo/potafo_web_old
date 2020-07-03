<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
use Helpers\Commonsource;
use App\CustomerList;
use Excel;
use Session;
class CustomerController extends Controller
{
   //view customer list page
    public function view_customer(Request $request)
    {
       $staffid = Session::get('staffid');
		if(!$staffid){return redirect('');}
        return view('customer.manage_customer');
    }
     public function __construct(\Maatwebsite\Excel\Excel $excel)
        {
            $this->excel = $excel;
        }
     //Filtering of Customer List
      public function excel_download(Request $request) {
         $search = '';
        
        $flt_phone = $request['flt_phone_xl'];
        $flt_name = $request['flt_name_xl'];
        $flt_from = $request['flt_from_xl'];
        $flt_to   = $request['flt_to_xl'];
       if($flt_phone!=''){
           $search.=" AND mobile_contact LIKE '".$flt_phone."%'";
       }
       if($flt_name!=''){
          $search.=" AND lower(CONCAT(name,' ',lname)) LIKE '%".strtolower($flt_name)."%'";
       }
       if($flt_from!='' && $flt_to =='' && $flt_phone=='' && $flt_name==''){
           $search.=" AND registration_date = '".date('Y-m-d', strtotime(($flt_from)))."'";
       }
       if($flt_from!='' && $flt_to !='' && $flt_phone=='' && $flt_name==''){
           $search.=" and  registration_date  BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
       }
       if($flt_phone =='' && $flt_name=='' && $flt_from=='' && $flt_to==''){
           $search.=" AND registration_date BETWEEN DATE_SUB(DATE(NOW()), INTERVAL 4 DAY) AND DATE(NOW()) ";
       }
        $details = array();
        $j=0;$m=0;
        $customer_details = DB::SELECT("SELECT id,name,lname,email,mobile_contact,otp_generated,address,registration_date,total_orders,default_address FROM `customer_list` WHERE id != '' $search and password IS NOT NULL  ORDER BY registration_date DESC ");
       
        foreach($customer_details as $i)
        {
            $id = $i->id;
            $name = $i->name;
            $lname = $i->lname;
            $mobile_contact = $i->mobile_contact;
            $email = $i->email;
            $addresslimit = $i->default_address;
            $registration_date = $i->registration_date;
			$address_type = '';
			$address_line1 = '';
			$address_line2 = '';
			$address_default = '';
			$address_pincode = '';
			$address_landmark = '';
			if($addresslimit!=''){
				$active_address = DB::SELECT("SELECT address->>'$.$addresslimit.TYPE' AS address_type,
			   address->>'$.$addresslimit.LINE1' AS address_line1,
			   address->>'$.$addresslimit.LINE2' AS address_line2,
			   address->>'$.$addresslimit.DEFAULT' AS address_default,
			   address->>'$.$addresslimit.PINCODE' AS address_pincode,
			   address->>'$.$addresslimit.LANDMARK' AS address_landmark
			   FROM customer_list where address->>'$.$addresslimit.DEFAULT' = 'Y' And id='".$id."'");
                        if(count($active_address)!=0){
                                    $address_line1=$active_address[0]->address_line1;
                                    $address_line2=$active_address[0]->address_line2;
                                    $address_landmark=$active_address[0]->address_landmark;
                                    $address_pincode=$active_address[0]->address_pincode;;
                        }
			}
                        

            $default_adress = $address_line1.", ".$address_line2.", ".$address_landmark.", ".$address_pincode;
            $data[$m] = ['Name'  => $name,
                         'Last Name'   => $lname,
                          'Mobile'    => $mobile_contact,
                          'Email'  => $email,
                          'Address'  => $default_adress,
                          'Registered on'   => $registration_date
                     ];
               
                $m++;
               
        }
       
        //return $data;
          Excel::create('customer1', function($excel) use ($data) {
           
            $excel->sheet('Customer Master', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
                $sheet->getStyle('A1:AN1')->getFont()->setBold(true);
              
            });
            
        })->download('xlsx');
       
     }
    public function filter_customer_list(Request $request)
    {
        $search = '';
        $flt_phone = $request['flt_phone'];
        $flt_name = $request['flt_name'];
        $flt_from = $request['flt_from'];
        $flt_to   = $request['flt_to'];
       if($flt_phone!=''){
           $search.=" AND mobile_contact LIKE '".$flt_phone."%'";
       }
       if($flt_name!=''){
          $search.=" AND lower(CONCAT(name,' ',lname)) LIKE '%".strtolower($flt_name)."%'";
       }
       if($flt_from!='' && $flt_to =='' && $flt_phone=='' && $flt_name==''){
           $search.=" AND registration_date = '".date('Y-m-d', strtotime(($flt_from)))."'";
       }
       if($flt_from!='' && $flt_to !='' && $flt_phone=='' && $flt_name==''){
           $search.=" and  registration_date  BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
       }
       if($flt_phone =='' && $flt_name=='' && $flt_from=='' && $flt_to==''){
           $search.=" AND registration_date BETWEEN DATE_SUB(DATE(NOW()), INTERVAL 4 DAY) AND DATE(NOW()) ";
       }
      $pointing = $request['current_count'];
      if($pointing=='')
      {
          $pointing=1;
      }
       $startlimit = ($pointing-1)*20;
       $endlimit = ($pointing)*20;
        $totaldetails = DB::SELECT("SELECT count(id) as totalcustomers FROM `customer_list` WHERE id != ''  and password IS NOT NULL ORDER BY registration_date DESC");


        $details = array();
        $append='';
        $customer_totaldetails = DB::SELECT("SELECT count(id) as totalcustomers FROM `customer_list` WHERE id != '' $search and password IS NOT NULL ORDER BY registration_date DESC");
        //return "SELECT count(id) as totalcustomers FROM `customer_list` WHERE id != '' $search and password IS NOT NULL ORDER BY registration_date DESC";
        $count = $customer_totaldetails[0]->totalcustomers;
        $customer_res = round($customer_totaldetails[0]->totalcustomers/20,0);
        $customer_mode = ($customer_totaldetails[0]->totalcustomers)%(20);
        if($customer_mode!=0)
        {
            $customer_res = $customer_res+1;
        }
        $total_cutomers=$customer_res;
        $customer_details = DB::SELECT("SELECT id,name,lname,email,mobile_contact,otp_generated,address,registration_date,total_orders FROM `customer_list` WHERE id != '' $search and password IS NOT NULL  ORDER BY registration_date DESC LIMIT $startlimit,20 ");
        $m=$startlimit;
        $append .= '<table id="example1"  class="table table-striped table-bordered">';
        $append .='<thead>';
        $append .='<tr>';
        $append .='<th style="min-width:30px">Slno</th>';
        $append .='<th style="min-width:100px">Name</th>';
        $append .='<th style="min-width:100px">Last Name</th>';
        $append .='<th style="min-width:90px">Mobile</th>';
        $append .='<th style="min-width:140px">Email </th>';
        $append .='<th style="min-width:180px">Address</th>';
        $append .='<th style="min-width:100px">Registered on</th>';
        $append .='</tr>';
        $append .='</thead>';
        $append .='<tbody >';
        foreach($customer_details as $i)
        {
            $id = $i->id;
            $name = $i->name;
             $lname = $i->lname;
            if($i->lname=='null') {
              $lname ="";  
            }
            $mobile_contact = $i->mobile_contact;
            $email = $i->email;
            $total_orders = $i->total_orders;
            $address = $i->address;
            $registration_date = $i->registration_date;
            $length = DB::SELECT("SELECT json_length(`address`) as count FROM `customer_list` WHERE id='".$id."'");
            $settings[$m] = $length[0]->count;
            $address_count =$length[0]->count; 
			$address_type = '';
			$address_line1 = '';
			$address_line2 = '';
			$address_default = '';
			$address_pincode = '';
			$address_landmark = '';
            for($l=1;$l<=$address_count;$l++) 
            {
               $addresslimit = 'ADDRESS'.$l;
               $active_address = DB::SELECT("SELECT address->>'$.$addresslimit.TYPE' AS address_type,
			   address->>'$.$addresslimit.LINE1' AS address_line1,
			   address->>'$.$addresslimit.LINE2' AS address_line2,
			   address->>'$.$addresslimit.DEFAULT' AS address_default,
			   address->>'$.$addresslimit.PINCODE' AS address_pincode,
			   address->>'$.$addresslimit.LANDMARK' AS address_landmark
			   FROM customer_list where address->>'$.$addresslimit.DEFAULT' = 'Y' And id='".$id."'");
			   if(count($active_address)!=0) 
                           {
				$address_type = $active_address[0]->address_type;  
				$address_line1 = substr($active_address[0]->address_line1, 0, 50) ;  
				$address_line2 = $active_address[0]->address_line2;  
				$address_default = $active_address[0]->address_default;
                                if($active_address[0]->address_pincode != 'null' || $active_address[0]->address_pincode != '0')
                                {
                                    $address_pincode = '';
                                }
                                else
                                {
                                    $address_pincode = $active_address[0]->address_pincode;
                                }
                                if($active_address[0]->address_landmark == '0' || $active_address[0]->address_landmark == 'null')
                                {
                                    $address_landmark = '';
                                }
                                else
                                {
                                    $address_landmark = $active_address[0]->address_landmark;
                                }
			   }
            }
		$default_adress = $address_line1.", ".$address_line2.", ".$address_landmark.", ".$address_pincode;
		//$details[$m] = ['name'=>$name,'lname'=>$lname,'mobile_contact'=>$mobile_contact,'email'=>$email,'total_orders'=>$total_orders,'registration_date'=>$registration_date,'default_adress'=>$default_adress];
               
                $m++;
                $append .= '<tr><td style="min-width:30px;">'.$m.'</td>';
                          $append .= '<td style="min-width:100px;"><strong>'.$name.'</strong></td>';
                          $append .= '<td style="min-width:100px;"><strong>'.$lname.'</strong></td>';
                          $append .= '<td style="min-width:90px;">'.$mobile_contact.'</td>';
                          $append .= '<td style="min-width:140px;">'.$email.'</td>';
                          $append .= '<td style="min-width:180px;">'.$default_adress.'</td>';
                          $append .= '<td style="min-width:100px;">'.$registration_date.'</td>';
                          $append .= '</tr>';
        }
        $append .='</tbody>';
        $append .='</table>';
         return response::json(['filter_data'=>$append,'data_count'=>$total_cutomers,'count' =>$totaldetails[0]->totalcustomers,'searchcount' =>$customer_totaldetails[0]->totalcustomers]);
    }

    //mobile registration with mobile no send as parameter
    public function mobile_registration($mobile)
    {
        $otp = mt_rand(1000,9999); //generate 4 digit random otp number
        $customers = DB::SELECT("SELECT id from `customer_list` where ((`password` != '') && (`password` IS NOT NULL)) and mobile_contact = '".trim($mobile)."'");
        if(count($customers)>0)
        {
            $msg =  "Already Registered";
        }
       else
        {
            $lists = DB::SELECT("SELECT id from `customer_list` where mobile_contact = '".trim($mobile)."'");
            if(count($lists)<=0)
            {
                DB::INSERT("INSERT INTO `customer_list`(`mobile_contact`, `otp_generated`,`registration_date`) VALUES ('" . trim($mobile) . "','" . trim($otp) . "','" . date('Y-m-d') . "')");
            }
            else
            {
                DB::SELECT('update `customer_list` set `otp_generated` = "'.trim($otp).'" where `mobile_contact` ="'.trim($mobile).'"');
            }
            $sendmsg = urlencode("Thank you for registering with POTAFO. Your One-Time Password(OTP) for Mobile Verification is $otp");
            $smsurl = Datasource::smsurl($mobile,$sendmsg);
//          $smsurl = "http://sms1.webqua.com/httpapi/smsapi?uname=explore&password=Explore321&sender=EXPDNE&receiver=$mobile&route=T&msgtype=1&sms=".urlencode("Thank you for registering with POTAFO. Your One-Time Password(OTP) for Mobile Verification is $otp");
            $data = file_get_contents($smsurl);
           if(is_numeric($data))
           {
               $msg = 'OTP Generated';
           }
           else
           {
               $msg = 'Network Error.OTP Not Generated ';
           }
           $menulist = json_decode($data, true);
        }
        return response::json(compact('msg'));
    }
    //mobile registration new api with mobile no send as parameter
    public function mobile_registration_new(Request $request)
    {
        $mobile = $request['mobile'];
        $otp = mt_rand(1000,9999); //generate 4 digit random otp number
        $customers = DB::SELECT("SELECT id from `customer_list` where ((`password` != '') && (`password` IS NOT NULL)) and mobile_contact = '".trim($mobile)."'");
        if(count($customers)>0)
        {
            $msg =  "Already Registered";
        }
       else
        {
            $lists = DB::SELECT("SELECT id from `customer_list` where mobile_contact = '".trim($mobile)."'");
            if(count($lists)<=0)
            {
                   
                    $random_no  = mt_rand(1000000, 9999999). mt_rand(1000000, 9999999).$mobile[rand(0, strlen($mobile) - 1)]; //generate 4 digit random otp number
                    $string     = str_shuffle($random_no);
                    $userid     = substr($string,0,10);   
                    $userid     = $userid.date('dmy');
                DB::INSERT("INSERT INTO `customer_list`(id,`mobile_contact`, `otp_generated`,`registration_date`) VALUES ('$userid','" . trim($mobile) . "','" . trim($otp) . "','" . date('Y-m-d') . "')");
            }
            else
            {
                DB::SELECT('update `customer_list` set `otp_generated` = "'.trim($otp).'" where `mobile_contact` ="'.trim($mobile).'"');
            }
            $sendmsg = urlencode("Thank you for registering with POTAFO. Your One-Time Password(OTP) for Mobile Verification is $otp");
            $smsurl = Datasource::smsurl($mobile,$sendmsg);
//          $smsurl = "http://sms1.webqua.com/httpapi/smsapi?uname=explore&password=Explore321&sender=EXPDNE&receiver=$mobile&route=T&msgtype=1&sms=".urlencode("Thank you for registering with POTAFO. Your One-Time Password(OTP) for Mobile Verification is $otp");
            $data = file_get_contents($smsurl);
           if(is_numeric($data))
           {
               $msg = 'OTP Generated';
           }
           else
           {
               $msg = 'Network Error.OTP Not Generated ';
           }
           $menulist = json_decode($data, true);
        }
        return response::json(compact('msg'));
    }
    public function custid_updation_test(Request $request) {
         $lists = DB::SELECT("SELECT id,mobile_contact,name,DATE_FORMAT(registration_date,'%d%m%y') as registration_date from `customer_list` WHERE cust_token IS NULL ");
         foreach($lists as $list) {
                    $characters =$list->mobile_contact;
//                    $random_no  = mt_rand(1000000, 9999999). mt_rand(1000000, 9999999).$characters[rand(0, strlen($characters) - 1)]; //generate 4 digit random otp number
//                    $string     = str_shuffle($random_no);
//                    $userid     = substr($string,0,10);   
//                    $userid     = $userid.date('dmy');
//             DB::UPDATE("UPDATE customer_list SET id='$userid',old_id='$list->id' WHERE id='$list->id' ");
//             DB::UPDATE("UPDATE order_master SET customer_id='$userid',customer_id_old='$list->id' WHERE customer_id='$list->id' ");
//             DB::UPDATE("UPDATE ftoken_master SET customer_id='$userid',customer_old_id='$list->id' WHERE customer_id='$list->id' ");
                    $token        = bcrypt($list->id.$list->mobile_contact.$list->name);
            $token_string = str_replace(['"',"'",'/'],'_', $token); 
            $token_string = substr($token,4,20);  
            DB::UPDATE("UPDATE customer_list SET cust_token='$token_string' WHERE id='$list->id' ");
         }
       return "ok";
    }

    //customer details updated if otp matching list
    public function customer_registration($fst,$lst=null,$eml,$pswd,$mbl)
    {
        $list = DB::SELECT('select `id`,`name` from `customer_list` where mobile_contact = "'.trim($mbl).'"');
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($pswd, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        if(count($list) >0)
        {
            if(isset($lst) && $lst != '')
            {
                $last = $lst;
            }
            else{
                $last = null;
            }
            DB::SELECT('update `customer_list` set `name` = "'.trim(title_case($fst)).'",`lname` = "'.$last.'",`email` = "'.trim($eml).'",`password` = "'.$password.'" where id="'.trim($list[0]->id).'"');
            $msg = 'Successfully Registered.';
            $lists = DB::SELECT('select `id`,`name` from `customer_list` where mobile_contact = "'.trim($mbl).'"');
            $name = strtoupper(trim($lists[0]->name));
//          return response::json(['msg' => $msg,'user_id' =>$list[0]->id,'user_name' =>strtoupper($list[0]->name)]);
            return response::json(['msg' => $msg,'user_id' => (string)$lists[0]->id,'user_name' => $name]);
        }
        else
        {
            $msg = 'Unsuccessful';
            return response::json(['msg' => $msg]);

        }
    }

    //customer details updated if otp matching list
    public function customer_registration_new(Request $request)
    {
        $fst = urldecode($request['firstname']);
        if(isset($request['lastname']) && ($request['lastname']!= '' || $request['lastname']!= 'null'))
        {
            $lst =  urldecode($request['lastname']);
        }
        else
        {
            $lst = 'null';
        }
        $eml  = $request['email'];
        $pswd = $request['password'];
        $mbl  = $request['mobile'];
        $otp  = $request['verfication_code'];
        $list = DB::SELECT('select `id`,`name` from `customer_list` where mobile_contact = "'.trim($mbl).'" AND otp_generated="'.trim($otp).'" ');
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($pswd, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        if(count($list) >0)
        {
           
            if(isset($lst) && $lst != '')
            {
                $last = $lst;
            }
            else{
                $last = null;
            }
            $token        = bcrypt($list[0]->id.$mbl);
            $token_string = str_replace(['"',"'",'/'],'_', $token); 
            $token_string = substr($token,4,20);   
            DB::SELECT('update `customer_list` set `name` = "'.trim(title_case($fst)).'",`lname` = "'.$last.'",`email` = "'.trim($eml).'",`password` = "'.$password.'",otp_generated="",cust_token="'.$token_string.'" where id="'.trim($list[0]->id).'"');
            $msg = 'Successfully Registered.';
            $lists = DB::SELECT('select `id`,`name` from `customer_list` where mobile_contact = "'.trim($mbl).'"');
            $name = strtoupper(trim($lists[0]->name));
//          return response::json(['msg' => $msg,'user_id' =>$list[0]->id,'user_name' =>strtoupper($list[0]->name)]);
            return response::json(['msg' => $msg,'user_id' => (string)$lists[0]->id,'user_name' => $name,'cust_token'=>$token_string]);
        }
        else
        {
            $msg = 'Unsuccessful';
            return response::json(['msg' => $msg]);

        }
    }
    //customer exist or not
     public function customer_login($phone,$pswd)
    {
        $encr_method = Datasource::encr_method();
     $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
       $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
       $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($pswd, $encr_method, $key, 0, $iv);
       $password = base64_encode($password);
        $list = DB::SELECT('select `id`,`name`,`password` from `customer_list` where mobile_contact = "'.trim($phone).'"');
       if(count($list) >0)
        {
           if($list[0]->password == '' || $list[0]->password == '')
           {
             $msg  =  'User Not Registered';
              return response::json(['msg' => $msg]);
           }
           else
           {
               $user= DB::SELECT('select `id`,`name` from `customer_list` where mobile_contact = "'.trim($phone).'" and password = "'.$password.'"');
              if(count($user) >0)
              {
                  $msg  = 'Success';
                   return response::json(['msg' => $msg,'user_id' =>$user[0]->id,'user_name' => strtoupper($user[0]->name)]);
               }
               else
              {
                   $msg  =  'Sorry, Your Entered Password Is Incorrect';
                   return response::json(['msg' => $msg]);
               }
          }
        }
       else
       {
              $msg =  'Mobile Number Not Found';
             return response::json(['msg' => $msg]);
       }
    }
    public function customer_login_new(Request $request)
    {
        $phone      = $request['phone'];
        $pswd       = $request['password'];
        $mode       = $request['mode'];
        $version    = $request['version'];
        $ftoken     = $request['ftoken'];
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($pswd, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        $list = DB::SELECT('select `id`,`name`,`password`,cust_token,psw_update from `customer_list` where mobile_contact = "'.trim($phone).'"');
        if(count($list) >0)
        {
           if($list[0]->psw_update=='N')
           {
               $msg  =  'Reset Password';
               return response::json(['msg' => $msg]);
           }
           if($list[0]->password == '' || $list[0]->password == '')
           {
               $msg  =  'User Not Registered';
               return response::json(['msg' => $msg]);
           }
           else
           {
               $user= DB::SELECT('select `id`,`name`,cust_token from `customer_list` where mobile_contact = "'.trim($phone).'" and password = "'.$password.'"');
               if(count($user) >0)
               {
                   if($mode!='W'){
                    DB::DELETE("DELETE FROM ftoken_master WHERE trim(ftoken) IN('".$ftoken."')  ");
                    DB::INSERT('INSERT INTO ftoken_master(customer_id, slno, mode, version, ftoken, login_time) VALUES ("'.$user[0]->id.'",0,"'.$mode.'","'.$version.'","'.$ftoken.'",now())');

                   }
                   $msg  = 'Success';
                   return response::json(['msg' => $msg,'user_id' =>$user[0]->id,'user_name' => strtoupper($user[0]->name),'cust_token'=>$user[0]->cust_token]);
               }
               else
               {
                     $msg  =  'Sorry, Your Entered Password Is Incorrect';
                   return response::json(['msg' => $msg]);
               }
           }
        }
        else
        {
              $msg =  'Mobile Number Not Found';
              return response::json(['msg' => $msg]);
        }
    }

    //otp verification
    public function otp_verification($phone,$otp)
    {
        $customer = DB::SELECT('SELECT  id from `customer_list` where `mobile_contact` = "'.$phone.'" and `otp_generated` = "'.$otp.'"');
        if(count($customer)>0)
        {
            DB::SELECT('update `customer_list` set `otp_generated` = "" where `mobile_contact` ="'.trim($phone).'"');
            $msg = 'OTP Verified';
        }
        else
        {
            $msg = 'OTP Invalid';

        }
        return response::json(compact('msg'));
    }

    //otp verification
    public function otp_verification_new(Request $request)
    {
        $phone = $request['mobile'];
        $otp = $request['otp'];
        $customer = DB::SELECT('SELECT  id from `customer_list` where `mobile_contact` = "'.$phone.'" and `otp_generated` = "'.$otp.'"');
        if(count($customer)>0)
        {
            $msg = 'OTP Verified';
        }
        else
        {
            $msg = 'OTP Invalid';

        }
        return response::json(compact('msg'));
    }

       //otp verification
    public function profileotp_verification($phone,$otp)
    {
        $customer = DB::SELECT('SELECT  id from `customer_list` where `temp_mobile` = "'.$phone.'" and `otp_generated` = "'.$otp.'"');
        if(count($customer)>0)
        {
            DB::SELECT('update `customer_list` set `otp_generated` = "" where `temp_mobile` ="'.trim($phone).'"');
            $msg = 'OTP Verified';
        }
        else
        {
            $msg = 'OTP Invalid';

        }
        return response::json(compact('msg'));
    }
      //otp verification new
    public function profileotp_verification_new(Request $request)
    {
        $phone = $request['mobile'];
        $otp = $request['otp'];
        $customer = DB::SELECT('SELECT  id from `customer_list` where `temp_mobile` = "'.$phone.'" and `otp_generated` = "'.$otp.'"');
        if(count($customer)>0)
        {
           // DB::SELECT('update `customer_list` set `otp_generated` = "" where `temp_mobile` ="'.trim($phone).'"');
            $msg = 'OTP Verified';
        }
        else
        {
            $msg = 'OTP Invalid';

        }
        return response::json(compact('msg'));
    }
    //forgot password
    public function forgot_otp($mobile)
    {
        $data=1;
        $list = DB::SELECT('SELECT * FROM customer_list WHERE ((`password` != "") && (`password` IS NOT NULL)) and `mobile_contact` ="'.trim($mobile).'"');
        if(count($list)>0) {
            $otp = mt_rand(1000, 9999); //generate 4 digit random otp number
            $sendmsg = urlencode("Thank you for registering with POTAFO. Your One-Time Password(OTP) for Mobile Verification is $otp");
            $smsurl = Datasource::smsurl($mobile, $sendmsg);
            $data = file_get_contents($smsurl);
            if (is_numeric($data))
            {
                DB::SELECT('update `customer_list` set `otp_generated` = "' . $otp . '" where `mobile_contact` ="' . trim($mobile) . '"');
                $msg = 'OTP Generated';
            } else
            {
                $msg = 'Network Error.OTP Not Generated';
            }
        }
        else
        {
            DB::DELETE("DELETE FROM customer_list WHERE trim(mobile_contact) = '".$mobile."'");
            $msg = "Mobile Number Not Found";
        }
       return response::json(compact('msg'));
    }
    //forgot password
    public function forgot_otp_new(Request $request)
    {
        $mobile = $request['mobile'];
        $data=1;
        $list = DB::SELECT('SELECT * FROM customer_list WHERE ((`password` != "") && (`password` IS NOT NULL)) and `mobile_contact` ="'.trim($mobile).'"');
        if(count($list)>0) {
            $otp = mt_rand(1000, 9999); //generate 4 digit random otp number
            $sendmsg = urlencode("Thank you for registering with POTAFO. Your One-Time Password(OTP) for Mobile Verification is $otp");
            $smsurl = Datasource::smsurl($mobile, $sendmsg);
            $data = file_get_contents($smsurl);
            if (is_numeric($data))
            {
                DB::SELECT('update `customer_list` set `otp_generated` = "' . $otp . '" where `mobile_contact` ="' . trim($mobile) . '"');
                $msg = 'OTP Generated';
            } else
            {
                $msg = 'Network Error.OTP Not Generated';
            }
        }
        else
        {
            DB::DELETE("DELETE FROM customer_list WHERE trim(mobile_contact) = '".$mobile."'");
            $msg = "Mobile Number Not Found";
        }
       return response::json(compact('msg'));
    }
public function force_psw_update(Request $request) {
   $mobile = $request['mobile'];
   $otp    = $request['verification_code'];
   $pswd   = $request['password'];
   $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($pswd, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
   $is_exist = DB::SELECT("SELECT id,password FROM customer_list WHERE mobile_contact='$mobile' AND otp_generated='$otp'");
   if(count($is_exist)!=0) {
       if($password==$is_exist[0]->password) {
            $msg = "Password Same As Your Old Password";
             return response::json(compact('msg')); 
       } 
       else{
           DB::UPDATE("UPDATE customer_list SET psw_update='Y',password='$password', otp_generated=''  WHERE mobile_contact='$mobile' AND otp_generated='$otp'  ");
           $msg = "Successfully Updated";
       }
       
   } else{
       $msg = "Invalid Info";
   }
     return response::json(compact('msg'));   
}

//forgot password
    public function forgot_password($mobile,$pswd)
    {
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($pswd, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        DB::SELECT('update `customer_list` set `password` = "'.$password.'" where `mobile_contact` ="'.trim($mobile).'"');
        $msg = 'Password Updated';
        return response::json(compact('msg'));
    }
      //forgot password
    public function forgot_password_new(Request $request)
    {
        $mobile= $request['mobile'];
        $pswd  = $request['password'];
        $otp   = $request['verfication_code'];
        $encr_method = Datasource::encr_method();
        $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
        $key1 = hash('sha256', $rowkey[0]->explore);
        $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
        $key = hash('sha256', $key1);
        $iv = substr(hash('sha256', $iv1), 0, 16);
        $password = openssl_encrypt($pswd, $encr_method, $key, 0, $iv);
        $password = base64_encode($password);
        DB::UPDATE('update `customer_list` set `password` = "'.$password.'",otp_generated="" where `mobile_contact` ="'.trim($mobile).'" AND otp_generated="'.$otp.'" ' );
        $msg = 'Password Updated';
        return response::json(compact('msg'));
    }
    public function addresslist(Request $request)
    {
        $cust_token = $request['cust_token'];
        $userid     = $request['user_id'];
        $array = array();
        $arr = array();
        $addressl = DB::SELECT("SELECT name,address,json_length(`address`) as count,default_address FROM `customer_list` WHERE id='".$userid."' AND cust_token ='".$cust_token."' ");
        if(count($addressl)>0)
        {
            $msg = 'Exist';

            foreach($addressl as $key=>$list)
            {
                $name    = $list->name;
                $count   = $list->count;
                $address = json_decode($list->address, true);
                if(count($address)>0)
                {
                    foreach ($address as $key => $val)
                    {
                     if(trim(strtoupper($list->default_address)) == strtoupper($key))
                     {
                          $def = 'Y';
                     }
                     else
                     {
                         $def = 'N';
                     }
                    $array['id'] = $key;
                    $array['type'] = $address[$key]['TYPE'];
                    $array['line1'] = $address[$key]['LINE1'];
                    $array['line2'] = $address[$key]['LINE2'];
                    $array['default'] = $def;
                    $array['pincode'] = $address[$key]['PINCODE'];
                    if(isset($address[$key]['LATITUDE']))
                    {
                        if($address[$key]['LATITUDE'] != 'null')
                        {
                            $array['latitude'] = $address[$key]['LATITUDE'];

                        }
                        else
                        {
                            $array['latitude'] = "0";
                        }
                    }
                    else
                    {
                        $array['latitude'] = "0";
                    }
                    if(isset($address[$key]['LONGITUDE']))
                    {
                        if($address[$key]['LONGITUDE'] != 'null')
                        {
                            $array['longitude'] = $address[$key]['LONGITUDE'];

                        }
                        else
                        {
                            $array['longitude'] = "0";
                        }
                    }
                        else
                        {
                            $array['longitude'] = "0";

                        }
                    if ($address[$key]['LANDMARK'] == ' ' || $address[$key]['LANDMARK'] == ' ' || $address[$key]['LANDMARK'] == null || $address[$key]['LANDMARK'] == 'null') {
                        $lndmrk = '0';
                    } else {
                        $lndmrk = $address[$key]['LANDMARK'];
                    }
//                    }
                    $array['landmark'] = $lndmrk;
                    $arr[] = $array;
                }
                }
                else
                {
                    $msg = 'Not Exist';
                }
            }
           return response::json(['msg' => $msg,'name'=>$name,'address' => $arr]);
           
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(compact('msg'));
        }
    }

  public function address_add($userid,$type,$line1,$line2,$default,$landmark,$pincode)
    {
        $landmark =  str_replace(array("'","\"","\\","/"),array("","","","_","_"),urldecode($landmark));
        $line2 =  str_replace(array("'","\"","\\","/"),array("","","","_","_"),$line2);
        $line1 =  str_replace(array("'","\"","\\","/"),array("","","","_","_"),$line1);
        if($line1 == 'null' || $line1 == '' || $line1 == ' ')
        {
                   $msg = 'Address Incomplete';
        }
        else
        {
        $length = DB::SELECT("SELECT name,json_length(`address`) as count,address FROM `customer_list` WHERE id='".$userid."'");
        $def=strtoupper($default);
            $addresslst = json_decode($length[0]->address,true);

            if(count($addresslst) >0)
            {
                foreach ($length as $key => $list)
                {
                    $count = $list->count;
                    $countinc = $list->count + 1;
                }
            }
            else{
                $count = 0;
                $countinc  = 1;
            }
        if ($count=='' || $count <= 0)
        {
            $add =  '{"ADDRESS1":{"TYPE":"'.$type.'","LINE1":"'.$line1.'","LINE2":"'.$line2.'","LANDMARK":"'.$landmark.'","PINCODE":"'.$pincode.'"}}';
            DB::SELECT("UPDATE customer_list SET address='$add' WHERE id ='$userid'");
        }
        else
        {
               /* foreach($addresslst as $key=>$val)
                {
//                  return "UPDATE customer_list SET address=JSON_SET(address,'$.$key.DEFAULT','N') WHERE id ='$userid'";
                    DB::SELECT("UPDATE customer_list SET address=JSON_SET(address,'$.$key.DEFAULT','N') WHERE id ='$userid'");
                }*/
            }
            if(count($addresslst)>0)
            {
                $lastid = array_keys($addresslst)[count(array_keys($addresslst)) - 1];
                $substr = 'ADDRESS' . (substr($lastid, 7,strlen($lastid)) + 1);
            }
            else{
                $substr = 'ADDRESS1';

            }
            DB::SELECT("UPDATE customer_list SET address=JSON_INSERT(address,'$.$substr',JSON_OBJECT('TYPE','$type','LINE1','$line1','LINE2','$line2','PINCODE','$pincode','LANDMARK','$landmark')),default_address = '$substr' WHERE id ='$userid'");
//        }
            $msg = 'Successful';
        }
        return response::json(['msg' => $msg]);
    }

  public function address_add_new(Request $request)
  {
        $post = $request->all();
        $userid = $post['user_id'];
        $cust_token = $post['cust_token'];
        $type = $post['type'];
        $line1 = $post['line1'];
        $line2 = $post['line2'];
        $default = $post['default'];
        $landmark  = urldecode(trim($post['landmark']));
        $pincode  = $post['pincode'];
        if($post['latitude'])
        {
            $latitude  =  str_replace(',','',$post['latitude']);
        }
        else
        {
            $latitude = "";
        }
         if($post['longitude'])
        {
            $longitude  = str_replace(',','',$post['longitude']);
        }
        else
        {
            $longitude = "";
        }
        $landmark =  str_replace(array("'","\"","\\","/"),array("","","","_","_"),$landmark);
        $line2 =  str_replace(array("'","\"","\\","/"),array("","","","_","_"),$line2);
        $line1 =  str_replace(array("'","\"","\\","/"),array("","","","_","_"),$line1);
        if($line1 == 'null' || $line1 == '' || $line1 == ' ')
        {
            $msg = array('msg' =>'Address Incomplete');

        }
        else
        {
          /*$line2 =  str_replace("'","", $line2);
            $landmark =  str_replace("'","", $landmark);*/
            $length = DB::SELECT("SELECT name,json_length(`address`) as count,address FROM `customer_list` WHERE id='".$userid."' AND cust_token='$cust_token' ");
            $def=strtoupper($default);
            $addresslst = json_decode($length[0]->address,true);

            if(count($addresslst) >0)
            {
                foreach ($length as $key => $list)
                {
                    $count = $list->count;
                    $countinc = $list->count + 1;
                }
            }
            else
            {
                $count = 0;
                $countinc  = 1;
            }
            if ($count=='' || $count <= 0)
            {
                $add =  '{"ADDRESS1":{"TYPE":"'.$type.'","LINE1":"'.$line1.'","LINE2":"'.$line2.'","LANDMARK":"'.$landmark.'","PINCODE":"'.$pincode.'","LATITUDE":"'.$latitude.'","LONGITUDE":"'.$longitude.'"}}';
                DB::SELECT("UPDATE customer_list SET address='$add' WHERE id ='$userid' AND cust_token='$cust_token' ");
            }
            if(count($addresslst)>0)
            {
                $lastid = array_keys($addresslst)[count(array_keys($addresslst)) - 1];
                $substr = 'ADDRESS' . (substr($lastid, 7,strlen($lastid)) + 1);

            }
            else{
                $substr = 'ADDRESS1';
            }
            DB::SELECT("UPDATE customer_list SET address=JSON_INSERT(address,'$.$substr',JSON_OBJECT('TYPE','$type','LINE1','$line1','LINE2','$line2','PINCODE','$pincode','LANDMARK','$landmark','LATITUDE','$latitude','LONGITUDE','$longitude')),default_address = '$substr' WHERE id ='$userid' AND cust_token='$cust_token' ");
//        }
            $msg = array('msg' => 'Successful','id' => $substr);
        }
        return response::json($msg);
    }

    public function address_edit($userid,$addressid,$type,$line1,$line2,$default,$landmark,$pincode)
    {
       /*$line1 =  str_replace("'","", $line1);
        $line1 =  str_replace("\"","", $line1);
        $line1 =  str_replace("\\","", $line1);
        $line1 =  str_replace("\/","", $line1);
        $line2 =  str_replace("'","", $line2);
        $line2 =  str_replace("\"","", $line2);
        $line2 =  str_replace("\\","", $line2);
        $line2 =  str_replace("\/","", $line2);
        $landmark =  str_replace("'","", $landmark);
        $landmark =  str_replace("\"","", $landmark);
        $landmark =  str_replace("\\","", $landmark);
        $landmark =  str_replace("\/","", $landmark);*/
        $landmark =  str_replace(array("'","\"","\\","/"),array("","","","_","_"),$landmark);
        $line2 =  str_replace(array("'","\"","\\","/"),array("","","","_","_"),$line2);
        $line1 =  str_replace(array("'","\"","\\","/"),array("","","","_","_"),$line1);

        if($line1 == 'null' || $line1 == '' || $line1 == ' ')
        {
            $msg = 'Address Incomplete';
        }
        else
        {
          /*  $line2 = str_replace("'", "", $line2);
            $landmark = str_replace("'", "", $landmark);*/
            $addid = strtoupper($addressid);
            $def = strtoupper($default);
            $length = DB::SELECT("SELECT name,json_length(`address`) as count FROM `customer_list` WHERE id='" . $userid . "'");
            foreach ($length as $key => $list)
            {
                $count = $list->count;
            }
            if ($def == 'Y')
            {
                for ($l = 1; $l <= $count; $l++)
                {
                    DB::SELECT("UPDATE customer_list SET address=JSON_SET(address,'$.ADDRESS$l.DEFAULT','N') WHERE id ='$userid'");
                }
            }
            DB::SELECT("UPDATE customer_list SET address=JSON_SET(address,'$.$addid',JSON_OBJECT('TYPE','$type','LINE1','$line1','LINE2','$line2','DEFAULT','$default','LANDMARK','$landmark','PINCODE','$pincode')) WHERE id ='$userid'");
            $msg = 'Successful';
        }
        return response::json(['msg' => $msg]);
    }

    public function sendotp($mobile,$oldno)
    {
        $customers = DB::SELECT("SELECT id from `customer_list` where ((`password` != '') && (`password` IS NOT NULL)) and mobile_contact = '".$mobile."'");
        if(count($customers)>0)
        {
            $msg =  "Already Registered";
        }
        else
        {
            $otp = mt_rand(1000, 9999); //generate 4 digit random otp number
            $sendmsg = urlencode("Thank you for registering with POTAFO. Your One-Time Password(OTP) for Mobile Verification is $otp");
            $smsurl = Datasource::smsurl($mobile, $sendmsg);
            $data = file_get_contents($smsurl);
            if (is_numeric($data))
            {
                DB::SELECT('update `customer_list` set `otp_generated` = "' . $otp . '",`temp_mobile` = "' . $mobile . '" where `mobile_contact` ="' . trim($oldno) . '"');
                $msg = 'OTP Generated';
            }
            else
            {
                $msg = 'Network Error.OTP Not Generated';
            }
        }
        return response::json(['msg' => $msg]);
    }
    public function sendotp_new(Request $request)
    {
        $mobile = $request['newmobile'];
        $oldno = $request['oldmobile'];
        $cust_token = $request['cust_token'];
        //return "SELECT id FROM `customer_list` WHERE mobile_contact='".$oldno."' AND cust_token ='".$cust_token."' ";
        $is_valid_user = DB::SELECT("SELECT id FROM `customer_list` WHERE mobile_contact='".$oldno."' AND cust_token ='".$cust_token."' ");
        if(count($is_valid_user)!=0){
            $customers = DB::SELECT("SELECT id from `customer_list` where ((`password` != '') && (`password` IS NOT NULL)) and mobile_contact = '".$mobile."'");
                if(count($customers)>0)
                {
                    $msg =  "Already Registered";
                }
                else
                {
                    $otp = mt_rand(1000, 9999); //generate 4 digit random otp number
                    $sendmsg = urlencode("Thank you for registering with POTAFO. Your One-Time Password(OTP) for Mobile Verification is $otp");
                    $smsurl = Datasource::smsurl($mobile, $sendmsg);
                    $data = file_get_contents($smsurl);
                    if (is_numeric($data))
                    {
                        DB::SELECT('update `customer_list` set `otp_generated` = "' . $otp . '",`temp_mobile` = "' . $mobile . '" where `mobile_contact` ="' . trim($oldno) . '"');
                        $msg = 'OTP Generated';
                    }
                    else
                    {
                        $msg = 'Network Error.OTP Not Generated';
                    }
                }
        } else{
            $msg = 'Invalid Info';
        }
        return response::json(['msg' => $msg]);
    }

    public function updateprofile(Request $request)
    {
        $oldmob = $request['old_mob'];
        $newmob = $request['new_mob'];
        $email = $request['email'];
        $cust_token = $request['cust_token'];
        $otp = $request['verification_code'];
        $name=DB::SELECT("SELECT name FROM `customer_list` WHERE mobile_contact='".$oldmob."' AND cust_token='$cust_token' ");
        if(count($name)>0)
        {
            if($otp=='') {
                DB::SELECT('update `customer_list` set `email` = "' . $email . '",`temp_mobile` = "" where `mobile_contact` ="' . trim($oldmob) . '"');
            } else{
            DB::SELECT('update `customer_list` set `email` = "' . $email . '",`mobile_contact` = "' . $newmob . '",otp_generated="",`temp_mobile` = "" where `mobile_contact` ="' . trim($oldmob) . '"');
            }
            $msg = 'Updated';
        }
        else
        {
           $msg = 'Not Exist';
        }
        return response::json(['msg' => $msg]);
    }
    public function user_details(Request $request)
    {
        $id = $request['user_id'];
        $cust_token = $request['cust_token'];
        
        $customer = DB::SELECT("select name,lname,email,mobile_contact as mobile from customer_list where id = '$id' AND cust_token='$cust_token'  ");
        if(count($customer) >0)
        {
           
            $custarr['name'] = $customer[0]->name;
            $custarr['lname'] = $customer[0]->lname;
            $custarr['email'] = $customer[0]->email;
            $custarr['mobile'] = $customer[0]->mobile;
            $custarr['password'] = "";
            $msg = 'Exist';
            return response::json(['msg' => $msg,'details' => $custarr]);
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);
        }
    }
    //address remove
    public function address_remove($userid,$id)
    {
        $name=DB::SELECT("SELECT name,default_address FROM `customer_list` WHERE id='".$userid."'");
        if(count($name)>0)
        {
            if(strtoupper($id) == $name[0]->default_address)
            {
                DB::SELECT("UPDATE customer_list SET `address` = JSON_REMOVE(`address`, '$.$id'),default_address ='' WHERE `id` = '$userid'");
            }
            else{
                DB::SELECT("UPDATE customer_list SET `address` = JSON_REMOVE(`address`, '$.$id') WHERE `id` = '$userid'");
            }
            $msg ='success';
        }
        else
        {
             $msg = 'User Not Exist';
        }
        return response::json(['msg' => $msg]);
    }
    //address remove new
    public function address_remove_new(Request $request)
    {
        $userid = $request['userid'];
        $id = $request['addressid'];
        $name=DB::SELECT("SELECT name,default_address FROM `customer_list` WHERE id='".$userid."'");
        if(count($name)>0)
        {
            if(strtoupper($id) == $name[0]->default_address)
            {
                DB::SELECT("UPDATE customer_list SET `address` = JSON_REMOVE(`address`, '$.$id'),default_address ='' WHERE `id` = '$userid'");
            }
            else{
                DB::SELECT("UPDATE customer_list SET `address` = JSON_REMOVE(`address`, '$.$id') WHERE `id` = '$userid'");
            }
            $msg ='success';
        }
        else
        {
             $msg = 'User Not Exist';
        }
        return response::json(['msg' => $msg]);
    }
    //get default location
    public function default_location($id)
    {
           $resultarr = array();
           $select =  DB::SELECT("SELECT IFNULL(default_address,0) AS defaultadd,id FROM customer_list  where id='$id'");
           if(count($select) >0)
           {
               $addrsid = TRIM($select[0]->defaultadd);
               if($addrsid == '0')
               {
                $resultarr = array('msg' => 'EXIST','line1' => '','latitude' => '11.6161665','longitude' =>'76.0507957');
                return response::json($resultarr);
               }
               else
               {
                   $lines = DB::SELECT("SELECT IFNULL(address->>'$.$addrsid.LINE1',0) as line1,IFNULL(address->>'$.$addrsid.LATITUDE',0) as lat,IFNULL(address->>'$.$addrsid.LONGITUDE',0) as lon FROM `customer_list` WHERE id='" . $id . "'");
                   if (count($lines) > 0)
                   {
                       if ($lines[0]->line1 == '0')
                       {
                        $resultarr = array('msg' => 'EXIST','line1' => '','latitude' => '11.6161665','longitude' =>'76.0507957');
                        return response::json($resultarr);
                       }
                       else
                       {
                           if($lines[0]->lat == '0' || $lines[0]->lon == '0')
                           {
                               $cordinates = Commonsource::latitude_longitude($lines[0]->line1);
                               if($cordinates[2] == '0')
                               {
                                $resultarr = array('msg' => 'EXIST','line1' => '','latitude' => '11.6161665','longitude' =>'76.0507957');
                                return response::json($resultarr);
                               }
                               else
                               {
                                   $resultarr = array('msg' => 'EXIST','line1' => $lines[0]->line1,'latitude' => (string)$cordinates[0],'longitude' =>(string)$cordinates[1]);
                               }
                           }
                           else
                           {
                               $resultarr = array('msg' => 'EXIST','line1' => $lines[0]->line1,'latitude' => (string)$lines[0]->lat,'longitude' =>(string)$lines[0]->lon);
                           }
                           return response::json($resultarr);
                       }
                   }
                   else
                   {
                    $resultarr = array('msg' => 'EXIST','line1' => '','latitude' => '11.6161665','longitude' =>'76.0507957');
                    return response::json($resultarr);
                   }
               }

           }
           else
           {
            $resultarr = array('msg' => 'EXIST','line1' => '','latitude' => '11.6161665','longitude' =>'76.0507957');
            return response::json($resultarr);
           }
    }
    public function ftoken_check(Request $request)
    {
        $userid = trim($request['userid']);
        $token = trim($request['token']);
        $mode = trim($request['mode']);
        $version = trim($request['version']);
        $user_exist= DB::SELECT("SELECT * FROM customer_list WHERE id ='".$userid."' ");
        if(count($user_exist)!=0){
        $is_exist= DB::SELECT("SELECT * FROM ftoken_master WHERE customer_id ='".$userid."'   and ftoken ='".$token."'");
        if(count($is_exist)!=0)
        {
            $msg= "success";
            if(trim($is_exist[0]->version) != $version)
            {
               DB::SELECT("UPDATE `ftoken_master` set version = '".$version."' where customer_id ='".$userid."' and ftoken ='".$token."'");
            }
        }
        else
        {
           if(isset($token) && ($token != ''  || $token != ' ' || strtolower($token) != 'null'))
           {
               DB::INSERT('INSERT INTO `ftoken_master`(`customer_id`,`mode`, `version`, `ftoken`, `login_time`) VALUES ("'.$userid.'","'.$mode.'","'.$version.'","'.$token.'",now())');
           }
            $msg= "success";
        }
        }
        else{
           $msg= "NoUserFound"; 
        }
        return response::json(['msg' => $msg]);
    }

    public function ftoken_delete(Request $request)
    {
        $userid = $request['userid'];
        $token = trim($request['token']);
        $is_exist= DB::SELECT("SELECT * FROM ftoken_master WHERE customer_id ='".$userid."'   and ftoken ='".$token."' ");
        if(count($is_exist)!=0)
        {
             DB::DELETE("delete from ftoken_master where customer_id ='".$userid."'   and ftoken ='".$token."'");
         }
        $msg = 'success';
        return response::json(['msg' => $msg]);
    }


    //notification send
    public function notification_send(Request $request)
    {
        $result = Commonsource::notification($request);
        return $result;
    }
    public function notification_check(Request $request) {
        $userid = '"'.$request['userid'].'"';
        $result = array();
        $notifications = DB::SELECT("SELECT id,is_all,DATE_FORMAT(entry_date, '%Y-%m-%d %h:%i:%s %p') as entry_date,title,message FROM notifications WHERE now()<expiry ");
        $i=0;
        foreach($notifications as $notif){
            if($notif->is_all=='Y'){
                $title = $notif->title;
                $message = $notif->message;
                $entrytime = $notif->entry_date;
                $result[$i]=['title'=>$title,'message'=>$message,'entrytime'=>$entrytime];
                $i++;
            }
            else{
                $user_notification = DB::SELECT("SELECT b.title,b.message,DATE_FORMAT(b.entry_date, '%Y-%m-%d %h:%i:%s %p') as entry_date
                FROM notifications b WHERE json_contains(b.user_list,'[$userid]') AND now()<b.expiry and id=$notif->id");
                foreach($user_notification as $usernotf)
                {
                    $title = $usernotf->title;
                    $message = $usernotf->message;
                    $entrytime = $notif->entry_date;
                     $result[$i]=['title'=>$title,'message'=>$message,'entrytime'=>$entrytime];
                     $i++;
                }
                
            }
        }
        if($i!=0){
            $msg = 'exist';
        }else{
             $msg = 'notexist';
        }
       return response::json(['msg'=>$msg,'notifications' => $result]); 
    }

    public function notification_list(Request $request)
    {
        $groupid  = $request['groupid'];
        $flt_from = $request['flt_from'];
        $flt_to   = $request['flt_to'];
        $search = '';
        if($groupid!='all'){
            $search .=" AND a.groupid=$groupid ";
        }
        if($flt_from!='' && $flt_to ==''){
           $search.=" AND  date(a.entry_date)  = '".date('Y-m-d', strtotime(($flt_from)))."'";
       }
       if($flt_from!='' && $flt_to !=''){
           $search.=" AND  date(a.entry_date)   >='".date('Y-m-d', strtotime(($flt_from)))."' AND    date(a.entry_date)  <= '".date('Y-m-d', strtotime(($flt_to)))."'";
       }
        $pointing = $request['current_count'];
      if($pointing=='')
      {
          $pointing=1;
      }
        $startlimit = ($pointing-1)*20;
        $endlimit = ($pointing)*20;
        $details = array();$append='';
        $notification_totaldetails = DB::SELECT("SELECT count(id) as total FROM `notifications`  a LEFT JOIN notification_group b on a.groupid=b.g_id WHERE a.id != '' $search ORDER BY a.entry_date DESC");
        $notication_res = round($notification_totaldetails[0]->total/20,0);

        $notication_mode = ($notification_totaldetails[0]->total)%(20);
        if($notication_mode!=0)
        {
            $notication_res = $notication_res +1 ;
        }

        $total_notifications=$notication_res;
      $notifications = DB::SELECT("SELECT a.id,a.title,a.message,b.g_name,a.entry_date,a.expiry FROM notifications a LEFT JOIN notification_group b on a.groupid=b.g_id WHERE a.id != '' $search ORDER BY a.entry_date DESC LIMIT $startlimit,20");
      $m=$startlimit;
      $append .= '<table id="example1"  class="table table-striped table-bordered dataTable no-footer">';
      $append .='<thead>';
      $append .='<tr>';
      $append .='<th style="min-width:30px;">Action</th>';
      $append .='<th style="min-width:140px">Group</th>';
      $append .='<th style="min-width:180px">Title</th>';
      $append .='<th style="min-width:220px">Message</th>';
      $append .='<th style="min-width:20px">Entry Date </th>';
      $append .='<th style="min-width:20px">Expiry Date</th>';
      $append .='</tr>';
      $append .='</thead>';
      $append .='<tbody >';
        if(count($notifications)>0)
        {
            foreach($notifications as $notfy)
            {
                if($notfy->g_name  == '')
                {
                    $gname = 'All';
                }
                else
                {
                    $gname = $notfy->g_name;
                }
                $m++;
                $append .='<tr>';
                $append .='<td style="min-width:30px">
                               <a class="btn button_table" onclick="notificationdelete(\''.$notfy->id.'\');" title="Delete"><i class="fa fa-trash-o"></i></a>
                               </td>';
                $append .='<td  style="min-width:140px">'.title_case($gname).'</td>';
                $append .='<td style="min-width:180px"><div class="tooltips">'.str_limit($notfy->title, 40, '...');
                if(strlen($notfy->title) >40)
                {
                    $append .='<span class="tooltiptext">'.$notfy->title.'</span>';
                }
                $append .='<td style="min-width:220px"><div class="tooltips">'.str_limit($notfy->message, 40, '...');
                if(strlen($notfy->message) >40)
                {
                    $append .='<span class="tooltiptext">'.$notfy->message.'</span>';
                }
                $append  .= '</div></td>';
                $append .='<td style="min-width:20px">'.date('Y-m-d H:i',strtotime($notfy->entry_date)).'</td>';
                $append .='<td style="min-width:20px">'.date('Y-m-d H:i',strtotime($notfy->expiry)).'</td>';
                $append .='</tr>';
            }
        }
        else
        {
            $append .='<tr>';
            $append .='<td  style="border:none;"></td>';
            $append .='<td  style="border:none;"></td>';
            $append .='<td  style="text-align: center; border:none;">No Items Found</td>';
            $append .='<td  style="border:none;"></td>';
            $append .='<td  style="border:none;"></td>';
            $append .='<td  style="border:none;"></td>';
            $append .='</tr>';
        }
        $append .='</tbody>';
        $append .='</table>';
      return ['filter_data'=>$append,'data_count'=>$total_notifications];
    }


}
