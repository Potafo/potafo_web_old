<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});
    Route::group(['middleware' => ['api','cors']], function () {
   });


  //USER LOGIN SECTION
Route::get('emailcheck','UserLoginController@emailcheck');
Route::post('logintest','UserLoginController@logintest');
Route::post('add_area', 'AreaController@add_area');//Add and Edit Designation
Route::post('add_restaurantlogin', 'RestaurantController@add_restaurantlogin');//Add and Edit Restaurant Login
Route::post('add_designation', 'DesignationController@add_designation');//Add and Edit Designation
Route::post('add_staff', 'StaffController@add_staff');//Add and Edit Staff
Route::post('add_restaurant', 'RestaurantController@add_restaurant');//Add Restaurant
Route::post('filter/restaurant', 'RestaurantController@filter_restaurant');//Filter Restaurant
Route::post('filter/menu', 'MenuController@filter_menu');//Filter Restaurant
Route::post('menu/add', 'MenuController@submit_menu');//Add menu
Route::post('menu/edit', 'MenuController@menu_editsubmit');//Add menu
Route::post('category/add', 'MenuController@category_add');//Add menu
Route::post('subcategory/add', 'MenuController@subcategory_add');//Add menu
Route::get('category/{id}', 'MenuController@category');//List actegory per  Restaurant
Route::get('subcategory/{id}', 'MenuController@subcategory');//List actegory per  Restaurant
Route::post('edit_restaurant', 'RestaurantController@edit_restaurant');//Edit Restaurant
Route::post('openclose_time', 'RestaurantController@openclose_time');//Open Close Time Add and update
Route::post('get_taxvalue', 'MenuController@get_taxvalue');//get tax value for particular tax
Route::get('view_time', 'RestaurantController@view_time');//View Open Close Time in Restaurant
Route::get('delete_time', 'RestaurantController@delete_time');//Delete Open Close Time in Restaurant
Route::get('rest_order/{id}/{val}','RestaurantController@rest_order');
Route::get('login_restaurant/{id}','RestaurantController@login_restaurant');
Route::post('update_rest_auth','RestaurantController@update_rest_auth');
Route::post('radius_calculate','RestaurantController@radius_calculate');
Route::post('filter/staff_list', 'StaffController@filter_staff_list');//Filter Staff List
Route::post('staff_area', 'StaffController@staff_area');//Staff Area Add
Route::post('staffarea_list', 'StaffController@staffarea_list');//Staff Area Add
Route::post('staff_area_delete', 'StaffController@staff_area_delete');//Staff Area Add
Route::get('get_staff_credit_limit','StaffController@get_staff_credit_limit');//get staff credit limit
Route::post('filter/credits_list', 'CreditController@filter_credits_list');//Filter Staff List
Route::post('filter/creditpays_list', 'CreditPaysController@filter_creditpays_list');//Filter Staff List
Route::post('filter/salaryadj_list', 'SalaryAdjController@filter_salaryadj_list');//Filter Staff List
Route::post('remove_salary_adj', 'SalaryAdjController@remove_salary_adj');
Route::post('remove_rest_login', 'RestaurantController@remove_rest_login');


//Route::get('rest_order/{id}/{val}','RestaurantController@rest_order');
Route::post('add_tax', 'TaxController@add_tax');//Add and Edit Tax
Route::post('menu/upload', 'MenuController@menu_upload');//get tax value for particular tax
Route::post('filter/review', 'ReviewController@filter_review');//Filter Review
Route::post('filter/customer_list', 'CustomerController@filter_customer_list');//Filter Customer List

//Banner add
Route::post('banner/add','BannerController@banner_submit');
Route::post('banner/appadd','BannerController@banner_appsubmit');
Route::get('banner_delete/{id}','BannerController@banner_delete');
Route::get('banner_order/{id}/{val}','BannerController@banner_order');

// General Offers
Route::post('add_gen_offers', 'GeneralOfferController@add_gen_offers');//Add General Offers
Route::post('edit_gen_offers', 'GeneralOfferController@edit_gen_offers');//Edit General Offers
Route::post('filter/genoffer', 'GeneralOfferController@filter_genoffer');//Filter General Offers
Route::post('remove_gen_offer/{offerid}', 'GeneralOfferController@remove_gen_offer');//Filter General Offers
Route::post('apply_gen_offer/{userid}/{couponcode}/{optn}', 'GeneralOfferController@add_coupon_discount');//Filter General Offers

