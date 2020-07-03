<?php

namespace App\Http\Controllers;

use Helpers\Commonsource;
use Illuminate\Http\Request;
use Image;
use Razorpay\Api\Api;
use DB;
use Response;
use Illuminate\Support\Facades\Input;

class RazorpayController extends Controller
{
    public function __construct()
    {
        $this->key_id     = config('razor.key_id');
        $this->secret     = config('razor.key_secret');
        $this->api        = new Api($this->key_id , $this->secret);
    }
    
    public function create_virtualbankbccount(Request $request)
    {
        $api =  $this->api;
       $virtualAccount  = $api->virtualAccount->create(array('receiver_types' => array('bank_account'), 'description' => 'First Virtual Account', 'notes' => array('receiver_key' => 'receiver_value')));
       return $virtualAccount->id;
    }
    public function create_baratqr(Request $request)
    {
        $api =  $this->api;
        // Bharat QR
//$bharatQR = $api->virtualAccount->create(array('receivers' => array('types' => array('qr_code')), 'description' => 'First QR code', 'amount_expected' => 100, 'notes' => array('receiver_key' => 'receiver_value'))); // Create Static QR
  
//$bharatQR = $api->virtualAccount->create(array('receivers' => array('types' => array('qr_code')), 'description' => 'First Payment by BharatQR','amount_expected' => '100', 'notes' => array('receiver_key' => 'reference_value'))); // Create Dynamic QR
//return $bharatQR->id;
$details = $api->virtualAccount->fetch('va_EZL30CaJC4iMl4');
return $details->short_url;

     

    }
	
}
