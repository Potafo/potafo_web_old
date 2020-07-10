@extends('layouts.app')
@section('content')
    <style>
        .not-active {
            pointer-events: none;
            cursor: default;opacity: 0.5;
            font-weight: bold;
        }
        .add-work-done-poppup-textbox-box label{font-weight:lighter;}
        .inner-textbox-cc input:focus ~ label, input:valid ~ label{font-size:13px;top: -10px;}
        .group{margin-bottom: 14px}
        .sweet-alert{width:300px !important;left: 0 !important;right: 0;margin: auto !important;}

        .bootstrap-select.btn-group .dropdown-menu.inner{max-height:  300px !important;}
        .staff_master_tbl_tbody{
            width: 100%;
            height: 150px;
            margin-bottom: 2px;
            float: left;
            overflow: auto;
        }
        .main_inner_class_track .bootstrap-select{border: solid 1px #ccc;}
        .table_staff_scr_scr thead{ display: inline-block;width: 100%;}
        .table_staff_scr_scr tbody{ display: inline-block;width: 100%;max-height:  390px;overflow: auto   }
        .table_staff_scr_scr tr{ display: inline-block;width: 100%;}
        .table_staff_scr_scr td{ width: 100px;}
        .table_staff_scr_scr th{ width: 100px;}
        .pagination_total_showing{float: left;width: auto;padding-top: 12px;padding-left: 10px;color: #000000;}
        .add-work-done-poppup-textbox-box label{font-weight:lighter;}.inner-textbox-cc input:focus ~ label, input:valid ~ label{font-size:13px;top: -10px;}.group{margin-bottom: 14px}.add-work-done-poppup{height: auto;} div.dataTables_wrapper div.dataTables_filter{float: right;top: 4px;position: relative;}.dataTables_length{top: 7px;position: relative;float: left}
        .dataTables_scrollHeadInner{width: 100% !important}.dataTables_scrollHeadInner table{width: 100% !important}.dataTables_scrollBody table{width: 100% !important} .dataTables_scrollBody {  height: 350px;}
        .table_section_scroll{min-height: 1030px}
        .chosen-container-multi .chosen-choices{border: 0;background-image: none}
    .restaurant_more_detail_box .chosen-container{height: 34px;padding-top: 5px}
        .multiselect-container>li>a>label{    padding: 3px 4px 3px 4px;}
    .multiselect-container>li>a{    padding: 2px 10px;}
   .btn-default, .btn-default:hover, .btn-default:focus, .btn-default:active, .btn-default.active, .btn-default.focus, .btn-default:active, .btn-default:focus, .btn-default:hover, .open > .dropdown-toggle.btn-default{ background-color: #fff!important;border: 0px solid #ccc!important;box-shadow: none}
    </style>

    <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <div class="col-sm-12">
        <div class="col-sm-12">

            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('index') }}">Dashboard</a>
                </li>

                <li>
                    <a href="{{ url('manage_restaurant') }}">{{$restaurantdetail[0]->name}}</a>
                </li>
                <li class="active ms-hover">
                    Restaurants Details
                </li>
            </ol>
        </div>
         <div class="col-sm-12">
             <a ><div class="potafo_top_menu_sec potafo_top_menu_act">About</div></a>
            <a href="{{ url('menu/list/'.$id) }}"><div class="potafo_top_menu_sec">Menu</div></a>
            <a href="{{ url('category/list/'.$id) }}"><div class="potafo_top_menu_sec">Category</div></a>
            <a href="{{ url('menu/review/'.$id) }}"><div class="potafo_top_menu_sec">Review</div></a>
            <a href="{{ url('menu/tax/'.$id) }}"><div class="potafo_top_menu_sec">Tax %</div></a>
          </div>

        <form enctype="multipart/form-data" id="upload_form" role="form" method="POST" action="" >
            <div>
                <div class="col-md-6">
                    <div class="card-box table-responsive" style="padding: 8px 10px;">

                        <div class="table_section_scroll">
                            <input type="hidden" name="restaurant_time_count" id="restaurant_time_count" value="{{$restaurant_time_count}}">
                            <input type='hidden' id="eddietsave" name="eddietsave" value="">
                            <input type='hidden' id="edp_exclusive" name="edp_exclusive" value="">
                            <!--<input type='text' id="edrid" name="edrid" value="{{ $id }}">-->
                            {{ Form::hidden('edrid',$id, array ('id'=>'edrid','name'=>'edrid')) }}
                            <input type="hidden" name="oldlogo" id="oldlogo" value="{{ (isset($restaurantdetail[0]->logo)?$restaurantdetail[0]->logo:null) }}">
                            <input type="hidden" name="oldbanner" id="oldbanner" value="{{ (isset($restaurantdetail[0]->banner)?$restaurantdetail[0]->banner:null) }}">
                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Restaurant Name*</span>
                                    {{ Form::text('edrname',$restaurantdetail[0]->name, array ('id'=>'edrname','name'=>'edrname','required','class'=>'form-control','onkeypress' => 'return charonly(event);')) }}
                                </div>
                            </div>
                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Tag Line</span>
                                    {!! Form::text('edtagline',$restaurantdetail[0]->tagline,array('class'=>'form-control','id'=>'edtagline','name'=>'edtagline','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;")) !!}
                                </div>
                            </div>

                            <div class="restaurant_more_detail_row" id="pgroupdiv">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Restaurant Group*</span>
                                    {!! Form::text('edgroup',$restaurantdetail[0]->group_name, array('class'=>'form-control','id'=>'edgroup','name'=>'edgroup','onKeyUp' => 'groupchange(this.value)','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;")) !!}
                                    <div class="searchlist" style="display:none;width: 130%;background:#FFF;margin-top:1px;float:left;" id="suggesstionsgroup"  onMouseOut="mouseoutfnctn(this);">
                                    </div>
                                </div>
                            </div>


                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Restaurant Description</span>
                                    {{ Form::text('eddescription',$restaurantdetail[0]->description, array ('id'=>'eddescription','name'=>'eddescription','required','class'=>'form-control','onkeypress' => 'return charonly(event);')) }}

                                </div>
                            </div>

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Diet*</span>
                                    <div class="status_chck_cc">
                                        <div class="status_chck">
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" @if($restaurantdetail[0]->pure_veg=='N') checked @endif>
                                                <label class="onoffswitch-label" for="myonoffswitch">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            
                            <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text" >
                        <span class="restaurant_more_detail_text_nm">Potafo Exclusive*</span>
                        
                       <div class="onoffswitch" style="float:left;margin-top:8px;">
                                     <input type="checkbox" name="myonoffswitch51" class="onoffswitch-checkbox" id="myonoffswitch51" @if($restaurantdetail[0]->p_exclusive=='Y') checked @endif>
                                     <label class="onoffswitch-label" for="myonoffswitch51">
                                       <span class="onoffswitch-inner"></span>
                                       <span class="onoffswitch-switch"></span>
                                      </label>
                       </div>
                    </div>
                </div>

                            

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Category*</span>
                                    <select id="edcategory" name="edcategory" class="restaurant_more_detail_text_sel">
                                        <option  value='Select' >Select Category</option>
                                        <option @if($restaurantdetail[0]->category=='Juice Bar') selected=selected @endif value='Juice Bar' >Juice Bar</option>
                                        <option @if($restaurantdetail[0]->category=='Restaurant') selected=selected @endif value='Restaurant' >Restaurant</option>
                                        <option @if($restaurantdetail[0]->category=='Cafe') selected=selected @endif value='Cafe' >Cafe</option>
                                        <option @if($restaurantdetail[0]->category=='Potafo Mart') selected=selected @endif value='Potafo Mart' >Potafo Mart</option>
                                    </select>
                                </div>
                            </div>
                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm"> Address </span>
                                    {!! Form::text('edaddress',$restaurantdetail[0]->address, array('class'=>'form-control','id'=>'edaddress','name'=>'edaddress','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;")) !!}
                                </div>
                            </div>
                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Email </span>
                                    {!! Form::text('edemail',$restaurantdetail[0]->email, array('class'=>'form-control','id'=>'edemail','name'=>'edemail','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;")) !!}
                                </div>
                            </div>
                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Currency*</span>

                                    <select id="edcurrency" name="edcurrency" class="restaurant_more_detail_text_sel">
                                        <option value="Select">Select Currency</option>
                                        @foreach($currencylist as $currency_list)
                                            <option @if($restaurantdetail[0]->currency==$currency_list) selected=selected @endif value="{{$currency_list}}">{{$currency_list}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Mobile*</span>
                                    {!! Form::text('edcode',$restaurantdetail[0]->code, array('id'=>'edcode','name'=>'edcode','onkeypress' => 'return numonly(event);','placeholder'=>'+91','style'=>"width:10%;")) !!}
                                    {!! Form::text('edmobile',$restaurantdetail[0]->mob, array('id'=>'edmobile','name'=>'edmobile','onkeypress' => 'return numonly(event);','style'=>"width:90%;")) !!}
                                </div>
                            </div>

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Landline </span>
                                    {!! Form::text('edphone',$restaurantdetail[0]->phone, array('class'=>'form-control','id'=>'edphone','name'=>'edphone','onkeypress' => 'return numonly(event);','required','style'=>"background-color:transparent;")) !!}
                                </div>
                            </div>
                            <div class="restaurant_more_detail_row" style="width:50%;display:none;">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Point of contact</span>
                                    {!! Form::text('edptcontact',$restaurantdetail[0]->point_of_contact, array('class'=>'form-control','id'=>'edptcontact','name'=>'edptcontact','required','style'=>"background-color:transparent;")) !!}

                                </div>
                            </div>
                             <div class="restaurant_more_detail_row" style="width:100%;">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Safety Tag</span>
                                   <select id="safetytag" name="safetytag" class="restaurant_more_detail_text_sel">
                            <option value="0" <?php if($restaurantdetail[0]->safety_tag == 0){ ?>selected <?php } ?>>No Tag</option>
                            <option value="1" <?php if($restaurantdetail[0]->safety_tag == 1){ ?>selected <?php } ?>>Silver Tag</option>
                            <option value="2" <?php if($restaurantdetail[0]->safety_tag == 2){ ?>selected <?php } ?>>Gold Tag</option>
                        </select>
                                </div>
                            </div>
                            <div class="restaurant_more_detail_row" style="width:50%;display:none;">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Popular Display Order* </span>
                                    {!! Form::text('edptcontact',$restaurantdetail[0]->popular_display_order, array('class'=>'form-control','id'=>'edptcontact','name'=>'edptcontact','required','style'=>"background-color:transparent;")) !!}

                                </div>
                            </div>
                            <div class="restaurant_more_detail_row" style="width:48%;margin-right:2%">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Area* </span>
                                    {{ Form::select('edcity',['Select'=>'Select Area']+$citylist,$restaurantdetail[0]->city,['id' => 'edcity','name' =>'edcity', 'class'=>"restaurant_more_detail_text_sel"])}}
                                </div>
                            </div>
                            <div class="restaurant_more_detail_row" style="width:50%">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Country* </span>
                                    <select id="edcountry" name="edcountry" class="restaurant_more_detail_text_sel">
                                        <option value="Select">Select Country</option>
                                        @foreach($countrylist as $list)
                                            <option @if($restaurantdetail[0]->country==$list) selected=selected @endif value="{{$list}}">{{$list}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                    <div class="restaurant_more_detail_row" style="width:48%;margin-right:2%" >
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Delivery Charge</span>
                    {!! Form::text('eddel_charge',$restaurantdetail[0]->delivery_charge, array('class'=>'form-control','id'=>'eddel_charge','name'=>'eddel_charge','onkeypress' => 'return numonly(event);','required','style'=>"background-color:transparent;")) !!}
                    </div>
                    </div>  
                
                    <div class="restaurant_more_detail_row" style="width:50%">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Packing Charge</span>
                    {!! Form::text('edpack_charge',$restaurantdetail[0]->packing_charge, array('class'=>'form-control','id'=>'edpack_charge','name'=>'edpack_charge','onkeypress' => 'return numonly(event);','required','style'=>"background-color:transparent;")) !!}
                    </div>
                    </div>
                            
                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Busy</span>
                                    <select id="busy" name="busy" class="restaurant_more_detail_text_sel">
                                        <option @if($restaurantdetail[0]->busy=='Y') selected=selected @endif value='Y' >Busy</option>
                                        <option @if($restaurantdetail[0]->busy=='N') selected=selected @endif value='N' >Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="restaurant_more_detail_row">
                            <div class="restaurant_more_detail_text">
                            <span class="restaurant_more_detail_text_nm">Restaurant Timing</span>
                            <span class="add_time_btn" onclick="timing()" >ADD TIMING</span>

                            </div>
                            </div>


                        </div>

                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card-box table-responsive" style="padding: 8px 10px;">

                        <div class="table_section_scroll">

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Distance Radius* </span>
                                    <select style="width:40%;margin-right:3%" class="restaurant_more_detail_text_sel" id="edunit" name="edunit">
                                        <option @if($restaurantdetail[0]->unit=='km') selected=selected @endif value="km">KM</option>
                                        {{--<option @if($restaurantdetail[0]->unit=='miles') selected=selected @endif value="miles">Miles</option>--}}
                                    </select>
                                    {{--<input style="width:57%" type="text"   id="range" name="range" placeholder="0">--}}
                                    {!! Form::text('edrange',$restaurantdetail[0]->ranges, array('class'=>'form-control pad-right-50','id'=>'edrange','name'=>'edrange','onkeypress' => 'return numonly(event);','required','style'=>"width:57%;margin-top:10px")) !!}

                                    <div onclick="checkradius()" class="check_radius_btn">Check</div>

                                </div>
                            </div>

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Min Delivery Time </span>
                                    {{--<input style="width:57%" type="text" placeholder="0" value="" id="del_time" name="del_time">(MIN)--}}
                                    {!! Form::text('eddel_time',$restaurantdetail[0]->min_delivery_time, array('class'=>'form-control','id'=>'eddel_time','name'=>'eddel_time','onkeypress' => 'return numonly(event);','required','style'=>"width:57%")) !!}(MIN)
                                </div>
                            </div>

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Min Cart Value</span>
                                    {!! Form::text('edcart_value',$restaurantdetail[0]->min_cart_value, array('class'=>'form-control','id'=>'edcart_value','name'=>'edcart_value','onkeypress' => 'return numonly(event);','required','style'=>"background-color:transparent;")) !!}
                                </div>
                            </div>

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Min Preparation Time delviery</span>
                                    {!! Form::text('edpre_deltime',$restaurantdetail[0]->min_prepration_time, array('class'=>'form-control','id'=>'edpre_deltime','name'=>'edpre_deltime','onkeypress' => 'return numonly(event);','required','style'=>"width:57%")) !!}(MIN)

                                </div>
                            </div>
                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Special Messages Time Delviery</span>
                                    {{--<input type="text" id="message" name="message">--}}
                                    {!! Form::text('edmessage',$restaurantdetail[0]->speical_message, array('class'=>'form-control','id'=>'edmessage','name'=>'edmessage','required','style'=>"background-color:transparent;")) !!}
                                </div>
                            </div>

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Cuisines</span>
                                    {{--<input type="text" id="cuisine" name="cuisine">--}}
                                    {!! Form::text('edcuisine',$restaurantdetail[0]->cuisines, array('class'=>'form-control','id'=>'edcuisine','name'=>'edcuisine','required','style'=>"background-color:transparent;")) !!}
                                </div>
                            </div>

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">License Certification and Numbers</span>
                                    {{--<input type="text" id="lic_cert" name="lic_cert">--}}
                                    {!! Form::text('edlic_cert',$restaurantdetail[0]->license_numbers, array('class'=>'form-control','id'=>'edlic_cert','name'=>'edlic_cert','required','style'=>"background-color:transparent;")) !!}
                                </div>
                            </div>

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm"> Extra Rate %<span style="float: right;margin-right: 5%;color: red;font-size: 11px;display: none" id="extra_per_id"><strong>EXTRA RATE ADD SUCCESSFULLY</strong></span></span>
                                    {{--<input type="text" id="extra_rate" name="extra_rate">--}}
                                    {!! Form::text('edextra_rate',$restaurantdetail[0]->extra_rate_percent, array('id'=>'edextra_rate','name'=>'edextra_rate', 'style'=>'width:80%','onkeypress' => 'return numonly(event);','placeholder'=>'0')) !!}
                                    <a href="#" onclick="apply_to_all('<?=$id?>');" class="rest_edit_apl_btn">APPLY ALL</a>
                                </div>
                            </div>


                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm"> Expensive Rating</span>
                                    <div class="">
                                        <select class="restaurant_more_detail_text_sel" id="edexp_rate" name="edexp_rate">
                                            <option @if($restaurantdetail[0]->expensive_rating=='र') selected=selected @endif value="र">र</option>
                                            <option @if($restaurantdetail[0]->expensive_rating=='र र') selected=selected @endif value="र र"> र र </option>
                                            <option @if($restaurantdetail[0]->expensive_rating=='र र र') selected=selected @endif value="र र र">र र र</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Star Rating</span>
                                    <div class="rating_cc">
                                        <fieldset class="rating" >
                                            <input  type="radio" id="star5" name="rating" value="5" /><label class = "full" for="star5" title=" 5 stars"></label>
                                            <input  type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="4.5 stars"></label>
                                            <input checked="checked"  type="radio" id="star4" name="rating" value="4" /><label class = "full" for="star4" title=" 4 stars"></label>
                                            <input  type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="3.5 stars"></label>
                                            <input   type="radio" id="star3" name="rating" value="3" /><label class = "full" for="star3" title=" 3 stars"></label>
                                            <input  type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="2.5 stars"></label>
                                            <input  type="radio" id="star2" name="rating" value="2" /><label class = "full" for="star2" title="2 stars"></label>
                                            <input  type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="1.5 stars"></label>
                                            <input type="radio" id="star1" name="rating" value="1" /><label class = "full" for="star1" title="1 star"></label>
                                            <input  type="radio" id="starhalf" name="rating" value="half" /><label  class="half" for="starhalf" title="0.5 stars"></label>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Google Address *</span>
                                    <input type='hidden' id="edlat" name="edlat" value="<?php  if(isset($restaurantdetail[0]->geo_cordinates) && $restaurantdetail[0]->geo_cordinates != '') { echo explode(',',$restaurantdetail[0]->geo_cordinates)[0]; }?>">
                                    <input type='hidden' id="edlong" name="edlong" value="<?php  if(isset($restaurantdetail[0]->geo_cordinates) && $restaurantdetail[0]->geo_cordinates != '') { echo explode(',',$restaurantdetail[0]->geo_cordinates)[1]; }?>">
                                    {!! Form::text('edgeo_location',$restaurantdetail[0]->google_location, array('id'=>'edgeo_location','name'=>'edgeo_location')) !!}
                                </div>
                            </div>
                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Status</span>
                                    <select id="edstatus" name="edstatus" class="restaurant_more_detail_text_sel">
                                        <option @if($restaurantdetail[0]->status=='Y') selected=selected @endif value='Y' >Active</option>
                                        <option @if($restaurantdetail[0]->status=='N') selected=selected @endif value='N' >Inactive</option>
                                    </select>
                                </div>
                            </div>
                           <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm">Display Order</span>

                                    {!! Form::text('edorder',$restaurantdetail[0]->popular_display_order, array('class'=>'form-control','id'=>'edorder','name'=>'edorder','onkeypress' => 'return numonly(event);','required','style'=>"background-color:transparent;")) !!}

                                </div>
                            </div>
                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm"> Upload Logo</span>
                                    <div class="file-upload">


                                        <div class="image-upload-wrap image_logo">
                                            <input style="height:100%" class="file-upload-input inp_logo" type='file' id="logo" name="logo" onchange="readlogoURL(this);" accept="image/*" />
                                            <div class="drag-text">
                                                <h3>Drag and drop a file or select add Image</h3>
                                            </div>
                                        </div>
                                        <div class="file-upload-content content_logo">
                                            <img class="file-upload-image file_logo" src="{{$siteurl.$restaurantdetail[0]->logo}}" alt="your image" />
                                            <div class="image-title-wrap">
                                                <button  type="button" onclick="removelogo()" class="remove-image">Remove <span class="image-title title_logo">Uploaded Image</span></button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="restaurant_more_detail_row">
                                <div class="restaurant_more_detail_text">
                                    <span class="restaurant_more_detail_text_nm"> Upload Offer Banner </span>
                                    <div class="file-upload">


                                        <div class="image-upload-wrap image_banner">
                                            <input style="height:100%" class="file-upload-input inp_banner" type='file' id="banner" name="banner" onchange="readbannerURL(this);" accept="image/*" />
                                            <div class="drag-text">
                                                <h3>Drag and drop a file or select add Image</h3>
                                            </div>
                                        </div>
                                        <div class="file-upload-content upload_banner">
                                            <img class="file-upload-image file_banner" src="{{$siteurl.$restaurantdetail[0]->banner}}" alt="your image" />
                                            <div class="image-title-wrap">
                                                <button  type="button" onclick="removebanner()" class="remove-image">Remove <span class="image-title title_banner">Uploaded Image</span></button>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </form>

        <div class="col-sm-12">
            <a id="updating" name="updating" class="staff-add-pop-btn staff-add-pop-btn-new" style="display: block;" onclick="update_restdet();">UPDATE</a>

        </div>

    </div>

    <div class="timing_popup_cc">
        <div class="timing_popup" style="width: 821px !important;">
            <div class="timing_popup_head">Opening/Close Timing
                <div onclick='closebutton()' class="timing_popup_cls"><img src="{{asset('public/assets/images/cancel.png') }}"></div>
                <div class="restaurant_more_detail_text" style="width:40%;margin-right:2%">
                    <span class="restaurant_more_detail_text_nm">Day Select</span>
                    <div class="restaurant_more_detail_box" style="border: solid 1px #e2e2e2;margin-top: 5px;">
                        <select data-placeholder="Select day"  class="day_select" tabindex="3" name='day' id='day' style="display:none">
                            <option value='ALL'>ALL</option>
                            <option value='MONDAY' >MON</option>
                            <option value='TUESDAY'>TUE</option>
                            <option value='WEDNESDAY'>WED</option>
                            <option value='THURSDAY'>THU</option>
                            <option value='FRIDAY'>FRI</option>
                            <option value='SATURDAY'>SAT</option>
                            <option value='SUNDAY'>SUN</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="timing_popup_contant">
                <div class="restaurant_more_detail_row">

                    <div class="restaurant_more_detail_text" style="width:33%;margin-right:2%">
                        <span class="restaurant_more_detail_text_nm">From</span>
                        <select  class="restaurant_more_detail_box_sel" tabindex="3" name='from1' id='from1'>
                            <option value="">Select Hour</option>
                           @for($i=0;$i<25;$i++)
                            <option value='{{ sprintf("%02d",$i)}}' >{{sprintf("%02d",$i)}}</option>
                           @endfor
                        </select>
                        {{--<input type="number" style="width:50%" class="restaurant_more_detail_box_sel" id='from1' name='from1' min="1" max="24">--}}
                        {{--<input type="number" style="width:50%" class="restaurant_more_detail_box_sel" id='fromsec1' name='fromsec1' min="0" max="59">--}}
                        <select  class="restaurant_more_detail_box_sel" tabindex="3" name='fromsec1' id='fromsec1'>
                            <option value="">Select Minutes</option>
                            @for($i=00;$i<60;$i++)
                                <option value='{{sprintf("%02d",$i)}}' >{{sprintf("%02d",$i)}}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="restaurant_more_detail_text" style="width:33%;margin-right:5%">
                        <span class="restaurant_more_detail_text_nm">To</span>
                        <select  class="restaurant_more_detail_box_sel" tabindex="3" name='to1' id='to1'>
                            <option value="">Select Hour</option>
                            @for($i=0;$i<25;$i++)
                                <option value='{{sprintf("%02d",$i)}}' >{{sprintf("%02d",$i)}}</option>
                            @endfor
                        </select>
                        {{--<input type="number" style="width:50%" class="restaurant_more_detail_box_sel" id='from1' name='from1' min="1" max="24">--}}
                        {{--<input type="number" style="width:50%" class="restaurant_more_detail_box_sel" id='fromsec1' name='fromsec1' min="0" max="59">--}}
                        <select  class="restaurant_more_detail_box_sel" tabindex="3" name='tosec1' id='tosec1'>
                            <option value="">Select Minutes</option>
                            @for($i=00;$i<60;$i++)
                                <option value='{{sprintf("%02d",$i)}}' >{{sprintf("%02d",$i)}}</option>
                            @endfor
                        </select>
                        {{--<input type="number" style="width:50%" class="restaurant_more_detail_box_sel" name='to1' id='to1' min="1" max="24">--}}
                        {{--<input type="number" style="width:50%" class="restaurant_more_detail_box_sel" name='tosec1' id='tosec1' min="0" max="59">--}}
                    </div>
                    <input type="hidden" id="slno" name="slno">
                    <div class="restaurant_more_detail_text" style="width:15%;">
                        <span class="add_time_btn_pop" onclick='addtime();timing();' >ADD</span>
                    </div>
                </div>


                <div class="timing_popup_contant_tabl timing">
                    <table id="listing" class="timing_sel_popop_tbl">
                        <thead>
                        <tr>
                            <th style="width:100px">DAY</th>
                            <th style="width:90px">From</th>
                            <th  style="width:90px">To</th>
<!--                            <th  style="width:90px">From</th>
                            <th  style="width:90px">To</th>-->
                            <th  style="width:40px">Action</th>
                        </tr>
                        </thead>
                        <tbody id="tBody">
                      
                            
                            
                            
<!--                        <td  style="width:100px">Monday</td>
                            <td  style="width:90px">10 AM <a class="btn button_table"><i class="fa fa-pencil"></i></a></td>
                            <td  style="width:90px">11 PM <a class="btn button_table"><i class="fa fa-pencil"></i></a></td>-->
<!--                            <td  style="width:90px"><div class="restaurant_more_detail_text" style="width:100%;">
                                    <input type="number" style="width:50%" class="restaurant_more_detail_box_sel">
                                    <select style="width:50%" class="restaurant_more_detail_box_sel" id='ampm3' name='ampm3'>
                                        <option value='AM'>AM</option>
                                        <option value='PM'>PM</option>
                                    </select>
                                </div></td>-->
                            <!--<td  style="width:90px"><a class="btn button_table"><i class="fa fa-plus"></i></a></td>-->
<!--                            <td  style="width:40px"><a class="btn button_table"><i class="fa fa-trash"></i></a></td>-->
                     
                        </tbody>

                    </table>
                </div>
<!--                <div class="col-sm-12">
                    <a  class="staff-add-pop-btn staff-add-pop-btn-new" style="display: block;">SAVE</a>

                </div>-->

            </div>
        </div>
    </div>

    <div class="check_radius_popup_sec" style="display:none;">
        <div class="check_radius_popup">

        <div class="timing_popup_head">CHECK DISTANCE RADIUS
                <div  class="timing_popup_cls" onclick="closeradius()"><img src="../public/assets/images/cancel.png"></div>
        </div>
        <div class="check_radius_popup_contant">
            <div class="restaurant_more_detail_text" style="width:78%;">
                    <span class="restaurant_more_detail_text_nm">Google Address</span>
                    <input type='hidden' id="checklat" name="checklat" value="">
                    <input type='hidden' id="checklong" name="checklong" value="">
                    <input style="padding-right:40px;" type="text" id="check_address" name="check_address" value="" placeholder="Enter a location" autocomplete="off">
                    <div onclick="radiusclear()"class="check_radius_clear_btn">C</div>
            </div>
            <div class="restaurant_more_detail_text" style="width:20%;margin-left:2%;">
                    <span class="restaurant_more_detail_text_nm">Radius</span>
                    <input type="text" disabled id="radius" name="radius" placeholder="" autocomplete="off">
            </div>
        </div>

        </div>
        
    </div>



    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    <style>#datatable-fixed-col_filter{display:none}table.dataTable thead th{white-space:nowrap;padding-right: 20px;}
        .on-default	{margin-left:10px;}div.dataTables_info {padding-top:13px;}
        .height_align{
            margin-top: 12px;
        }
    </style>
    <link href="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
    <script src="{{ asset('public/assets/pages/jquery.sweet-alert.init.js') }}"></script>
    <style>#datatable-fixed-col_filter{display:none}table.dataTable thead th{white-space:nowrap;padding-right: 20px;}
        .on-default	{margin-left:10px;}div.dataTables_info {padding-top:13px;}
    </style>

@section('jquery')


    <script src="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <link href="{{ asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('public/assets/dark/plugins/custombox/css/custombox.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />

    <script>
        $(document).ready(function()
        {
          $("#rname").focus();
          var logoload = $("#oldlogo").val();
          var bannerload = $("#oldbanner").val();
          if(logoload != '') 
          {
            $(".content_logo").show();
            $(".image_logo").hide();
          }
          if(bannerload != '')
          {
            $(".upload_banner").show();
            $(".image_banner").hide();
          }

        });
        function readlogoURL(input) {
            if (input.files && input.files[0]) {

                var reader = new FileReader();

                reader.onload = function(e) {
                    $('.image_logo').hide();

                    $('.file_logo').attr('src', e.target.result);
                    $('.content_logo').show();

                    $('.title_logo').html(input.files[0].name);
                };

                reader.readAsDataURL(input.files[0]);

            } else {
                removelogo();
            }
        }

        function readbannerURL(input) {
            if (input.files && input.files[0]) {

                var reader = new FileReader();

                reader.onload = function(e) {
                    $('.image_banner').hide();

                    $('.file_banner').attr('src', e.target.result);
                    $('.upload_banner').show();

                    $('.title_banner').html(input.files[0].name);
                };

                reader.readAsDataURL(input.files[0]);

            } else {
                removebanner();
            }
        }

        function removelogo() {
            $('.inp_logo').replaceWith($('.inp_logo').clone());
            $('.content_logo').hide();
            $('.image_logo').show();
        }

        function removebanner() {
            $('.inp_banner').replaceWith($('.inp_banner').clone());
            $('.upload_banner').hide();
            $('.image_banner').show();
        }

        $('.image-upload-wrap').bind('dragover', function () {
            $('.image-upload-wrap').addClass('image-dropping');
        });
        $('.image-upload-wrap').bind('dragleave', function () {
            $('.image-upload-wrap').removeClass('image-dropping');
        });

        function update_restdet()
        {
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var eddiet = $("#myonoffswitch").prop("checked");
            $("#eddietsave").val(eddiet);
            var edp_exclusive = $("#myonoffswitch51").prop("checked");
            $("#edp_exclusive").val(edp_exclusive);
            var edrid = $("#edrid").val();
            var edrname = $("#edrname").val();
            var edgroup = $("#edgroup").val();
            var eddescription = $("#eddescription").val();
            var edtagline = $("#edtagline").val();
            var edcode = $("#edcode").val();
            var edmobile = $("#edmobile").val();
            var edcategory = $("#edcategory").val();
            var edaddress = $("#edaddress").val();
            var edemail = $("#edemail").val();
            var busy = $("#busy").val();
            var edcurrency = $("#edcurrency").val();
            var edcountry = $("#edcountry").val();
            var edphone = $("#edphone").val();
            var edptcontact = $("#edptcontact").val();
            var edcity = $("#edcity").val();
            var edunit = $("#edunit").val();
            var edrange = $("#edrange").val();
            var eddel_time=$("#eddel_time").val()
            var edcart_value = $("#edcart_value").val();
            var edpre_deltime = $("#edpre_deltime").val();
            var edmessage = $("#edmessage").val();
            var edcuisine = $("#edcuisine").val();
            var edlic_cert = $("#edlic_cert").val();
            var edextra_rate = $("#edextra_rate").val();
            var edgeo_location = $("#edgeo_location").val();
            var logo = $("#logo").val();
            var edstatus = $("#edstatus").val();
            var edorder = $("#edorder").val();
            var eddel_charge = $("#eddel_charge").val();
            var edpack_charge = $("#edpack_charge").val();
            var edlat = $("#edlat").val();
            var edlong = $("#edlong").val();
            var banner = $("#banner").val();
            if(edrname == '') {
                $("#edrname").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter Restaurant Name');
                return false;
            }
            if(edrname.indexOf('\'') > -1)
            {
                $("#edrname").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Single Quotes Not allowed In Restuarant Name.');
                return false;
            }
            if(edrname.indexOf('\"') > -1)
            {
                $("#edrname").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Double Quotes Not allowed In Restuarant Name.');
                return false;
            }
            if(edgroup == '')
            {
                $("#edgroup").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter Group');
                return false;
            }

            if(edcategory == 'Select') {
                $("#edcategory").focus();
               $.Notification.autoHideNotify('error', 'bottom right','Select Category');
                return false;
            }
            if(edcurrency == 'Select') {
                $("#edcategory").focus();
               $.Notification.autoHideNotify('error', 'bottom right','Select Currency');
                return false;
            }
            if(edmobile == '') {
                $("#edmobile").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter Mobile');
                return false;
            }
            if(edcity == 'Select')
            {
                $("#edcity").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Select Area');
                return false;
            }
            if(edcountry == 'Select')
            {
                $("#edcountry").focus();
               $.Notification.autoHideNotify('error', 'bottom right','Select country');
                return false;
            }
            if(edgeo_location == '') {
                $("#edgeo_location").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Select Location');
                return false;
            }
          if (logo != '')
                 {
                     if (!hasExtension('logo', ['.jpg','.jpeg', '.gif', '.png','.JPG','.JPEG','.GIF','.PNG']))
                     {
                         $.Notification.autoHideNotify('error', 'bottom right', 'Upload Gif or Jpg Images Only.');
                         $("#logo").focus();
                         return false;
                     }
                 }
         if (banner != '')
                 {
                     if (!hasExtension('banner', ['.jpg','.jpeg', '.gif', '.png','.JPG','.JPEG','.GIF','.PNG']))
                     {
                         $.Notification.autoHideNotify('error', 'bottom right', 'Upload Gif or Jpg Images Only.');
                         $("#banner").focus();
                         return false;
                     }
                 }

            if(true)
            {
                var formdata = new FormData($('#upload_form')[0]);
                $.ajax({
                    method: "post",
                    url : "../api/edit_restaurant",
                    data : formdata,
                    cache : false,
                    crossDomain : true,
                    async : false,
                    processData : false,
                    contentType: false,
                    dataType :'text',
                    success : function(result)
                    {
                        var json_x= JSON.parse(result);
                        if((json_x.msg)=='success')
                        {
                            updateto_firebase(edlat,edlong,edrname,edcity,edrid,"update",edstatus);
                           window.location.href = "../manage_restaurant";
                            swal({

                                title: "",
                                text: "Updated Successfully",
                                timer: 4000,
                                showConfirmButton: false
                            });

                        }
                        else if((json_x.msg)=='already exist')
                        {
                            window.location.href = "../manage_restaurant";
                            swal({

                                title: "",
                                text: "Already Exist",
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
            return true;

        }
function updateto_firebase(lat,long,rname,city,resultid,status,edstatus)
{//alert(edstatus)
    $.ajax({
        method: "post",
        url: "../api/insertrest_firebase",
        data:{"lat":lat,"long":long,"rname":rname,"city":city,"resultid":resultid,"status":status,"edstatus":edstatus},
        success: function(result)
        { //alert("gg")
            var json_x= JSON.parse(result);
            alert(json_x.msg);
        },
        error: function (jqXHR, textStatus, errorThrown) {
//                    alert(errorThrown);
            $("#errbox").text(jqxhr.responseText);
        }
    });
}
        function groupchange(val)
        {
            var val = val;
            var n = val.lastIndexOf(',');
            var str1 =  val.slice(0,n);
            var str2 =  val.slice(n+1,val.length);
            if(n == -1)
            {
                var val = val;
            }
            else
            {
                var str1 =  val.slice(0,n);
                var str2 =  val.slice(n+1,val.length);
                var val  =str2;
            }

            if(val != '')
            {
                var temp = val;
                var count = temp.length;
                var segments = val.split(',');
                if (temp.indexOf(',') != -1) {
                    var val = segments[1];
                }
                else{
                    var val = val;
                }
                var count = val.length;
                if(parseInt(count)>= 1) {


                    var data = {'searchterm': val};
                    $.ajax({

                        method: "get",
                        url : "../groupautosearch",
                        data : data,
                        cache : false,
                        crossDomain : true,
                        async : false,
                        dataType :'text',
                        success: function (data)
                        {
                            $("#suggesstionsgroup").empty();
                            $.each(JSON.parse(data), function (i, indx)
                            {
                                if ($("#search_" + indx.b_id).length == 0)
                                {

                                    $("#suggesstionsgroup").show();
                                    $("#suggesstionsgroup").append('<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="search_' + indx.id + '" onclick=\'selectname("' + indx.group_name + '","' + indx.id + '")\'>' + '<p>' + indx.group_name + '</p></div>');
                                }
                            });

                        },
                        error: function () {
                            $("#errbox").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                        }
                    });
                }
                else
                {
                    $("#suggesstionsgroup").html('');
                }
            }
            return true;
        }
        function selectname(selected_value,id)
        {
            var val = $("#edgroup").val();
            var n = val.lastIndexOf(',');
            var str1 =  val.slice(0,n);
            if (n == -1)
            {
                $("#edgroup").val(selected_value,id);

            }
            else
            {
                $("#edgroup").val(str1 + ',' + selected_value);
                $("#suggesstionsgroup").hide();
            }
            $("#suggesstionsgroup").hide();


        }
    </script>
    <script>
        function numonly(evt)
        {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31
                    && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

    </script>
    <script>
        $("#day").change(function()
        {
            $("#from1").val('');
            $("#fromsec1").val('');
            $("#to1").val('');
            $("#tosec1").val('');
            timing();
        });
      
        function timing() 
        {
           $(".timing_popup_cc").show();
           var edrid = $("#edrid").val();
           var prodhtml =  $('#listing');
            var day =$("#day").val();
           var data = {"edrid": edrid,'day':day};
           $.ajax({
                method: "get",
                url: "../api/view_time",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                   $('#listing tbody').html('');
                   $('#listing tbody').html(result);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#errbox").text(jqxhr.responseText);
                }
            });
        }

    function closebutton(){
    $(".timing_popup_cc").hide();
 }


    </script>

<script type="text/javascript">
   $(document).ready(function() {
     $('.day_select').multiselect({
        checkboxName: function(option) {
            return 'multiselect[]';
         },
        includeSelectAllOption: true,
     });
     });   
</script>
<script>
function addtime()
{
 var day = $("#day").val();
 var from1 = $("#from1").val();
 var fromsec1 = $("#fromsec1").val();
 var to1 = $("#to1").val();
 var tosec1 = $("#tosec1").val();
 var edrid = $("#edrid").val();
 var count = $("#restaurant_time_count").val();
 var tablecount=$("#listing tbody tr").length;
//
     if (day != 'ALL') {

         //  if(parseInt(tablecount) == 1)
// {
//
//     return false;
// }
         if(parseInt(tablecount)  >= parseInt(count)) {
             swal({

                 title: "",
                 text: "Entry Restricted",
                 timer: 2000,
                 showConfirmButton: false
             });
             $("#from1").val('');
             $("#fromsec1").val('');
             $("#to1").val('');
             $("#tosec1").val('');
             return false;
         }
     }
// }
// else
// {
        /*if (day == '') {
            $("#day").focus();
            swal({

                title: "",
                text: "Select day",
                timer: 2000,
                showConfirmButton: false
            });
            return false;
        }*/
        if (from1 == '') {
            $("#from1").focus();
            swal({

                title: "",
                text: "Select from Time",
                timer: 2000,
                showConfirmButton: false
            });
            return false;
        }
        if (to1 == '') {
            $("#to1").focus();
            swal({

                title: "",
                text: "Select to Time",
                timer: 2000,
                showConfirmButton: false
            });
            return false;
        }
        var tovalue = $(".timeappend").find('td:eq(2)').text();
        if(from1 <= tovalue)
        {
            swal({

                title: "",
                text: "From Time should be greater than previous time.To proceed please delete time",
                timer: 4000,
                showConfirmButton: false
            });
            return false;
        }
//        if (true) {
            var reset_data = {
                "day": day,
                "from1": from1,
                "fromsec1": fromsec1,
                "to1": to1,
                "tosec1": tosec1,
                "edrid": edrid,
            };
            $.ajax({
                method: "post",
                url: "../api/openclose_time",
                data: reset_data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (data) {
                    var jsonx = JSON.parse(data);
                    if (jsonx['msg'] == 'success') {
                        swal({
                            title: "",
                            text: "Added Successfully",
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }
                    else {
                        swal({
                            title: "",
                            text: jsonx['msg'],
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }
                    $('#from1').val('');
                    $('#fromsec1').val('');
                    $('#to1').val('');
                    $('#tosec1').val('');
                    $('#append_filter_data').html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
    //}
//  }
            return true;
}

function time_delete(day,slno,index,total)
{
   $(".timing_popup_cc").show();
   var prodhtml =  $('#listing');
   var day = day;
   var slno = slno;
   var edrid = $("#edrid").val();
   var data = {"day": day,"slno":slno,"edrid":edrid};
    var count = $("#restaurant_time_count").val();
    var resultss = confirm("Want to delete?");
    if(total == count)
    {
        if(index == total)
        {
            swal({
                title: "",
                text: "Delete First Timing to continue",
                timer: 1000,
                showConfirmButton: false
            });
            return false;
        }
    }
    if (resultss) {
           $.ajax({
                method: "get",
                url: "../api/delete_time",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                $.ajax({
                method: "get",
                url: "../api/view_time",
                data:  {"day": $("#day").val(),"slno":slno,"edrid":edrid},
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                   $('#listing tbody').html('');
                   $('#listing tbody').html(result);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#errbox").text(jqxhr.responseText);
                }
            });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#errbox").text(jqxhr.responseText);
                }
            });
}
}

</script>
<script>
        function apply_to_all(id)
        {
            var edextra_rate = $('#edextra_rate').val();
            $('#edextra_rate').focus();
            $.ajax({
                    method: "post",
                    url : "../api/tax_apply_to_all_menu",
                    data : {'id':id,'edextra_rate':edextra_rate},
                    success : function(result)
                    {
                          if(result = 'succ')
                          {
                              $('#extra_per_id').show();
                          }else{
                              $('#extra_per_id').hide();
                          }
                           $('#edextra_rate').focus();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            
        }

    function checkradius()
    {
        $(".check_radius_popup_sec").show();
        $("#radius").val('');
        $("#check_address").val('');
    }
    function closeradius()
    {
        $(".check_radius_popup_sec").hide();
    }
    function radiusclear()
    {
        $("#check_add").val('');
        $("#radius").val('');
    }
</script>
<script>
    $(document).ready(function() {
    var autocomplete = new google.maps.places.Autocomplete($("input[name=edgeo_location]")[0], {});
    autocomplete.setComponentRestrictions({'country': ['IN' ]});
    autocomplete.setComponentRestrictions({'locality': ['kozhikode']});
    google.maps.event.addListener(autocomplete, 'place_changed', function(){
            var place = autocomplete.getPlace();
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            $("#edlat").val(lat);
            $("#edlong").val(lng);
    });
   var autocompletes = new google.maps.places.Autocomplete($("input[name=check_address]")[0], {});
        autocompletes.setComponentRestrictions({'country': ['IN' ]});
        autocompletes.setComponentRestrictions({'locality': ['kozhikode']});
        google.maps.event.addListener(autocompletes, 'place_changed', function(){
            var lat2 = $("#edlat").val();
            var long2 = $("#edlong").val();
            if(lat2 == '' || long2 == '')
            {
                $.Notification.autoHideNotify('error', 'bottom right','Select Restaurant Google Address');
                return false;
            }
            else
            {
                var places = autocompletes.getPlace();
                var lats = places.geometry.location.lat();
                var lngs = places.geometry.location.lng();   //  alert(lats+' '+lngs);
                $("#checklat").val(lats);
                $("#checklong").val(lngs);
                $.ajax({
                    method: "post",
                    url : "../api/radius_calculate",
                    data : {'lat1':lats,'long1':lngs,'lat2':lat2,'long2':  long2},
                    success : function(result)
                    {
                       $("#radius").val(result);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
    });
    });
</script>

@stop
@endsection