//Restaurant Offer
Route::post('restaurant/add_offer','RestaurantOfferController@restaurant_offer');
Route::post('edit_rest_offers', 'RestaurantOfferController@edit_rest_offers');//Edit General Offers
Route::post('remove_offer/{restid}/{slno}', 'RestaurantOfferController@remove_rest_offers');//Edit General Offers


//Category
Route::get('category_order/{id}/{slno}/{val}','CategoryController@category_order');

Route::get('manage_order_filter_tables','OrderController@manage_order_filter_tables');//Returns the filter data of orders
Route::get('manage_order_filter_div','OrderController@manage_order_filter_div');//Returns the filter data of orders
Route::get('view_order_details_list','OrderController@view_order_details_list');//Returns the data of orders details
Route::get('view_order_address_list','OrderController@view_order_address_list');
Route::get('delete_menuorder','OrderController@delete_menuorder');//Deletes the menuorder
Route::get('assign_staff_list','OrderController@take_assign_staff_list');
Route::get('update_releasehold','OrderController@update_releasehold');//update_releasehold

//ORDER HISTORY
Route::get('order_history_filter_tables','OrderHistoryController@order_history_filter_tables');//Returns the filter data of orders
Route::get('view_order_history_details','OrderHistoryController@view_order_history_details');
Route::post('check_paymentstatus','OrderController@check_paymentstatus');
//summary of orders in layout page
Route::get('view_order_details_all','DashboardController@total_summary_orders');
//tax aplly to all menu
Route::post('tax_apply_to_all_menu','RestaurantController@tax_apply_to_all_menu');

//General Reports Filter
Route::get('filter_general_reports','GeneralReportController@filter_general_reports');
Route::get('search_item_name','GeneralReportController@orderitem_search');
Route::get('filter_staff_reports','StaffReportController@filter_staff_reports');

//Notification
Route::post('add_notification', 'NotificationGroupController@add_notification');
Route::post('update_notification','NotificationGroupController@update_notification');
Route::post('group_delete','NotificationGroupController@group_delete');
Route::post('customer_list','NotificationGroupController@customer_list');
Route::post('staff_list','StaffController@staff_list');
Route::post('filter/customer', 'NotificationGroupController@filter_customer');
Route::get('restaurantlist','NotificationGroupController@restaurantlist' );

//Manage order
Route::get('manage_order_filter_tables','OrderController@manage_order_filter_tables');//Returns the filter data of orders
Route::get('manage_order_filter_div','OrderController@manage_order_filter_div');//Returns the filter data of orders
Route::get('view_order_details_list','OrderController@view_order_details_list');//Returns the data of orders details
Route::get('addmenuorder','OrderController@addmenuorder');
Route::get('addnewmenuorder','OrderController@addnewmenuorder');
Route::get('edit_order_details','OrderController@edit_order_details');
Route::get('saveedit_order','OrderController@saveedit_order');
Route::get('confirm_order','OrderController@confirm_order');
Route::get('cancel_order','OrderController@cancel_order');//
Route::get('autocomplete_can_reason','OrderController@autocomplete_reason');//
Route::get('savepassword','StaffPermissionController@savepassword');//Save and update password for staff login
Route::get('savepermission','StaffPermissionController@savepermission');
Route::get('get_restraurent_category','TaxController@get_restraurent_category');//Returns categories under restraurent
Route::post('insert_restraurent_category','TaxController@update_restraurent_category_taxes_values');//insert tax to menu
Route::get('staff_mobile/{id}','StaffController@staff_mobile');

