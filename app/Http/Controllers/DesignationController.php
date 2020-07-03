<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Helpers\Datasource;
use App\Designation;
class DesignationController extends Controller
{
   
     public function view_designation(Request $request)
    {
        $filterarr = array();
        $rows=Designation::select('id','designation','active')
              ->orderBy('id','desc')
              ->paginate(25);
        return view('staff.manage_designation',compact('rows','filterarr'));
    }
    
    public function add_designation(Request $request)
    {
        
        $type =$request['type'];
        if($type == 'insert')
        {
            
        $designation = Designation::where('designation',$request['designation'])
              
            ->get();
        if(count($designation)>0)
        {

            $msg = 'already exist';
            return response::json(compact('msg'));
        }
        else
        {
            $designation= new Designation();
            $designation->designation = $request['designation'];
            $designation->save();
         

            $msg = 'success';
            return response::json(compact('msg'));
        }
       
        return redirect('manage_designation');
        }
        
        else if($type == 'update')
        {
            
            $designation = Designation::where('designation',$request['designation'])
            ->where('id','!=',$request['userid'])
            ->first();
        if(count($designation)>0)
        {

            $msg = 'exist';
            return response::json(compact('msg'));
        }
        else {
            Designation::where('id', $request['userid'])->update(
                ['designation' => $request['designation'],
                    'active' => $request['status']
                ]);

            $msg = 'done';
            return response::json(compact('msg'));
        }
        return redirect('manage_designation');
            
        }
    }
    public function designation_status(Request $request)
    {
        $designation = Designation::where('id','=',$request['ids'])
                      ->where('active','=','Y')
                      ->get();
        if(count($designation)>0)
        {
            Designation::where('id', $request['ids'])->update(
                [
                    'active' => 'N'
                ]);
        }
        else
        {
            Designation::where('id', $request['ids'])->update(
                [
                    'active' => 'Y'
                ]);
        }

    }
}
