<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
use App\CustomerList;
class CustomerController extends Controller
{
   //view customer list page
    public function view_customer(Request $request)
    {
         $append = '';
        
        $customer_details = DB::table('customer_list')->select('id','name','lname','email','mobile_contact','otp_generated','registration_date','total_orders','address')
           ->get();
	$m=0;
        foreach($customer_details as $i)
        {
            $id = $i->id;
            $name = $i->name;
            $lname = $i->lname;
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
				$address_line1 = $active_address[0]->address_line1;  
				$address_line2 = $active_address[0]->address_line2;  
				$address_default = $active_address[0]->address_default;  
				$address_pincode = $active_address[0]->address_pincode;  
				$address_landmark = $active_address[0]->address_landmark;  
			   }
            }
		$default_adress = $address_line1.", ".$address_line2.", ".$address_landmark.", ".$address_pincode;
		$details[$m] = ['name'=>$name,'lname'=>$lname,'mobile_contact'=>$mobile_contact,'email'=>$email,'total_orders'=>$total_orders,'registration_date'=>$registration_date,'default_adress'=>$default_adress];
		$m++;
        }
        
        return view('customer.manage_customer',compact('details','active_address'));
    }
    
     //Filtering of Customer List
    public function filter_customer_list(Request $request)
    {
        $search = '';
        $flt_phone = $request['flt_phone'];
        $flt_name = $request['flt_name'];
       
     /*   if((isset($flt_from) && $flt_from != '') && (isset($flt_to) && $flt_to != ''))
        {
            if($search == "")
            {
                $search.="  registration_date BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
            }
            else
            {
                $search.=" and  registration_date  BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
            }
        }*/
       
        if(isset($flt_name) && $flt_name != '')
        {
            if($search == "")
            {
                  $search.="  LOWER(name) LIKE '%".strtolower($flt_name)."%'";
            }
            else
            {
                 $search.=" and  LOWER(name) LIKE '%".strtolower($flt_name)."%'";
            }
        }
         if(isset($flt_phone) && $flt_phone != '')
        {
            if($search == "")
            {
                $search.="  mobile_contact LIKE '".$flt_phone."%'";
            }
            else
            {
                $search.=" and  mobile_contact LIKE '".$flt_phone."%'";
            }
        }
        if($search!="")
        {
            $search="where $search and ";
        }
        else
        {
            $search ="where ";
        }
  
        $customer_details = DB::SELECT('SELECT id,name,lname,email,mobile_contact,otp_generated,address,registration_date,total_orders FROM `customer_list` '.$search.' `customer_list`.`id` != " " ORDER BY id');
        $m=0;
        foreach($customer_details as $i)
        {
            $id = $i->id;
            $name = $i->name;
            $lname = $i->lname;
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
				$address_line1 = $active_address[0]->address_line1;  
				$address_line2 = $active_address[0]->address_line2;  
				$address_default = $active_address[0]->address_default;  
				$address_pincode = $active_address[0]->address_pincode;  
				$address_landmark = $active_address[0]->address_landmark;  
			   }
            }
		$default_adress = $address_line1.", ".$address_line2.", ".$address_landmark.", ".$address_pincode;
		$details[$m] = ['name'=>$name,'lname'=>$lname,'mobile_contact'=>$mobile_contact,'email'=>$email,'total_orders'=>$total_orders,'registration_date'=>$registration_date,'default_adress'=>$default_adress];
		$m++;
        }
        
        return $details;
    }

    //mobile registration with mobile no send as parameter
    public function mobile_registration($mobile)
    {
        $otp = mt_rand(1000,9999); //generate 4 digit random otp number
        $customers = DB::SELECT("SELECT id from `customer_list` where ((`password` != '') && (`password` IS NOT NULL)) and mobile_contact = '".$mobile."'");
        if(count($customers)>0)
        {
            $msg =  "Already Registered";
        }
       else
        {
            $lists = DB::SELECT("SELECT id from `customer_list` where mobile_contact = '".$mobile."'");
            if(count($lists)<=0)
            {
                DB::INSERT("INSERT INTO `customer_list`(`mobile_contact`, `otp_generated`,`registration_date`) VALUES ('" . trim($mobile) . "','" . trim($otp) . "','" . date('Y-m-d') . "')");
            }
            else
            {
                DB::SELECT('update `customer_list` set `otp_generated` = "'.$otp.'" where `mobile_contact` ="'.trim($mobile).'"');
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
                   $msg  =  'Password Mismatch';
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
    //forgot password
    public function forgot_otp($mobile)
    {
        $data=1;
        $list = DB::SELECT('SELECT * FROM customer_list WHERE `mobile_contact` ="'.trim($mobile).'"');
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
            $msg = "Mobile Number Not Found";
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
    public function addresslist($userid)
    {
        $array = array();
        $arr = array();
        $name=DB::SELECT("SELECT name FROM `customer_list` WHERE id='".$userid."'");
        $address = DB::SELECT("SELECT name,address,json_length(`address`) as count FROM `customer_list` WHERE id='".$userid."'");
        if(count($address)>0)
        {
            $msg = 'Exist';
            foreach($address as $key=>$list)
            {
                $name=$list->name;
                $address = json_decode($list->address,true);
                $count = $list->count;
                for($i=1;$i<=$count;$i++)
                {
                    $array['id']='ADDRESS'.$i;
                    $array['type'] = $address['ADDRESS'.$i]['TYPE'];
                    $array['line1'] = $address['ADDRESS'.$i]['LINE1'];
                    $array['line2'] = $address['ADDRESS'.$i]['LINE2'];
                    $array['default'] = $address['ADDRESS'.$i]['DEFAULT'];
                    $array['pincode'] = $address['ADDRESS'.$i]['PINCODE'];
                    if($address['ADDRESS'.$i]['LANDMARK'] == ' ' || $address['ADDRESS'.$i]['LANDMARK'] == ' ' || $address['ADDRESS'.$i]['LANDMARK'] == null || $address['ADDRESS'.$i]['LANDMARK'] == 'null')
                    {
                        $lndmrk = '0';
                    }
                    else
                    {
                        $lndmrk = $address['ADDRESS'.$i]['LANDMARK'];
                    }
                    $array['landmark'] = $lndmrk;
                    $arr[] =$array;
                }
            }
            return response::json(['msg' => $msg,'name'=>$name,'address' => $arr]);
        }
        else
        {
            $msg = 'Not Exist';
            return response::json(['msg' => $msg]);

        }
    }

  public function address_add($userid,$type,$line1,$line2,$default,$landmark,$pincode)
    {
        $line1 =  str_replace("'","", $line1);
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
        $landmark =  str_replace("\/","", $landmark);
      $length = DB::SELECT("SELECT name,json_length(`address`) as count FROM `customer_list` WHERE id='".$userid."'");
      $def=strtoupper($default);
      foreach($length as $key=>$list)
        {
          $count=$list->count;
          $countinc=$list->count+1;
        }
        if ($count=='')
        {
            $add =  '{"ADDRESS1":{"TYPE":"'.$type.'","LINE1":"'.$line1.'","LINE2":"'.$line2.'","DEFAULT":"'.$default.'","LANDMARK":"'.$landmark.'","PINCODE":"'.$pincode.'"}}';
            DB::SELECT("UPDATE customer_list SET address='$add' WHERE id ='$userid'");
        }
        else
        {
           if($def=='Y')
           {
               for($l=1;$l<=$count;$l++)
               {
                  DB::SELECT("UPDATE customer_list SET address=JSON_SET(address,'$.ADDRESS$l.DEFAULT','N') WHERE id ='$userid'");
               }
           }
            DB::SELECT("UPDATE customer_list SET address=JSON_INSERT(address,'$.ADDRESS$countinc',JSON_OBJECT('TYPE','$type','LINE1','$line1','LINE2','$line2','DEFAULT','$def','PINCODE','$pincode','LANDMARK','$landmark')) WHERE id ='$userid'");
        }
        $msg = 'Successful';
        return response::json(['msg' => $msg]);
    }
    
    public function address_edit($userid,$addressid,$type,$line1,$line2,$default,$landmark,$pincode)
    {
        $line1 =  str_replace("'","", $line1);
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
        $landmark =  str_replace("\/","", $landmark);
        $addid=strtoupper($addressid);
        $def=strtoupper($default);
        $length = DB::SELECT("SELECT name,json_length(`address`) as count FROM `customer_list` WHERE id='".$userid."'");
        foreach($length as $key=>$list)
        {
          $count=$list->count;
        }
        if($def=='Y')
           {
               for($l=1;$l<=$count;$l++)
               {
                  DB::SELECT("UPDATE customer_list SET address=JSON_SET(address,'$.ADDRESS$l.DEFAULT','N') WHERE id ='$userid'");
               }
           }
        DB::SELECT("UPDATE customer_list SET address=JSON_SET(address,'$.$addid',JSON_OBJECT('TYPE','$type','LINE1','$line1','LINE2','$line2','DEFAULT','$default','LANDMARK','$landmark','PINCODE','$pincode')) WHERE id ='$userid'");
        $msg = 'Successful';
        return response::json(['msg' => $msg]);
    }

    public function sendotp($mobile,$oldno)
    {
        $otp = mt_rand(1000, 9999); //generate 4 digit random otp number
        $sendmsg = urlencode("Thank you for registering with POTAFO. Your One-Time Password(OTP) for Mobile Verification is $otp");
        $smsurl = Datasource::smsurl($mobile, $sendmsg);
        $data = file_get_contents($smsurl);
        if (is_numeric($data))
        {
            DB::SELECT('update `customer_list` set `otp_generated` = "' . $otp . '" where `mobile_contact` ="' . trim($oldno) . '"');
            $msg = 'OTP Generated';
        } else
        {
            $msg = 'Network Error.OTP Not Generated';
        }
        return response::json(['msg' => $msg]);
    }

    public function updateprofile($oldmob,$newmob,$email,$password)
    {
        $name=DB::SELECT("SELECT name FROM `customer_list` WHERE mobile_contact='".$oldmob."'");
        if(count($name)>0)
        {
            $encr_method = Datasource::encr_method();
            $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
            $key1 = hash('sha256', $rowkey[0]->explore);
            $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
            $key = hash('sha256', $key1);
            $iv = substr(hash('sha256', $iv1), 0, 16);
            $passwd = openssl_encrypt($password, $encr_method, $key, 0, $iv);
            $passwrd = base64_encode($passwd);
            DB::SELECT('update `customer_list` set `email` = "' . $email . '",`password` = "' . $passwrd . '",`mobile_contact` = "' . $newmob . '" where `mobile_contact` ="' . trim($oldmob) . '"');
            $msg = 'Updated';
        }
        else
        {
           $msg = 'Not Exist';
        }
        return response::json(['msg' => $msg]);

    }
}
