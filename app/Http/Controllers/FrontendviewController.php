<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Response;
use Mail;
use Helpers\Datasource;
use Helpers\Sharesource;
use App\GeneralSettingSmtp;
use Illuminate\Support\Facades\Input;
use Image;

class FrontendviewController extends Controller
{
    public function __construct()
    {
        $settings = GeneralSettingSmtp::first(); 
        $this->toaddress = $settings['to_email'];
        $this->domain_name = $settings['domain_name'];
        $host = $settings['host']; 
        $port = $settings['port'];
        $username = $settings['user_name'];
        $password = $settings['password'];
        $encryption = $settings['encryption'];
        $fromaddress = $settings['from_email'];
        $fromname = $settings['from_name'];
        $fromname = $settings['from_name'];

        \Config::set('mail.host', trim($host));
        \Config::set('mail.port', trim($port));
        \Config::set('mail.username', trim($username));
        \Config::set('mail.password', trim($password));
        \Config::set('mail.encryption', trim($encryption));
        \Config::set('mail.from.address', trim($fromaddress));
        \Config::set('mail.from.name', trim($fromname));
    } 
    public function take_categories(Request $request) {
        $active_main_cat = DB::SELECT("SELECT id,cat_name,image_details->>'$.image_link' as image_link,image_details->>'$.image_alt' as image_alt FROM vendor_categories WHERE active='Y' AND parent_id=0");
        return $active_main_cat;
    }
    public function take_pdt_under_coltns(Request $request) {
        $active_coltns = DB::SELECT("SELECT id,title,page_view FROM collections WHERE active='Y' ORDER BY display_order ");
        $c=0;
        $collection = array();
        $pdt_details = array();
        $crnt_crtid    = $request['crnt_crtid'];
         $convertion_rate = 1;
         $currency_code = 'AED';
        if($crnt_crtid!='') {
            $convrtn_rate_info = DB::SELECT("SELECT convertion_rate,currency_short_code FROM conversion_rate WHERE id='$crnt_crtid' ");
            $convertion_rate = $convrtn_rate_info[0]->convertion_rate;
            $currency_code = $convrtn_rate_info[0]->currency_short_code;
        }
        foreach($active_coltns as $coltns){
            $coltn_id = $coltns->id;
            $coltn_title = $coltns->title;
            $page_view = $coltns->page_view;
            $products = DB::SELECT("SELECT a.id,a.name,a.review_details,a.vendor_id,a.category,a.brand_details->>'$.brand_name' as brand_name,a.pdt_details->>'$.code' as code,a.order_details->>'$.packing_qty' as packing_qty,a.order_details->>'$.unit' as unit,a.price->>'$.selling_price' as selling_price,a.inventory_details->>'$.current_stock_count' as current_stock_count FROM vendor_product a   WHERE json_contains(a.collections,'[$coltn_id]') AND a.active='Y' AND a.approved='Approved'  ");
            $p=0;
            $pdt_details = array();
            foreach($products as $pdt){
                    $price = $pdt->selling_price;
                    $current_stock_count = $pdt->current_stock_count;
                    if($current_stock_count!='' && $current_stock_count!=0 && $price!=0){
                 $ofr_comsn =    Sharesource::ofr_comsn_price_calculation($price,json_decode($pdt->category),$pdt->vendor_id,$pdt->id,1);
                 $image_url = 'uploads/dummy/no-image.jpg';
                 $featch_image_url = DB::SELECT("SELECT image_url FROM vendor_pdt_image WHERE pdt_id=$pdt->id AND active='Y' AND main_img='Y' LIMIT 1");
                 if(count($featch_image_url)!=0) {
                     $image_url = $featch_image_url[0]->image_url;
                 }
                     $converted_org_cmsn = $convertion_rate*$ofr_comsn['comsn_rate'];
                     $converted_sel_price = $convertion_rate*$ofr_comsn['selling_price'];
                $pdt_details[$p] = [
                        'pdt_id'=>$pdt->id,
                        'vendor_id'=>$pdt->vendor_id,
                        'pdt_name'=>$pdt->name,
                        'pdt_brand'=>$pdt->brand_name,
                        'pdt_code'=>$pdt->code,
                        'pdt_pack_qty'=>$pdt->packing_qty,
                        'pdt_unit'=>$pdt->unit,
                        'rating'=>$pdt->review_details,
                        'pdt_org_price'=>$price,
                        'value_type'=>$ofr_comsn['value_type'],
                        'value'=>$ofr_comsn['value'],
                        'org_price_comsn'=>$ofr_comsn['comsn_rate'],
                        'pdt_selling_price'=>$ofr_comsn['selling_price'],
                        'converted_org_cmsn'=>$converted_org_cmsn,
                        'converted_sel_price'=>$converted_sel_price,
                        'currency_code'=>$currency_code,
                        'pdt_offer_price'=>$ofr_comsn['offer_price'],
                        'offerid'=>$ofr_comsn['offerid'],
                        'comsn_val'=>$ofr_comsn['com_val'],
                        'image_url'=>$image_url
                ];
                $p++;
                    }
            }
            $collection[$c]=[
                'coltn_id'=>$coltn_id,
                'coltn_title'=>$coltn_title,
                'page_view'=>$page_view,
                'pdt_details'=>$pdt_details
            ];
            $c++;
        }
      return $collection;  
    }
  
