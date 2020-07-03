<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('userlogin.login');
});
Route::get('logout', function () {
    return view('userlogin.login');
});
Route::get('manage_user', function () {
    return view('user.manage_user');
});
Route::get('welcome_restaurant', function () {
    return view('restaurant_login.home');
});
Route::get('restaurant_report', function () {
    return view('restaurant_login.restaurant_report');
});

Route::get('catering_details/{id}', 'CateringOrderController@catering_details');
Route::get('complaint_details/{id}', 'ComplaintsController@complaint_details');
//Route::get('index', function () {
//    return view('index');
//});
Route::get('userexistcheck','UserLoginController@userexistcheck');
Route::get('session/set', 'UserLoginController@setsession');

Route::group(['middlewareGroups' => 'usersession'], function () {
Route::get('index','DashboardController@index_function');
Route::get("manage_designation","DesignationController@view_designation");
Route::get("area","AreaController@view_area");
Route::get("manage_restaurant","RestaurantController@view_restaurant");
Route::get("restaurant_details","RestaurantController@view_restaurantdetails");
Route::get("manage_staff","StaffController@view_staff");
Route::get("manage_credits","CreditController@view_credits");
Route::get("manage_review","ReviewController@manage_review");
Route::get("manage_creditpays","CreditPaysController@view_creditpays");
 Route::get('groupautosearch', 'RestaurantController@groupautosearch');
Route::get("designation_status","DesignationController@designation_status");//Change Designation Status
Route::get("restaurantlogin_status","RestaurantController@restaurantlogin_status");//Change Restaurant Status
Route::get("manage_customer","CustomerController@view_customer");
Route::get("manage_order","OrderController@view_order");
Route::get('restaurant_edit/{id}', 'RestaurantController@restaurant_edit');
Route::get("ratings","RatingController@ratings");
Route::get("staff_permission/{id}","StaffPermissionController@view_staffpermission");
Route::get("manage_salaryadj","SalaryAdjController@view_salaryadj");//salary adj
Route::get("manage_order_v2","V2_OrderController@view_order");
//Menu
Route::get("menu/list/{id}","MenuController@menu_list");
Route::get("menu/add/{id}","MenuController@menu_add");
Route::get("most_selling","MenuController@most_selling");//Change most_selling status
Route::get("menu/review/{id}","ReviewController@menu_review");
Route::get("menu/tax/{id}","TaxController@menu_tax");
Route::get("menu/edit/{rid}/{mid}","MenuController@menu_edit");
Route::get("menu/download/{id}","MenuController@menu_excel_download");
Route::get("tax_status","TaxController@tax_status");
Route::get("review_status","ReviewController@review_status");//Change Review Status
Route::get("restaurant_status","RestaurantController@restaurant_status");//busy status change
Route::get("restaurantclose_status","RestaurantController@restaurantclose_status");//Change Review Status

//Menu Category
Route::get("category/list/{id}","CategoryController@category_list");
Route::get("rescategory_imgview","CategoryController@category_imgview");//Change Category Image View

Route::get('get_restaurants_category/{type}','GeneralReportController@get_restaurants_category');

// Category Menu Types
Route::get("menu/types/{id}","CategoryMenuTypeController@menu_types");
Route::get("menu/category/{id}","CategoryMenuTypeController@menu_category");
Route::get("menu/{id}","CategoryMenuTypeController@menu");
Route::get("category_imgview","CategoryMenuTypeController@category_imgview");//Change Category Image View

//Restaurant offer
Route::get("restaurant/offer/{id}","RestaurantOfferController@restaurantoffer");
Route::get('offeritem/search','RestaurantOfferController@offeritem_search');
Route::get("rest_offer_status","RestaurantOfferController@rest_offer_status");//Change Restaurant Offer Status

//Restaurant Login
Route::get("restaurant/login/{id}","RestaurantController@restaurantlogin");

//Banners
Route::get('manage/banners','BannerController@banners_view');
Route::get('banner/add','BannerController@banners_add');
Route::get('banner/appadd/{id}','BannerController@banners_appadd');


Route::get("general_offers","GeneralOfferController@generaloffer");
Route::get("genoffer_status","GeneralOfferController@genoffer_status");//Change General offer Status

// general reports view
Route::get("general_reports","GeneralReportController@view_general_report");

// staff reports view
Route::get("staff_reports","StaffReportController@view_staff_report");
Route::get("test","PaymentController@test");

Route::get("notification","NotificationController@notification_view");
Route::get("notification_group","NotificationGroupController@notificationgroup_view");

Route::get('orderitem/search','OrderController@orderitem_search');


//EXCEL DOWNLOAD
Route::get("excel_download","CustomerController@excel_download");
Route::get("excel_download_credit","CreditController@excel_download_credit");
Route::get("excel_download_creditpay","CreditPaysController@excel_download_creditpay");

//ORDER HISTORY
Route::get('order_history','OrderHistoryController@order_history');
Route::get('manage_cateringorder','CateringOrderController@manage_cateringorder');

// new catering controller
Route::get("manage_category","CateringController@view_category");
Route::get("manage_banner","CateringController@view_banner");
Route::get('catering/banner_app_add','CateringController@add_banners');
Route::get("manage_city","CateringController@view_city");
Route::get("catering_restaurant","CateringController@view_restaurant");
Route::get("cat_restaurant_details","CateringController@view_restaurantdetails");
Route::get("catrestaurant_status","CateringController@restaurant_status");
Route::get('cat_restaurant_edit/{id}', 'CateringController@cat_restaurant_edit');
Route::get("cat_restaurant_category/{id}","CateringController@cat_rest_category");
Route::get("cat_restaurant_pincode/{id}","CateringController@cat_rest_pincode");
Route::get("cat_restaurant_tax/{id}","CateringController@cat_rest_tax");
Route::get("cat_tax_status","CateringController@cat_tax_status");

//order mapload
Route::get("order_mapload/{id}/{long}","OrderController@order_mapload");
//catering dashboard
Route::get('cat_dashboard','Cat_DashboardController@catering_dashboard');
//Route::get('test_pag', 'TestController@getBottles');
//firebase
Route::get('firebase','FirebaseController@index_set');
//firebase -bulk inserting restuarant_master (area,rest id, rest name,lat,long)
Route::get('fb_restmasterfullinsert','FirebaseController@fb_restmaster');
//Route::get('fb_distance_calc','FirebaseController@fb_distance_staffid_check');

//complaints
Route::get("complaints","ComplaintsController@complaints_view");

});

Route::group(['middlewareGroups' => 'prevent-back-history'],function(){
 Auth::routes();
});
