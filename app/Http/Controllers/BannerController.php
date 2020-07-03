<?php

namespace App\Http\Controllers;

use Helpers\Commonsource;
use Illuminate\Http\Request;
use Image;
use App\BannerList;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Session;

class BannerController extends Controller
{
    //view banner page
    public function banners_view(Request $request)
    {
		$staffid = Session::get('staffid');
		if(!$staffid){return redirect('');}
		
        $banner = $this->banner_list();
        return view('banners.banner_index',compact('banner'));
    }

    //view banner add page
    public function banners_add(Request $request)
    {
		$staffid = Session::get('staffid');
		if(!$staffid){return redirect('');}
        return view('banners.banner_add');
    }

    //banner submit
    public function banner_submit(Request $request)
    {
      /*  //save app banner image
        $data1 = $request['img1'];
        $ext1 = explode(';',explode('/',$data1)[1]);
        $extension1 = $ext1[0];
        $url1 = "banner-".time().".".$extension1;
        $path1 ='uploads/banner/app/' . $url1;
        $img1 = str_replace('data:image/'.$extension1.';base64,', '', $data1);
        $img1 = str_replace(' ', '+', $img1);
        $base_data1 = base64_decode($img1);
        file_put_contents( base_path().'/'.$path1, $base_data1);*/

        //save web banner image
        $data2 = $request['img1'];
        $range = $request['range'];
        $geolocation = $request['lat'].','.$request['long'];
        $google_location= $request['geo_location'];
        $ext2 = explode(';',explode('/',$data2)[1]);
        $extension2 = $ext2[0];
        $url2 = "banner-".time().".".$extension2;
        $path1 = 'uploads/banner/web/' . $url2;
        $img1 = str_replace('data:image/'.$extension2.';base64,', '', $data2);
        $img1 = str_replace(' ', '+', $img1);
        $base_data2 = base64_decode($img1);
        file_put_contents( base_path().'/'.$path1, $base_data2);
        $status = 'success';
        $response = array('status' => 'success');

        $bannerlist = new BannerList();
        $bannerlist->app_banners = 'uploads/banner/app/dummy.jpg';
        $bannerlist->web_banners = $path1;
        $bannerlist->geo_cordinates = $geolocation;
        $bannerlist->google_location = $google_location;
        $bannerlist->delivery_range = $range;
        $bannerlist->entry_date = date('Y-m-d');
        $bannerlist->save();
        return redirect('manage/banners')->with('status');
    }

    public function banner_appsubmit(Request $request)
    {
      //save app banner image
      $data1 = $request['img1'];
      $ext1 = explode(';',explode('/',$data1)[1]);
      $extension1 = $ext1[0];
      $url1 = "banner-".time().".".$extension1;
      $path1 ='uploads/banner/app/' . $url1;
      $img1 = str_replace('data:image/'.$extension1.';base64,', '', $data1);
      $img1 = str_replace(' ', '+', $img1);
      $base_data1 = base64_decode($img1);
      file_put_contents( base_path().'/'.$path1, $base_data1);
        BannerList::where('id',$request['id'])->update(['app_banners' => $path1]);
        /*$bannerlist = new BannerList();
        $bannerlist->id = $request['id'];
        $bannerlist->app_banners = $path1;
        $bannerlist->entry_date = date('Y-m-d');
        $bannerlist->save();*/

        return redirect('manage/banners')->with('status');
    }

    //banner list
    public function banner_list()
    {
        $banner = BannerList::where('id','!=','')->select('app_banners','web_banners','id','order_no')->get();
        return $banner;
    }

    //banner image delete
    public function banner_delete($id)
    {
        DB::select('delete from banner_list where id="'.$id.'"');
        $banners = $this->banner_list();
        $msg = 'deleted';
        return response::json(['msg' => $msg,'banners' => $banners]);
    }