    public function take_banner_image(Request $request) {
        
        $banner1 = DB::SELECT("SELECT banner_url,alt_tag FROM banner1 WHERE active='Y' ORDER BY display_order ASC ");
            if(count($banner1)==0) {
                 $banner1 = DB::SELECT("SELECT 'uploads/dummy/banner_1.jpg' as banner_url ,'dummbanner1' as alt_tag ");
            }
        //$banner2 = DB::SELECT("SELECT banner_url,alt_tag FROM banner2 WHERE active='Y' LIMIT 1 ");
            $banner2 = DB::SELECT("SELECT image as banner_url,imag_alt as alt_tag FROM general_offers WHERE image IS NOT NULL AND image!=''  AND active='Y'");
            if(count($banner2)==0) {
                 $banner2 = DB::SELECT("SELECT 'uploads/dummy/banner_offer_1.jpg' as banner_url ,'dummbanner2' as alt_tag ");
            }
        $banner3 = DB::SELECT("SELECT banner_url,alt_tag FROM banner3 WHERE active='Y' LIMIT 1 ");
            if(count($banner3)==0) {
                 $banner3 = DB::SELECT("SELECT 'uploads/dummy/banner_offer_2.jpg' as banner_url ,'dummbanner3' as alt_tag ");
            }
        return ['banner1'=>$banner1,'banner2'=>$banner2,'banner3'=>$banner3];
    }
    public function take_all_product(Request $request) {
        $minValue    = $request['minValue'];
        $maxValue    = $request['maxValue'];
        $price_list    = $request['price_list'];
        $sele_brands = $request['sele_brands'];
        $sele_units  = $request['sele_units'];
        $order_by    = $request['price_order_by'];
        $category_id    = $request['category_id'];
        $crnt_crtid    = $request['crnt_crtid'];
        $curnt_page    = $request['curnt_page'];
        $convertion_rate = 1;
        $currency_code = 'AED';
        if($crnt_crtid!='') {
            $convrtn_rate_info = DB::SELECT("SELECT convertion_rate,currency_short_code FROM conversion_rate WHERE id='$crnt_crtid' ");
            if(count($convrtn_rate_info)!=0) {
            $convertion_rate = $convrtn_rate_info[0]->convertion_rate;
            $currency_code = $convrtn_rate_info[0]->currency_short_code;
            }
        }
           
        $search_case = '';
        if($minValue!='' && $maxValue!='') {
             $search_case = "AND price->>'$.selling_price' BETWEEN $minValue and $maxValue ";
        }
//        if($price_list!='') {
//             //$search_case = "AND price->>'$.selling_price' BETWEEN $price_list ";
//             $explode_prcie =  explode(' and ', $price_list);
//             $minValue = $explode_prcie[0];
//             $maxValue = $explode_prcie[1];
//        }
        if($sele_brands!=''){
            $sele_brands = str_replace(',', '","', $sele_brands);
            $sele_brands = '"'.$sele_brands.'"';
            $search_case .= "AND brand_details->>'$.brand_name' IN ($sele_brands) ";
        }
        if($sele_units!=''){
            $sele_units       = str_replace(',', '","', $sele_units);
            $formatted_units  = '"'.$sele_units.'"';
            $search_case .= "AND order_details->>'$.unit' IN ($formatted_units) ";
        }
        if($category_id !='') {
            //$search_case .= "AND json_contains(category,'[$category_id]')";
             $checked_cat_explode = explode(',', $category_id);
             $search_case .= "AND (";
                 foreach($checked_cat_explode as $sel_cat) {
                     $search_case .= " json_contains(category,'[$sel_cat]') OR";
                 }
            //cat name heirachi 
                 
                 $search_case =  substr($search_case, 0,-2).")";
        }
        if($order_by !='') {
            $search_case .= "ORDER BY CAST(price->>'$.selling_price' as decimal(15,2)) $order_by ";
        }
       else {
            $search_case .= "ORDER BY b.display_order  ";
        }
      if($curnt_page=='') {
            $curnt_page =1;
        }
        $slimit = 20;
        //$curnt_page=1;
        $limit  = (($curnt_page-1)*$slimit);
       $midlelimit = $limit+$slimit;
       $endlimit = $midlelimit+$slimit;
       $products_total = DB::SELECT("SELECT a.id,a.name,a.review_details,a.vendor_id,category,a.brand_details->>'$.brand_name' as brand_name,a.pdt_details->>'$.code' as code,a.order_details->>'$.packing_qty' as packing_qty,a.order_details->>'$.unit' as unit,a.price->>'$.selling_price' as selling_price,a.inventory_details->>'$.current_stock_count' as current_stock_count FROM vendor_product a LEFT JOIN vendor_categories b on a.disp_cat_id=b.id  WHERE  a.active='Y' AND a.approved='Approved' AND a.inventory_details->>'$.current_stock_count'!='' AND a.inventory_details->>'$.current_stock_count'!=0 AND a.price->>'$.selling_price'!=0 $search_case ");
       $total_count = count($products_total);
       $endcount = round($total_count/$slimit,0);
         $count_mode = ($total_count)%($slimit);
         if($count_mode!=0 && $count_mode<10) {
             $endcount=$endcount+1;
         }

       $paginations  = '';
        $limitcase = "LIMIT $limit,$slimit";
//       if($minValue=='') {
//           $limitcase = "LIMIT $limit,$slimit";
//       }
//       else{
//           $limitcase= "";
//       }
    //return "SELECT a.id,a.name,a.review_details,a.vendor_id,category,a.brand_details->>'$.brand_name' as brand_name,a.pdt_details->>'$.code' as code,a.order_details->>'$.packing_qty' as packing_qty,a.order_details->>'$.unit' as unit,a.price->>'$.selling_price' as selling_price,a.inventory_details->>'$.current_stock_count' as current_stock_count FROM vendor_product a LEFT JOIN vendor_categories b on a.disp_cat_id=b.id  WHERE  a.active='Y' AND a.approved='Approved' AND a.inventory_details->>'$.current_stock_count'!='' AND a.inventory_details->>'$.current_stock_count'!=0 AND a.price->>'$.selling_price'!=0 $search_case  $limitcase ";    
        $products = DB::SELECT("SELECT a.id,a.name,a.review_details,a.vendor_id,category,a.brand_details->>'$.brand_name' as brand_name,a.pdt_details->>'$.code' as code,a.order_details->>'$.packing_qty' as packing_qty,a.order_details->>'$.unit' as unit,a.price->>'$.selling_price' as selling_price,a.inventory_details->>'$.current_stock_count' as current_stock_count FROM vendor_product a LEFT JOIN vendor_categories b on a.disp_cat_id=b.id  WHERE  a.active='Y' AND a.approved='Approved' AND a.inventory_details->>'$.current_stock_count'!='' AND a.inventory_details->>'$.current_stock_count'!=0 AND a.price->>'$.selling_price'!=0 $search_case  $limitcase ");
       $p=0;
       $result_count = 0;
        $pdt_details[$p] = [
                        'id'=>0,
                        'vendor_id'=>0,
                        'name'=>'',
                        'brand_name'=>'',
                        'code'=>'',
                        'packing_qty'=>0,
                        'unit'=>'',
                        'rating'=>'',
                        'pdt_org_price'=>0,
                        'value_type'=>'',
                        'value'=>'',
                        'org_price_comsn'=>0,
                        'pdt_selling_price'=>0,
                        'pdt_offer_price'=>0,
                        'offerid'=>'',
                        'comsn_val'=>0,
                        'image_url'=>''
                ];
       foreach($products as $pdt) {
            $price = $pdt->selling_price;
            $current_stock_count = $pdt->current_stock_count;
            if($current_stock_count!='' && $current_stock_count!=0 && $price!=0){
                $selling_price = $price;
                    $offer_price = 0;
                    $value_type = '';
                    $value='';
                    $offerid = '';
                     $ofr_comsn =    Sharesource::ofr_comsn_price_calculation($price,json_decode($pdt->category),$pdt->vendor_id,$pdt->id,1);
                     $image_url = 'uploads/dummy/no-image.jpg';
                 $featch_image_url = DB::SELECT("SELECT image_url FROM vendor_pdt_image WHERE pdt_id=$pdt->id AND active='Y' AND main_img='Y' LIMIT 1");
                 if(count($featch_image_url)!=0) {
                     $image_url = $featch_image_url[0]->image_url;
                 }
                     $converted_org_cmsn = $convertion_rate*$ofr_comsn['comsn_rate'];
                     $converted_sel_price = $convertion_rate*$ofr_comsn['selling_price'];
                 if($minValue<=$ofr_comsn['selling_price'] && $maxValue>=$ofr_comsn['selling_price']){
                      $pdt_details[$p] = [
                        'id'=>$pdt->id,
                        'vendor_id'=>$pdt->vendor_id,
                        'name'=>$pdt->name,
                        'brand_name'=>$pdt->brand_name,
                        'code'=>$pdt->code,
                        'packing_qty'=>$pdt->packing_qty,
                        'unit'=>$pdt->unit,
                        'rating'=>$pdt->review_details,
                        'pdt_org_price'=>$price,
                        'value_type'=>$ofr_comsn['value_type'],
                        'value'=>$ofr_comsn['value'],
                        'org_price_comsn'=>$ofr_comsn['comsn_rate'],
                        'pdt_selling_price'=>$ofr_comsn['selling_price'],
                        'converted_org_cmsn'=>$converted_org_cmsn,
                        'converted_sel_price'=>$converted_sel_price,
                        'pdt_offer_price'=>$ofr_comsn['offer_price'],
                        'offerid'=>$ofr_comsn['offerid'],
                        'comsn_val'=>$ofr_comsn['com_val'],
                        'image_url'=>$image_url
                ];
                $p++;
                 } else if($minValue=='' && $maxValue==''){
                     $pdt_details[$p] = [
                        'id'=>$pdt->id,
                        'vendor_id'=>$pdt->vendor_id,
                        'name'=>$pdt->name,
                        'brand_name'=>$pdt->brand_name,
                        'code'=>$pdt->code,
                        'packing_qty'=>$pdt->packing_qty,
                        'unit'=>$pdt->unit,
                        'rating'=>$pdt->review_details,
                        'pdt_org_price'=>$price,
                        'value_type'=>$ofr_comsn['value_type'],
                        'value'=>$ofr_comsn['value'],
                        'org_price_comsn'=>$ofr_comsn['comsn_rate'],
                        'pdt_selling_price'=>$ofr_comsn['selling_price'],
                        'converted_org_cmsn'=>$converted_org_cmsn,
                        'converted_sel_price'=>$converted_sel_price,
                        'pdt_offer_price'=>$ofr_comsn['offer_price'],
                        'offerid'=>$ofr_comsn['offerid'],
                        'comsn_val'=>$ofr_comsn['com_val'],
                        'image_url'=>$image_url
                ];
                $p++; 
                 }
                
               
            }
                    
                //product org price is actual price of product
                //product selling price is actual price or offer pric(if offer exist) of product
                //product offer price is offer price of product
        }
         $result_count = $p;
         $pgnt_cnt = count($products)-$result_count;
        return response::json(compact('pdt_details','count_mode','result_count','currency_code','paginations','curnt_page','limit','endlimit','midlelimit','endcount','total_count'));

    }
    public function take_brand_list(Request $request) {
        $brand_list = DB::SELECT("SELECT brand_name,brand_orgin,IFNULL(brand_logo_url,'uploads/dummy/no-image.jpg') as brand_logo_url  FROM brand_master WHERE active='Y' ");
        return $brand_list;
    }
    public function AssociationImages(Request $request) {
            $brand_list    =   DB::select("SELECT * FROM tbl_homepage_projects");
             return response::json(compact('brand_list'));
    }
    public function take_unit_list(Request $request) {
        $unit_list = DB::SELECT("SELECT DISTINCT(order_details->>'$.unit') as unit_name FROM vendor_product WHERE order_details->>'$.unit'!='' ");
        return $unit_list;
    }
    public function update_cart_list(Request $request) {
        $cookiesessionid = $request['cookiesessionid'];
        $vendor_id       = $request['vendor_id'];
        $userid          = $request['userid'];
        $productid       = $request['productid'];
        $price           = $request['price'];
        $orgprice        = $request['orgprice'];
        $copouncode        = $request['copouncode'];
//        $commision       = $request['commision'];
//        $rate_incl_comsn = $request['rate_incl_comsn'];
//        $ofrprice        = $request['ofrprice'];
        $method          = $request['method'];
        $cartid          = $request['cartid'];
        $logged_userid   = $request['logged_userid'];
        $response = '';
         $condition = '';
        if($logged_userid!='' && $logged_userid !='undefined') {
            $condition = "user_id = $logged_userid";
            $cookiesessionid = $logged_userid;
        }
        else {
            $condition = "cookie_id='$cookiesessionid'";
            $logged_userid =0;
        }
        if($method=='clear_all'){
             DB::DELETE("DELETE FROM cart_items WHERE $condition ");
            $msg = "cleared";
            return response::json(compact('msg','response'));
        }
        $pdt_cats = DB::SELECT("SELECT inventory_details->>'$.current_stock_count' as current_stock_count,category,price->>'$.selling_price' as selling_price,vendor_id,order_details->>'$.maximum_order_qty' as maximum_order_qty,order_details->>'$.minimum_order_qty' as minimum_order_qty FROM vendor_product WHERE id='$productid' ");
        $vendor_id            = $pdt_cats[0]->vendor_id;
        $orgprice             = $pdt_cats[0]->selling_price;
        $max_qty              = $pdt_cats[0]->maximum_order_qty;
        $min_qty              = $pdt_cats[0]->minimum_order_qty;
        $current_stock_count  = $pdt_cats[0]->current_stock_count;
        $ofr_comsn  = Sharesource::ofr_comsn_price_calculation($orgprice,json_decode($pdt_cats[0]->category),$vendor_id,$productid,1);    
        $price      = $ofr_comsn['selling_price'];
        $comsn_rate = $ofr_comsn['comsn_rate'];
        $ofrid      = $ofr_comsn['offerid'];
        $ofrprice   = $ofr_comsn['offer_price'];
       if($ofrid ==''){
            $ofrid = 0;
        }
        if($method==''){
            $exsit_check = DB::SELECT("SELECT id,qty FROM cart_items WHERE pdt_id='$productid' AND $condition  ");
            if(count($exsit_check)==0 ){
                //return "INSERT INTO cart_items(cookie_id,user_id,vendor_id, pdt_id, price,org_price,offer_price,ofrid, total_price) VALUES ('$cookiesessionid','$logged_userid','$vendor_id',$productid,$price,$orgprice,$ofrprice,$ofrid,$price)";
                DB::INSERT("INSERT INTO cart_items(cookie_id,user_id,vendor_id, pdt_id, price,org_price,offer_price,ofrid, total_price,tax_details,tax_price) VALUES ('$cookiesessionid','$logged_userid','$vendor_id',$productid,$price,$comsn_rate,$ofrprice,$ofrid,$price,JSON_OBJECT('next_index',1,'deleted_index',0),0)");
                $msg = "added";
            }
            else{
                DB::UPDATE("UPDATE cart_items SET qty=qty+1,price=$price,total_price = $price*qty,ofrid=$ofrid WHERE pdt_id='$productid' AND $condition ");
                $msg = "qty_updated";
            }
        }
        else if($cartid!='' && $method=='ADD'){
            $qty_check = DB::SELECT("SELECT qty FROM cart_items WHERE id=$cartid ");
            if($qty_check[0]->qty<$max_qty && $qty_check[0]->qty<$current_stock_count){
              DB::UPDATE("UPDATE cart_items SET qty=qty+1,price=$price,total_price = $price*qty WHERE id=$cartid ");
               $msg = "qty_added";
            }
            else if($qty_check[0]->qty>$current_stock_count){
               $msg       = "no_stock";
                $response = "Available Qty Is Only $current_stock_count";
            }
            else{
                $msg      = 'max_qty_over';
                $response = "Max.Purchase Qty Is Only $max_qty";
            }
            
           
        }
        else  if($method=='MINUS'){
            $qty_check = DB::SELECT("SELECT qty FROM cart_items WHERE id=$cartid ");
            if($qty_check[0]->qty>=2 && $qty_check[0]->qty>$min_qty){
                DB::UPDATE("UPDATE cart_items SET qty=qty-1,price=$price,total_price = $price*qty WHERE id=$cartid ");
                 $msg = "qty_minus";
            }
            else{
                $msg = 'min_qty_over';
                $response = "Min.Purchase Qty Is Only $min_qty";
            }
           
        }
       
        else  if($method=='remove'){
             DB::DELETE("DELETE FROM cart_items WHERE id=$cartid ");
            $msg = "removed";
        }
        else  if($method=='clear_all'){
             DB::DELETE("DELETE FROM cart_items WHERE $condition ");
            $msg = "cleared";
        }
        if($copouncode!=''){
            $this->apply_genral_offer($request, [
            'copouncode'     => $copouncode,
            'logged_userid'     => $logged_userid,
        ]);
        }
        return response::json(compact('msg','response'));
    }
    public function take_cart_list(Request $request) {
        $cookiesessionid   = $request['cookiesessionid'];
        $logged_userid     = $request['logged_userid'];
        $sub_total = array();
        $cart_list = array();
        $comparison_length = 0;
        if($logged_userid !='' && $logged_userid!='undefined') {
            $condition = "a.user_id = $logged_userid";
        }
        else{
            $logged_userid = 0;
             $condition = "a.cookie_id='$cookiesessionid'";
        }
        if($cookiesessionid !='' && $cookiesessionid !='undefined'){
        $cart_list         = DB::SELECT("SELECT a.id,a.vendor_id,a.pdt_id,a.qty,a.price,a.org_price,a.offer_price,a.ofrid,a.total_price,b.name FROM cart_items a LEFT JOIN vendor_product b on a.pdt_id=b.id  WHERE $condition  ");
        $cart_total_price  = DB::SELECT("SELECT IFNULL(SUM(a.total_price),0) as subtotal FROM cart_items a  WHERE $condition  ");
        //return ['cart_list'=>$cart_list,'sub_total'=>$cart_total_price];
        $sub_total = $cart_total_price[0]->subtotal;
         $comparison = DB::SELECT("SELECT * FROM product_comparison_list WHERE cookie_id='$cookiesessionid'");
         $comparison_length = count($comparison);
        }
        return response::json(compact('sub_total','cart_list','comparison_length'));
    }
    public function take_cart_list_summary(Request $request) {
        $cookiesessionid   = $request['cookiesessionid'];
        $logged_userid     = $request['logged_userid'];
        $copouncode     = $request['copouncode'];
        
        $sub_total = array();
        $cart_list = array();
        $tax_info = array();
        $expec_delv_date= "";
        if($logged_userid !='' && $logged_userid!='undefined') {
            $condition = "a.user_id = $logged_userid";
            $uniqueid = $logged_userid;
        }
        else{
            $logged_userid = 0;
             $condition = "a.cookie_id='$cookiesessionid'";
              $uniqueid = $cookiesessionid;
        }
        if($cookiesessionid !='' && $cookiesessionid !='undefined'){
            //BILL OFFER CALCULATION BEGIN
            //BILL OFFER CALCULATION END
        $cart_info = DB::SELECT("SELECT a.id,a.qty,a.vendor_id,a.final_total_incl_genofr,a.gen_ofr_details->>'$.copouncode' as copouncode, a.gen_ofr_details->>'$.offer_per' as offer_per,a.pdt_id,a.qty,a.price,a.org_price,a.offer_price,a.ofrid,a.total_price,b.tax_details,b.name,b.price->>'$.selling_price' as selling_price,b.category,a.delivery_charge FROM cart_items a LEFT JOIN vendor_product b on a.pdt_id=b.id  WHERE $condition  ");
        $delivery_info = DB::SELECT("SELECT DATE_FORMAT(DATE_ADD(DATE(now()), INTERVAL max(cast(b.order_details->>'$.delivery_time' as UNSIGNED) ) DAY),'%d-%b-%Y') as deliverydate FROM cart_items a LEFT JOIN vendor_product b on a.pdt_id=b.id  WHERE $condition  ");
        $expec_delv_date = $delivery_info[0]->deliverydate;
        $c = 0;
      // $copouncode = '';
       $offer_per = '';
        $restrict_class ='';
       DB::DELETE("DELETE FROM `cart_master`  WHERE userid='$uniqueid' ");
        foreach($cart_info as $cart){
        $vendor_id            = $cart->vendor_id;
        $orgprice             = $cart->selling_price;
        $pdtcategory          = $cart->category;
        //pdt_id
       $ofr_comsn    = Sharesource::ofr_comsn_price_calculation($orgprice,json_decode($pdtcategory),$vendor_id,$cart->pdt_id,$cart->qty);    
       $price        = $ofr_comsn['selling_price'];//return $price;
       $comsn_rate   = $ofr_comsn['comsn_rate'];
       $ofrid        = $ofr_comsn['offerid'];
       if($ofrid==''){
           $ofrid=0;
       }
       $ofrprice     = $ofr_comsn['offer_price'];
       //return "UPDATE cart_items SET price=$price,total_price = $price*qty,ofrid=$ofrid,offer_price=$ofrprice WHERE pdt_id='$cart->pdt_id' AND id=$cart->id ";
        DB::UPDATE("UPDATE cart_items SET price=$price,total_price = $price*qty,ofrid=$ofrid,offer_price=$ofrprice WHERE pdt_id='$cart->pdt_id' AND id=$cart->id ");
        if($copouncode!=''){
            $this->apply_genral_offer($request, [
            'copouncode'     => $copouncode,
            'logged_userid'     => $logged_userid,
        ]);
        }
        //FOR GETTING VALUES APPLYING AFTER TAX,OFFER,CPN ETC.
        $cart_info_details = DB::SELECT("SELECT a.id,a.qty,a.vendor_id,a.final_total_incl_genofr,a.gen_ofr_details->>'$.copouncode' as copouncode, a.gen_ofr_details->>'$.offer_per' as offer_per,a.pdt_id,a.qty,a.price,a.org_price,a.offer_price,a.ofrid,a.total_price,b.tax_details,b.name,b.price->>'$.selling_price' as selling_price,b.category FROM cart_items a LEFT JOIN vendor_product b on a.pdt_id=b.id  WHERE a.id=$cart->id  ");
            $tax_details = json_decode($cart->tax_details);
            $t=0;$taxprice=0;$append ='';
            $cartqty = $cart->qty;
            if($cart->final_total_incl_genofr==0) {
                $tax_excPrice = $cart_info_details[0]->total_price; 
            }
            else{
                $tax_excPrice = $cart_info_details[0]->final_total_incl_genofr; 
                $copouncode = $cart_info_details[0]->copouncode; 
                $offer_per  = $cart_info_details[0]->offer_per; 
            }
            
            for($x=0;$x<count($tax_details);$x++) {
                $tax = $tax_details[$x];
                $tax_info =  DB::SELECT("SELECT id,tax_name,tax_type,tax_value FROM taxmaster WHERE item_tax='Y' AND status='Y' AND id='$tax'");
               if(count($tax_info)!=0) {
                  $tax_id     = $tax_info[0]->id;
                  $tax_name     = $tax_info[0]->tax_name;
                  $tax_type     = $tax_info[0]->tax_type;
                  $tax_value    = $tax_info[0]->tax_value;
                  if($tax_type == 'P') {
                      $taxprice_dis = ($tax_excPrice*$tax_value)/100;
                      $taxtitle = $tax_name.'@'.$tax_value.'%';
                  }
                  else{
                      $taxprice_dis = $tax_value;
                       $taxtitle = $tax_name.'@'.$tax_value.'AED';
                  }
                  $append .="<p>$taxtitle</p>";
                  $taxprice = round($taxprice+$taxprice_dis,2);
                  $t++;
                  DB::UPDATE("UPDATE cart_items SET tax_details=json_set(tax_details,'$.tax$t',json_object('taxid','$tax_id','taxname','$tax_name','taxamount','$taxprice_dis','taxtitle','$taxtitle')) WHERE id=$cart->id");
                 $tax_exist =  DB::SELECT("SELECT * FROM cart_master WHERE taxid='$tax_id' AND userid='$uniqueid' ");
                        if(count($tax_exist)!=0) {
                            $tax_rate_ex  = $tax_exist[0]->tax_rate+$taxprice_dis;
                            DB::UPDATE("UPDATE cart_master SET tax_rate='$tax_rate_ex' WHERE userid='$uniqueid' AND taxid='$tax_id' ");
                        }
                        else{
                            $tax_rate_ex = $taxprice_dis;
                          DB::UPDATE("INSERT INTO `cart_master`(`userid`,sl_no,cookie_id, `taxid`, `tax_title`, `tax_rate`) VALUES ('$uniqueid',1,'$cookiesessionid','$tax_id','$taxtitle','$tax_rate_ex')");

                        }
               }
            }
            if($append==''){
                $append .="<p>NIL</p>";
            }
           $taxprice_total = $tax_excPrice+$taxprice;
           DB::UPDATE("UPDATE cart_items SET totalprice_incl_tax=$taxprice_total,tax_price=$taxprice WHERE id=$cart->id");
           $qty_check = DB::SELECT("SELECT inventory_details->>'$.current_stock_count' as current_stock_count,order_details->>'$.maximum_order_qty' as maximum_order_qty,order_details->>'$.minimum_order_qty' as minimum_order_qty FROM `vendor_product` WHERE id=$cart->pdt_id");
                    $qty_exceed = 'N';
                    $not_active = 'N';
                    $qty_min = 'N';
                    $stock_out = 'N';
                    $qty_msg ='';
                   if($cart->qty>=$qty_check[0]->maximum_order_qty) {
                         $not_active = 'Y';
                         $qty_msg = "Max. Qty - ".($qty_check[0]->maximum_order_qty);
                   }
                   if($cart->qty>=$qty_check[0]->current_stock_count) {
                         $not_active = 'Y';
                         $qty_msg = "Max Available Qty-".$qty_check[0]->current_stock_count;
                   }
                   if($cart->qty==1) {
                       $qty_min = 'Y';
                   }
                   if($cart->qty<=$qty_check[0]->minimum_order_qty && $qty_check[0]->minimum_order_qty!=1) {
                       $qty_min = 'Y';
                       $qty_msg = "Min. Qty - ".$qty_check[0]->minimum_order_qty;
                   }
                   //return $cart->qty.'====='.$qty_check[0]->maximum_order_qty.'====='.$qty_check[0]->current_stock_count .'====='.$qty_check[0]->minimum_order_qty;
                   if($cart->qty>$qty_check[0]->maximum_order_qty || $cart->qty>$qty_check[0]->current_stock_count || $cart->qty<$qty_check[0]->minimum_order_qty){
                       $restrict_class ='disable_btn';
                   }
                   $image_url = 'uploads/dummy/no-image.jpg';
                 $featch_image_url = DB::SELECT("SELECT image_url FROM vendor_pdt_image WHERE pdt_id=$cart->pdt_id AND active='Y' AND main_img='Y' LIMIT 1");
                 if(count($featch_image_url)!=0) {
                     $image_url = $featch_image_url[0]->image_url;
                 }
           $cart_list[$c] = [
                        'id'=>$cart->id,
                        'vendor_id'=>$cart->vendor_id,
                        'pdt_id'=>$cart->pdt_id,
                        'qty'=>$cart->qty,
                        'not_active'=>$not_active,
                        'qty_exceed'=>$qty_exceed,
                        'stock_out'=>$stock_out,
                        'qty_min'=>$qty_min,
                        'qty_msg'=>$qty_msg,
                        'price'=>$price,
                        'org_price'=>$cart->org_price,
                        'offer_price'=>$ofrprice,
                        'ofrid'=>$ofrid,
                        'total_price'=>$cart_info_details[0]->total_price,
                        'name'=>$cart->name,
                        'delivery_charge'=>$cart->delivery_charge,
                        'img_large_url'=>$image_url,
                        'append'=>$append,
                   ];
           //$price $comsn_rate $ofrid $ofrprice
           $c++;
        }
        $cart_total_price  = DB::SELECT("SELECT IFNULL(SUM(a.total_price),0) as subtotal,IFNULL(SUM(a.totalprice_incl_tax),0) as finaltotal,IFNULL(SUM(a.gen_ofr_details->>'$.genral_amnt'),0) as gen_ofr_total FROM cart_items a  WHERE $condition  ");
       $sub_total = $cart_total_price[0]->subtotal;
        $final_total = $cart_total_price[0]->finaltotal;
        $gen_ofr_total = $cart_total_price[0]->gen_ofr_total;
        $tax_info = DB::SELECT("SELECT tax_rate,tax_title FROM cart_master WHERE userid='$uniqueid'");
        }
        //return $tax_info;
         return response::json(compact('sub_total','final_total','cart_list','tax_info','gen_ofr_total','offer_per','copouncode','restrict_class','expec_delv_date'));
        
    }
    public function filter_price_list(){
        //$price_list = DB::SELECT("SELECT start_range,end_range FROM price_filter_master WHERE 1 ORDER BY end_range ASC");
        $price_list = DB::SELECT("SELECT filter_price_min,filter_price_max FROM general_settings WHERE 1");
        return $price_list;
    }