//API FOR FRONT END SERVICES
Route::get('restaurants/{id}/{category}/{search}','RestaurantController@restaurantlists'); //returns restaurants not busy nad open at the time in particular location
Route::get('mobile_reg/{no}','CustomerController@mobile_registration');//mobile number registration and otp send
Route::get('verify_otp/{no}/{otp}','CustomerController@otp_verification');//otp ver
Route::get('verify_otp/profile/{no}/{otp}','CustomerController@profileotp_verification');//otp ver
Route::get('forgot_otp/{no}','CustomerController@forgot_otp');
Route::get('forgot_password/{no}/{password}','CustomerController@forgot_password');
Route::post('customer_reg/{frst}/{lst}/{eml}/{pswd}/{mbl}','CustomerController@customer_registration');
Route::get('customer_login/{phone}/{password}','CustomerController@customer_login');
Route::post('restaurant_offers','RestaurantOfferController@restaurant_offerslist');//returns the restaurant and general offer details
Route::get('bannerapp','BannerController@bannerapplist');//returns list of App Banner
Route::post('bannerapp_new','BannerController@bannerapplist_new');//returns list of App Banner
Route::get('bannerweb','BannerController@bannerweblist'); //returns list of Web Banner
Route::post('bannerweb_new','BannerController@bannerweblist_new'); //returns list of Web Banner
Route::get('popular/restaurants','RestaurantController@mostpopular_restaurants');
Route::get('restaurant/about/{id}','RestaurantController@about_restaurants');//Returns the details of Restaurant
Route::get('restaurant/review/{id}','RestaurantController@review_restaurants');//Returns the review of particular restaurant
Route::post('addresses','CustomerController@addresslist');//Returns the Address of Customer
Route::get('address_add/{userid}/{type}/{line1}/{line2}/{default}/{landmark}/{pincode}','CustomerController@address_add');//Add address of the customer
Route::get('address_edit/{userid}/{addressid}/{type}/{line1}/{line2}/{default}/{landmark}/{pincode}','CustomerController@address_edit');//Edit address of the customer
Route::post('cart_order_web','OrderController@cart_order_web');//Adding menu to cart
Route::post('cart_order/{item_id}/{rate}/{qty}/{prefrnce}/{userid}/{rest_id}/{portion}','OrderController@cart_order');
Route::get('cart_list/{userid}','OrderController@cart_list');//Cart List
Route::post('cart_clear','OrderController@cart_clear');//Clear Cart
Route::post('cart_edit/{userid}/{slno}/{qty}/{prefrnce}','OrderController@cart_edit');//Cart Edit
Route::get('cartitem_delete/{userid}/{slno}','OrderController@cartitem_delete');//Delete Cart Item
Route::get('order_confirmation/{userid}/{type}/{line1}/{line2}/{landmark}/{pincode}/{paymethod}','OrderController@order_confirmation');//Confirm the order
Route::get('menu/{id}/{user_id}','MenuController@menulist');//Returns the menu List
Route::get('cart_total/{userid}','OrderController@cart_total');//Returns the menu count and final total
Route::get('restaurant/web/{id}','RestaurantController@restaurant_web');//Returns the menu List
Route::get('restaurant/category/{id}','RestaurantController@restaurant_category');//Returns the menu List
Route::post('deliverystaff_login','StaffController@deliverystaff_login');//Lists the staff name and number if code matches
Route::post('staff_login_credentials','StaffController@staff_login_credentials');//Lists the staff name and number if code matches
Route::post('staff_forgot_credentials','StaffController@staff_forgot_credentials');//Lists the staff name and number if code matches
Route::get('deliverystaff_details/{staffid}','StaffController@deliverystaff_details');//List the entry details of the staff
Route::post('deliverystaff_addtime/{staffid}','StaffController@deliverystaff_addtime');//Add entry of a particular staff
Route::post('deliverystaff_attendance/{staffid}','StaffController@deliverystaff_attendance');//Add entry of a particular staff
Route::get('deliverycount_list/{staffid}/{frmdate}/{todate}','StaffController@deliverycount_list');//List the delivery count of particular staff in a date range
Route::get('delivery_orders/{staffid}','StaffController@delivery_orders');//List of delivery orders.
Route::get('delivery_order_details/{order_number}','StaffController@delivery_order_details');//List the delivery order details
Route::post('order_status/{order_number}/{status}','StaffController@order_status');//Change the order status of particular order
Route::get('home_search_list/{search_term}','RestaurantController@search_restaurent_menu');
Route::post('payment_mode','PaymentController@payment_mode');//Returns the payment mode
Route::post('user_orderlist','OrderController@user_orderlist');
Route::post('user_info','OrderController@user_info');
Route::get('order_review/{order_number}','OrderController@order_review');
Route::get('order_review_add/{order_number}/{star_rating}/{review}/{staffrate}/{staffreview}','MailerController@order_review_add');
Route::get('user_orderdetails/{order_number}','OrderController@user_orderdetails');
Route::get('home_orderstatus/{userid}','OrderController@home_orderstatus');
Route::get('sendotp/profile/{mob}/{oldno}','CustomerController@sendotp');
Route::post('updateprofile','CustomerController@updateprofile');
Route::get('repeat_order/{userid}/{order_number}','OrderController@repeat_order');//To repeat the order
Route::get('new_order_check/{staffid}','StaffController@new_order_check');
Route::get('favourite_restaurant/update/{userid}/{restid}/{status}','RestaurantController@favourite_update');
Route::get('new_order_status/{staffid}','StaffController@new_order_status');
Route::get('fav_list/{userid}','RestaurantController@fav_list');
Route::get('android_version','RestaurantController@android_version');
Route::get('ios_version','RestaurantController@ios_version');
Route::post('user_details','CustomerController@user_details');
Route::get('address_remove/{userid}/{id}','CustomerController@address_remove');
Route::get('payment/initialize/{id}','PaymentController@payment_initialize');
Route::post('payment/complete/{id}/{refid}','PaymentController@payment_complete');
Route::post('payment/order','PaymentController@create_order');
Route::post('payment/order/new','PaymentController@create_order_new');
Route::post('payment/orderconfirmation_new','PaymentController@orderconfirmation_new');
Route::post('delivery_range_check','StaffController@delivery_range_check');
Route::get('default_location/{id}','CustomerController@default_location');
Route::get('minimum_cartvalue_check/{userid}','StaffController@minimumcartcheck');
Route::get('test','StaffController@testing');
Route::post('ftoken_check','CustomerController@ftoken_check');
Route::post('send_notification','CustomerController@notification_send');
Route::post('order_notification','OrderController@order_notification');
Route::post('ftoken_staff_check','StaffController@ftoken_staff_check');
Route::post('notificationsubmit','StaffController@notificationsubmit');
Route::post('notification_check','CustomerController@notification_check');
Route::post('filter/notification_list','CustomerController@notification_list');
Route::post('ftoken_delete','CustomerController@ftoken_delete');
Route::get('notification_delete/{id}','NotificationController@notification_delete');
Route::post('category_cat','CateringController@category_cat');
Route::post('banner_cat','BannerController@banner_cat');//returns list of App Banner
Route::post('insertrest_firebase','FirebaseController@insertrest_firebase');
Route::post('filter/filter_complaintslist', 'ComplaintsController@filter_complaintslist');












