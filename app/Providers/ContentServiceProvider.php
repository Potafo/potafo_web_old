<?php

namespace App\Providers;

use App\GeneralSetting;
use App\MenuPortion;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Helpers\Datasource;
use Helpers\Commonsource;
use Response;
use DB;
use App\Designation;
use App\City;
use App\Country;
use Session;
use App\UserMaster;
class ContentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadheader();
        $this->activestafflist();
        $this->loaddecimal();
        $this->loadrestauarnt_day_count_limit();
        $this->siteurl();
        $this->loaddesignationdetails();
        $this->loadcity();
        $this->loadcountry();
        $this->loadcurrency();
        $this->loadmenuportion();
        $this->staffpermission();
        $this->superpermission();
        $this->notificationgroups();
        $this->googleapikey();
        $this->paymode();
        $this->module_list();
        $this->categoryorderstatus();
        $this->orderstatus();
        $this->load_dropdowns();
        $this->login_designation();
    }
    private function loadheader()
    {
        view()->composer(['*'], function($view)
        {
            $url = Datasource::geturl();

            $view->with('url', $url);
        });
    }
    private function googleapikey()
    {
        view()->composer(['*'], function($view)
        {
            $googlekey = Commonsource::googleapikey();

            $view->with('googlekey', $googlekey);
        });
    }
    private function paymode()
    {
        view()->composer(['*'], function($view)
        {
            $payment_method = DB::SELECT("select name FROM payment_methods WHERE active='Y'");
            $view->with('paymode', $payment_method);
        });
    }
    private function siteurl()
    {
        view()->composer(['*'], function($view)
        {
            $siteUrl = Datasource::getsiteurl();
            $view->with('siteUrl', $siteUrl);
        });
    }
   private function loaddecimal()
    {
        view()->composer(['*'], function($view)
        {
            $general= GeneralSetting::where('id','1')->select('decimal_digit')->first();
            $view->with('decimal_digit', $general['decimal_digit']);
        });
    }
    private function loadrestauarnt_day_count_limit()
    {
        view()->composer(['*'], function($view)
        {
            $general= GeneralSetting::where('id','1')->select('restaurant_time_count')->first();
            $view->with('restaurant_time_count', $general['restaurant_time_count']);
        });
    }
     private function loaddesignationdetails() {
        view()->composer(['*'], function($view) {
            $designationlist = Designation::select('designation', 'designation')
                            ->where('active','=','Y')
                            ->pluck('designation', 'designation')->all();
            $view->with('designationlist', $designationlist);
        });
    }
    private function loadcity() {
        view()->composer(['*'], function($view) {
            $citylist = City::select('name', 'id')
                ->where('active','Y')
                ->pluck('name', 'id')
                ->all();
            $view->with('citylist', $citylist);
        });
    }



    private function categoryorderstatus() {
        view()->composer(['*'], function($view) {
            $citylist = City::select('name', 'id')
                ->where('active','Y')
                ->pluck('name', 'id')
                ->all();
            $view->with('citylist', $citylist);
        });
    }
    private function loadmenuportion()
    {
        view()->composer(['menu.menu_add','menu.menu_edit'], function($view)
        {
            $portion = MenuPortion::where('mp_status','Y')
                ->select('mp_portion')
                ->pluck('mp_portion', 'mp_portion')
                ->all();
            $view->with('portion', $portion);
        });
    }
    private function loadcountry() {
        view()->composer(['*'], function($view) {
            $countrylist = DB::SELECT("SELECT name->>'$.country' as country FROM country");
			$i=0;
			$list = array();
			foreach($countrylist as $list) {
				$country[$i] = $list->country;
				$i++;
			}
            $view->with('countrylist', $country);
     
        });
    }
    private function loadcurrency()
    {
        view()->composer(['*'], function($view) {
            $currencylist = DB::SELECT("SELECT name->>'$.currency ' as currency FROM country");
            $i=0;
            $currency_list = array();
            foreach($currencylist as $currency_list) {
                $currency[$i] = $currency_list->currency;
                $i++;
            }
            $view->with('currencylist', $currency);
        });
    }
     private function notificationgroups()
     {
           view()->composer(['*'],function($view)
           {
             $result = DB::SELECT("SELECT g_name,g_id from `notification_group` where g_active = 'Y'");
             $view->with('group',$result);
           });
     }
    private function staffpermission()
    {
        view()->composer(['*'], function($view)
        {
            $staffid = Session::get('staffid');
            $logingroup = Session::get('logingroup');
            if($logingroup=='H'){
                $permission = DB::SELECT("SELECT modules->>'$.RestauranrReports' as RestauranrReports,JSON_UNQUOTE(modules->'$.banner') as banner,JSON_UNQUOTE(modules->'$.restaurant') as restaurant,JSON_UNQUOTE(modules->'$.staff') as staff,JSON_UNQUOTE(modules->'$.offers') as offers,JSON_UNQUOTE(modules->'$.orders') as orders,JSON_UNQUOTE(modules->'$.reports') as reports,JSON_UNQUOTE(modules->'$.customer') as customer,JSON_UNQUOTE(modules->'$.designation') as designation,JSON_UNQUOTE(modules->'$.staff_report') as staff_report,JSON_UNQUOTE(modules->'$.general_report') as general_report,JSON_UNQUOTE(modules->'$.send_notification') as send_notification,JSON_UNQUOTE(modules->'$.notification_group') as notification_group,modules->>'$.order_history' as order_history from  users a LEFT JOIN internal_staffs b on a.id=b.id where a.restaurant_id= '".$staffid."'");
            }
            else{
//                $permission = DB::SELECT("select module_name,sub_module,active from module_master LEFT JOIN users_modules ON module_master.m_id=users_modules.module_id where user_id='$staffid'");
                 $permission = DB::SELECT("SELECT modules->>'$.RestauranrReports' as RestauranrReports,JSON_UNQUOTE(modules->'$.banner') as banner,JSON_UNQUOTE(modules->'$.restaurant') as restaurant,JSON_UNQUOTE(modules->'$.staff') as staff,JSON_UNQUOTE(modules->'$.offers') as offers,JSON_UNQUOTE(modules->'$.orders') as orders,JSON_UNQUOTE(modules->'$.reports') as reports,JSON_UNQUOTE(modules->'$.customer') as customer,JSON_UNQUOTE(modules->'$.designation') as designation,JSON_UNQUOTE(modules->'$.staff_report') as staff_report,JSON_UNQUOTE(modules->'$.general_report') as general_report,JSON_UNQUOTE(modules->'$.send_notification') as send_notification,JSON_UNQUOTE(modules->'$.notification_group') as notification_group,modules->>'$.order_history' as order_history from  users a LEFT JOIN internal_staffs b on a.id=b.id where a.staffid= '".$staffid."'");

            }
            $view->with('permission',$permission);
        });
    }
     private function superpermission()
    {
        view()->composer(['*'], function($view)
        {
            $staffid = Session::get('staffid');
            $superadmin = DB::SELECT("SELECT login_group,staffid from users");
            $view->with('superadmin',$superadmin);
        });
    }

    private function orderstatus()
    {
        view()->composer(['*'], function($view)
        {
            $staffid = Session::get('staffid');
            $order_cat = DB::SELECT("SELECT `order_list_cat` FROM `internal_staffs` WHERE `id`='".$staffid."'");
            $view->with('order_cat',$order_cat);
        });
    }

    private function activestafflist()
    {
        view()->composer(['*'], function($view)
        {
            $stafflist = DB::SELECT("SELECT first_name as name,id FROM internal_staffs where designation ='Delivery staff' and active = 'Y' order by first_name asc");
            $i=0;
            $staff = array();
            foreach($stafflist as $list) {
                $staff[$list->id] = $list->name;
                $i++;
            }
            $view->with('staff', $staff);
        });
    }
     private function load_dropdowns()
    {
        view()->composer(['*'], function($view)
        {
            $dropdown = DB::SELECT("SELECT `dm_name`,`dm_value` FROM `all_dropdown_master` WHERE `dm_page`='Complaint' and `dm_section`='Priority' ORDER BY `dm_disp_order`");
            $i=0;
            $dropdown_list = array();
            foreach($dropdown as $list) {
                $dropdown_list[$list->dm_value] = $list->dm_name;
                $i++;
            }
            $view->with('load_dropdowns', $dropdown_list);
        });
    }
     private function module_list()
    {
         view()->composer(['*'], function($view)
        {
            $staffid = Session::get('staffid');
            $setuserid = Session::get('setuserid');
 //           $module = DB::SELECT("SELECT DISTINCT(mm.module_name),(select `m_id` from module_master  mms where mms.module_name = mm.module_name limit 0,1) as mid,(select page_link from module_master mst where mm.module_name =  mst.module_name limit 0,1) as page_link,(select count(`sub_module`) from module_master ms where ms.module_name = mm.module_name) as count from module_master mm LEFT JOIN users_modules um ON mm.m_id=um.module_id where mm.module_for='C' and um.user_id = '$setuserid' and um.active='Y' order by mid asc");
             $module = DB::SELECT("SELECT DISTINCT(mm.module_name),mm.display_order,(select page_link from module_master mst where mm.module_name =  mst.module_name limit 0,1) as page_link,(select count(`sub_module`) from module_master ms where ms.module_name = mm.module_name) as count from module_master mm LEFT JOIN users_modules um ON mm.m_id=um.module_id where mm.module_for='C' and um.user_id = '$setuserid' and um.active='Y' order by display_order asc");
            $modulesublist = DB::SELECT("SELECT mm.module_name,mm.m_id,mm.sub_module,mm.page_link from module_master mm LEFT JOIN users_modules um ON mm.m_id=um.module_id where mm.module_for='C' and um.user_id = '$setuserid' and um.active='Y'");
            $view->with(['module' => $module,'modulesublist'=>$modulesublist,'staffid'=>$staffid,'setuserid'=>$setuserid]);
        });
    }
    private function login_designation()
    {
         view()->composer(['*'], function($view)
        {
            $staffid = Session::get('staffid');
            $setuserid = Session::get('setuserid');
             $designation_logged = DB::SELECT("SELECT `designation` FROM `internal_staffs` WHERE `id`='$staffid'");   
            $view->with(['designation_logged' => $designation_logged]);
        });
    }
  }