    public function apply_genral_offer(Request $request) {
        $copouncode    = $request['copouncode'];
        $userid        = $request['logged_userid'];
        $result = 'Invalid Coupon Codesss';
        $gen_ofr_info = DB::SELECT("SELECT usage_limit,offer_details->>'$.amt_abv' as amt_abv,offer_details->>'$.max_amt' as max_amt,offer_details->>'$.offer_per' as offer_per FROM general_offers WHERE active='Y' AND coupon_code='$copouncode'  ");
        if(count($gen_ofr_info)!=0) {
            $validity_check = DB::SELECT("SELECT usage_limit,offer_details->>'$.amt_abv' as amt_abv,offer_details->>'$.max_amt' as max_amt,offer_details->>'$.offer_per' as offer_per FROM general_offers WHERE active='Y' AND now() BETWEEN  offer_details->>'$.valid_from' AND  offer_details->>'$.valid_to' AND coupon_code='$copouncode' ");
            if(count($validity_check)!=0){
                $usage_limit_check = DB::SELECT("SELECT COUNT(applied_cpn_code) as usedcount FROM order_master WHERE userid='$userid' and applied_cpn_code='$copouncode'");
                    $used_count = $usage_limit_check[0]->usedcount;
                    $usage_limit  = $gen_ofr_info[0]->usage_limit;
                    if($used_count<$usage_limit){
                           $amt_abv   = $gen_ofr_info[0]->amt_abv;
                           $max_amt   = $gen_ofr_info[0]->max_amt;
                           $offer_per = $gen_ofr_info[0]->offer_per;
                           $cart_info = DB::SELECT("SELECT a.id,a.vendor_id,a.final_total_incl_genofr,a.pdt_id,a.qty,a.price,a.org_price,a.offer_price,a.ofrid,a.total_price FROM cart_items a WHERE a.user_id=$userid  ");
                           $cart_price = DB::SELECT("SELECT SUM(total_price) as total_price,COUNT(pdt_id) as totalitems FROM cart_items WHERE user_id=$userid");
                           $genral_amnt =0;
                           $total_price = $cart_price[0]->total_price;
                           $total_items_count  = $cart_price[0]->totalitems;
                           $genral_amnt = ($total_price*$offer_per)/100;
                           if($max_amt<=$genral_amnt){
                                        $genral_amnt = $max_amt;
                                    }
                            $genral_amnt_single =   $genral_amnt/$total_items_count;      
                           foreach($cart_info as $cart){
                               $total_itemprice    = $cart->total_price;
                               if($total_price>=$amt_abv ) {
                                   $final_incl_ofr_prcie = $total_price-$genral_amnt_single;
                                   DB::UPDATE("UPDATE cart_items SET final_total_incl_genofr='$final_incl_ofr_prcie',gen_ofr_details=json_object('copouncode','$copouncode','genral_amnt','$genral_amnt_single','offer_per','$offer_per') WHERE id='$cart->id' ");
                                   $result = 'Applied';
                               } else if($total_price<$amt_abv){
                                   $result = "Coupon is Applicable only for Above $amt_abv";
                               } else if($total_price>$max_amt){
                                   $result = "Coupon is Applicable only for Below $max_amt";
                               } 
                               else{
                                   DB::UPDATE("UPDATE cart_items SET final_total_incl_genofr=0 WHERE id='$cart->id' ");
                                   $result = "Coupon is Not Available Now ";
                               }
                           }
                       // return $genral_amnt.' and max amount is '.$max_amt.' and offer rate is '.$offer_per.' and totla price is '.$total_itemprice;

                    }
                    else{
                       $result = 'Coupon Usage Limit Exceeded!'; 
                    }
                    
            }
            else{
                 $result = 'Coupon Expired!';
            }
            
        }
        else{
            $result = 'Invalid Coupon Code';
        }
        return response::json(compact('result','copouncode'));
    }
    public function update_order_info(Request $request) {
            $odernumber = '';
            $customer    = $request['logged_userid'];
            $user_info = DB::SELECT("SELECT cslno FROM customer_list WHERE id='$customer' ");
            if(count($user_info)!=0) {
                         $cslno = $user_info[0]->cslno;
            $ship_address_index =  $request['ship_address_index'];
            $exist_adrs_indx    =  $request['exist_adrs_indx'];
            $ship_address_sel   =  $request['ship_address_sel'];
            $shp_fname   = $request['shp_fname'];
            $shp_lname   = $request['shp_lname'];
            $shp_phone   = $request['shp_phone'];
            $shp_email   = $request['shp_email'];
            $shp_company = $request['shp_company'];
            $shp_address = $request['shp_address'];
            $shp_country = $request['shp_country'];
            $shp_state   = $request['shp_state'];
            $shp_city          = $request['shp_city'];
            $shp_lanmark       = $request['shp_lanmark'];
            $shp_zipcode       = $request['shp_zipcode'];
            $shp_order_notes   = $request['shp_order_notes'];
            $delivery_charge   = $request['delivery_charge'];
            $paymenthod        = $request['paymenthod'];
            $expec_delv_date   = date('Y-m-d H:i:s',strtotime($request['expec_delv_date']));
            
            $subtotal            = $request['subtotal'];
            $final_total         = $request['final_total'];
            
            $gen_ofr_total       = $request['gen_ofr_total'];
                if($gen_ofr_total=='') {
                    $gen_ofr_total = 0;
                }
            $applied_cpn_code    = $request['applied_cpn_code'];
                if($applied_cpn_code==''){
                    $applied_cpn_code = '';
                }
            $offer_per           = $request['offer_per'];
                if($offer_per=='') {
                    $offer_per = 0;
                }
            
                 $latitude        =  $request['latitude'];
                 $longitude       =  $request['longitude'];
                 $frmtd_adrs      =  $request['formatted_address'];
                 $url             =  $request['url'];
             if($ship_address_index!=0 || $ship_address_index!='edit'){
                $shp_fname   =  $ship_address_sel['shp_fname'];
                $shp_lname   =  $ship_address_sel['shp_lname'];
                $shp_phone   =  $ship_address_sel['shp_phone'];
                $shp_email   =  $ship_address_sel['shp_email'];
                $shp_company =  $ship_address_sel['shp_company'];
                $shp_address =  $ship_address_sel['shp_address'];
                $shp_country =  $ship_address_sel['shp_country'];
                $shp_state   =  $ship_address_sel['shp_state'];
                $shp_city    =  $ship_address_sel['shp_city'];
                $shp_lanmark =  $ship_address_sel['shp_lanmark'];
                $shp_zipcode =  $ship_address_sel['shp_zipcode'];
                $latitude    =  $ship_address_sel['latitude'];
                $longitude   =  $ship_address_sel['longitude'];
                $frmtd_adrs  =  $ship_address_sel['frmtd_adrs'];
                $url         =  $ship_address_sel['url'];
            }
             
            //order number generation
            
            $gnrl_settings = DB::SELECT("SELECT month,slno,DATE_FORMAT(NOW(),'%y%m') as month,slno FROM general_settings  WHERE month=DATE_FORMAT(NOW(),'%y%m')");
            if(count($gnrl_settings)==0) {
                DB::UPDATE("UPDATE general_settings SET month=DATE_FORMAT(NOW(),'%y%m'),slno=100");
                $slno = 100; 
                $month = date('ym');
            } else{
                $slno = $gnrl_settings[0]->slno+1; 
                $month = $gnrl_settings[0]->month;
                DB::UPDATE("UPDATE general_settings SET slno=slno+1 WHERE month=DATE_FORMAT(NOW(),'%y%m') ");
            }
            $odernumber = $month.$slno.$cslno;
            //return 'INSERT INTO order_master(order_number, order_date, userid, sub_total, final_total,total_details,gen_ofr_total,applied_cpn_code,offer_per, current_status,shipping_address,payment_method)  VALUES ("'.$odernumber.'",now(),"'.$customer.'","'.$subtotal.'","'.$final_total.'",json_object("delivery_charge","'.$delivery_charge.'"),"'.$gen_ofr_total.'","'.$applied_cpn_code.'","'.$offer_per.'","1",json_object("shp_fname","'.$shp_fname.'","shp_lname","'.$shp_lname.'","shp_phone","'.$shp_phone.'","shp_email","'.$shp_email.'","shp_company","'.$shp_company.'","shp_address","'.$shp_address.'","shp_country","'.$shp_country.'","shp_state","'.$shp_state.'","shp_city","'.$shp_city.'","shp_lanmark","'.$shp_lanmark.'","shp_zipcode","'.$shp_zipcode.'","latitude","'.$latitude.'","longitude","'.$longitude.'","frmtd_adrs","'.$frmtd_adrs.'","url","'.$url.'"),"'.$paymenthod.'")';
            DB::INSERT('INSERT INTO order_master(order_number, order_date, userid, sub_total, final_total,total_details,gen_ofr_total,applied_cpn_code,offer_per, current_status,shipping_address,payment_method,payment_info,expec_delv_date)  VALUES ("'.$odernumber.'",now(),"'.$customer.'","'.$subtotal.'","'.$final_total.'",json_object("delivery_charge","'.$delivery_charge.'"),"'.$gen_ofr_total.'","'.$applied_cpn_code.'","'.$offer_per.'","1",json_object("shp_fname","'.$shp_fname.'","shp_lname","'.$shp_lname.'","shp_phone","'.$shp_phone.'","shp_email","'.$shp_email.'","shp_company","'.$shp_company.'","shp_address","'.$shp_address.'","shp_country","'.$shp_country.'","shp_state","'.$shp_state.'","shp_city","'.$shp_city.'","shp_lanmark","'.$shp_lanmark.'","shp_zipcode","'.$shp_zipcode.'","latitude","'.$latitude.'","longitude","'.$longitude.'","frmtd_adrs","'.$frmtd_adrs.'","url","'.$url.'"),"'.$paymenthod.'",json_object("order_ref","","order_url",""),"'.$expec_delv_date.'")');
            //json_object("blng_fname","'.$blng_fname.'","blng_lname","'.$blng_lname.'","blng_phone","'.$blng_phone.'","blng_email","'.$blng_email.'","blng_company","'.$blng_company.'","blng_address","'.$blng_address.'","blng_country","'.$blng_country.'","blng_state","'.$blng_state.'","blng_city","'.$blng_city.'","blng_lanmark","'.$blng_lanmark.'","blng_zipcode","'.$blng_zipcode.'","blng_latitude","'.$blng_latitude.'","blng_longitude","'.$blng_longitude.'","blng_frmtd_adrs","'.$blng_frmtd_adrs.'","blng_url","'.$blng_url.'")
            if($exist_adrs_indx==0) {
            $adress_index =  DB::SELECT("SELECT IFNULL(ship_adress_list->>'$.next_index',1) as shp_next_index, IFNULL(ship_adress_list->>'$.deleted_index',0) as deleted_index FROM customer_list WHERE id=$customer");
                    $shp_next_index    = $adress_index[0]->shp_next_index;
                    $deleted_index     = $adress_index[0]->deleted_index;
                $new_ship_adr =0;
    //ADDRESS EXISTING CHECK AND UPDATE BEGIN           
            for($l=1;$l<=$shp_next_index;$l++) {
                    $address = 'address'.$l;
                    $shp_adrs_exist = DB::SELECT("SELECT ship_adress_list FROM `customer_list` WHERE  ship_adress_list->>'$.$address.shp_fname'='$shp_fname' AND ship_adress_list->>'$.$address.shp_lname'='$shp_lname' AND ship_adress_list->>'$.$address.shp_phone'='$shp_phone' AND ship_adress_list->>'$.$address.shp_address'='$shp_address' AND ship_adress_list->>'$.$address.shp_country'='$shp_country'  AND ship_adress_list->>'$.$address.shp_state'='$shp_state' AND ship_adress_list->>'$.$address.shp_city'='$shp_city'  AND ship_adress_list->>'$.$address.shp_zipcode'='$shp_zipcode' AND id=$customer");
                    if(count($shp_adrs_exist)!=0) {
                        $new_ship_adr = 1;
                    }
            }
          
            if($new_ship_adr==0) {
               if($deleted_index!=0) {
                   $adr_index = $deleted_index;
               }
               else{
                   $adr_index = $shp_next_index;
               }
               $shp_new_index  = $shp_next_index+1;
                DB::UPDATE('UPDATE customer_list SET  ship_adress_list=json_set(ship_adress_list,"$.next_index",'.$shp_new_index.'),ship_adress_list=json_set(ship_adress_list,"$.deleted_index",0),ship_adress_list=json_insert(ship_adress_list,"$.address'.$adr_index.'",json_object("shp_fname","'.$shp_fname.'","shp_lname","'.$shp_lname.'","shp_phone","'.$shp_phone.'","shp_email","'.$shp_email.'","shp_company","'.$shp_company.'","shp_address","'.$shp_address.'","shp_country","'.$shp_country.'","shp_state","'.$shp_state.'","shp_city","'.$shp_city.'","shp_lanmark","'.$shp_lanmark.'","shp_zipcode","'.$shp_zipcode.'","latitude","'.$latitude.'","longitude","'.$longitude.'","frmtd_adrs","'.$frmtd_adrs.'","url","'.$url.'")) WHERE id="'.$customer.'" ');
                //DB::UPDATE('UPDATE customer_list SET ship_adress_list=json_set(ship_adress_list,"$.next_index",'.$shp_new_index.'),ship_adress_list=json_set(ship_adress_list,"$.deleted_index,0) WHERE id="'.$customer.'"');
            }
            }
            else{
                $exist_adrs_indx = $exist_adrs_indx+1;
                DB::UPDATE('UPDATE customer_list SET ship_adress_list=json_set(
                        ship_adress_list,"$.address'.$exist_adrs_indx.'.shp_fname","'.$shp_fname.'",
                         "$.address'.$exist_adrs_indx.'.shp_lname","'.$shp_lname.'",
                        "$.address'.$exist_adrs_indx.'.shp_phone","'.$shp_phone.'",
                        "$.address'.$exist_adrs_indx.'.shp_email","'.$shp_email.'",
                        "$.address'.$exist_adrs_indx.'.shp_company","'.$shp_company.'",
                        "$.address'.$exist_adrs_indx.'.shp_address","'.$shp_address.'",
                        "$.address'.$exist_adrs_indx.'.shp_country","'.$shp_country.'",
                        "$.address'.$exist_adrs_indx.'.shp_state","'.$shp_state.'",
                        "$.address'.$exist_adrs_indx.'.shp_city","'.$shp_city.'",
                        "$.address'.$exist_adrs_indx.'.shp_lanmark","'.$shp_lanmark.'",
                        "$.address'.$exist_adrs_indx.'.shp_zipcode","'.$shp_zipcode.'",
                        "$.address'.$exist_adrs_indx.'.latitude","'.$latitude.'",
                        "$.address'.$exist_adrs_indx.'.longitude","'.$longitude.'",
                       "$.address'.$exist_adrs_indx.'.frmtd_adrs","'.$frmtd_adrs.'",
                        "$.address'.$exist_adrs_indx.'.url","'.$url.'") WHERE id="'.$customer.'" ');
            }
    //ADDRESS EXISTING CHECK AND UPDATE END        
            $cart_list = DB::SELECT("SELECT b.price->>'$.selling_price' as actual_price,b.category,a.gen_ofr_details->>'$.offer_per' as offer_per,a.gen_ofr_details->>'$.copouncode' as copouncode,a.gen_ofr_details->>'$.genral_amnt' as genral_amnt,DATE_ADD(now(), INTERVAL b.order_details->>'$.delivery_time' DAY) as delverydate,b.pdt_shp_cats,a.* FROM cart_items a LEFT JOIN vendor_product b on a.pdt_id=b.id WHERE user_id='$customer' AND b.active='Y' AND b.approved='Approved' ");
           $subtotal     = 0;
           $final_total  = 0;
            foreach($cart_list as $item) {
                $org_price     = $item->actual_price;
                $selling_price = $item->actual_price;
                $unit_price = $item->price;
                $offer_price = 0;
                $com_val     = 0;
                $comsn_rate  = 0;
                $offerid     = '';
                $offername   = '';
                $value_type  = '';
                $value       = '';
                $total_price = $item->total_price;
             $ofr_comsn =    Sharesource::ofr_comsn_price_calculation($org_price,json_decode($item->category),$item->vendor_id,$item->pdt_id,$item->qty);    
                       $selling_price = $ofr_comsn['selling_price'];
                       $offer_price = $ofr_comsn['offer_price_dis'];
                       $offerid     = $ofr_comsn['offerid'];
                       $offername   = '';
                       $value_type  = $ofr_comsn['value_type'];
                       $value       = $ofr_comsn['value'];
                       $com_val     = $ofr_comsn['com_val'];
                       $comsn_rate  = $ofr_comsn['comsn_rate'];
                    $total_price    = $ofr_comsn['total_price'];
                    $subtotal       = $subtotal+$total_price;
                    //add delivery charge with final total
                    $final_total = $final_total+$total_price;
                    if($item->gen_ofr_details=='') {
                        $genfr_det = "json_object()";
                    }
                    else{
                        $genfr_det =  "$item->gen_ofr_details"; 
                    }
                    if($item->tax_details=='') {
                        $tax_det = "json_object()";
                    }
                    else{
                        $tax_det =  "$item->tax_details"; 
                    }
                    DB::INSERT("INSERT INTO order_details(order_number, slno, venodr_id, item_id,unit_price, org_price,commision_rate,price_inc_commision, offer_price, selling_price, offer_details, qty, total_price,gen_ofr_details,final_total_incl_genofr,tax_details,tax_price,totalprice_incl_tax,delivery_date,delivery_charge,ship_cat_id)"
                            . " VALUES ('".$odernumber."','1','$item->vendor_id','$item->pdt_id','$unit_price','$org_price','$com_val','$comsn_rate','$offer_price','$selling_price',json_object('offerid','$offerid','offername','$offername','value_type','$value_type','value','$value'),'$item->qty','$total_price',json_object('offer_per','$item->offer_per','copouncode','$item->copouncode','genral_amnt','$item->genral_amnt'),'$item->final_total_incl_genofr','$tax_det','$item->tax_price','$item->totalprice_incl_tax','$item->delverydate','$item->delivery_charge','$item->pdt_shp_cats')");
                    DB::INSERT("INSERT INTO order_status_history(order_number, item_id, sl_no, order_status, time) VALUES ('$odernumber','$item->pdt_id','1','Processing',DATE_FORMAT(now(),'%Y-%m-%d %H:%i'))");
            
                     $stk_updated =    Sharesource::stock_updation($item->pdt_id,$item->qty,'-');
                    }
            //invoice number genration
             $invoice_settings = DB::SELECT("SELECT year_invoice,invoice,DATE_FORMAT(NOW(),'%y') as month,slno FROM general_settings  WHERE year_invoice=DATE_FORMAT(NOW(),'%y')");
                    if(count($invoice_settings)==0) {
                        DB::UPDATE("UPDATE general_settings SET year_invoice=DATE_FORMAT(NOW(),'%y'),invoice=101");
                        $invoice       = 100; 
                        $year_invoice = date('y');
                    } else{
                        $invoice       = $invoice_settings[0]->invoice; 
                        $year_invoice  = $invoice_settings[0]->year_invoice;
                        DB::UPDATE("UPDATE general_settings SET invoice=invoice+1 WHERE year_invoice=DATE_FORMAT(NOW(),'%y') ");
                    }
                    $invoicenumber = $year_invoice.$invoice;
            DB::UPDATE("UPDATE order_master SET invoice_number=$invoicenumber,final_total=$delivery_charge+(SELECT SUM(totalprice_incl_tax) FROM order_details  WHERE order_number='$odernumber' ),total_tax_price=(SELECT SUM(tax_price) FROM order_details WHERE order_number='$odernumber') WHERE order_number='$odernumber' ");
            if($paymenthod=='COD') {
                 DB::DELETE("DELETE FROM cart_items WHERE user_id='$customer' ");
                 DB::DELETE("DELETE FROM cart_master WHERE userid='$customer'");
            }
            if($paymenthod=='Online'){
                
            }
            DB::select('call proc_deliverycharge_split("'.$odernumber.'")');
            $msg =  'placed';
            return response::json(compact('msg','odernumber'));
            }
            else{
                $msg = 'invalid';
                return response::json(compact('msg','odernumber'));
            }
           
    }
    public function stock_updation(Request $request) {
        $itemid  = $request['itemid'];
        $method  = $request['method'];return $method;
        $itemqty = $request['itemqty'];return $itemqty;
        DB::UPDATE("UPDATE vendor_product SET inventory_details->>'$.current_stock_count'=inventory_details->>'$.current_stock_count' $method $itemqty WHERE id=$itemid");
        return "updated";
    }
    public function get_payments(Request $request) {
        $payment_method = DB::SELECT("SELECT * FROM payment_method WHERE active='Y' ");
        return $payment_method;
    }
    public function take_order_details($orderid) {
       $ordermaster = DB::SELECT("SELECT DISTINCT(concat(b.gen_ofr_details->>'$.copouncode','@',b.gen_ofr_details->>'$.offer_per')) as copouncode,SUM(b.gen_ofr_details->>'$.genral_amnt') as genral_amnt,SUM(b.tax_price) as taxtotal,a.order_number ,a.order_date,a.userid,a.sub_total,a.final_total, a.shipping_address->>'$.shp_fname' as shp_fname,a.shipping_address->>'$.shp_lname' as shp_lname,a.shipping_address->>'$.shp_phone' as shp_phone, a.shipping_address->>'$.frmtd_adrs' as frmtd_adrs, a.shipping_address->>'$.shp_address' as shp_address,a.shipping_address->>'$.shp_city' as shp_city,a.shipping_address->>'$.shp_lanmark' as shp_lanmark,a.shipping_address->>'$.shp_state' as shp_state,a.shipping_address->>'$.shp_country' as shp_country, a.shipping_address->>'$.shp_zipcode' as shp_zipcode,a.total_details->>'$.delivery_charge' as delivery_charge FROM order_master a LEFT JOIN order_details b on a.order_number=b.order_number  WHERE a.order_number='$orderid' GROUP BY a.order_number  "); 
       $order_detail = DB::SELECT("SELECT  b.id as hints,a.qty,b.name,b.brand_details->>'$.brand_name' as brand_name,b.pdt_details->>'$.model' as model,c.image_url,c.alt_tag,a.total_price,a.current_status as current_status,a.crnt_status_order as status_order,a.delivery_date,a.status_limit,a.ship_details->>'$.trck_no' as trck_no,a.ship_details->>'$.service_id' as service_id,a.ship_details->>'$.remarks' as remarks FROM order_details a LEFT JOIN vendor_product b on a.item_id=b.id LEFT JOIN vendor_pdt_image c on c.pdt_id=a.item_id AND c.main_img='Y'   WHERE a.order_number='$orderid' GROUP by a.item_id ");
      $i=0;$order_details = array();
       foreach($order_detail as $details){
         $order_history = DB::SELECT("SELECT DISTINCT (order_status) as order_status,item_id FROM order_status_history  WHERE order_number ='$orderid' AND item_id=$details->hints ");
         $serviceid = $details->service_id;
         if($serviceid) {
              $servicve_master = DB::SELECT("SELECT company_name,tracking_website FROM logistic_company_master WHERE id='$serviceid' ");
              $logist_co    = $servicve_master[0]->company_name;
              $track_web    = $servicve_master[0]->tracking_website;
              $track_number = $details->trck_no;
              $shp_remarks  = $details->remarks;
         } else {
              $logist_co    = "";
              $track_web    = "";
              $track_number = "";
              $shp_remarks    = "";
         }
         $order_details[$i]=[
             'hints'=>$details->hints,
             'qty'=>$details->qty,
             'name'=>$details->name,
             'brand_name'=>$details->brand_name,
             'model'=>$details->model,
             'image_url'=>$details->image_url,
             'alt_tag'=>$details->alt_tag,
             'total_price'=>$details->total_price,
             'current_status'=>$details->current_status,
             'status_order'=>$details->status_order,
             'delivery_date'=>$details->delivery_date,
             'status_limit'=>$details->status_limit,
             'logist_co'=>$logist_co,
             'track_web'=>$track_web,
             'track_no'=>$track_number,
             'shp_remarks'=>$shp_remarks,
             'order_history'=>$order_history,
                 ];
         $i++;
       }
       $ord_status_list = DB::SELECT("SELECT os_status,os_display_order FROM order_status_master ORDER BY os_status_id");
     
       return response::json(compact('ordermaster','order_details','ord_status_list'));
    }
    public function take_order_history(Request $request) {
        $userid = $request['logged_userid'];
        $order_history = DB::SELECT("SELECT a.order_number, a.order_date,a.final_total,count(b.order_number) as total_items FROM order_master a LEFT JOIN order_details b on a.order_number=b.order_number WHERE a.userid=$userid GROUP BY order_number DESC ");
        return $order_history;
    }
    public function take_existing_adress_list($userid) {
        $adr_length = DB::SELECT("SELECT IFNULL(ship_adress_list->>'$.next_index',0) as ship_next_index FROM `customer_list` WHERE id='$userid' ");
        $shipping_address = array();
        
        $shp_adr_length = $adr_length[0]->ship_next_index;
        $a=0;
        if($shp_adr_length!=0){
            for($i=1;$i<$shp_adr_length;$i++) {
               $address =  DB::SELECT("SELECT 
                            ship_adress_list->>'$.address$i.url' as url,
                            ship_adress_list->>'$.address$i.latitude' as latitude,
                            ship_adress_list->>'$.address$i.shp_city' as shp_city,
                            ship_adress_list->>'$.address$i.longitude' as longitude,
                            ship_adress_list->>'$.address$i.shp_email' as shp_email,
                            ship_adress_list->>'$.address$i.shp_fname' as shp_fname,
                            ship_adress_list->>'$.address$i.shp_lname' as shp_lname,
                            ship_adress_list->>'$.address$i.shp_phone' as shp_phone,
                            ship_adress_list->>'$.address$i.shp_state' as shp_state,
                            ship_adress_list->>'$.address$i.frmtd_adrs' as frmtd_adrs,
                            ship_adress_list->>'$.address$i.shp_address' as shp_address,
                            ship_adress_list->>'$.address$i.shp_company' as shp_company,
                            ship_adress_list->>'$.address$i.shp_country' as shp_country,
                            ship_adress_list->>'$.address$i.shp_lanmark' as shp_lanmark,
                            ship_adress_list->>'$.address$i.shp_zipcode' as shp_zipcode
                            FROM customer_list WHERE id='$userid'");
               if($address[0]->shp_fname!='') {
                   $shipping_address[$a] = [
                   'aid'=>$i,
                   'url'=>$address[0]->url,
                   'latitude'=>$address[0]->latitude,
                   'shp_city'=>$address[0]->shp_city,
                   'longitude'=>$address[0]->longitude,
                   'shp_email'=>$address[0]->shp_email,
                   'shp_fname'=>$address[0]->shp_fname,
                   'shp_lname'=>$address[0]->shp_lname,
                   'shp_phone'=>$address[0]->shp_phone,
                   'shp_state'=>$address[0]->shp_state,
                   'frmtd_adrs'=>$address[0]->frmtd_adrs,
                   'shp_address'=>$address[0]->shp_address,
                   'shp_company'=>$address[0]->shp_company,
                   'shp_country'=>$address[0]->shp_country,
                   'shp_lanmark'=>$address[0]->shp_lanmark,
                   'shp_zipcode'=>$address[0]->shp_zipcode
                   
                       ];
                   $a++;
               }
               
            }
        }
        return response::json(compact('shipping_address'));

    }
    public function global_search(Request $request) {
        $search_term = $request['search_term'];
        //        $pdt_list = DB::SELECT("SELECT id,name FROM vendor_product WHERE active='Y' AND approved='Approved' AND name LIKE '$search_term%' ");
//        $cat_list = DB::SELECT("SELECT cat_name,id FROM vendor_categories WHERE active='Y' AND cat_name LIKE '$search_term%' ");
//        $brand_list = DB::SELECT("SELECT brand_name FROM brand_master WHERE active='Y' AND brand_name LIKE '$search_term%' ");
        $pdt_list = DB::SELECT("SELECT  id,name,order_item,1 as type  FROM (
            SELECT  id,name,1 as order_item FROM vendor_product WHERE active='Y' AND approved='Approved' AND name = '$search_term' 
            UNION ALL
           SELECT  id,name,2 as order_item FROM vendor_product WHERE active='Y' AND approved='Approved' AND name LIKE '$search_term%'
            UNION ALL
           SELECT  id,name,3 as order_item FROM vendor_product WHERE active='Y' AND approved='Approved' AND name LIKE '%$search_term'
            UNION ALL
           SELECT  id,name,4 as order_item FROM vendor_product WHERE active='Y' AND approved='Approved' AND name LIKE '%$search_term%' ) a 
                 GROUP BY id ORDER BY order_item, name ASC");
        $cat_list = DB::SELECT("SELECT  id,cat_name as name,cat_item as order_item,2 as type FROM (
            SELECT cat_name,id,1 as cat_item FROM vendor_categories WHERE active='Y' AND cat_name= '$search_term%'  
            UNION ALL
           SELECT cat_name,id,2 as cat_item FROM vendor_categories WHERE active='Y' AND cat_name LIKE '$search_term%' 
            UNION ALL
          SELECT cat_name,id,3 as cat_item FROM vendor_categories WHERE active='Y' AND cat_name LIKE '%$search_term' 
            UNION ALL
           SELECT cat_name,id,4 as cat_item FROM vendor_categories WHERE active='Y' AND cat_name LIKE '%$search_term%'  ) a 
                 GROUP BY id ORDER BY cat_item, cat_name ASC");
        $brand_list = DB::SELECT("SELECT  id,brand_name as name,br_item as order_item,3 as type  FROM (
            SELECT brand_name,id,1 as br_item FROM brand_master WHERE active='Y' AND brand_name= '$search_term%'  
            UNION ALL
           SELECT brand_name,id,2 as br_item FROM brand_master WHERE active='Y' AND brand_name LIKE '$search_term%' 
            UNION ALL
          SELECT brand_name,id,3 as br_item FROM brand_master WHERE active='Y' AND brand_name LIKE '%$search_term' 
            UNION ALL
           SELECT brand_name,id,4 as br_item FROM brand_master WHERE active='Y' AND brand_name LIKE '%$search_term%'  ) a 
                 GROUP BY id ORDER BY br_item, brand_name ASC");
        $new_array_test = array_merge($cat_list,$pdt_list,$brand_list);
             return response::json(compact('new_array_test','cat_list','pdt_list','brand_list'));
        //return ['pdt_list'=>$pdt_list,'cat_list'=>$cat_list];
    }
    public function pdt_cats_search(Request $request) {
        $search_cat = $request['search_cat'];
        $search_pdt = $request['search_pdt'];
        $pdt_list = array();
       $pdt_list = DB::SELECT("SELECT id,name FROM vendor_product WHERE active='Y' AND approved='Approved' AND name LIKE '%$search_pdt%' AND json_contains(category,'[$search_cat]') ");
        return response::json(compact('pdt_list'));
    }
     public function submit_bulk_quote(Request $request) {
        $logged_userid = $request['logged_userid'];
        if(!$logged_userid){
            $logged_userid ="NULL";
        }
        $productid     = $request['productid'];
        $product_name  = $request['product_name'];
        $blk_name      = $request['blk_name'];
        $blk_phnnumber = $request['blk_phnnumber'];
        $blk_email     = $request['blk_email'];
        $blk_qty       = $request['blk_qty'];
        $blk_date      = date('Y-m-d',strtotime($request['blk_date']));;
        $blk_msg       = $request['blk_msg'];
        DB::INSERT("INSERT INTO bulk_purchase_quote(userid, pid, contact_name, contact_number, contact_email, qty, blk_date, remarks) VALUES ($logged_userid,'$productid','$blk_name','$blk_phnnumber','$blk_email','$blk_qty','$blk_date','$blk_msg')");
       $this->rfq_blk_rqst($product_name,$blk_qty,$request['blk_date'],'',$blk_name,$blk_email,$blk_phnnumber,$blk_msg,'');
        $msg =  "Request For Bulk Qty Has been Submitted. We will return back to you with in 8 Hours";
         return response::json(compact('msg')); 
    }
    public function rfq_blk_rqst($rfq_pdts,$rfq_qty,$rfq_date,$rfq_company,$rfq_cname,$rfq_cemail,$rfq_cphone,$rfq_remarks,$image_url) {
         $getsiteurl  = Datasource::getsiteurl();
                    $getadminurl = Datasource::geturl();
                    $varskartteam = $this->toaddress;
                    $arr = ["varskartteam"=>$varskartteam,'getsiteurl'=>$getsiteurl,'getadminurl'=>$getadminurl,'rfq_pdts'=>$rfq_pdts,'rfq_qty'=>$rfq_qty,'rfq_date'=>$rfq_date,'rfq_company'=>$rfq_company,'rfq_cname'=>$rfq_cname,'rfq_cemail'=>$rfq_cemail,'rfq_cphone'=>$rfq_cphone,'rfq_remarks'=>$rfq_remarks,'image_url'=>$image_url];
                    $mail1 =  Mail::send('Mailer_templates.rfq_bulk_reqst.enquiry_mail',
                                $arr, function ($message) use ($varskartteam,$getsiteurl,$getadminurl,$rfq_pdts,$rfq_qty,$rfq_date,$rfq_company,$rfq_cname,$rfq_cemail,$rfq_cphone,$rfq_remarks,$image_url) {
                                $message->to($varskartteam)
                                        ->subject('New Bulk Order Request');
                                });
                    $mail2 =  Mail::send('Mailer_templates.rfq_bulk_reqst.thank_you',
                                $arr, function ($message) use ($varskartteam,$getsiteurl,$getadminurl,$rfq_pdts,$rfq_qty,$rfq_date,$rfq_company,$rfq_cname,$rfq_cemail,$rfq_cphone,$rfq_remarks) {
                                $message->to($rfq_cemail)
                                        ->subject('Thank You For Your Bulk Request');
                                });
    }
    public function submit_rfq_form(Request $request) {
        $rfq_cat      = $request['rfq_cat'];
        $rfq_pdts     = $request['rfq_pdts'];
        $rfq_qty      = $request['rfq_qty'];
        $rfq_date     = date('Y-m-d',strtotime($request['rfq_date']));
        $rfq_company  = $request['rfq_company'];
        $rfq_cname    = $request['rfq_cname'];
        $rfq_cemail   = $request['rfq_cemail'];
        $rfq_cphone   = $request['rfq_cphone'];
        $rfq_remarks  = $request['rfq_remarks'];
        $rfq_attach   = $request['rfq_attach'];
        $timeDate    = date("jmYhis") . rand(991, 9999);
        $date        = date('Y-m-d');

        if($rfq_attach == "" || $rfq_attach=="undefined")
        {
            $image_url = null;
        }
        else
        {
            $extension =$request->file('rfq_attach')->getClientOriginalExtension();
            $filename = rand(11111111, 99999999). '.' . $extension;
            $image_url = $request->file('rfq_attach')->move('uploads/rfq_attachments/', $filename);
        }

//        $pdt_exist = DB::SELECT("SELECT id FROM vendor_product WHERE name='$rfq_pdts' AND json_contains(category,'[$rfq_cat]') ");
//        if(count($pdt_exist)!=0) {
//            $pdt_id = $pdt_exist[0]->id;
//             }
//        else{
//            $msg ='pdt_not_exist';
//        }
                   DB::INSERT("INSERT INTO rfq_form(pdt_name, qty,rfq_date, company_name, contact_name, email_id, contact_number, attachment, overview) VALUES ('$rfq_pdts','$rfq_qty','$rfq_date','$rfq_company','$rfq_cname','$rfq_cemail','$rfq_cphone','$image_url','$rfq_remarks')");
                   $this->rfq_blk_rqst($rfq_pdts,$rfq_qty,$request['rfq_date'],$rfq_company,$rfq_cname,$rfq_cemail,$rfq_cphone,$rfq_remarks,$image_url);
                   $msg='added';
       
         return response::json(compact('msg'));
    }
    public function product_details(Request $request) {
        $pid        = $request['pdtindex'];
        $crnt_crtid = $request['crnt_crtid'];
        $convertion_rate = 1;
        $currency_code = 'AED';
        if($crnt_crtid!='') {
            $convrtn_rate_info = DB::SELECT("SELECT convertion_rate,currency_short_code FROM conversion_rate WHERE id='$crnt_crtid' ");
            $convertion_rate = $convrtn_rate_info[0]->convertion_rate;
            $currency_code = $convrtn_rate_info[0]->currency_short_code;
        }
        $pdt_details = DB::SELECT("SELECT name,vendor_id,review_details as rating,
                     order_details->>'$.packing_qty' as packing_qty,
                     order_details->>'$.unit' as unit,
                     order_details->>'$.piece_per_unit' as piece_per_unit,
                     order_details->>'$.delivery_time' as delivery_time,
                     price->>'$.selling_price' as pdt_price,
                    pdt_details->>'$.sku' as sku,
                    pdt_details->>'$.code' as code,
                    pdt_details->>'$.model' as model,
                    pdt_details->>'$.series' as series,
                    pdt_details->>'$.tagline' as tagline,
                    pdt_details->>'$.material' as material,
                    brand_details->>'$.brand_name' as brand_name,
                     b.brand_orgin,
                    description,warranty_info,features,return_policy,catelog_url,youtube_url,category,vendor_id,specification_details->>'$.next_index' as details_count,
                    inventory_details->>'$.current_stock_count' as current_stock_count,
                    order_details->>'$.maximum_order_qty' as maximum_order_qty
                     FROM vendor_product  LEFT JOIN brand_master b on trim(b.brand_name)=trim(vendor_product. brand_details->>'$.brand_name') WHERE vendor_product.id=$pid");
        $sp_count = $pdt_details[0]->details_count;
        $dynamic_features = '';$i=0;
        if($sp_count!=0 || $sp_count!=''){
            for($s=1;$s<$sp_count;$s++){
                $smaster_data = DB::SELECT("SELECT a.specification_details->>'$.sp_$s.value' as svalue,b.id,b.title FROM vendor_product a LEFT JOIN specification_master b on a.specification_details->>'$.sp_$s.id'=b.id WHERE a.id=$pid");
                $id     = $smaster_data[0]->id;
                $title  = $smaster_data[0]->title;
                $svalue = $smaster_data[0]->svalue;
                if($id!='') {
                    $i++;
                    $dynamic_features .="<tr class='last odd'>";
                            $dynamic_features .="<td><strong>".$title."</strong></td>";
                            $dynamic_features .="<td  class='data last'>".$smaster_data[0]->svalue."</td>";
                    $dynamic_features .="</tr>";
                }
                
            }
        }
        $pdt_price = $pdt_details[0]->pdt_price;
        $selling_price = $pdt_price;
        $offer_value = 0;
        $offer_type = 0;
        $offer_price =0;
        $offerid  =0;
       $ofr_comsn =    Sharesource::ofr_comsn_price_calculation($pdt_price,json_decode($pdt_details[0]->category),$pdt_details[0]->vendor_id,$pid,1);    
            $selling_price = $ofr_comsn['selling_price'];
            $offer_value   = $ofr_comsn['value'];
            $offer_type    = $ofr_comsn['value_type'];
            $offer_price   = $ofr_comsn['offer_price'];
            $offerid       = $ofr_comsn['offerid'];
            $comsn_rate       = $ofr_comsn['comsn_rate'];
            $converted_comsn_rate          = $ofr_comsn['comsn_rate']*$convertion_rate;
            $converted_selling_price       = $ofr_comsn['selling_price']*$convertion_rate;
            

        $pdt_categories = json_decode($pdt_details[0]->category);
        $i=0;
        $json_catlist = '';
        foreach ($pdt_categories as $cats) {
            $json_catlist .=" json_contains(a.category,'[$cats]') ||";
        }
        $json_catlist = substr($json_catlist, 0,-2);
        $related_items = array();
        $pdt_main_image = array();
        $pdt_images = array();
        $related_items = DB::SELECT("SELECT a.id,a.name,a.price->>'$.selling_price' as selling_price,IFNULL(b.image_url,'uploads/dummy/no-image.jpg') as image_url FROM vendor_product a LEFT JOIN vendor_pdt_image b on a.id=b.pdt_id AND b.main_img='Y' WHERE a.active='Y' AND a.approved='approved' AND a.id!=2  AND ($json_catlist) AND a.id!='$pid' AND a.price->>'$.selling_price' !=0 AND a.inventory_details->>'$.current_stock_count'>=1 ");
        $pdt_main_image = DB::SELECT("SELECT IFNULL(image_url,'uploads/dummy/no-image.jpg') as image_url,IFNULL(img_large_url,'uploads/dummy/no-image.jpg') as img_large_url,alt_tag FROM vendor_pdt_image WHERE pdt_id=$pid AND active='Y'  AND main_img='Y' LIMIT 1 ");
        $pdt_images = DB::SELECT("SELECT sl_no,image_url,img_large_url,alt_tag FROM vendor_pdt_image WHERE pdt_id=$pid AND active='Y'");
       return response::json(compact('currency_code','converted_comsn_rate','converted_selling_price','selling_price','comsn_rate','pdt_details','offer_value','offer_type','related_items','offer_price','offerid','dynamic_features','pdt_images','pdt_main_image'));
    }
    public function pdt_cart_info(Request $request) {
        $cart_info = array();
        $csid   = $request['cookiesessionid'];
        $userid = $request['logged_userid'];
        $condition= '';
        if($userid !='') {
            $condition = "user_id = $userid";
        }
        else {
            $condition = "cookie_id = '".$csid."'";
        }
        $pdtid  = $request['productid'];
        if($csid!='' || $userid!='' || $userid!='undefined') {
                    $cart_info = DB::SELECT("SELECT * FROM cart_items WHERE pdt_id=$pdtid AND $condition");

        }
        return response::json(compact('cart_info'));
    }
    public function item_to_compare(Request $request) {
       $cookied_id = $request['cookiesessionid'];
       $productid   = $request['productid'];
       $msg ='Item Is not In Same Category';
       $is_exist = DB::SELECT("SELECT a.*,b.category FROM product_comparison_list a LEFT JOIN vendor_product b on a.pdt_id=b.id WHERE a.cookie_id='".$cookied_id."' ");
       if(count($is_exist)==0) {
           DB::INSERT("INSERT INTO product_comparison_list(cookie_id, sl_no, pdt_id) VALUES ('$cookied_id',1,'$productid')");
           $msg = 'Item Added To Compare';
       }
       else if(count($is_exist)==4) {
          $msg = "Only 4 Item Can be compared";
       }
       else{
           $is_related = 'N';$i=0;
           $pdt_exist = DB::SELECT("SELECT * FROM product_comparison_list WHERE cookie_id='$cookied_id' AND pdt_id='$productid'");
           if(count($pdt_exist)==0){
                    foreach($is_exist as $product) {
                        $i++;
                        $categories = json_decode($product->category);
                        $json_catlist = '';
                         foreach ($categories as $cats) {
                             $json_catlist .=" json_contains(category,'[$cats]') ||";
                         }
                         $json_catlist = substr($json_catlist, 0,-2);
                        $cat_relation =  DB::SELECT("SELECT id FROM vendor_product WHERE id=$productid AND ($json_catlist)");
                        if(count($cat_relation)!=0) {
                           DB::INSERT("INSERT INTO product_comparison_list(cookie_id, sl_no, pdt_id) VALUES ('$cookied_id',1,'$productid')");
                          $msg = 'Item Added To Compare';
                           break;
                        }


               }
           }
           else  if(count($pdt_exist)!=0){
                  $msg =  'Already Added To Compare';
                   return response::json(compact('msg'));
               }
          
           else if($is_related=='N') {
               $msg = 'Item Is not In Same Category';
           }
           
       }
       
       return response::json(compact('msg'));
    }
    public function comparison_result($cookied_id) {
        //$cookied_id = $request['cookiesessionid'];
        $i=0;
        $comp_result = array();
        $all_json_cat_list ='';
       $comp_details =  DB::SELECT("SELECT b.id,b.name,b.category,b.specification_details->>'$.next_index' as next_index,b.specification_index,b.vendor_id,b.pdt_details->>'$.sku' as sku,b.pdt_details->>'$.code' as code,b.pdt_details->>'$.model' as model,b.pdt_details->>'$.series' as series,b.pdt_details->>'$.tagline' as tagline,b.pdt_details->>'$.material' as material,b.pdt_details->>'$.size.width' as width,b.pdt_details->>'$.size.length' as length,b.pdt_details->>'$.size.breadth' as breadth,b.pdt_details->>'$.size.weight' as weight,b.brand_details->>'$.brand_name' as brand_name,b.brand_details->>'$.brand_orgin' as brand_orgin,IFNULL(description,'-') as description,order_details->>'$.unit' as unit,order_details->>'$.packing_qty' as packing_qty,order_details->>'$.delivery_time' as delivery_time,order_details->>'$.packing_charge' as packing_charge,order_details->>'$.piece_per_unit' as piece_per_unit,order_details->>'$.shipping_charge' as shipping_charge,order_details->>'$.shipping_charge' as shipping_charge,order_details->>'$.maximum_order_qty' as maximum_order_qty,order_details->>'$.minimum_order_qty' as minimum_order_qty,IFNULL(return_policy,'-') as return_policy,price->>'$.selling_price' as selling_price,price->>'$.marketing_price' as marketing_price,IFNULL(warranty_info,'-') as warranty_info,c.image_url FROM product_comparison_list a LEFT JOIN vendor_product b on a.pdt_id=b.id LEFT JOIN vendor_pdt_image c on c.pdt_id=a.pdt_id AND c.main_img='Y' WHERE a.cookie_id='$cookied_id' ");
            foreach($comp_details as $result) {
                $dynamic_specifh = "";
                $dynamic_specif = "";
                $dys= array();
                        $categories = json_decode($result->category);
                        $json_catlist = '';
                        foreach ($categories as $cats) {
                             $json_catlist .=" json_contains(category,'[$cats]') ||";
                             $all_json_cat_list .=" json_contains(category,'[$cats]') ||";
                        }
//                        $json_catlist = substr($json_catlist, 0,-2);
//                        $specfic_master = DB::SELECT("SELECT  id,title FROM specification_master WHERE $json_catlist AND active='Y'");
//                        $sp=0;
                       
//                $dynamic_specif = '';
//               $specifc_next_index = $result->next_index;
//               for($n=1;$n<$specifc_next_index;$n++) {
//                   $sp_details = DB::SELECT("SELECT a.specification_details->>'$.sp_$n.value' as specificvalue,b.title FROM vendor_product a LEFT JOIN specification_master b on b.id=a.specification_details->>'$.sp_$n.id' WHERE a.id='$result->id'");
//                    $dynamic_specif = "";
//               }
                         
                $offer_price = 0;
                $value_type='';
                $value=0;
                $offerid =0;
                $pdt_price     = $result->selling_price;
                $selling_price = $result->selling_price;
             $ofr_comsn =    Sharesource::ofr_comsn_price_calculation($pdt_price,json_decode($result->category),$result->vendor_id,$result->id,1);    

               $comp_result[$i]  = [
                            'id' =>$result->id,
                            'name' =>$result->name,
                            'vendor_id' =>$result->vendor_id,
                            'sku' =>$result->sku,
                            'code' =>$result->code,
                            'model' =>$result->model,
                            'series' =>$result->series,
                            'tagline' =>$result->tagline,
                            'material' =>$result->material,
                            'dynamic_specif' =>$dynamic_specif,
                            'dynamic_specifh' =>$dynamic_specifh,
                            'dys' =>$dys,
                            'width' =>$result->width,
                            'length' =>$result->length,
                            'breadth' =>$result->breadth,
                            'weight' =>$result->weight,
                            'brand_name' =>$result->brand_name,
                            'brand_orgin' =>$result->brand_orgin,
                            'description' =>$result->description,
                            'unit' =>$result->unit,
                            'packing_qty' =>$result->packing_qty,
                            'delivery_time' =>$result->delivery_time,
                            'packing_charge' =>$result->packing_charge,
                            'piece_per_unit' =>$result->piece_per_unit,
                            'shipping_charge' =>$result->shipping_charge,
                            'maximum_order_qty' =>$result->maximum_order_qty,
                            'minimum_order_qty' =>$result->minimum_order_qty,
                            'return_policy' =>$result->return_policy,
                            'marketing_price' =>$result->marketing_price,
                            'warranty_info' =>$result->warranty_info,
                            'image_url' =>$result->image_url,
                            'offerid'=>$ofr_comsn['offerid'],
                            'offer_price'=>$ofr_comsn['offer_price'],
                            'value_type'=>$ofr_comsn['value_type'],
                            'value'=>$ofr_comsn['value'],
                            'pdt_price'=>$pdt_price,
                            'selling_price'=>$ofr_comsn['selling_price'],
                            ];
                            $i++;
          }
          $all_json_cat_list = substr($all_json_cat_list, 0,-2);
           $specfic_master = DB::SELECT("SELECT  id,title FROM specification_master WHERE $all_json_cat_list AND active='Y'");
           foreach($specfic_master as $spm){
               
           }
       return response::json(compact('comp_result'));
    }
    public function remove_from_comparison(Request $request) {
        $productid = $request['productid'];
        $cookiesessionid = $request['cookiesessionid'];
        DB::DELETE("DELETE FROM product_comparison_list WHERE pdt_id='$productid' AND cookie_id='$cookiesessionid' ");
        return "removed";
    }
    public function remove_address(Request $request) {
        $index = $request['index'];
        $userid = $request['userid'];
        DB::UPDATE("UPDATE `customer_list` SET ship_adress_list=JSON_REMOVE(ship_adress_list, '$.address$index'),ship_adress_list=json_set(ship_adress_list,'$.deleted_index',$index) WHERE id=$userid");
        return "removed";
    }
    public function take_all_categories(Request $request) {
        $main_cats = DB::SELECT("SELECT cat_name,id FROM vendor_categories WHERE active='Y' AND parent_id=0");
        $m=0;
        $catlist = array();
        foreach($main_cats as $cats) {
            $cat_id = $cats->id;
            $cat_name = $cats->cat_name;
            $sub_cats = DB::SELECT("SELECT cat_name,id,image_details->>'$.image_alt' as image_alt,image_details->>'$.image_link' as image_link FROM vendor_categories WHERE active='Y' AND parent_id='$cat_id' ");
            if(count($sub_cats)!=0) {
                 $catlist[$m] = ['cat_name'=>$cat_name,'sub_cats'=>$sub_cats];
                 $m++;
            }
        }
        return $catlist;
    }
    public function active_sub_unbdercat(Request $request) {
        $main_cats = DB::SELECT("SELECT cat_name,id FROM vendor_categories WHERE active='Y' AND parent_id=0 ");
        $i=0;
        $catlist = array();
        foreach($main_cats as $cats) {
            $sub_cats = array();
          $sub_cats = DB::SELECT("SELECT cat_name,id FROM vendor_categories WHERE active='Y' AND parent_id='$cats->id' ");
          $catlist[$i]=[
                    'cat_id'=>$cats->id,
                    'cat_name'=>$cats->cat_name,
                    'sub_cats'=>$sub_cats
                  ];
          $i++;
        }
        return $catlist;
    }
    public function submit_new_pdtqstn(Request $request) {
        $logged_userid  = $request['logged_userid'];
        $new_qstn       = $request['new_qstn'];
        $productid      = $request['productid'];
        DB::INSERT("INSERT INTO product_question(pdt_id,user_id, question, entry_time,answers,last_answer_time) VALUES ('$productid','$logged_userid','$new_qstn',now(),json_object('next_index','1'),now())");
        $msg =  "Question Has Been Submitted For Review";
        return response::json(compact('msg'));
    }
    public function view_qst_ans($pdtid) {
        $qstn_ans = DB::SELECT("SELECT pdt_id,id,question,answers->>'$.next_index' as next_index FROM product_question WHERE pdt_id='$pdtid' AND active='Y' ");
        $q=0;$qestion_list = array();
        foreach($qstn_ans as $qstn){
            $limit = $qstn->next_index;
            $answer_list = array();
            $a=0;
            for($l=1;$l<$limit;$l++){
              $answer_info =   DB::SELECT("SELECT a.answers->>'$.ans$l.answer' as answer,concat(b.c_fname,' ',b.c_lname) as username FROM product_question a LEFT JOIN customer_list b on a.answers->>'$.ans$l.userid'=b.id WHERE a.answers->>'$.ans$l.active'='Y' AND a.id='$qstn->id'");
              if(count($answer_info)!=0 ){
                  $answer_list[$a] = ['answer'=>$answer_info[0]->answer,'username'=>$answer_info[0]->username];
                  $a++;
              }
               
            }
            $qestion_list[$q] = ['id'=>$qstn->id,'pdt_id'=>$qstn->pdt_id,'question'=>$qstn->question,'answers_list'=>$answer_list]; 
            $q++;
        }
        return response::json(compact('qestion_list'));
    }
    public function view_all_ans($qid) {
        $qstn_ans = DB::SELECT("SELECT pdt_id,id,question,answers->>'$.next_index' as next_index FROM product_question WHERE id='$qid' AND active='Y' ");
        $q=0;
        $qestion_list = array();
        
        foreach($qstn_ans as $qstn){
            $limit = $qstn->next_index;
            $answer_list = array();
            for($l=1;$l<$limit;$l++){
              $answer_info =   DB::SELECT("SELECT a.answers->>'$.ans$l.answer' as answer,concat(b.c_fname,' ',b.c_lname) as username FROM product_question a LEFT JOIN customer_list b on a.answers->>'$.ans$l.userid'=b.id WHERE a.answers->>'$.ans$l.active'='Y' AND a.id='$qstn->id'");
              if(count($answer_info)!=0) {
               $answer_list[$l-1] = ['answer'=>$answer_info[0]->answer,'username'=>$answer_info[0]->username];
              }
            }
            $qestion_list[$q] = ['id'=>$qstn->id,'pdt_id'=>$qstn->pdt_id,'question'=>$qstn->question,'answers_list'=>$answer_list]; 
            $q++;
        }
        return response::json(compact('qestion_list'));
    }
    public function submit_new_pdtanswer(Request $request) {
        $logged_userid = $request['logged_userid'];
        $new_ansr      = $request['new_ansr'];
        $productid     = $request['productid'];
        $qstnid        = $request['qstnid'];
        $next_index_info = DB::SELECT("SELECT answers->>'$.next_index' as next_index FROM product_question WHERE id='$qstnid' ");
        $next_index = $next_index_info[0]->next_index;
        $new_index = $next_index+1;
        DB::UPDATE("UPDATE product_question SET answers=json_set(answers,'$.ans$next_index',json_object('answer','$new_ansr','userid','$logged_userid','entry_time',now(),'active','N')),answers=json_set(answers,'$.next_index',$new_index),last_answer_time=now() WHERE id='$qstnid' ");
        $msg =  "Answer Has Been Submitted For Review";
        return response::json(compact('msg')); 
    }
   
    public function featch_delivery_charge(Request $request) {
        $city     = $request['city'];
        $userid   = $request['logeduserid'];
        $delivery_charge = 'No';
        $result          = 'Delivery Out of Bound for items - ';
        $restricted_pdt = "";
        //$delivery_info = DB::SELECT("SELECT rate FROM consolidated_delivery_charges WHERE to_city='$city' AND active='Y' ");
        $delivery_info = DB::SELECT("SELECT a.pdt_id,IFNULL(c.rate,0) as rate FROM cart_items a LEFT JOIN vendor_product b on a.pdt_id=b.id LEFT JOIN consolidated_delivery_charges c on c.ship_category=b.pdt_shp_cats WHERE a.user_id='$userid' AND lower(c.to_city)='".strtolower($city)."' AND c.active='Y' ");
        foreach($delivery_info as $info){
                DB::UPDATE("UPDATE cart_items SET delivery_charge=$info->rate WHERE pdt_id=$info->pdt_id AND user_id='$userid' ");
            }
        $del_chrg_info = DB::SELECT("SELECT b.name FROM cart_items a LEFT JOIN vendor_product b on a.pdt_id=b.id WHERE a.delivery_charge='NIL' AND a.user_id='$userid' "); 
        if(count($del_chrg_info)!=0) {
            $delivery_charge='No';
            foreach($del_chrg_info as $list) {
                $restricted_pdt .=$list->name.", ";
            }
            $restricted_pdt          .= ' Please Contact Varskart Team';
        }
        else{
            $charge_info = DB::SELECT("select sum(delivery_charge) as delivery_charge from 
                                        (SELECT c.rate as delivery_charge FROM cart_items a LEFT JOIN vendor_product b on a.pdt_id=b.id LEFT JOIN consolidated_delivery_charges c on c.ship_category=b.pdt_shp_cats WHERE a.user_id='$userid' AND lower(c.to_city)='".strtolower($city)."' AND c.active='Y' group by  c.id ) A ");
            $delivery_charge = $charge_info[0]->delivery_charge;
            $result = '';
        }
         return response::json(compact('result','delivery_charge','restricted_pdt')); 
    }
    public function featch_pdt_reviews($pid) {
        $review_details = array();
        $review_details = DB::SELECT("SELECT IFNULL(a.review_details->>'$.rating',0) as rating,a.review_details->>'$.remarks' as remarks,a.review_details->>'$.entry_date' as entry_date,concat(c.c_fname,' ',c.c_lname) as username FROM order_details a LEFT JOIN order_master b on a.order_number=b.order_number LEFT JOIN customer_list c on c.id=b.userid WHERE a.item_id='$pid' AND a.review_details->>'$.active'='Y' AND a.review_details->>'$.rating' IS NOT NULL");
        return response::json(compact('review_details')); 
    }
    public function pdt_review_post(Request $request) {
        $rating      = $request['rating'];
        $review_desc = $request['review_desc'];
        $pdt_id      = $request['review_pdt_id'];
        $orderid     = $request['orderid'];
        DB::UPDATE("UPDATE order_details SET review_details=json_object('rating','$rating','remarks','$review_desc','entry_date',now(),'active','N') WHERE order_number='$orderid' AND item_id='$pdt_id'");
        return 'updated';
    }
   
    public function pdt_review_byorder(Request $request) {
       $orderid = $request['orderid'];
       $pdtid   = $request['pdtid'];
       $my_pdt_review= array();
       $my_pdt_review = DB::SELECT("SELECT IFNULL(a.review_details->>'$.rating',0) as rating,a.review_details->>'$.remarks' as remarks,a.review_details->>'$.entry_date' as entry_date FROM order_details a WHERE a.order_number='$orderid' AND a.item_id='$pdtid' AND a.review_details->>'$.rating' IS NOT NULL");
        return response::json(compact('my_pdt_review')); 
    }
    public function get_currency_list(Request $request) {
        $currency_list = DB::SELECT("SELECT id,currency_short_code,flag_img,convertion_rate FROM conversion_rate WHERE active='Y' ORDER BY display_order ");
        return $currency_list;
    }
    public function submit_vndr_register(Request $request) {
        $vndr_name   = $request['vndr_name'];
        $vndr_email  = $request['vndr_email'];
        $vndr_mobile = $request['vndr_mobile'];
        $vndr_pswrd  = $request['vndr_pswrd'];
        $vndr_full_adrs  = $request['vndr_full_adrs'];
    }
    public function clear_shipping_charges($userid) {
        DB::UPDATE("UPDATE cart_items SET delivery_charge='NIL' WHERE user_id=$userid");
        return 'cleared';
    }
}
