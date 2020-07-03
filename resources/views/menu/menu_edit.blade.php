@extends('layouts.app')
@section('title','Potafo - Edit Menu')
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
        .staff-add-pop-btn-new{float: none;display: inline-block}
        .chosen-container-multi .chosen-choices{border: 0;background-image: none}
        .restaurant_more_detail_box .chosen-container{height: 34px;padding-top: 5px}
        .onoffswitch{float: left}
        .restaurant_more_detail_text input{border: 1px #ededed solid;height: 35px; margin-top: 5px;}
        .restaurant_more_detail_text_sel{border: 1px #ededed solid;}
        .restaurant_more_detail_box{border: 1px #ededed solid;}
        .status_mnad .onoffswitch-inner:before{content: "Inactive";text-transform: capitalize;font-size: 13px;}
        .status_mnad .onoffswitch-inner:after{content: "Active";text-transform: capitalize;font-size: 13px;padding-left: 26px;text-align: center}
        .multiselect-container>li>a>label{    padding: 3px 4px 3px 4px;}
        .multiselect-container>li>a{    padding: 2px 10px;}
        .btn-default, .btn-default:hover, .btn-default:focus, .btn-default:active, .btn-default.active, .btn-default.focus, .btn-default:active, .btn-default:focus, .btn-default:hover, .open > .dropdown-toggle.btn-default{ background-color: #fff!important;border: 0px solid #ccc!important;box-shadow: none}
        .mlt_day .multiselect{overflow: hidden}
        .restaurant_more_detail_box{margin-top: 5px}
        .category_add_btn{margin-top: 5px}
        .popover {width: 180px;height: 120px;}.popover img{width:100%}
    </style>
    <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{asset('public/assets/script/common.js') }}" type="text/javascript"></script>

    <link href="{{ asset('public/assets/css/jquery.timepicker.min.css') }}" rel="stylesheet">

    <div class="col-sm-12 col-xs-12 mob_nopad">
        <div class="col-sm-12 col-xs-12">
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('index') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{  url('manage_restaurant') }}">Restaurants</a>
                </li>
                <li class="active ms-hover">
                    <a href="{{ url('menu/list/'.$resid) }}">Menu List</a>
                </li>
                <li class="active ms-hover">
                    Add Menu
                </li>

            </ol>
        </div>

        <div class="col-md-12 col-xs-12 text-center mob_nopad">
            <div class="col-md-9 col-xs-12 add_menu_cc mob_nopad">
                <div class="card-box table-responsive" style="padding: 8px 10px;">
                    <h3 style="margin-bottom:40px;text-align: center;">EDIT MENU</h3>
                    <div class="table_section_scroll">
                        {!! Form::open(['enctype'=>'multipart/form-data','url'=>'api/menu/add', 'name'=>'frm_add', 'id'=>'frm_add','method'=>'post',]) !!}
                        <input type="hidden" value="{{ $resid }}" id="res_id" name="res_id">
                        <input type="hidden" value="{{ $details[0]->menuid }}" id="menuid" name="menuid">
                        <input type="hidden"  id="tot_count" name="tot_count" value="{{ count($portion) }}">
                        <input type="hidden" value="{{ $extra_percent }}" id="extra_percent" name="extra_percent">
                        <input type="hidden"  id="portion_count" name="portion_count" value="{{ isset($details[0]->count)?$details[0]->count:0 }}">
                        <input type="hidden"  id="img1" name="img1" value="{{ isset($details[0]->img)?$details[0]->img:''}}">
                        <div class="restaurant_more_detail_row" style="width:48%;margin-right:2%">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Type</span>
                                {{ Form::select('menu_type',['Menu' => 'Menu'],$details[0]->type,['id' => 'menu_type','class' => 'restaurant_more_detail_text_sel']) }}
                            </div>
                        </div>
                        <div class="restaurant_more_detail_row" style="width:48%;margin-right:2%">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Menu Name</span>
                                {!! Form::text('menu_name',title_case($details[0]->name), ['class'=>'form-control','id'=>'menu_name','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'placeholder'=>"Enter Menu Name"]) !!}

                            </div>
                        </div>
                        <div class="restaurant_more_detail_row" id="pgroupdiv">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Category</span>
                                <div class="restaurant_more_detail_box" style="width:90%;min-height:35px;">
                                    <select id="category" name="category[]" data-placeholder="Select Category" multiple class="main_categ multiselect-native-select" tabindex="3" style="display:none;">
                                        @if(count($category) >0)
                                            @foreach($category as $cat)
                                                <option @if(isset($details[0]->category) && $details[0]->category != 'null' && in_array($cat->name,json_decode($details[0]->category))) selected="true" @endif value="{{ $cat->name }}">{{ $cat->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="category_add_btn category_add">+</div>
                            </div>
                        </div>

                        <div class="restaurant_more_detail_row" style="width:68%;margin-right:2%">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Sub category</span>
                                <div class="restaurant_more_detail_box" style="width:85%;min-height:35px;">
                                    <select id="subcategory" name="subcategory[]" data-placeholder="Select SubCategory" multiple class="main_categ" tabindex="3" style="display:none">
                                        @if(count($subcategory) >0)
                                            @foreach($subcategory as $subcat)
                                                <option  @if(isset($details[0]->subcategory) && $details[0]->subcategory != 'null' &&  in_array($subcat->name,json_decode($details[0]->subcategory))) selected="true" @endif value="{{ $subcat->name }}">{{ $subcat->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div style="width:12%" class="category_add_btn subcat_btn">+</div>
                            </div>
                        </div>
                        <div class="restaurant_more_detail_row" style="width:30%">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Tax</span>
                                <div class="restaurant_more_detail_box" style="width:100%;min-height:35px;">
                                    <select id="tax" name="tax[]" data-placeholder="Select Tax" multiple class="main_categ" tabindex="3" style="display:none">
                                        @if(count($taxlist)>0)
                                            @foreach($taxlist as $item)
                                                <option   @if(isset($details[0]->tax)&& $details[0]->tax != 'null' && in_array($item->t_name,json_decode($details[0]->tax))) selected="true" @endif value="{{ $item->t_name }}">{{ $item->t_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="rate_sec_mn">
                            <div class="rate_sec_mn_head">Rate</div>
                            @for($i = count($portion); $i >= 1;$i--)
                               <?php
                                $prtn = 'portion'.$i;
                                 $excrte = 'exc_rate'.$i;
                                  $incrate = 'inc_rate'.$i;
                                  $extrate = 'extra_rate'.$i;
                                  $fnlrate = 'final_rate'.$i;
                                  $extraval = 'extra_val'.$i;
                                 ?>
                            <div id="portion_div{{ $i }}" class="portion_dtl" style="@if(isset($details[0]->$prtn) || $i == count($portion)) display:block; @else display:none @endif">
                                <div class="restaurant_more_detail_row" style="width:20%;margin-right:2%">
                                    <div class="restaurant_more_detail_text">
                                        <span class="restaurant_more_detail_text_nm">Portion</span>
                                        {{ Form::select('portion'.$i,['' => 'Select portion']+$portion,isset($details[0]->$prtn)?$details[0]->$prtn:null,['id' => 'portion'.$i,'class' => 'restaurant_more_detail_text_sel']) }}
                                    </div>
                                </div>
                                <div class="restaurant_more_detail_row" style="width:16%;">
                                    <div class="restaurant_more_detail_text">
                                        <span class="restaurant_more_detail_text_nm">Exc Tax Rate</span>
                                        @if($details[0]->$excrte != '')
                                        <input type="text" class="form-control" onfocus="this.blur()" value="{{ isset($details[0]->$excrte)?str_replace(',', '',number_format((float)$details[0]->$excrte,$decimal_digit)):'' }}" onkeypress="return isNumberKey(event)"  onkeyup="return exclusiveentry(this.value,'{{ count($portion) }}')" id="exc_rate{{ $i }}" name="exc_rate{{ $i }}">
                                        @else 
                                        <input type="text" class="form-control" value="{{ isset($details[0]->$excrte)?number_format((float)$details[0]->$excrte,2)- 0:'' }}" onkeypress="return isNumberKey(event)"  onkeyup="return exclusiveentry(this.value,'{{ count($portion) }}')" id="exc_rate{{ $i }}" name="exc_rate{{ $i }}">
                                        @endif
                                    </div>
                                </div>
                                <div class="restaurant_more_detail_row" style="width:16%;">
                                    <div class="restaurant_more_detail_text">
                                        <span class="restaurant_more_detail_text_nm">Inc Rate</span>
                                        <input type="text" class="form-control"  value="{{ isset($details[0]->$incrate)?str_replace(',', '',number_format((float)$details[0]->$incrate,$decimal_digit)):'' }}"   onfocus="this.blur()"  readonly="readonly"  id="inc_rate{{ $i }}" name="inc_rate{{ $i }}">
                                    </div>
                                </div>

                                <div class="restaurant_more_detail_row" style="width:17%">
                                    <div class="restaurant_more_detail_text">
                                        <span class="restaurant_more_detail_text_nm">Extra %</span>
                                        @if($details[0]->$extrate != '')
                                        <input type="text" class="form-control" onfocus="this.blur()" value="@if(isset($details[0]->$extrate)){{ isset($details[0]->$extrate)?str_replace(',', '',number_format((float)$details[0]->$extrate,$decimal_digit)):'' }}@else {{ $extra_percent }} @endif"  onkeypress="return isNumberKey(event)"  onkeyup="return extrarateentry(this.value,'{{ count($portion) }}')"  id="extra_rate{{ $i }}" name="extra_rate{{ $i }}">
                                        @else
                                        <input type="text" class="form-control"  value="@if(isset($details[0]->$extrate)){{ isset($details[0]->$extrate)?str_replace(',', '',number_format((float)$details[0]->$extrate,$decimal_digit)):'' }}@else {{ $extra_percent }} @endif"  onkeypress="return isNumberKey(event)"  onkeyup="return extrarateentry(this.value,'{{ count($portion) }}')"  id="extra_rate{{ $i }}" name="extra_rate{{ $i }}">
                                        @endif
                                        <input type="hidden" class="form-control"  id="extra_val{{$i}}" name="extra_val{{$i}}" value="@if(isset($details[0]->$extraval)){{ isset($details[0]->$extraval)?str_replace(',', '',number_format((float)$details[0]->$extraval,$decimal_digit)):'' }} @endif">
                                    </div>
                                </div>
                                <div class="restaurant_more_detail_row" style="width:17%">
                                    <div class="restaurant_more_detail_text">
                                        <span class="restaurant_more_detail_text_nm" >Final Rate</span>
                                        <input type="text" class="form-control"  onfocus="this.blur()"  value="{{ isset($details[0]->$fnlrate)?str_replace(',', '',number_format((float)$details[0]->$fnlrate,$decimal_digit)):''  }}"   readonly="readonly"  id="final_rate{{ $i }}" name="final_rate{{ $i }}">
                                    </div>
                                </div>
                                @if($i == count($portion))
                                    <div style="margin-top: 26px; margin-left: 1%; background-color: #30c392;border-radius: 50%; width: 35px;"  id="portion_add{{ count($portion) }}"  onclick="return portion_divshow('{{ count($portion) }}')" class="category_add_btn portion_add">+</div>
                                @else
                                    <div style="margin-top: 26px; margin-left: 1%; background-color: #f00;border-radius: 50%;width: 35px;"  id="portion_add{{$i}}"  onclick="return portion_delete('{{$i}}','{{count($portion) }}')" class="category_add_btn portion_add"><i class="fa fa-trash" aria-hidden="true"></i></div>
                                @endif
                            </div>
                            @endfor
                        </div>
                        <div class="restaurant_more_detail_row" style="width:23%;margin-right:2%">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Packing Charge</span>
                                <input name="pack_rate" id="pack_rate"  value="{{ isset($details[0]->pack_rate)?number_format((float)$details[0]->pack_rate,2)- 0:'' }}" onkeypress="return isNumberKey(event)" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="restaurant_more_detail_row" style="width:30%;margin-right:2%">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Select Day</span>
                                <div class="restaurant_more_detail_box mlt_day" style="width:100%;min-height:35px;margin-top:5px;">
                                    <select class="restaurant_more_detail_text_sel day_sel" id="days" name="days[]" multiple style="display:none">
                                        <option  @if(isset($details[0]->days) && $details[0]->days != 'null' && in_array('Sun',json_decode($details[0]->days))) selected="true" @endif value="Sun">Sunday</option>
                                        <option  @if(isset($details[0]->days) && $details[0]->days != 'null' && in_array('Mon',json_decode($details[0]->days))) selected="true" @endif value="Mon">Monday</option>
                                        <option  @if(isset($details[0]->days) && $details[0]->days != 'null' && in_array('Tue',json_decode($details[0]->days))) selected="true" @endif value="Tue">Tuesday</option>
                                        <option @if(isset($details[0]->days) && $details[0]->days != 'null' && in_array('Wed',json_decode($details[0]->days))) selected="true" @endif  value="Wed">Wednesday</option>
                                        <option  @if(isset($details[0]->days) && $details[0]->days != 'null' && in_array('Thu',json_decode($details[0]->days))) selected="true" @endif value="Thu">Thursday</option>
                                        <option  @if(isset($details[0]->days)&& $details[0]->days != 'null' && in_array('Fri',json_decode($details[0]->days))) selected="true" @endif value="Fri">Friday</option>
                                        <option  @if(isset($details[0]->days) && $details[0]->days != 'null' && in_array('Sat',json_decode($details[0]->days))) selected="true" @endif value="Sat">Saturday</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="restaurant_more_detail_row" style="width:20%;margin-right:2%">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">From Time</span>
                                <input name="from_time" value="{{ $details[0]->from_time }}" id="from_time" type="text" class="form-control form_datetime timepicker">
                            </div>
                        </div>
                        <div class="restaurant_more_detail_row" style="width:20%;">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">To Time</span>
                                <input name="to_time" id="to_time" value="{{ $details[0]->to_time }}" type="text" class="form-control form_datetime timepicker">
                            </div>
                        </div>
                        <div class="restaurant_more_detail_row" style="width:55%;margin-right:2%">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Image
                                    @if(isset($details[0]->img) && $details[0]->img != '')
                                        <a rel="popover" data-img="{{ $siteUrl.$details[0]->img }}" href="#" style="margin-left: 25px;">View</a>
                                    @endif
                                </span>
                                <input type="file" id="menu_image" name="menu_image" class="form-control">
                            </div>
                        </div>
                        <div class="restaurant_more_detail_row" style="width:20%;">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Diet</span>
                                {{ Form::select('diet',['Veg' => 'Veg','Non Veg' => 'Non Veg','General' => 'General'],$details[0]->diet,['id' => 'diet','class' => 'restaurant_more_detail_text_sel']) }}
                            </div>
                        </div>
                        <div class="restaurant_more_detail_row" style="width:20%;margin-left:2%">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Status</span>
                                {{ Form::select('status',['Y' => 'Active','N' => 'Inactive'],$details[0]->status,['id' => 'status','class' => 'restaurant_more_detail_text_sel']) }}
                            </div>
                        </div>
                        <div class="restaurant_more_detail_row" style="width:55%;margin-right:2%">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Description</span>
                                {!! Form::textarea('description',$details[0]->description, ['class'=>'form-control','id'=>'description','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'placeholder'=>"","rows"=>"2","cols"=>"80",'maxlength' => '50']) !!}
                            </div>
                        </div>
                        <div class="restaurant_more_detail_row" style="width:30%;">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Most Selling</span>
                                {{ Form::select('most_selling',['Y' => 'Yes','N' => 'No'],$details[0]->m_most_selling,['id' => 'most_selling','class' => 'restaurant_more_detail_text_sel']) }}
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                    <div class="col-sm-12 col-xs-12 text-center">
                        <a class="staff-add-pop-btn staff-add-pop-btn-new" href="{{ url('menu/list/'.$resid) }}"><i class="ti-arrow-left"></i> BACK</a>
                        <a class="staff-add-pop-btn staff-add-pop-btn-new" onclick="return edit_menu()"><i class="ti-check"></i> SAVE</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="timing_popup_cc category_add_popup">
        <div class="timing_popup" style="width: 390px;">
            <div class="timing_popup_head">Add Category
                <div class="timing_popup_cls"><img src="{{ asset('public/assets/images/cancel.png') }}"></div>
            </div>
            <div class="timing_popup_contant">
                <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text" style="width:83%;margin-right:2%;">
                        <span class="restaurant_more_detail_text_nm">Category Name</span>
                        <input style="padding-left:5px;" type="text" name="cat_name" id="cat_name" placeholder="Enter Category">
                        <input type="hidden" name="c_status" id="c_status" placeholder="Enter Category">
                    </div>
                    <div class="restaurant_more_detail_text" style="width:15%;">
                        <span style="margin-top: 23px;" class="add_time_btn_pop" onclick="return submit_category();">ADD</span>
                    </div>
                </div>
                <div class="col-sm-12" style="display: none;">
                    <a  class="staff-add-pop-btn staff-add-pop-btn-new" style="float: right;">SAVE</a>
                </div>
            </div>
        </div>
    </div>

    <div class="timing_popup_cc subcategory_add_popup">
        <div class="timing_popup" style="width: 390px;">
            <div class="timing_popup_head">Add Sub Category
                <div class="timing_popup_cls"><img src="{{ asset('public/assets/images/cancel.png') }}"></div>
            </div>
            <div class="timing_popup_contant">
                <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text" style="width:83%;margin-right:2%">
                        <span class="restaurant_more_detail_text_nm">Sub Category </span>
                        <input style="padding-left:5px;" type="text" name="scat_name" id="scat_name" placeholder="Enter Sub Category">
                        <input type="hidden" name="sc_status" id="sc_status" placeholder="Enter Sub Category">
                    </div>

                    <div class="restaurant_more_detail_text" style="width:15%;">
                        <span style="margin-top: 23px;" class="add_time_btn_pop" onclick="return submit_subcategory();">ADD</span>
                    </div>
                </div>
                <div class="col-sm-12" style="display: none;">
                    <a  class="staff-add-pop-btn staff-add-pop-btn-new" style="float: right;">SAVE</a>
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

    <script src="{{asset('public/assets/js/jquery.timepicker.js') }}"></script>
    <link href="{{ asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('public/assets/dark/plugins/custombox/css/custombox.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script src="{{asset('public/assets/js/index.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function()
        {
            var count = $("#tot_count");
            var portion = $("#portion"+count.val()).val();
            if(portion == '')
            {
                for(var i = 1;i<=parseInt(count.val());i++)
                {
                    var p1 = $("#portion"+i);
                    if(p1.val() != '')
                    {
                        $("#portion"+count.val()+" option[value='" + p1.val() + "']").remove();
                    }

                }
            }

            //autofocus first field
         //   $("#menu_name").get(0).focus();
            $('.main_categ').multiselect({
                checkboxName: function(option)
                {
                    return 'multiselect[]';
                },
                // includeSelectAllOption: true,
                enableFiltering: true
            });
            $('.day_sel').multiselect({
                checkboxName: function(option)
                {
                    return 'multiselect[]';
                },
                includeSelectAllOption: true,
                // enableFiltering: true
            });
        });

        //on category add plus button click
        $(".category_add").click(function(){
            $("#cat_name").val('');
            $("#cat_status").attr('checked','true');
            var res_id = $("#res_id");
            $("#cat_name").removeClass('input_focus');
            $(".category_add_popup").show();
        });

        //on sub category add  plus button click
        $(".subcat_btn").click(function()
        {
            $("#scat_name").val('');
            $("#scat_status").attr('checked','true');
            var res_id = $("#res_id");
            $("#scat_name").removeClass('input_focus');
            $('.subcategory_add_popup').show();
        });

        $(".timing_popup_cls").click(function(){
            $(".category_add_popup").hide();
            $('.subcategory_add_popup').hide();
        });

    </script>

    <script>

        $(document).ready(function()
        {
            $(".day_select").fadeIn(1000);
            $(".day_select").chosen({
                display_selected_options:true,
                search_contains:true,
                display_disabled_options:false,
                single_backstroke_delete:false,
                inherit_select_classes:true ,

            });
        });
        $('#time_availability').datetimepicker({
            //language:  'fr',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 1,
            minView: 0,
            maxView: 1,
            forceParse: 0
        });


        //add restaurant menus
        function edit_menu()
        {
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var count = '0';
            var ptn_count = $("#tot_count").val();
            var res_id = $("#res_id");
            var menuname = $('#menu_name');
            var category = $('#category');
            var subcategory = $('#subcategory');
            var pack_rate = $('#pack_rate');
            var days = $('#days');
            var from_time = $('#from_time');
            var to_time = $('#to_time');
            var menu_image = document.getElementById('menu_image');
            if(menuname.val() == '')
            {
                menuname.addClass('input_focus');
                $.Notification.autoHideNotify('error', 'bottom right','Enter Menu Name.');
                return false;
            }
            if(menuname.val().indexOf('\'') > -1)
            {
                menuname.addClass('input_focus');
                $.Notification.autoHideNotify('error', 'bottom right','Single Quotes Not allowed In Menu Name.');
                return false;
            }
            if(menuname.val().indexOf('\"') > -1)
            {
                menuname.addClass('input_focus');
                $.Notification.autoHideNotify('error', 'bottom right','Double Quotes Not allowed In Menu Name.');
                return false;
            }
            if(category.val() == '')
            {
                category.addClass('input_focus');
                $.Notification.autoHideNotify('error', 'bottom right','Select Category.');
                return false;
            }
            for(var i=1;i <= parseInt(ptn_count) ;i++)
            {
                if ($("#portion"+i).val() != '')
                {
                    var exclusive_rate = $("#exc_rate"+i);
                    if(exclusive_rate.val() == '')
                    {
                        $.Notification.autoHideNotify('error', 'bottom right','Enter Exc Tax Rate.');
                        exclusive_rate.addClass('input_focus');
                        return false;
                    }
                    var count = parseInt(count) + 1;
                }
            }
            $("#portion_count").val(count);
            if(count == 0)
            {
                $.Notification.autoHideNotify('error', 'bottom right','Select Portion.');
                $("#portion"+ptn_count).addClass('input_focus');
                return false;
            }
           /* if(from_time.val() >= to_time.val())
            {
                menuname.focus();
                $.Notification.autoHideNotify('error', 'top right','Invalid time.');
                return false;
            }*/

            if(menu_image.value != '') {
                if (!hasExtension('menu_image', ['.jpg','.jpeg', '.gif', '.png','.JPG', '.JPEG', '.GIF', '.PNG'])) {
                    $.Notification.autoHideNotify('error', 'bottom right','Upload Gif or Jpg Images Only.');
                    menu_image.addClass('input_focus');
                    return false;
                }
            }
            if(pack_rate.val() == '')
            {
                pack_rate.val('0');
            }
            var formdata = new FormData($('#frm_add')[0]);
            $.ajax({
                method: "post",
                url: "../../../api/menu/edit",
                data: formdata,
                cache: false,
                crossDomain: true,
                async: false,
                processData: false,
                contentType: false,
                dataType: 'text',
                success: function (result)
                {
                    var json_x = JSON.parse(result);
                    if ((json_x.msg) == 'success') {
                        window.location.href="../../../menu/list/"+res_id.val();
                        swal({

                            title: "",
                            text: "Updated Successfully",
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
                    else if ((json_x.msg) == 'Already exist')
                    {
                        location.reload();
                        swal({

                            title: "",
                            text: "Already Exist",
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                }
            });
            return true;
        }

        //add category  not existing from popup
        function submit_category()
        {
            $('.notifyjs-wrapper').remove();
            var res_id = $("#res_id");
            var cat_name = $("#cat_name");
            var categrylist     = $("#categrylist");
            var selects    =  $("#category");
            var selected_cat = selects.val();
            cat_name.removeClass('input_focus');

            if(cat_name.val() == '')
            {
                     cat_name.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right','Enter Category.');
                     return false;
            }
            $('#category option').each(function(index, element)
            {
                     element.remove();
            });
            var data = {"category" : cat_name.val(),"status" :   'true',"res_id":res_id.val()};

            $.ajax({
                method: "post",
                url : "../../../api/category/add",
                data : data,
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(result)
                {
                    var json_rslt = JSON.parse(result);
                    if(json_rslt.msg == 'success')
                    {
                        $.each(json_rslt.category,function(i,indx)
                        {
                                  var count = parseInt(i)+1;
                                  var htm = '';
                                  htm += '<option value="' + indx.name + '">' + indx.name + '</option>';
                                  selects.append(htm);
                                  selects.multiselect('rebuild');
                        });
                              var dat = selected_cat;
                              dat.push(toTitleCase(cat_name.val()));
                              var dataarray = dat;
                              dataarray = dataarray.filter(function() { return true; });
                              selects.val(dataarray);
                              selects.multiselect('refresh');
                              $("input[name=cat_status]").attr('checked','true');
                              $(".category_add_popup").hide();
                    }
                    else
                    {
                        swal({

                               title: "",
                               text: "Already Exist",
                               timer: 4000,
                               showConfirmButton: false
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                   // $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
            return true;
        }

        //add sub category  not existing from popup
        function submit_subcategory()
        {
            $('.notifyjs-wrapper').remove();
            var res_id = $("#res_id");
            var sub_cat_name = $("#scat_name");
            var subcategrylist     = $("#subcategrylist");
            var select     = $("#subcategory");
            var selected_subcat = select.val();
            sub_cat_name.removeClass('input_focus');
            if(sub_cat_name.val() == '')
            {
                sub_cat_name.addClass('input_focus');
                $.Notification.autoHideNotify('error', 'bottom right','Enter Sub Category.');
                return false;
            }
            $('#subcategory option').each(function(index, element)
            {
                element.remove();
            });
            var data = {"subcategory" : sub_cat_name.val(),"status" :   "true","res_id":res_id.val()};
            $.ajax({
                method: "post",
                url : "../../../api/subcategory/add",
                data : data,
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(result)
                {
                    var json_rslt = JSON.parse(result);
                    if(json_rslt.msg == 'success')
                    {
                        $.each(json_rslt.category,function(i,indx)
                        {
                            var count = parseInt(i)+1;
                            if(indx.status == 'Y')
                            {
                                var menustatus = 'Active';
                            }
                            else
                            {
                                var menustatus = 'Inactive';
                            }
                            var htm = '';
                            htm += '<option value="' + indx.name + '">' + indx.name + '</option>';
                            select.append(htm);
                            select.multiselect('rebuild');
                        });
                        var dat = selected_subcat;
                        dat.push(toTitleCase(sub_cat_name.val()));
                        var dataarray = dat;
                        dataarray = dataarray.filter(function() { return true; });
                        select.val(dataarray);
                        select.multiselect('refresh');
                        $("#scat_name").val('');
                        $("input[name=scat_status]").attr('checked','true');
                        $('.subcategory_add_popup').hide();
                    }
                    else
                    {
                        swal({

                            title: "",
                            text: "Already Exist",
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                //    alert(textStatus);
                //    $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
            return true;
        }
        //multiple portion add
        function portion_divshow(slno)
        {
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var count =  $("div.portion_dtl").not(":hidden").length;
            var portion = $("#portion"+slno);
            var exclusive_rate = $("#exc_rate"+slno);
            var inclusive_rate = $("#inc_rate"+slno);
            var extra_rate = $("#extra_rate"+slno);
            var extra_val = $("#extra_val"+slno);
            var final_rate = $("#final_rate"+slno);

            if(portion.val() == '')
            {
                portion.addClass('input_focus')
                $.Notification.autoHideNotify('error', 'bottom right','Select Portion.');
                return false;
            }

            if(exclusive_rate.val() == '')
            {
                exclusive_rate.addClass('input_focus')
                $.Notification.autoHideNotify('error', 'bottom right','Enter Exclusive Rates.');
                return false;
            }

            if(true)
            {
                for(var n =1;n <=parseInt(count);n++)
                {
//                  var prfx = '1';
                    $('#portion_div'+n).css('display', 'block');//show div

                    if( n == slno)
                    {
                        $.Notification.autoHideNotify('error', 'bottom right','Portion Exceeded.');
                    }

                }

                if (parseInt(count) <= (parseInt(slno) - 1))
                {
                    var prfx = count;

                    //show all data in list
                    $("#portion" + prfx).val(portion.val());
                    $("#exc_rate" + prfx).val(exclusive_rate.val());
                    $("#inc_rate" + prfx).val(inclusive_rate.val());
                    $("#extra_rate" + prfx).val(extra_rate.val());
                    $("#extra_val" + prfx).val(extra_val.val());
                    $("#final_rate" + prfx).val(final_rate.val());

                    if($('#portion'+slno+' option[value = "'+portion.val()+'"').length != 0)
                    {
                        $("#portion"+slno+" option[value='" + portion.val() + "']").remove();
                    }
                    clearportion(slno);
                }
            }
            return  true;
        }
        //delete particular portion
        function portion_delete(id,slno)
        {
            var val =   $("#portion"+id);
             if ($('#portion'+slno+' option[value = "'+val.val()+'"').length === 0)
             {
                 $("#portion"+slno).append('<option value="' + val.val() + '">' + val.val() + '</option>');
             }
            $("#portion_div"+id).hide();
            clearportion(id);
            return true;
        }

        //clear main catgeory add fields
        function clearportion(id)
        {
            var extrapct = $("#extra_percent");
            $("#portion"+id).prop("selectedIndex", 0);
            $("#exc_rate"+id).val('');
            $("#inc_rate"+id).val('');
            $("#extra_rate"+id).val(extrapct.val());
            $("#final_rate"+id).val('');
            return true;
        }
        //on exclusive rate entry
        function exclusiveentry(value,slno)
        {
            if(value != '' || value != '0')
            {
                var taxlist = $("#tax").val();
                var extra_percent = $("#extra_rate"+slno).val();
                var len = taxlist.length;
                var sum = 0;
                var res_id = $("#res_id");
                var incrate =  $("#inc_rate" + slno);
                var extra_rate =  $("#extra_rate" + slno);
                var fnl_rate =   $("#final_rate" + slno);
                if (parseInt(len) > 0) {
                    for (var i = 0; i < len; i++) {
                        $.ajax({
                            method: "post",
                            url: "../../../api/get_taxvalue",
                            data: {'tax': taxlist[i], 'id': res_id.val()},
                            cache: false,
                            crossDomain: true,
                            async: false,
                            dataType: 'text',
                            success: function (result) {
                                var json_x = JSON.parse(result);
                                var inc_rate = parseFloat(value) * parseFloat(json_x.taxvalue) / 100;
                                sum += inc_rate;
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                //  $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                            }
                        });
                    }
                }
                else {
                    sum = '0.00';
                }
                var inclrate = parseFloat(value) + parseFloat(sum);
                if(parseFloat(inclrate) >0)
                {
                    if (!isNaN(inclrate) && parseFloat(inclrate) > 0) {
                        incrate.val(inclrate.toFixed(2));
                    }
                    var extra_value = parseFloat(extra_percent)*parseFloat(inclrate)/100;
                    if (!isNaN(extra_value)  && parseFloat(extra_value) >= 0)
                    {
                        var extra_value = extra_value;
                    }
                    else
                    {
                        var extra_value = '0'
                    }
                    var final_rate = extra_value +  parseFloat(inclrate);
                    if (!isNaN(extra_value)  && parseFloat(extra_value) >= 0)
                    {
                        $("#extra_val" + slno).val(extra_value);
                    }
                    if (!isNaN(final_rate)  && parseFloat(final_rate) > 0)
                    {
                        fnl_rate.val(final_rate.toFixed(2));
                    }
                }
                else if(parseFloat(inclrate) ==0)
                {
                    $("#inc_rate"+slno).val(0);
                    $("#extra_val" + slno).val(0);
                    $("#final_rate"+slno).val(0);
                }
            }
            return true;
        }
        //on extra rate entry
        function extrarateentry(val,slno)
        {
            var sum = 0;
            var inc_rate   = $("#inc_rate"+slno).val();
            var exc_rate   = $("#exc_rate"+slno).val();
            var final_rate = parseFloat(val)*parseFloat(exc_rate)/100;
            if (!isNaN(final_rate)  && parseFloat(final_rate) > 0)
            {
                $("#extra_val" + slno).val(final_rate);
            }
            if(val == '')
            {
                $("#final_rate"+slno).val(parseFloat(inc_rate).toFixed(2));
            }
            if(!isNaN(final_rate))
            {
                var final = parseFloat(final_rate) + parseFloat(inc_rate);
                $("#final_rate"+slno).val(final.toFixed(2));
            }
            else
            {
                $("#final_rate"+slno).val(parseFloat(inc_rate).toFixed(2));
            }
            return true;
        }
    </script>
<script>
        $(document).ready(function() {
         $('a[rel=popover]').popover({
              html: true,
              trigger: 'hover',
              placement: 'bottom',
              content: function(){return '<img src="'+$(this).data('img') + '" width="200" height="100"/>';}
            });
        });
 </script>
@stop
@endsection