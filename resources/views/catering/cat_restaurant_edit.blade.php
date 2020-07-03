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
                    <a href="{{ url('catering_restaurant') }}">{{$restaurantdetail[0]->cr_name}}</a>
                </li>
                <li class="active ms-hover">
                    Restaurants Details
                </li>
            </ol>
        </div>
         <div class="col-sm-12">
             <a ><div class="potafo_top_menu_sec potafo_top_menu_act">About</div></a>
             <a href="{{ url('menu/types/'.$id) }}"><div class="potafo_top_menu_sec ">Type</div></a>
             <a href="{{ url('cat_restaurant_category/'.$id) }}"><div class="potafo_top_menu_sec ">Category</div></a>
            <a href="{{ url('cat_restaurant_pincode/'.$id) }}"><div class="potafo_top_menu_sec ">Pincode</div></a>
            <a href="{{ url('cat_restaurant_tax/'.$id) }}"><div class="potafo_top_menu_sec ">Tax</div></a>
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
                            <input type="hidden" name="oldlogo" id="oldlogo" value="{{ (isset($restaurantdetail[0]->cr_pic)?$restaurantdetail[0]->cr_pic:null) }}">
                            
                            
                            <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Restaurant Name*</span>
                        {!! Form::text('rest_name',$restaurantdetail[0]->cr_name, ['class'=>'form-control','id'=>'rest_name','name'=>'rest_name','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                    </div>
                </div>
                <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Cusines*</span>
                        {!! Form::text('rest_cusines',$restaurantdetail[0]->cr_cusines, ['class'=>'form-control','id'=>'rest_cusines','name'=>'rest_cusines','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                    </div>
                </div>
                <div class="restaurant_more_detail_row" id="pgroupdiv">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Custom messages</span>
                        {!! Form::text('rest_custmsg',$restaurantdetail[0]->cr_custom_message, ['class'=>'form-control','id'=>'rest_custmsg','name'=>'rest_custmsg','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                        <div class="searchlist" style="display:none;width: 130%;background:#FFF;margin-top:1px;float:left;" id="suggesstionsgroup"  onMouseOut="mouseoutfnctn(this);">
                        </div>
                    </div>
                </div>

 <!--<div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Veg Only*</span>
                        <div class="status_chck_cc">
                            <div class="status_chck">
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" @if($restaurantdetail[0]->cr_veg_only=='N') checked @endif>
                                                <label class="onoffswitch-label" for="myonoffswitch">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                            </div>
                        </div>    
                            
                    </div>
                </div>-->
 <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Diet*</span>
                        <select id="rest_veg" name="rest_veg" class="restaurant_more_detail_text_sel">
                            
                            <option value='Y' @if($restaurantdetail[0]->cr_veg_only=='Y') selected=selected @endif>Veg</option>
                            <option value='N' @if($restaurantdetail[0]->cr_veg_only=='N') selected=selected @endif>Non-Veg</option>
                            
                        </select>
                    </div>
                </div>
               
                <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                       <span class="restaurant_more_detail_text_nm"> Address </span>
                        {!! Form::text('rest_address',$restaurantdetail[0]->cr_address, ['class'=>'form-control','id'=>'rest_address','name'=>'rest_address','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                    </div>
                </div>
                <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Display Order*</span>
                        {!! Form::text('rest_disporder',$restaurantdetail[0]->cr_display_order, ['class'=>'form-control','id'=>'rest_disporder','name'=>'rest_disporder','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                    </div>
                </div>
               <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Status*</span>
                        <select id="rest_status" name="rest_status" class="restaurant_more_detail_text_sel">
                            <option  value='Select' >Select Status</option>
                            <option value='Active' @if($restaurantdetail[0]->cr_status=='Active') selected=selected @endif >Active</option>
                            <option value='InActive'@if($restaurantdetail[0]->cr_status=='InActive') selected=selected @endif >InActive</option>
                            
                        </select>
                    </div>
                </div>
               <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Owner Name*</span>
                        {!! Form::text('rest_owner',$restaurantdetail[0]->cr_owner_name, ['class'=>'form-control','id'=>'rest_owner','name'=>'rest_owner','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                    </div>
                </div>
                <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Owner Contact*</span>
                        {{--<input style="width:10%" id="ind" name="ind" type="text" >--}}
                        {{--<input style="width:90%" id="mobile" name="mobile" type="text">--}}
                        {!! Form::text('rest_ownerind',+91, ['id'=>'rest_ownerind','name'=>'rest_ownerind','onkeypress' => 'return numonly(event);','style'=>"width:10%;",'disabled'=>'true']) !!}
                        {!! Form::text('rest_ownermobile',$restaurantdetail[0]->cr_owner_contact, ['id'=>'rest_ownermobile','name'=>'rest_ownermobile','onkeypress' => 'return numonly(event);','style'=>"width:90%;"]) !!}
                    </div>
                </div>
               <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Manager Name</span>
                        {!! Form::text('rest_manager',$restaurantdetail[0]->cr_manager_name, ['class'=>'form-control','id'=>'rest_manager','name'=>'rest_manager','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                    </div>
                </div>
                <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Manager Contact</span>
                        {{--<input style="width:10%" id="ind" name="ind" type="text" >--}}
                        {{--<input style="width:90%" id="mobile" name="mobile" type="text">--}}
                        {!! Form::text('rest_manind',+91, ['id'=>'rest_manind','name'=>'rest_manind','onkeypress' => 'return numonly(event);','style'=>"width:10%;",'disabled'=>'true']) !!}
                        {!! Form::text('rest_manmobile',$restaurantdetail[0]->cr_manager_contact, ['id'=>'rest_manmobile','name'=>'rest_manmobile','onkeypress' => 'return numonly(event);','style'=>"width:90%;"]) !!}
                    </div>
                </div>
               <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Office Contact</span>
                        {{--<input style="width:10%" id="ind" name="ind" type="text" >--}}
                        {{--<input style="width:90%" id="mobile" name="mobile" type="text">--}}
                        {!! Form::text('rest_ofcind',+91, ['id'=>'rest_ofcind','name'=>'rest_ofcind','onkeypress' => 'return numonly(event);','style'=>"width:10%;",'disabled'=>'true']) !!}
                        {!! Form::text('rest_ofcmobile',$restaurantdetail[0]->cr_office_contact, ['id'=>'rest_ofcmobile','name'=>'rest_ofcmobile','onkeypress' => 'return numonly(event);','style'=>"width:90%;"]) !!}
                    </div>
                </div>
                
              
              <!--  <div class="restaurant_more_detail_row" style="width:48%;margin-right:2%" >
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Delivery Charge</span>
                        <input type="text" id="rest_del_charge" name="rest_del_charge" value="<?=$restaurantdetail[0]->cr_dlv_charge?>" placeholder="0" onkeypress = "return numonly(event)">
                    </div>
                 </div>  
                
                    <div class="restaurant_more_detail_row" style="width:50%">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Packing Charge</span>
                        <input type="text" id="rest_pack_charge" name="rest_pack_charge" value="<?=$restaurantdetail[0]->cr_packing_charge?>" placeholder="0" onkeypress = "return numonly(event)">
                    </div>
                    </div>-->
                
                
                </div>


                        </div>

                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card-box table-responsive" style="padding: 8px 10px;">

                        <div class="table_section_scroll">

                           <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">FSSAI</span>
                        <input type="text" id="rest_fssai" name="rest_fssai" value="<?=$restaurantdetail[0]->cr_fssai?>">
                    </div>
                </div>
            <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">gstin</span>
                        <input type="text" id="rest_gstin" name="rest_gstin" value="<?=$restaurantdetail[0]->cr_gstin?>">
                    </div>
                </div>
            <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Bank Name</span>
                        <input type="text" id="rest_bankname" name="rest_bankname" value="<?=$restaurantdetail[0]->cr_bank_name?>">
                    </div>
                </div>
            <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Bank Account Name</span>
                        <input type="text" id="rest_bankaccount" name="rest_bankaccount" value="<?=$restaurantdetail[0]->cr_bank_account_name?>">
                    </div>
                </div>
            <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Bank Account Number</span>
                        <input type="text" id="rest_bankaccountnmbr" name="rest_bankaccountnmbr" value="<?=$restaurantdetail[0]->cr_bank_account_no?>">
                    </div>
                </div>
                <div class="restaurant_more_detail_row" style="width:48%;margin-right:2%">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Working From Time* </span>
                <!-- <input autocomplete="off"   id="rest_workfrom"  name="rest_workfrom" type="text" class="form-control datefield" value="<?=$restaurantdetail[0]->cr_working_from_time?>">-->
                        <div class="restaurant_more_detail_text" >
                        <?php
                        $fromtime=$restaurantdetail[0]->cr_working_from_time;
                        $str_arr = explode (":", $fromtime); 
                        
                        ?>
                        <select  class="restaurant_more_detail_box_sel" tabindex="3" name='rest_fromhour' id='rest_fromhour'>
                            <option value="">Select Hour</option>
                           @for($i=0;$i<25;$i++)
                            <option value='{{ sprintf("%02d",$i)}}' @if($str_arr[0]==$i) selected=selected @endif>{{sprintf("%02d",$i)}}</option>
                           @endfor
                        </select>
                        {{--<input type="number" style="width:50%" class="restaurant_more_detail_box_sel" id='from1' name='from1' min="1" max="24">--}}
                        {{--<input type="number" style="width:50%" class="restaurant_more_detail_box_sel" id='fromsec1' name='fromsec1' min="0" max="59">--}}
                        <select  class="restaurant_more_detail_box_sel" tabindex="3" name='rest_fromminut' id='rest_fromminut'>
                            <option value="">Select Minutes</option>
                            @for($i=00;$i<60;$i++)
                                <option value='{{sprintf("%02d",$i)}}' @if($str_arr[1]==$i) selected=selected @endif>{{sprintf("%02d",$i)}}</option>
                            @endfor
                        </select>
                    </div>

                    </div>
                </div>
            <div class="restaurant_more_detail_row" style="width:48%;margin-right:2%">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Working To Time* </span>
                  <!--<input autocomplete="off"   id="rest_workto"  name="rest_workto" type="text" class="form-control datefield" value="">-->
                        <div class="restaurant_more_detail_text" >
                         <?php
                         if(($restaurantdetail[0]->cr_working_to_time)!="")
                         {
                        $totime=$restaurantdetail[0]->cr_working_to_time;
                        $str_arr = explode (":", $totime); 
                         }else
                         {
                         $str_arr[0]="";
                         $str_arr[1]="";
                         }
                        
                        ?>
                        <select  class="restaurant_more_detail_box_sel" tabindex="3" name='rest_tohour' id='rest_tohour'>
                            <option value="">Select Hour</option>
                           @for($i=0;$i<25;$i++)
                            <option value='{{ sprintf("%02d",$i)}}' @if($str_arr[0]==$i) selected=selected @endif>{{sprintf("%02d",$i)}}</option>
                           @endfor
                        </select>
                        {{--<input type="number" style="width:50%" class="restaurant_more_detail_box_sel" id='from1' name='from1' min="1" max="24">--}}
                        {{--<input type="number" style="width:50%" class="restaurant_more_detail_box_sel" id='fromsec1' name='fromsec1' min="0" max="59">--}}
                        <select  class="restaurant_more_detail_box_sel" tabindex="3" name='rest_tominut' id='rest_tominut'>
                            <option value="">Select Minutes</option>
                            @for($i=00;$i<60;$i++)
                                <option value='{{sprintf("%02d",$i)}}' @if($str_arr[1]==$i) selected=selected @endif>{{sprintf("%02d",$i)}}</option>
                            @endfor
                        </select>
                    </div>

                    </div>
                </div>
            
            <div class="restaurant_more_detail_row" style="width:48%;margin-right:2%">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Service Delivery*</span>
                        <select id="rest_serdelivry" name="rest_serdelivry" class="restaurant_more_detail_text_sel" >
                           
                            <option value='Y' @if($restaurantdetail[0]->cr_service_del=='Y') selected=selected @endif>Yes</option>
                            <option value='N' @if($restaurantdetail[0]->cr_service_del=='N') selected=selected @endif>No</option>
                            
                        </select>
                    </div>
                </div>
            <div class="restaurant_more_detail_row" style="width:48%;margin-right:2%">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Catering</span>
                        <select id="rest_catering" name="rest_catering" class="restaurant_more_detail_text_sel">
                           
                            <option value='Y' @if($restaurantdetail[0]->cr_catering=='Y') selected=selected @endif>Yes</option>
                            <option value='N' @if($restaurantdetail[0]->cr_catering=='N')selected=selected @endif>No</option>
                            
                        </select>
                    </div>
                </div>
             <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Preparation Day Count*</span>
                        <input style="width:57%" type="text"  value="<?=$restaurantdetail[0]->cr_preparation_day_count?>" id="rest_prepdayct" name="rest_prepdayct" onkeypress = "return numonly(event)">(Days)
                    </div>
                </div>
            <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm">Max Order Per day*</span>
                        <input style="width:57%" type="text"  value="<?=$restaurantdetail[0]->cr_max_order_per_day?>" id="rest_maxorder" name="rest_maxorder" onkeypress = "return numonly(event)">
                    </div>
                </div>
                
                 <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text">
                        <span class="restaurant_more_detail_text_nm"> Upload Picture*</span>
                <div class="file-upload">


              <div class="image-upload-wrap image_logo">
                <input style="height:100%" class="file-upload-input inp_logo" type='file' id="logo" name="logo" onchange="readlogoURL(this);" accept="image/*" />
                <div class="drag-text">
                  <h3>Drag and drop a file or select add Image</h3>
                </div>
              </div>
              <div class="file-upload-content content_logo">
                <img class="file-upload-image file_logo" src="#" alt="your image" />
                <div class="image-title-wrap">
                  <button  type="button" onclick="removelogo()" class="remove-image">Remove <span class="image-title title_logo">Uploaded Image</span></button>
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
<script src="{{ asset('public/assets/js/bootstrap-datetimepicker.js') }}"></script>
    <script type="text/javascript">
 $('.datefield').datetimepicker({
               format: "yyyy-mm-dd h:i:00",
        showMeridian: true,
        autoclose: true,
        todayBtn: true
            });
</script>
    <script>
        $(document).ready(function()
        {
          $("#rname").focus();
          var logoload = "../" + $("#oldlogo").val();
         // var bannerload = $("#oldbanner").val();
          if(logoload != '') 
          {
            $(".content_logo").show();
            $(".image_logo").hide();
            $('.file_logo').attr('src',logoload);
          }
          /*if(bannerload != '')
          {
            $(".upload_banner").show();
            $(".image_banner").hide();
          }*/

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
  var veg = $("#myonoffswitch").prop("checked");
  $("#dietsave").val(veg);//alert(veg);
 // var p_exclusive = $("#myonoffswitch51").prop("checked");
 // $("#p_exclusive").val(p_exclusive);
  var rest_name = $("#rest_name").val();
  var rest_cusines = $("#rest_cusines").val();
  var rest_custmsg = $("#rest_custmsg").val();
  var rest_address= $("#rest_address").val();
  var rest_disporder= $("#rest_disporder").val();
  var rest_status= $("#rest_status").val();
  var rest_owner= $("#rest_owner").val();
  var rest_ownerind= $("#rest_ownerind").val();
  var rest_ownermobile= $("#rest_ownermobile").val();
  var rest_manager= $("#rest_manager").val();
    var rest_manind = $("#rest_manind").val();  
    var rest_manmobile= $("#rest_manmobile").val();
    var rest_ofcind = $("#rest_ofcind").val();
    var rest_ofcmobile= $("#rest_ofcmobile").val();
    var rest_del_charge= $("#rest_del_charge").val();
    var rest_pack_charge= $("#rest_pack_charge").val();
var logo = $("#logo").val();
    var rest_fssai= $("#rest_fssai").val();
    var rest_gstin= $("#rest_gstin").val();
    var rest_bankname= $("#rest_bankname").val();
    var rest_bankaccount= $("#rest_bankaccount").val();
    var rest_bankaccountnmbr= $("#rest_bankaccountnmbr").val();
    var rest_workfrom= $("#rest_workfrom").val();
    var rest_workto= $("#rest_workto").val();
    var rest_serdelivry= $("#rest_serdelivry").val();
    var rest_catering= $("#rest_catering").val();
    var rest_prepdayct= $("#rest_prepdayct").val();
    var rest_maxorder= $("#rest_maxorder").val();
 
    if(rest_name == '') {
        $("#rest_name").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Restaurant Name');
        return false;
    }
    if(rest_name.indexOf('\'') > -1)
    {
        $("#rest_name").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Single Quotes Not allowed In Restuarant Name.');
        return false;
    }
    if(rest_name.indexOf('\"') > -1)
    {
        $("#rest_name").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Double Quotes Not allowed In Restuarant Name.');
        return false;
    }
    if(rest_cusines == '') {
        $("#rest_cusines").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter cusines');
        return false;
    }
     if(rest_disporder == '') {
        $("#rest_disporder").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Display Order');
        return false;
    }
    if(rest_status == '') {
        $("#rest_status").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Status');
        return false;
    }
    if(rest_owner == '') {
        $("#rest_owner").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Owner Name');
        return false;
    }
    if(rest_ownermobile == '') {
        $("#rest_ownermobile").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Owner Mobile');
        return false;
    }
    if(rest_workfrom == '') {
        $("#rest_workfrom").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Work From Time');
        return false;
    }
    if(rest_workto == '') {
        $("#rest_workto").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Work To Time');
        return false;
    }
    if(rest_prepdayct == '0' || rest_prepdayct == '') {
        $("#rest_prepdayct").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Preparation count');
        return false;
    }
     if(rest_maxorder == '0' || rest_maxorder == '') {
        $("#rest_maxorder").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Maximum order');
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

            if(true)
            {
                var formdata = new FormData($('#upload_form')[0]);
                $.ajax({
                    method: "post",
                    url : "../api/edit_catrestaurant",
                    data : formdata,
                    cache : false,
                    crossDomain : true,
                    async : false,
                    processData : false,
                    contentType: false,
                    dataType :'text',
                    success : function(result)
                    {//alert(result);
                        var json_x= JSON.parse(result);
                        if((json_x.msg)=='success')
                        {
                           window.location.href = "../catering_restaurant";
                            swal({

                                title: "",
                                text: "Updated Successfully",
                                timer: 4000,
                                showConfirmButton: false
                            });

                        }
                        else if((json_x.msg)=='already exist')
                        {
                            window.location.href = "../catering_restaurant";
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