<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
use App\UserMaster;
use Session;
class UserLoginController extends Controller
{
    public function emailcheck(Request $request)
    {
        $email = $request['email'];
        $check = DB::SELECT("SELECT count(*) as datacount FROM `users` left Join `internal_staffs` on users.staffid = internal_staffs.id WHERE `name`='".$email."' and internal_staffs.active = 'Y'");
        if($check[0]->datacount >0)
        {
            return "exist";
        }
        else
        {
            return "not exist";
        }
      
    }
    public function logintest(Request $request)
    {
       
      return view('user.manage_user');
    }
    public function userexistcheck(Request $request)
	{
		$report = array();
        $arr = array();
		$email=$request['email'];
		$psw=$request['password'];
                $encr_method = Datasource::encr_method();
                $rowkey = DB::SELECT("SELECT `explore`,`explore2` FROM `general_settings`");
                $key1 = hash('sha256', $rowkey[0]->explore);
                $iv1 = substr(hash('sha256', $rowkey[0]->explore2), 0, 16);
                $key = hash('sha256', $key1);
                $iv = substr(hash('sha256', $iv1), 0, 16);
                $password = openssl_encrypt($psw, $encr_method, $key, 0, $iv);
                $password = base64_encode($password);
                $userdetails = DB::SELECT("SELECT * FROM `users` WHERE (name='".$email."') and (password='".$password."')");
                $count = count($userdetails);
		       if($count>0)
		       {

	                //$groupmaster = UserMaster::where('name',$email)->where('password',$password)->select('staffid')->first();
                    if($userdetails[0]->login_group=='H'){
                        $staffid= $userdetails[0]->restaurant_id;
                        $designation= '';
                    }
                    else{
                        $staffid= $userdetails[0]->staffid;
                        $setuserid= $userdetails[0]->id;
                        $desgnationdetails = DB::SELECT("SELECT designation  FROM `users` left Join internal_staffs on  users.staffid =  internal_staffs.id where staffid ='".$staffid."'");
                        $designation = $desgnationdetails[0]->designation;
                    }
                        $msg = 'User Exist';
                       return response()->json(['msg' => $msg,'userdetail'=>$userdetails,'login_group'=>$userdetails[0]->login_group,'designation'=>$designation,'staffid' =>$staffid,'setuserid'=>$setuserid]);

                   }

		else
		{
			$msg = 'Password Mismatch';
			return  response()->json(['msg' => $msg]);
		}

	}
        
        
        public function setsession(Request $request)
	   {
	        $staffid = $request['staffid'];
	        $designation = $request['designation'];
            $setuserid = $request['setuserid'];
	        $logingroup= $request['logingroup'];
            $request->session()->put('staffid',$staffid);
            $request->session()->put('logingroup',$logingroup);
            $request->session()->put('setuserid',$setuserid);
            return 'success';
	}
        
        
    
}