Route::post('order_status_change','StaffController@order_status_change');
Route::post('mart_force_update','MartController@mart_force_update');
Route::post('mart_login','MartController@mart_login');
Route::post('no_contact_del','PaymentController@no_contact_del');
Route::post('cust_app_topbar_msg','BannerController@cust_app_topbar_msg');//returns list of App Banner
Route::post('call_customer','PaymentController@call_customer');//chnage the option to call and not to call customers
Route::post('cat_order_history_details','CateringController@cat_order_history_details');//returns list of orderhistory
Route::post('cat_place_order','CateringController@cat_place_order');//for min and max pax of menu category
Route::post('catering_terms','CateringController@catering_terms');//for min and max pax of menu category
Route::post('pax_min_max','CateringController@pax_min_max');//for min and max pax of menu category
Route::post('cat_order_review','CateringController@cat_order_review');//for getting order review
Route::post('cat_review_in','CateringController@cat_review_in');//for order review
Route::post('cat_order_history','CateringController@cat_order_history');//returns list of orderhistory
Route::post('menus_selection_validate','CateringController@menus_selection_validate');//to update the selection
Route::post('menus_selection_update','CateringController@menus_selection_update');//to update the selection
Route::post('cat_cust_add_menu','CateringController@cat_cust_add_menu');//returns list of menu and add data to order master
Route::post('cat_rest_review','CateringController@cat_rest_review');//returns list of rest reviews
Route::post('cat_rest_menu_types','CateringController@cat_rest_menu_types');//returns list of menu types
Route::post('peoplelimit','CateringController@peoplelimit_app');//returns list of city list of catering
Route::post('citylist','CateringController@citylist');//returns list of city list of catering
Route::post('citypincodes','CateringController@city_pincodes');//returns list of city list of catering
Route::post('cat_restaurants','CateringController@cat_restaurants');//returns list of city list of catering


