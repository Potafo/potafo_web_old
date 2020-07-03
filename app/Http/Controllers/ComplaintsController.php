<?php

namespace App\Http\Controllers;
use App\CustomerList;
use App\CategoryOrderStatus;
use App\FollowUps;
use App\CategoryExtraCharges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use DB;
use Image;
use Session;
use Helpers\Datasource;
use Response;
use DateTime;
use DateTimeZone;


class ComplaintsController extends Controller
{
   public function complaints_view(Request $request)
    {
		 $staffid = Session::get('staffid');
		if(!$staffid){return redirect('');}
       $category = DB::SELECT('SELECT * FROM `complaints_cat` WHERE `cc_status`="Y" ORDER BY cc_id ');
        return view('complaints.complaints',compact('category'));
    }
    public function complaints_load_custmobile(Request $request) {
        $append ='';$i=1;
        $reasonlist = DB::SELECT("SELECT `id`,`name`,`lname` FROM `customer_list` WHERE `mobile_contact` LIKE '%".$request['mobile']."%' ");
        if(count($reasonlist)>0)
        {
            $name=$reasonlist[0]->id.":".$reasonlist[0]->name." ".$reasonlist[0]->lname;
            $append .= $name;
//        foreach($reasonlist as $list){
//            $name=$list->first_name." ".$list->last_name;
//            $append .= '<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="searchreason_'.$list->id.'" onclick=\'selectmobilenumber("'.$list->id.'","'.$list->mobile.'","'.$name.'")\'><a href="javascript:vojavasid(0);"><span>'.$list->mobile.'</span></a></div></br>';
//            $i++;
//        }
        }  else {
         $append="0";   
        }
        return $append;
    }
    public function category_complaints(Request $request)
    {
        $post = $request->all();
       $category = DB::SELECT('SELECT * FROM `complaints_cat` WHERE `cc_name`="'.trim(title_case($post['category'])).'"');
        if(count($category) >0)
        {
            $msg = 'Already exist';
            return response::json(compact('msg'));
        }
        else
        {
            DB::INSERT("INSERT INTO `complaints_cat`( `cc_name`) VALUES ('".trim(title_case($post['category']))."')");
           
            $msg = "success";
            $category = DB::SELECT('SELECT * FROM `complaints_cat` WHERE `cc_status`="Y" ORDER BY cc_id ');
            return response::json(['msg'=>$msg,'category' =>$category]);
        }
    }
    public function complaints_add(Request $request) 
            {
        //add_mobile cust_name upld_file cpl_date heading description custid
        try {
        //$post = $request->all();D:\wamp\tmp\php40DF.tmp
      $data2 = $request['upld_file'];
      $timeDate = date("jmYhis") . rand(991, 9999);
      
      
      
      $image_url='';
      $timezone = 'ASIA/KOLKATA';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d H:i:s');
      $cpl_date= isset($request['cpl_date'])?date("Y-m-d", strtotime(trim($request['cpl_date']))):'';
        if(isset($data2) && $data2 !='')
        {
            $extension2 = strtolower($data2->getClientOriginalExtension());
            $uploadfile = "complaints-".time(). '.' .$extension2;
            
            //$path1 = 'uploads/banner/web/' . $url2;
            $image_url = 'uploads/complaints/' . $uploadfile;
            move_uploaded_file($data2,$image_url);
//        $img1 = str_replace('data:image/'.$extension2.';base64,', '', $data2);
//        $img1 = str_replace(' ', '+', $img1);
//        $base_data2 = base64_decode($img1);
//        file_put_contents( base_path().'/'.$image_url, $base_data2);
            //Image::make($data2)->save(base_path() . '/uploads/complaints/' . $uploadfile);//->resize(700, 640)
           
            
         //$image_url=$path1;
        }
        else
        {
            $image_url = '';
        }
        $custmrid='';
        if($request['custid']==0)
        {
        $custmrid= null;
        }
        else {
          $custmrid  =$request['custid'];
        }
        DB::INSERT("INSERT INTO `complaints`(`ct_customer_mobile`, `ct_customer_name`, `ct_customer_id`, `ct_heading`,`ct_type_id`, `ct_descriptions`, `ct_images`,`ct_priority`,`ct_date_of_complaint`, `ct_entry_date`, `ct_attended_by`) 
        VALUES ('".$request['add_mobile']."','".$request['cust_name']."','".$custmrid."','".$request['heading']."','".$request['category']."','".trim($request['description'])."','".$image_url."','".$request['priority']."','".$cpl_date."','".$datetime."','".$request['staff_id']."')");        
        return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
//               / console.log($e->getMessage());
                 }
                   
    }
    public function filter_complaintslist(Request $request)
    {
        $search = '';
        $itemsarr = array();
        $i=0;
         try
        {
        $staffid = $request['staff_id'];
        $flt_status = $request['flt_status'];
        $flt_from = $request['flt_from'];
        $flt_to = $request['flt_to'];
        $flt_priority = $request['flt_priority'];
        $site_url = $request['site_url'];
        $flt_category = $request['flt_category'];
        $flt_id = $request['flt_id'];
///`ct_date_of_complaint` `ct_priority` `ct_status`
       if($flt_id!='')
        {
          if($search=='')
                {
                $search.=" c.ct_customer_mobile like '".$flt_id."%'";
                }  else {
                 $search.=" AND c.ct_customer_mobile like '".$flt_id."%'";    
                } 
        }  else {     
            if($flt_status!='')
            {
                if($search=='')
                {
                $search.=" c.ct_status = '".$flt_status."'";
                }  else {
                 $search.=" AND c.ct_status = '".$flt_status."'";   
                }
            }
            if($flt_category!='')
            {
                if($search=='')
                {
                $search.=" c.ct_type_id = '".$flt_category."'";
                }  else {
                 $search.=" AND c.ct_type_id = '".$flt_category."'";   
                }
            }
            if($flt_priority!='')
            {
                if($search=='')
                {
                $search.=" c.ct_priority = '".$flt_priority."'";
                }  else {
                 $search.=" AND c.ct_priority = '".$flt_priority."'";   
                }

            }
            if($flt_from!='' && $flt_to !=''){
                if($search=='')
                {
                $search.="  DATE(c.ct_date_of_complaint)  BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
                }  else {
                $search.=" and  DATE(c.ct_date_of_complaint)  BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
                }

            }
            else if($flt_from!='' && $flt_to =='')
            {
                if($search=='')
                {
                 $search.=" DATE(c.ct_date_of_complaint) = '".date('Y-m-d', strtotime(($flt_from)))."' ";
                }  else {
                 $search.=" and  DATE(c.ct_date_of_complaint) = '".date('Y-m-d', strtotime(($flt_from)))."' ";
                }

            }
            else if($flt_from=='' && $flt_to !='')
            {
                if($search=='')
                {
                 $search.=" DATE(c.ct_date_of_complaint)='".date('Y-m-d', strtotime(($flt_to)))."'";
                }  else {
                 $search.=" and  DATE(c.ct_date_of_complaint)='".date('Y-m-d', strtotime(($flt_to)))."'";
                }

            }
            else
            {
                if($search=='')
                {
                 $search.=" DATE(c.ct_date_of_complaint)=DATE(now()) ";
                }  else {
                  $search.=" and  DATE(c.ct_date_of_complaint)=DATE(now()) ";
                }

            }
       }
        $pointing = $request['current_count'];
        if($pointing=='')
        {
            $pointing=1;
        }
        $startlimit = ($pointing-1)*20;
        $endlimit = ($pointing)*20;
        $totaldetails = DB::SELECT("SELECT count(*) as totaldetails FROM `complaints` as c JOIN complaints_cat as cc WHERE c.ct_type_id=cc.cc_id AND $search");
        $details = array();
        $areaarr = array();
        $append='';
        $customer_totaldetails = DB::SELECT("SELECT count(*) as totaldetails FROM `complaints` as c JOIN complaints_cat as cc WHERE c.ct_type_id=cc.cc_id AND $search");
        $count = $customer_totaldetails[0]->totaldetails;
        $customer_res = round($customer_totaldetails[0]->totaldetails/20,0);
        $customer_mode = ($customer_totaldetails[0]->totaldetails)%(20);
        if($customer_mode!=0){$customer_res = $customer_res+1;}
        $total_cutomers=$customer_res;
        $rows = DB::SELECT("SELECT * FROM `complaints` as c JOIN complaints_cat as cc WHERE c.ct_type_id=cc.cc_id AND $search ORDER BY c.ct_entry_date DESC,c.ct_status ASC LIMIT $startlimit,20");
        $appends = '';
        //if($rows>0)
        //{
        foreach($rows as $data)
        {
            //`ct_id`, `ct_customer_mobile`, `ct_customer_name`, `ct_customer_id`, `ct_heading`, `ct_descriptions`, 
            //`ct_images`, `ct_status`, `ct_priority`, `ct_date_of_complaint`, `ct_entry_date`, `ct_attended_by`
            $ct_id = $data->ct_id;
            $ct_customer_mobile = $data->ct_customer_mobile;
            $ct_customer_name = $data->ct_customer_name;
            $ct_heading = $data->ct_heading;
            $ct_descriptions = $data->ct_descriptions;
            $ct_images = $data->ct_images;
            $ct_status = $data->ct_status;
            $ct_priority =  $data->ct_priority;
            $ct_date_of_complaint =  $data->ct_date_of_complaint;
            $ct_entry_date =  $data->ct_entry_date;
            $ct_attended_by =  $data->ct_attended_by;
            $cc_name =  $data->cc_name;
            
            
            $itemsarr[$i] = ['ct_id'=>$ct_id,'ct_customer_mobile'=>$ct_customer_mobile,'ct_heading'=>$ct_heading,'ct_images'=>$ct_images,'ct_customer_name'=>$ct_customer_name,'ct_status'=>$ct_status,'ct_priority'=>$ct_priority,'ct_date_of_complaint'=>$ct_date_of_complaint,'ct_entry_date'=>$ct_entry_date,'ct_attended_by'=>$ct_attended_by,'cc_name'=>$cc_name];
            $i++;
        }
        $m=$startlimit;
        $append .= '<table id="example1"  class="table table-striped table-bordered">';
        $append .='<thead>';
        $append .='<tr>';
        $append .='<th style="min-width:10px">Cmpt ID</th>';
        $append .='<th style="min-width:10px">Mobile</th>';
        $append .='<th style="min-width:20px">Name</th>';
        $append .='<th style="min-width:8px">Status </th>';
        $append .='<th style="min-width:10px">Priority </th>';
        $append .='<th style="min-width:10px">Date</th>';
        $append .='<th style="min-width:10px">Category</th>';
        $append .='<th style="min-width:20px">Title</th>';
        $append .='<th style="min-width:10px">File</th>';       
         $append .='<th style="min-width:5px">Action</th>';  
        
        
        $append .='</tr>';
        $append .='</thead>';
        $append .='<tbody >';
        if(isset($itemsarr))
        {
        foreach($itemsarr as $i)
        {
            // //`ct_id`, `ct_customer_mobile`, `ct_customer_name`, `ct_customer_id`, `ct_heading`, `ct_descriptions`, 
            //`ct_images`, `ct_status`, `ct_priority`, `ct_date_of_complaint`, `ct_entry_date`, `ct_attended_by`
            $ct_id = $i['ct_id'];
            $ct_customer_mobile = $i['ct_customer_mobile'];
            $ct_customer_name = $i['ct_customer_name'];
            $ct_status = $i['ct_status'];
            $ct_date_of_complaint = $i['ct_date_of_complaint'];
            $ct_images =$site_url.$i['ct_images'];
            $ct_heading = $i['ct_heading'];
            $ct_priority = $i['ct_priority'];
           $cc_name = $i['cc_name'];
            $m++;$imgload='';
            if($i['ct_images']!="")
            {
            $imgload='<a href="'.$ct_images.'" target="_blank" >View</a>';
            }  else {
             $imgload=' No File ';   
            }
            $color='';
            if($ct_priority=="Critical")
            {
              $color= " color: #d81414 !important;";  
            }  else if($ct_priority=="Medium"){
              $color= "  color: #f3d604 !important;";    
            }else if($ct_priority=="Low"){
              $color= "  color: #3214d8 !important;";    
            }
            
            $append .= '<td style="min-width:10px;">'.$ct_id.'</td>';
            $append .= '<td style="min-width:10px;">'.$ct_customer_mobile.'</td>';
            $append .= '<td style="min-width:20px;">'.$ct_customer_name.'</td>';
            $append .= '<td style="min-width:8px;">'.$ct_status.'</td>';
            $append .= '<td style="min-width:10px;font-weight:bold; '.$color.'">'.$ct_priority.'</td>';
            $append .= '<td style="min-width:10px;">'.date('d-m-Y',strtotime($ct_date_of_complaint)).'</td>';
            $append .= '<td style="min-width:10px;">'.$cc_name.'</td>';
            $append .= '<td style="min-width:20px;"><div class="tooltips">'.substr($ct_heading, 0, 20);   
            if(strlen($ct_heading) >20)
                {
                    $append .='...<span class="tooltiptext">'.$ct_heading.'</span>';
                }
                $append  .= '</div></td>';
            $append .= '<td style="min-width:10px;">'.$imgload.'</td>';                    
            $append .= '<td style="min-width:5px;"><a class="btn button_table vie_odr_btn" onclick="return view_details(\'' . $ct_id . '\');"><i class="fa fa-cog"></i></a></td>';
            
            
            
            

            $append .= '</tr>';
        }
        }else
        {
            $append .= '<div >No details </div>';
        }
        $append .='</tbody>';
        $append .='</table>';//return $itemsarr;
        
       // }  else {
        //    $append .= '<div >No details </div>';
            //return response::json(['filter_data'=>$append,'data_count'=>0,'count' =>0,'searchcount' =>0]);
        //}
       // return "SELECT * FROM `complaints` WHERE $search ORDER BY ct_date_of_complaint DESC LIMIT $startlimit,20";
        return response::json(['filter_data'=>$append,'data_count'=>$total_cutomers,'count' =>$totaldetails[0]->totaldetails,'searchcount' =>$customer_totaldetails[0]->totaldetails]);
 } catch (\Exception $e) {
            return $e->getMessage();
//               / console.log($e->getMessage());
                 }
    }
    public function complaint_details($id)
    {
        $staffid = Session::get('staffid');
        try
        {
 //`ct_id`, `ct_customer_mobile`, `ct_customer_name`, `ct_customer_id`, `ct_heading`, `ct_type_id`, `ct_descriptions`, `ct_images`, `ct_status`, `ct_priority`, `ct_date_of_complaint`, `ct_entry_date`, `ct_attended_by`
        $complaints = DB::SELECT("SELECT * FROM `complaints` as c JOIN complaints_cat as cc WHERE c.ct_type_id=cc.cc_id  AND `ct_id`=$id "); 
        $followups = DB::SELECT("SELECT * FROM `complaints_followups` WHERE `cf_id`=$id ");
        $changpermision = DB::SELECT("SELECT `complaint_status_change` FROM `internal_staffs` WHERE `id`=$staffid ");
        
        return view('complaints.complaint_details',compact('complaints','followups','changpermision'));
        } catch (\Exception $e) {
            echo $e->getMessage();
            return $e->getMessage();
        
        }
    }
 public function submitcomment(Request $request)
    {
           $timezone = 'ASIA/KOLKATA';
           $date = new DateTime('now', new DateTimeZone($timezone));
           $datetime = $date->format('Y-m-d H:i:s');
           //$post = $request->all();
            DB::INSERT("INSERT INTO `complaints_followups`(`cf_id`, `cf_datetime`, `cf_status`, `cf_comments`, `cf_notify_customer`) 
        VALUES ('".$request['compid']."','".$datetime."','".$request['status']."','".$request['comment']."','".$request['checkvalue']."')");        
        
           
           DB::SELECT('update complaints set ct_status="'.$request['status'].'" where ct_id="'.$request['compid'].'"');
           $append     = "";
           $FollowUpsdetail =DB::SELECT("Select * from complaints_followups where cf_id='".$request['compid']."'");
           if(count($FollowUpsdetail)>0) {
           foreach($FollowUpsdetail as $item=>$value) {
               
               $append .= '<tr>';
               $append .= '<td>'.date('d-m-Y H:i:s',strtotime($value->cf_datetime)).'</td>';
               $append .= '<td>'.$value->cf_comments.'</td>';
               $append .= '<td>'.$value->cf_status.'</td>';
               $append .= '<td>'.$value->cf_notify_customer.'</td>';
               $append .= '<td><a onclick="return compldel('.$value->cf_id.','.$value->cf_slno.')" class="btn button_table clear_edit" >
                                        <i class="fa fa-trash-o"></i>
                                    </a></td>';
               $append .= '</tr>';
           }
        }
        return $append;
    }
    public function followup_delete($id,$slno)
    {
        
         DB::select('delete from complaints_followups where cf_id="'.$id.'" and cf_slno="'.$slno.'"');
       // $banners = $this->cat_banner_list();
        $msg = 'deleted';
        return response::json(['msg' => $msg]);
    } 
}