    //banner order edit
    public function banner_order($id,$val)
    {
        BannerList::where('id',trim($id))->update(['order_no' =>$val]);
        $banners = $this->banner_list();
        $msg = 'editted';
        return response::json(['msg' => $msg,'banners' => $banners]);
    }
     public function bannerapplist(Request $request)
    {
        $bannerapp = DB::SELECT("SELECT `order_no`,`app_banners` from banner_list WHERE `banner_list`.`order_no` != ' ' ORDER BY order_no");
        $msg = 'Exist';
        return response::json(['msg' => $msg,'bannerapp' => $bannerapp]);
    }
    public function bannerapplist_new(Request $request)
    {
//       $bannerarr = array();
//        $location = $request['location'];
//        if($location=='' || $location == 'NULL' || $location=='null') {
//            $location = 'lat_11.2528194$75.7710131';
//        }
//        $locate = explode('_', $location);
//        $cordinates = explode('$',$locate[1]);
//        $lat2 = $cordinates[0];
//        $long2 = $cordinates[1];
//        $bannerapp = DB::SELECT("SELECT `order_no`,`app_banners`,`google_location`,`geo_cordinates`,`delivery_range` from banner_list WHERE `banner_list`.`order_no` != ' ' ORDER BY order_no");
//        if(count($bannerapp)>0)
//        {
//             foreach($bannerapp as $key=>$item)
//             {
//                 $bannercordinate = explode(',',$item->geo_cordinates);
//                 $radius = Commonsource::distance_calculate($lat2,$bannercordinate[0],$long2,$bannercordinate[1]);
//                 if($radius <= $item->delivery_range)
//                 {
//                      $bannerarr['order_no']     = $item->order_no;
//                      $bannerarr['app_banners']  = $item->app_banners;
//                      $banners[]                 = $bannerarr;
//                 }
//             }
//        }
//         if(isset($banners))
//         {
//             $count = count($banners);
//             if($count == 1)
//             {
//                 $bannerarr['order_no']     = 2;
//                 $bannerarr['app_banners']  = 'uploads/banner/app/dummy1.jpg';
//                 $banners[]                 = $bannerarr;
//             }
//             $bannerlist = $banners;
//         }
//        else
//        {
//            for($i = 1;$i <= 2;$i++)
//            {
//                $bannerarr['order_no']     = $i;
//                $bannerarr['app_banners']  = 'uploads/banner/app/dummy'.$i.'.jpg';
//                $banners[]                 = $bannerarr;
//            }
//            $bannerlist = $banners;
//        }
//        $msg = 'Exist';
//        return response::json(['msg' => $msg,'bannerapp' => $bannerlist]);
		
		 $location = $request['location'];
        $bannerapp = DB::SELECT("SELECT `order_no`,`app_banners` from banner_list WHERE `banner_list`.`order_no` != ' ' ORDER BY order_no");
        $msg = 'Exist';
        return response::json(['msg' => $msg,'bannerapp' => $bannerapp]);
		
    }
     public function bannerweblist(Request $request)
    {
        $bannerweb = DB::SELECT("SELECT `order_no`,`web_banners` from banner_list WHERE `banner_list`.`order_no` != ' ' ORDER BY order_no");
        $msg = 'Exist';
        return response::json(['msg' => $msg,'bannersweb' => $bannerweb]);
    }
    public function bannerweblist_new(Request $request)
    {
        $bannerarr = array();
        $location = $request['location'];
        $cordinates = explode('$', $location);
        $lat2 = $cordinates[0];
        $long2 = $cordinates[1];
        $bannerweb = DB::SELECT("SELECT `order_no`,`web_banners`,`google_location`,`geo_cordinates`,`delivery_range` from banner_list WHERE `banner_list`.`order_no` != ' ' ORDER BY order_no");
        if(count($bannerweb)>0)
        {
            foreach($bannerweb as $key=>$item)
            {
                $bannercordinate = explode(',',$item->geo_cordinates);
                $radius = Commonsource::distance_calculate($lat2,$bannercordinate[0],$long2,$bannercordinate[1]);
                if($radius <= $item->delivery_range)
                {
                    $bannerarr['order_no']     = $item->order_no;
                    $bannerarr['web_banners']  = $item->web_banners;
                    $banners[]                 = $bannerarr;
                }
            }
        }
        if(isset($banners))
        {
            $count = count($banners);
            if($count == 1)
            {
                $bannerarr['order_no']     = 2;
                $bannerarr['web_banners']  = 'uploads/banner/web/dummy1.jpg';
                $banners[]                 = $bannerarr;
            }
            $bannerlist = $banners;
        }
        else
        {
            for($i = 1;$i <= 2;$i++)
            {
                $bannerarr['order_no']     = $i;
                $bannerarr['web_banners']  = 'uploads/banner/web/dummy'.$i.'.jpg';
                $banners[]                 = $bannerarr;
            }
            $bannerlist = $banners;
        }
        $msg = 'Exist';
        return response::json(['msg' => $msg,'bannersweb' => $bannerlist]);
    }

    public  function banners_appadd($id)
    {
        return view('banners.banner_appadd',compact('id'));
    }
	
	public function banner_cat(Request $request)
    {

		 $location = $request['location'];
        $bannerapp = DB::SELECT("SELECT `app_banner` from cat_banner WHERE `display_order` != ' ' ORDER BY display_order");
        $msg = 'Exist';
        return response::json(['msg' => $msg,'bannerapp' => $bannerapp]);
		
    }

    public function cust_app_topbar_msg(Request $request)
    {

        $userid = $request['userid'];
        $location = $request['location'];

        $topbar_msg = DB::SELECT("SELECT IFNULL(cust_app_topbar_msg,'') as cust_app_topbar_msg from general_settings");
         return response::json(['msg' => $topbar_msg[0]->cust_app_topbar_msg,'potafo_mart' => 'Y','catering' =>'N']);
		
    }

	
	
	
}
