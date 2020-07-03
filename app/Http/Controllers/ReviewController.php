<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Response;
use App\Review;
use Session;

class ReviewController extends Controller
{
    public function menu_review($id)
    {
        $resid = $id;
        $restaurant = DB::SELECT('SELECT name_tagline->>"$.name" as name,id FROM `restaurant_master` WHERE `status` = "Y"' );
        $restaurant_name = DB::SELECT('SELECT name_tagline->>"$.name" as name FROM `restaurant_master` WHERE `id` = "'.$resid.'"' );
        $details = DB::SELECT("SELECT order_number as id,rest_id,rest_details->>'$.name' as restaurant,customer_details->>'$.name' as name,review_star as star,review_details->>'$.review' as review,review_details->>'$.status' as status,str_to_date(review_details->>'$.date', '%d-%m-%y') as entry_date,order_number,order_date FROM order_master WHERE rest_id ='".$resid."' ORDER BY review_details->>'$.date' desc");
        return view('review.review',compact('details','resid','details','restaurant','restaurant_name'));
    }
    
    //Filtering of Review

    public function filter_review(Request $request)
    {
        $search = '';
        $flt_from = $request['flt_from'];
        $resid = $request['flt_restname'];
        $flt_to = $request['flt_to'];
        $flt_name = $request['flt_name'];

        if((isset($flt_from) && $flt_from != '') && (isset($flt_to) && $flt_to != ''))
        {
            if($flt_from == $flt_to)
            {
                if ($search == "")
                {
                    $search .= "  review_details->>'$.date'  LIKE '" . date('Y-m-d', strtotime(($flt_from))) . "%'";
                }
                else
                {
                    $search .= " and  review_details->>'$.date'  LIKE'" . date('Y-m-d', strtotime(($flt_from))) . "%'";
                }
            }
            else {
                if ($search == "") {
                    $search .= "  str_to_date(review_details->>'$.date', '%Y-%m-%d')   BETWEEN  '".date('Y-m-d', strtotime(($flt_from)))."'  AND '".date('Y-m-d', strtotime(($flt_to)))."'";
                } else {
                    $search .= " and  str_to_date(review_details->>'$.date', '%Y-%m-%d')   BETWEEN  '".date('Y-m-d', strtotime(($flt_from)))."'  AND '".date('Y-m-d', strtotime(($flt_to)))."'";
                }
            }
        }

        if(isset($flt_name) && $flt_name != '')
        {
            if($search == "")
            {
                $search.="  LOWER(customer_details->>'$.name')   LIKE '%".strtolower($flt_name)."%'";
            }
            else
            {
                $search.=" and  LOWER(customer_details->>'$.name')   LIKE '%".strtolower($flt_name)."%'";
            }
        }

        if(isset($resid) && $resid != '')
        {
            if($search == "")
            {
                $search.="  rest_id   = '".$resid."'";
            }
            else
            {
                $search.=" and  rest_id   = '".$resid."'";
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
        $pointing = $request['current_count'];
        if($pointing=='')
        {
            $pointing=1;
        }
        $startlimit = ($pointing-1)*15;
        $endlimit = ($pointing)*15;
        $totaldetails = DB::SELECT("SELECT count(*) as totalreviews FROM order_master  where  rest_id !=''  ORDER BY review_details->>'$.date' desc");
        $details = array();
        $append='';
        $details = DB::SELECT("SELECT count(*) as totalreviews FROM order_master  ".$search."  rest_id !=''  ORDER BY review_details->>'$.date' desc");
        $count = $details[0]->totalreviews;
        $review_res = round($details[0]->totalreviews/15,0);
        $review_mode = ($details[0]->totalreviews)%(15);
      /*  if($review_mode!=0)
        {
            $review_res = $review_res+1;
        }*/
        $total_reviews=$review_res;
        //return "SELECT order_number as id,rest_id,rest_details->>'$.name' as restaurant,customer_details->>'$.name' as name,review_star as star,review_details->>'$.review' as review,review_details->>'$.status' as status,str_to_date(review_details->>'$.date', '%d %m %y') as entry_date,order_number,order_date FROM order_master  ".$search."  rest_id !=''  ORDER BY review_details->>'$.date' desc LIMIT $startlimit,15";
        $reviewdetails = DB::SELECT("SELECT order_number as id,rest_id,rest_details->>'$.name' as restaurant,customer_details->>'$.name' as name,review_star as star,review_details->>'$.review' as review,review_details->>'$.status' as status,str_to_date(review_details->>'$.date', '%Y-%m-%d') as entry_date,order_number,order_date FROM order_master  ".$search."  rest_id !=''  ORDER BY review_details->>'$.date' desc LIMIT $startlimit,15");
        $m=$startlimit;
        $append .= '<table class="table table-hover mails m-0 table table-actions-bar" id="example1">';
        $append .='<thead>';
        $append .='<tr>';
        $append .='<th style="min-width:10%;">Status</th>';
        $append .='<th style="min-width:13%;">Entry Dt.</th>';
        $append .='<th style="min-width:10%;">Ord. No</th>';
        $append .='<th style="min-width:10%;">Ord. Date</th>';
        $append .='<th style="min-width:10%;">Restaurant Name</th>';
        $append .='<th style="min-width:10%;">Name</th>';
        $append .='<th style="min-width:10%;">Rating</th>';
        $append .='<th style="min-width:40%;">Review</th>';
        $append .='</tr>';
        $append .='</thead>';
        $append .='<tbody >';
        foreach($reviewdetails as $i)
        {
            $id = $i->id;
            $m++;
            if( $i->status == "Y") {$status = 'checked'; }else{$status= "";}
            $append .= '<td style="min-width:10%;"> <div class="status_chck'.$i->id.'">
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch'.$i->id.'" '.$status.' >
                                                <label class="onoffswitch-label" for="myonoffswitch'.$i->id.'">
                                                    <span class="onoffswitch-inner" onclick="return  statuschange(\''.$i->id.'\',\''.$i->status.'\')"></span>
                                                    <span class="onoffswitch-switch" onclick="return  statuschange(\''.$i->id.'\',\''.$i->status.'\')"></span>
                                                </label>
                                            </div>
            </div></td>';
            $append .= '<td style="min-width:13%;">'.date('d-m-y', strtotime(($i->entry_date))).'</td>';
            $append .= '<td style="min-width:10%;">'.$i->id.'</td>';
            $append .= '<td style="min-width:10%;">'.date('d-m-y H:i:s', strtotime(($i->order_date))).'</td>';
            $append .= '<td style="min-width:10%;"><strong>'.title_case($i->restaurant).'</strong></td>';
            $append .= '<td style="min-width:10%;"><strong>'.title_case($i->name).'</strong></td>';
            $append .= '<td style="min-width:10%;">'. $i->star.'</td>';
            $append .= '<td style="min-width:40%;">'. $i->review.'</td>';
            $append .= '</tr>';
        }
        $append .='</tbody>';
        $append .='</table>';
        return response::json(['filter_data'=>$append,'data_count'=>$total_reviews,'count' =>$totaldetails[0]->totalreviews,'searchcount' =>$details[0]->totalreviews]);
    }

    public function filter_review_old(Request $request)
    {
        $search = '';
        $flt_from = $request['flt_from'];
        $resid = $request['resid'];
        $flt_to = $request['flt_to'];
        $flt_name = $request['flt_name'];
       
        if((isset($flt_from) && $flt_from != '') && (isset($flt_to) && $flt_to != ''))
        {
            if($search == "")
            {
                $search.="  entry_date BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
            }
            else
            {
                $search.=" and  entry_date  BETWEEN '".date('Y-m-d', strtotime(($flt_from)))."' AND '".date('Y-m-d', strtotime(($flt_to)))."'";
            }
        }
       
        if(isset($flt_name) && $flt_name != '')
        {
            if($search == "")
            {
                  $search.="  LOWER(entry_by->>'$.name')   LIKE '%".strtolower($flt_name)."%'";
            }
            else
            {
                 $search.=" and  LOWER(entry_by->>'$.name')   LIKE '%".strtolower($flt_name)."%'";
            }
        }

        if(isset($flt_restname) && $flt_restname != '')
        {
            if($search == "")
            {
                $search.="  LOWER(rest_id)   LIKE '%".strtolower($flt_restname)."%'";
            }
            else
            {
                $search.=" and  LOWER(rest_id)   LIKE '%".strtolower($flt_restname)."%'";
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
        $details = DB::SELECT('SELECT status,entry_by->>"$.name" as name,entry_by->>"$.ids" as ids,id,rest_id,entry_date,review->>"$.star" as star,review->>"$.text" as review FROM `restaurant_reviews` '.$search.' `restaurant_reviews`.`id` != " " and `rest_id` ="'.$resid.'" ORDER BY id');
        return $details;
    }
    
    public function review_status(Request $request)
    {
        $restdetails = DB::SELECT("SELECT rest_id from order_master where order_number = '".$request['ids']."'");
        $resid = $restdetails[0]->rest_id;
        if($request['status'] =='N')
        {
            $status = 'Y';
        }
        else
        {
           $status = 'N'; 
        }
        $update = DB::UPDATE("UPDATE order_master SET review_details=json_set(review_details,'$.status','".$status."') WHERE order_number='".$request['ids']."' ");
        $detail = DB::SELECT("SELECT count(order_number) AS count FROM order_master WHERE rest_id ='".$resid."' and review_details->>'$.status' = 'Y'");
        if(count($detail[0]->count)>0)
        {
            DB::SELECT("update `restaurant_master` set `star_rating` = JSON_SET(`star_rating`,'$.count','" . $detail[0]->count . "') where id='" . $resid . "'");
        }
      $takeavg_rating =  DB::SELECT("SELECT CAST(AVG(`review_star`) AS UNSIGNED) AS avg_rating FROM order_master where `rest_id` = $resid and `current_status` = 'D' AND review_star>0");
      $avg_rating = $takeavg_rating[0]->avg_rating;
      if($avg_rating<3){
          $avg_rating=3;
      }
      DB::UPDATE("UPDATE restaurant_master SET star_rating=json_set(star_rating,'$.value',$avg_rating) WHERE id=$resid");
      return $update;
    }

    public function manage_review()
    {
		  $staffid = Session::get('staffid');
          if(!$staffid){return redirect('');}
        $restaurant = DB::SELECT('SELECT name_tagline->>"$.name" as name,id FROM `restaurant_master` WHERE `status` = "Y" ORDER BY name_tagline->>"$.name" asc' );
        $details = DB::SELECT("SELECT order_number as id,rest_id,rest_details->>'$.name' as restaurant,customer_details->>'$.name' as name,review_star as star,review_details->>'$.review' as review,review_details->>'$.status' as status,review_details->>'$.date' as entry_date FROM order_master WHERE rest_id !='' and  review_details->>'$.date'  = '".date('Y-m-d')."' ORDER BY review_details->>'$.date' desc");
        return view('review.review',compact('details','restaurant'));
    }

}