//API UPDATED
Route::post('mobile_reg/new','CustomerController@mobile_registration_new');//mobile number registration and otp send
Route::post('custid_updation_test','CustomerController@custid_updation_test');//mobile number registration and otp send
Route::post('verify_otp/new','CustomerController@otp_verification_new');//otp ver
Route::post('customer_reg_new','CustomerController@customer_registration_new');
//Route::post('customer_reg_new','CustomerController@customer_registration_new');
Route::post('customer_login_new','CustomerController@customer_login_new');
Route::post('forgot_otp_new','CustomerController@forgot_otp_new');
Route::post('force_psw_update','CustomerController@force_psw_update');
Route::post('forgot_password_new','CustomerController@forgot_password_new');
Route::post('home_search_list_new','RestaurantController@search_restaurent_menu_new');
Route::post('home_search_list_new_location','RestaurantController@search_restaurent_menu_new_location');
Route::post('restaurants_new','RestaurantController@restaurantlists_new'); //returns restaurants not busy nad open at the time in particular location
Route::post('cart_order_new','OrderController@cart_order_new');
Route::post('cart_edit_new','OrderController@cart_edit_new');//Cart Edit
Route::post('cartitem_delete_new','OrderController@cartitem_delete_new');//Delete Cart Item
Route::post('address_add_new','CustomerController@address_add_new');//Add address of the customer
Route::post('menu_new','MenuController@menulist_new');//Returns the menu List
Route::post('order_review_add_new','MailerController@order_review_add_new');
Route::post('address_edit_new','CustomerController@address_edit_new');//Edit address of the customer
Route::post('address_remove_new','CustomerController@address_remove_new');
Route::post('ios_version_new','RestaurantController@ios_version_new');
Route::post('sendotp/profile_new','CustomerController@sendotp_new');
Route::post('verify_otp/profile_new','CustomerController@profileotp_verification_new');//otp ver
Route::post('apply_gen_offer_new', 'GeneralOfferController@add_coupon_discount_new');//Filter General Offers
Route::post('staff_credit_amount', 'StaffController@get_staff_credit_sum');//Get staff credit amount total
Route::post('credit_pay_post', 'StaffController@credit_pay_post');//ADD staff pay
Route::post('staff_orderwise_credit_amount', 'StaffController@credit_amount');//ADD staff pay
Route::post('staff_credit_pay', 'StaffController@staff_credit_pay');//ADD staff pay
Route::post('dlv_stff_notification','StaffController@dlv_staff_notification');//for delivery staff notifications
Route::post('dlvappforceupdate','StaffController@dlv_app_forceupdate');//for delivery staff force update app
Route::post('dlvstaffreviews','RestaurantController@dlvstaffreviews');//for delivery staff reviews
Route::post('staff_sal_amt_details','StaffController@staff_sal_amt_details');//for delivery staff reviews
Route::post('salary_adj_submit', 'SalaryAdjController@salary_adj_submit');//ADD staff pay

//Restauarnt apis
Route::post('restaurant_login','RestaurantController@restaurant_login');
Route::post('dashboardetails','RestaurantController@dasboard_details');
Route::post('orderstatus','RestaurantController@orderstatus');
Route::post('ordermanagement' ,'RestaurantController@ordermanagement');
Route::post('orderreviewdetails','RestaurantController@orderreviewdetails');
Route::post('ordermenudetails','RestaurantController@orderdetails');
Route::post('menucategory','RestaurantController@menucategory');
Route::post('menumanagement','RestaurantController@menumanagement');
Route::post('menuupdate','RestaurantController@menuupdate');
Route::post('generalsettings','usercontroller@generalsettings');
Route::post('busyupdate','usercontroller@busyupdate');
Route::post('forcecloseupdate','usercontroller@forcecloseupdate');
Route::post('restauranttimeadd','RestaurantController@restauranttimeadd');
Route::post('restauranttimedelete','RestaurantController@restauranttimedelete');
Route::post('restauranttimebyday','usercontroller@restauranttimebyday');
Route::post('restaurantreviews','RestaurantController@restaurantreviews');
Route::post('orderslist','RestaurantController@orderslist');
Route::post('tokenupdate','RestaurantController@tokenupdate');
Route::post('placedcount','RestaurantController@placedcount');
Route::post('restaurantconfirmation','RestaurantController@restaurantconfirmation');
Route::post('restaurant_readytopick','RestaurantController@restaurant_readytopick');
Route::post('restaurantforceupdate','RestaurantController@restaurant_force_update');
Route::post('restaurantnotification','RestaurantController@restaurantgroupnotification');
Route::post('restauranttime','RestaurantController@restauranttime');
Route::post('updateitemrates','RestaurantController@updateitemrates');
Route::post('categorystatus','RestaurantController@categorystatus');
// catering section by jeshina
//category
Route::post('add_category', 'CateringController@add_category');
Route::post('edit_category', 'CateringController@edit_category');

//banner
Route::post('catering/app_banner_submit','CateringController@app_banner_submit');
Route::get('cat_banner_order/{id}/{val}','CateringController@cat_banner_order');
Route::get('cat_banner_delete/{id}','CateringController@cat_banner_delete');

//city
Route::post('add_city', 'CateringController@add_city');
Route::get('cat_city_delete/{id}','CateringController@cat_city_delete');
Route::get('view_pincode/{id}', 'CateringController@view_pincode');
Route::post('add_pincode', 'CateringController@add_pincode');
Route::get('cat_pin_delete/{id}/{slno}','CateringController@cat_pin_delete');

//restaurant
Route::get('rest_disporder/{id}/{val}','CateringController@rest_disporder');
Route::post('add_catrestaurant', 'CateringController@add_catrestaurant');//Add Restaurant
Route::post('edit_catrestaurant', 'CateringController@edit_catrestaurant');//Edit 
Route::post('filter/cat_restaurant', 'CateringController@filter_catrestaurant');//Filter Restaurant
Route::get('cat_restcatgy_delete/{id}/{restid}','CateringController@cat_restcatgy_delete');
Route::get('add_restcategory/{id}/{restid}', 'CateringController@add_restcategory');
Route::get('cat_restpincode_delete/{id}/{restid}','CateringController@cat_restpincode_delete');
Route::get('add_restpincode/{id}/{restid}', 'CateringController@add_restpincode');
Route::post('filter/cat_restaurant', 'CateringController@filter_catrestaurant');//Filter Restaurant
Route::post('add_rest_tax', 'CateringController@add_rest_tax');//Add and Edit Tax
Route::get('load_pincodes/{id}', 'CateringController@load_catpincodes');
Route::post('filter/cat_pincodes', 'CateringController@filter_catpincodes');//Filter Restaurant
Route::get('sal_autocomplete_can_reason','SalaryAdjController@sal_autocomplete_reason');//
//
//types
Route::post('add_type', 'CategoryMenuTypeController@add_type');
Route::post('add_categorytype', 'CategoryMenuTypeController@add_categorytype');
Route::post('add_menu', 'CategoryMenuTypeController@add_menu');
Route::get('type_order/{id}/{typeid}/{val}','CategoryMenuTypeController@type_order');
Route::get('menu_order/{typeid}/{resid}/{slno}/{val}','CategoryMenuTypeController@menu_order');
Route::get('categorytype_order/{id}/{typeid}/{val}','CategoryMenuTypeController@categorytype_order');
Route::post('filter/category_type', 'CategoryMenuTypeController@category_type');//Filter Category Type
Route::post('filter/menus', 'CategoryMenuTypeController@menus');//Filter menus
Route::get('cateringorder_history_filter_tables','CateringOrderController@cateringorder_history_filter_tables');//Returns the filter data of orders
Route::post('submitcomment', 'CateringOrderController@submitcomment');
Route::post('submitextracharge', 'CateringOrderController@submitextracharge');
Route::post('deleteextracharge', 'CateringOrderController@deleteextracharge');

//RazorPay section starts

Route::post('create_virtualbankbccount', 'RazorpayController@create_virtualbankbccount');
Route::post('create_baratqr', 'RazorpayController@create_baratqr');

//firebase
Route::get('fb_check_inprogress/{area}','FirebaseController@fb_check_inprogress');

//complaints
Route::get('complaints_load_custmobile','ComplaintsController@complaints_load_custmobile');
Route::post('add_complaints','ComplaintsController@complaints_add');
Route::post('category_complaints/add', 'ComplaintsController@category_complaints');//Add menu
Route::post('submitcomment_cpl', 'ComplaintsController@submitcomment');
Route::get('followup_delete/{id}/{slno}','ComplaintsController@followup_delete');

//spot
Route::get('spot/{latitude?}/{longitude?}', 'SpotController@index');
Route::get('check-spot/{latitude}/{longitude}', 'SpotController@check_starting_spot');

Route::get('spot_delete/{spotid}/{cityid}','SpotController@spot_delete');
Route::post('add_spot', 'SpotController@add_spot');

Route::post('v2/staff/report', 'ApiController@attendance_report');
Route::post('v2/staff/split-report', 'ApiController@splitted_attendance_report');

Route::post('add_menu_image','MenuController@add_menu_image');

// delivery staff payment
Route::post('filter_delvystaff_payment','PaymentDeliveryStaffController@filter_delvystaff_payment');

Route::post('insert_delivery_staff_payment','PaymentDeliveryStaffController@insert_delivery_staff_payment');

Route::post('checkauth_payment','PaymentDeliveryStaffController@checkauth_payment');
Route::post('accept_amount','PaymentDeliveryStaffController@accept_amount');
Route::post('accept_upi','PaymentDeliveryStaffController@accept_upi');
Route::post('reject_amount','PaymentDeliveryStaffController@reject_amount');

