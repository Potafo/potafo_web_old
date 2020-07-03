@extends('layouts.app')
@section('title','Manage Complaints')
@section('content')

    <style>
        .disabledbutton{ pointer-events:none;opacity:0.4;}
        .filter_text_box_row{margin-bottom: 6px}
        #example1_wrapper .dt-buttons{position: absolute;right: 80px;top: 15px;}
        #example1_wrapper .dt-buttons .btn-sm { padding: 6px 10px;box-shadow: 2px 3px 5px #d0d0d0;
            font-weight: bold;}
        .pagination_container_sec{width: 100%;height: auto;float: left}
        .pagination_container_sec ul{margin: 0;float: right}
        .disable_field{pointer-events: none;}
        .cancel_reasons_view{ bottom:-36px; display: none;  background-color: azure !important;    box-shadow: -1px -5px 5px #ccc !important;}
        .cancel_reasons_view div a span{width:100%;float:left;padding:3px}
        .popover {width: 180px;height: 120px;}.popover img{width:100%}
        .cancel_reasons_view{bottom: auto; width: 100%; top: 56px; max-height: 110px; overflow: auto;position:relative}
        .timing_popup_cc_pop{width: 100%;
    height: 100%;
    position: fixed;
    left: 0;
    top: 0;
    background-color: rgba(0,0,0,0.7);
    z-index: 999;
    display: none;
    overflow: auto;}
    .loader_staff_sec{position: absolute; width: 100%; height: 100%; left: 0; top: 0; background-color: rgba(255, 255, 255, 0.62);
    text-align: center; padding-top: 20%;} .loader_staff_sec img{width:185px} 
     .tooltips {
        position: relative;
        display: inline-block;
       // border-bottom: 1px dotted black;
    }
    .tooltips .tooltiptext {
        visibility: hidden;
        width: 100%;
        background-color: #555;
        color: #fff;
        text-align:justify;
        float: right;
        border-radius: 6px;
        padding: 5px 0;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -60px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltips .tooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }

    .tooltips:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
    </style>
   

   
    <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <input type='hidden' id='url' value='{{$url}}' />
    <input type='hidden' id='site_url' value='{{$siteUrl}}' />

    <div class="col-sm-12">

        <div class="card-box table-responsive" style="padding: 8px 10px;">
            <div class="box master-add-field-box" >
                <div class="col-md-6 no-pad-left">
                    <h3>MANAGE COMPLAINTS </h3>
                </div>
                <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>
                <div class=" pull-right" style="display:block">
                    <div class="table-filter" style="margin-top: 4px;">
                        <div class="table-filter-cc">
                            {{--<a title="Filter" href="#"> <button type="submit"  style="margin-right: 10px;" class="on-default followups-popup-btn btn btn-primary filter_sec_btn" ><i class="fa fa-filter" aria-hidden="true"></i></button></a>--}}
                            <a href="#"> <button type="submit" style="margin-top: 20px; border-radius: 4px;margin-left: 0;" class="on-default followups-popup-btn btn btn-primary ad-work-clear-btn" >Add New</button></a>
                        </div>
 
                    </div>
                </div>
            </div>
            <div class="filter_box_section_cc diply_tgl" style="display:block">
                <!--                <div class="filter_box_section">FILTER</div>-->
                <div class="filter_text_box_row">
                    {!! Form::open(['url'=>'complaints', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'get','onkeypress'=>"return event.keyCode != 13;"]) !!}

                    <div class="main_inner_class_track" style="width: 12%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Customer Mobile</label>
                                <input id="flt_id"   name="flt_id" class="form-control" type="text"  >
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 12%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>From</label>
                                <input id="flt_from" data-date-format='dd-mm-yyyy'  name="flt_from" class="form-control" type="text"  >
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 12%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>To</label>
                                <input id="flt_to" data-date-format='dd-mm-yyyy' name="flt_to" class="form-control" type="text" >
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 15%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Status</label>
                                <select id="flt_status" name="flt_status" class="form-control">
                                    <option value="">Select Status</option>
                                   
                                        <option value="Active">Active</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                     <div class="main_inner_class_track" style="width: 15%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Priority</label>
                                <select id="flt_priority" name="flt_priority" class="form-control">
                                    <option value="">Select Priority</option>
                                   
                            @foreach($load_dropdowns as $list)
			<option value="{{$list}}">{{$list}}</option>
                           @endforeach
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 20%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Complaints Category</label>
                                 <select id="flt_category" name="flt_category"  class="form-control"  >
                               <option value="">Select Category</option>                                        
                            @foreach($category as $item)
			<option value="{{$item->cc_id}}">{{$item->cc_name}}</option>
                           @endforeach
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="search_button" class="main_inner_class_track" style="width: 12% !important;float: left">
                        <div class="table-filter-cc" style="margin-top: 22px;">
                            <a href="#" onclick="refresh_filter()" style="margin-left:0;width: 80px " class="on-default btn btn-primary">Search</a>
                            <span hidden="" id="searchcount"></span>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>
            </div>

            <div class="table_section_scroll" id="staff_list">
            </div>
            <input type="hidden" id="start_count"  />
            <input type="hidden" id="current_count"  />
            <input type="hidden" id="end_count"  />
            <div class="pagination_container_sec">
                <ul class="pagination" id="pagination">
                    <li class="paginate_button previous disabled" id="pagn_prev" ><a href="#">Previous</a></li>
                    <li class="paginate_button" id="pagn_start" ><a href="#">1</a></li>
                    <li class="paginate_button " id="pagn_midle" ><a href="#">2</a></li>
                    <li class="paginate_button " id="pagn_end" ><a href="#">3</a></li>
                    <li class="paginate_button next " id="pagn_next" ><a href="#">Next</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div id="loader_pop" class="timing_popup_cc_pop"style="display:none;" >
         <div class="loader_staff_sec" id='loadingmessage'  >
                                    <img src='public/assets/images/main-loader.gif'/>
            </div> 
    </div>
    <div id="rest_auth_sec" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup"  style="height: 83% !important;overflow: auto; width: 95%; max-width: 650px;">
            <div class="add-work-done-poppup-head">Add Complaints
                <a href="#" onclick="close_aut_log()"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
            <div style="text-align:center;" id="branchtimezone"></div>
            <div class="add-work-done-poppup-contant" >

                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">
{!! Form::open(['enctype'=>'multipart/form-data','url'=>'api/add_complaints', 'name'=>'frm_upload', 'id'=>'frm_upload','method'=>'post',]) !!}
                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">

                            <input type='hidden' id='url' value='{{$url}}' />
                            <input type='hidden' id='userid'  name="userid" />
                            <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Mobile No*</label>
                                        {!! Form::text('mobile',null, ['class'=>'form-control','id'=>'add_mobile','name'=>'add_mobile','autocomplete'=>'off','required','style'=>"background-color:transparent;","onkeyup"=>"load_mobilenumbers()"]) !!}
<div class="cancel_reasons_view" id="reason_suggestions" style="background-color:transparent;box-shadow:none">
                     </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="custid" name="custid">
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Name*</label>
                                        {!! Form::text('mobile',null, ['class'=>'form-control','id'=>'cust_name','name'=>'cust_name','autocomplete'=>'off','required','style'=>"background-color:transparent;"]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>File</label>
                                        <input style="padding-left:5px;" type="file" id="upld_file" name="upld_file" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Complaint Date*</label>
                                        <input id="cpl_date" data-date-format='dd-mm-yyyy'  name="cpl_date" class="form-control" type="text"  >
                                    </div>
                                </div>
                            </div>
                             <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Priority*</label>
                                        <select id="priority" name="priority" class="form-control">
                                    
                                   
                            @foreach($load_dropdowns as $list)
			<option value="{{$list}}">{{$list}}</option>
                           @endforeach
                                    
                                </select>
                                    </div>
                                </div>
                            </div>
                             <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Complaints Category*</label>
                                        <select style="width: 82%; float: left;" id="category" name="category"  class="form-control"  >
                                                                       
                            @foreach($category as $item)
			<option value="{{$item->cc_id}}">{{$item->cc_name}}</option>
                           @endforeach
                                    
                                </select>
                                        <div style="width: 15%; margin-top: 5px;" class="category_add_btn category_add">+</div>
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track " style="width: 97%;">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Title*</label>
                                        {!! Form::text('heading',null, ['class'=>'form-control','id'=>'heading','name'=>'heading','autocomplete'=>'off','required','style'=>"background-color:transparent;"]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track "  style="width: 97%;">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Description </label>
                                        {!! Form::textarea('description',null, ['class'=>'form-control','id'=>'description','required','style'=>"background-color:transparent;",'placeholder'=>"","rows"=>"2","cols"=>"80",'maxlength' => '500']) !!}
                                    </div>
                                </div>
                            </div>
                           
                            
                            <div class="box-footer" id="submitbuttn">
                                <input type="hidden" name="type" id="type" />
                                <a id="inserting" name="inserting" style="margin-top: 4%;" class="staff-add-pop-btn staff-add-pop-btn-new" onclick="add_complaints();">Submit</a>
                            </div>
                        </div>
 {!! Form::close() !!}

                    </div>
                </div>
            </div><!--add-work-done-poppup-textbox-cc-->
        </div>
    </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->
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
                        <span style="margin-top: 15px;" class="add_time_btn_pop" onclick="return submit_category();">ADD</span>
                    </div>
                </div>
                <div class="col-sm-12" style="display: none;">
                    <a  class="staff-add-pop-btn staff-add-pop-btn-new" style="float: right;">SAVE</a>
                </div>
            </div>
        </div>
    </div>

    <div id="edit_load">

    </div>

    <div id="urls"></div>

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
    <script>
        $.fn.dataTable.ext.errMode = 'none';
    </script>
    <script type="text/javascript">
        $(document).ready(function()
        {
            $('#flt_from').datepicker({
                autoclose: true,
                todayHighlight: true,

            });
            $('#flt_to').datepicker({
                autoclose: true,
                todayHighlight: true,

            });
 var date = new Date();
    date.setDate(date.getDate() - 30);
            $("#flt_from").datepicker().datepicker("setDate", date);

            $("#flt_to").datepicker().datepicker("setDate", new Date());
            $('#cpl_date').datepicker({
                autoclose: true,
                todayHighlight: true,

            });

            $("#cpl_date").datepicker().datepicker("setDate", new Date());


        } );
        $(".followups-popup-btn").click(function(){
            $("#rest_auth_sec").css("display","block");
        });

        $(".close-pop-ad-work-cc").click(function(){
            $("#rest_auth_sec").css("display","none");
        });

    </script>
    <script>
 $(".category_add").click(function(){
            $("#cat_name").val('');
            $("#cat_status").attr('checked','true');
            var res_id = $("#res_id");
            $("#cat_name").removeClass('input_focus');
            $(".category_add_popup").show();
        });
        
        $(document).ready(function()
        {
$('#loader_pop').css("display","block");
            var val = '';
            filter_change();
            $("#current_count").val(1);
            $("#start_count").val(1);
            $("#end_count").val(1);

        } );

        function search_filter(cv){
            $("#current_count").val(cv);

            filter_change();
        }
        function search_filter_btn(cv){
            var str = $("#start_count").val();
            var crnt = $("#current_count").val();
            var end = $("#end_count").val();
            var new_crn = 0;
            if(cv==1){
                new_crn = parseInt(crnt)-1;
                $("#current_count").val(new_crn);
            }
            else if(cv==2){
                new_crn = parseInt(crnt)+1;
                $("#current_count").val(new_crn);
            }

            filter_change();
        }
        function view_details(id)
        {
            window.location.href="complaint_details/"+id;
        }
        function refresh_filter()
        {
            $("#current_count").val(1);
            $("#start_count").val(1);
            $("#end_count").val(1);
            filter_change();
        }
        function filter_change()
        { //add_mobile cust_name upld_file cpl_date heading description custid
            
            //alert($("#site_url").val())
            
             $("#loader_pop").css("display","block");
            var flt_priority = $("#flt_priority").val();
            var flt_status = $("#flt_status").val();
            var flt_from = $("#flt_from").val();
            var flt_to = $("#flt_to").val();
            var flt_category = $("#flt_category").val();
            var flt_id = $("#flt_id").val();
            var start_cnt = $("#start_count").val();
            var staff_id = $("#staff_id").val();
            var current_cnt = $("#current_count").val();
            var end_cnt = $("#end_count").val();
            var site_url=$("#site_url").val();
            var s='';
            var m ='';
            var e='';
            var prev='p';
            var next="n";
            var frm = $('#frm_filter');
            var table = $('#example1').DataTable();
            $.ajax({
                method: "post",
                url   : "api/filter/filter_complaintslist",
                data  : {"flt_priority":flt_priority,"flt_id":flt_id,"flt_status":flt_status,"flt_category":flt_category,"flt_status":flt_status,"flt_to":flt_to,"flt_from":flt_from,"current_count":current_cnt,"staff_id":staff_id,"site_url":site_url},
                cache : false,
                crossDomain : true,
                async : true,
                dataType :'text',
                success : function(result)
                {//alert(result);
                    var filter_result = JSON.parse(result);
                    var cust_count = filter_result.count;
                    $("#count").html(cust_count);
                    $("#searchcount").html('&nbsp;<b>'+filter_result.searchcount+'</b>');
                    if(parseInt(filter_result.searchcount)>0) {
                        $("#staff_list").html(filter_result.filter_data);
                        
                        if (filter_result.data_count == 0) {
                            end_cnt = 1;
                        }
                        else {
                            end_cnt = filter_result.data_count;
                        }
                        $("#end_count").val(end_cnt);
                        if (current_cnt == '') {
                            current_cnt = 1;
                        }
                        if (start_cnt == '') {
                            start_cnt = 1;
                        }
                        $(".paginate_button").removeClass("active");
                        $(".paginate_button").removeClass("disabled");
                        if (current_cnt == start_cnt && end_cnt == 1) {
                            $("#pagination").html(' <li class="paginate_button previous disabled" id="pagn_1" ><a href="#">Previous</a></li>' +
                                    '<li class="paginate_button active" id="pagn_2" onclick="search_filter(1)"><a href="#">1</a></li>' +
                                    '<li class="paginate_button disabled" id="pagn_3" ><a href="#">2</a></li>' +
                                    '<li class="paginate_button disabled" id="pagn_4" ><a href="#">3</a></li>' +
                                    '<li class="paginate_button next disabled" id="pagn_5" ><a href="#">Next</a></li>');
                        }
                        else if (current_cnt == start_cnt && end_cnt == 2) {
                            $("#pagination").html(' <li class="paginate_button previous disabled" id="pagn_1" ><a href="#">Previous</a></li>' +
                                    '<li class="paginate_button active" id="pagn_2" onclick="search_filter(1)"><a href="#">1</a></li>' +
                                    '<li class="paginate_button " id="pagn_3" onclick="search_filter(2)"><a href="#">2</a></li>' +
                                    '<li class="paginate_button disabled" id="pagn_4" ><a href="#">3</a></li>' +
                                    '<li class="paginate_button next " id="pagn_5" onclick="search_filter_btn(2)"><a href="#">Next</a></li>');
                        }
                        else if (current_cnt == 2 && end_cnt == 2) {
                            $("#pagination").html(' <li class="paginate_button previous " id="pagn_1" onclick="search_filter_btn(1)" ><a href="#">Previous</a></li>' +
                                    '<li class="paginate_button " id="pagn_2" onclick="search_filter(1)"><a href="#">1</a></li>' +
                                    '<li class="paginate_button active" id="pagn_3" onclick="search_filter(2)"><a href="#">2</a></li>' +
                                    '<li class="paginate_button disabled" id="pagn_4" ><a href="#">3</a></li>' +
                                    '<li class="paginate_button next disabled" id="pagn_5" ><a href="#">Next</a></li>');
                        }
                        else if (current_cnt == start_cnt) {
                            $("#pagination").html(' <li class="paginate_button previous disabled" id="pagn_1"  ><a href="#">Previous</a></li>' +
                                    '<li class="paginate_button active" id="pagn_2" onclick="search_filter(1)" ><a href="#">1</a></li>' +
                                    '<li class="paginate_button " id="pagn_3" onclick="search_filter(2)"><a href="#">2</a></li>' +
                                    '<li class="paginate_button " id="pagn_4" onclick="search_filter(3)"><a href="#">3</a></li>' +
                                    '<li class="paginate_button next " id="pagn_5" onclick="search_filter_btn(2)"><a href="#">Next</a></li>');
                        } else if (current_cnt == end_cnt) {
                            s = parseInt(current_cnt) - 2;
                            m = parseInt(current_cnt) - 1;
                            e = current_cnt;
                            $("#pagination").html(' <li class="paginate_button previous " id="pagn_1" onclick="search_filter_btn(1)" ><a href="#">Previous</a></li>' +
                                    '<li class="paginate_button" id="pagn_2" onclick="search_filter(' + s + ')"><a href="#">' + s + '</a></li>' +
                                    '<li class="paginate_button " id="pagn_3" onclick="search_filter(' + m + ')"><a href="#">' + m + '</a></li>' +
                                    '<li class="paginate_button active" id="pagn_4" onclick="search_filter(' + e + ')"><a href="#">' + e + '</a></li>' +
                                    '<li class="paginate_button next disabled" id="pagn_5" ><a href="#">Next</a></li>');
                        }
                        else {
                            s = parseInt(current_cnt) - 1;
                            m = parseInt(current_cnt);
                            e = parseInt(current_cnt) + 1;
                            $("#pagination").html(' <li class="paginate_button previous " id="pagn_1" onclick="search_filter_btn(1)"><a href="#">Previous</a></li>' +
                                    '<li class="paginate_button" id="pagn_2" onclick="search_filter(' + s + ')"><a href="#">' + s + '</a></li>' +
                                    '<li class="paginate_button active" id="pagn_3" onclick="search_filter(' + m + ')"><a href="#">' + m + '</a></li>' +
                                    '<li class="paginate_button " id="pagn_4" onclick="search_filter(' + e + ')"><a href="#">' + e + '</a></li>' +
                                    '<li class="paginate_button next " id="pagn_5" onclick="search_filter_btn(2)"><a href="#">Next</a></li>');
                        }
                        $("#loader_pop").css("display","none");
                    }else
                    {
                        $("#loader_pop").css("display","none");
                        $("#staff_list").html("No Details");
                    }
                    $('#example1').DataTable(
                            {
                                scrollX: false,
                                dom: "Bfrtip",
                                scrollCollapse: true,
                                "searching": false,
                                "ordering": false,
                                "info": false,
                                "paging": false,
                            } );
//
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
            return true;
        }
         function load_mobilenumbers()
        {     
            var mobile = $("#add_mobile").val();
           
            if(mobile.length ==10){
                // $("#reason_suggestions").html("");
            //$("#user_can_reason").val("");
            //$("#reason_suggestions").hide();
                $.ajax({
                    method: "get",
                    url: "api/complaints_load_custmobile",
                    data: {"mobile": mobile},
                    success: function (result)
                    {
                        if(result!=0)
                        {
                            var field=result.split(':');
                            $("#cust_name").val(field[1]);
                            $("#custid").val(field[0]);
                       // $("#reason_suggestions").show();
                        //$("#reason_suggestions").html(result);
                    }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#errbox").text(jqXHR.responseText);
                    }
                });
            }
            else
            {
                $("#cust_name").val("");
                            $("#custid").val("");
            }
  }
  function selectmobilenumber(id,mobile,name)
        {
            $("#reason_suggestions").hide();
            $("#add_mobile").val(mobile);
            $("#cust_name").val(name);
            $("#custid").val(id);
        }
         function add_complaints()
    {
        $('.notifyjs-wrapper').remove();
        $('input').removeClass('input_focus');
        $('select').removeClass('input_focus');
        //add_mobile cust_name upld_file cpl_date heading description
        //mobile  custname cpl_date heading description upload_file
        var mobile = $("#add_mobile").val();
        var custname = $("#cust_name").val();
        var cpl_date = $("#cpl_date").val();
        var heading = $("#heading").val();
        var description = $("#description").val();
        var upload_file = $("#upld_file");
        var priority = $("#priority");
        var category = $("#category");
        if (mobile == '') {
            $("#add_mobile").focus();
            $.Notification.autoHideNotify('error', 'bottom right', 'Mobile number required');
            return false;
        }
        if (cpl_date == '') {
            $("#cpl_date").focus();
            $.Notification.autoHideNotify('error', 'bottom right', 'Select Date.');
            return false;
        }
        if (custname == '') {
            $("#cust_name").focus();
            $.Notification.autoHideNotify('error', 'bottom right', 'Enter Name.');
            return false;
        }
        if (heading == '') {
            $("#heading").focus();
            $.Notification.autoHideNotify('error', 'bottom right', 'Enter Title.');
            return false;
        }
        if (priority == '') {
            $("#priority").focus();
            $.Notification.autoHideNotify('error', 'bottom right', 'Select Priority');
            return false;
        }
         if (category == '') {
            $("#category").focus();
            $.Notification.autoHideNotify('error', 'bottom right', 'Select Category');
            return false;
        }
        if(upload_file.val() != '')
        {
            if (!hasExtension('upld_file', ['.jpg','.png','.jpeg','.pdf'])) {
                upload_file.addClass('input_focus');
                $.Notification.autoHideNotify('error', 'bottom right','File Format Not Supported');
                return false;
            }
        }
        if (true) {
             //var formdata = new FormData($('#frm_upload')[0]);
            table = $('#loginlisting');
            table.html('');//mobile  custname cpl_date heading description upload_file
//            var data = {
//                "mobile": mobile,
//                "custname": custname,
//                "cpl_date":cpl_date,
//                "heading": heading,
//                "description": description,
//               
//            };


        //alert("ok");
             var formdata = new FormData($('#frm_upload')[0]);
             try
             {
                 
            $.ajax({
                type: "POST",
                url: "api/add_complaints",
                data: formdata,
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                processData: false,
                contentType: false,
                success: function (result) {
//                   / var json_x = JSON.parse(result);
                    //alert(result);
                    if ((result) == 'success') {
                        swal({

                            title: "",
                            text: "Added Successfully",
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
                    else if ((result) == 'already exist') {
                        swal({

                            title: "",
                            text: "Already Exist",
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(jqXHR.responseText);
                    $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }catch(e)
        {
          alert("Error Name: " + e.name + ' Error Message: ' + e);  
        }

        }
    }
   
    </script>
    <script src="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <link href="{{ asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('public/assets/dark/plugins/custombox/css/custombox.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    {{--<script src="{{ asset('public/assets/js/angular.min.js') }}"></script>--}}
    <script src="{{asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />

@stop


<script src="{{asset('public/assets/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('public/assets/ckeditor/samples/js/sample.js')}}"></script>
<script> 
    
    $(".timing_popup_cls").click(function()
    {
        $(".timing_popup_cc").hide();
        filter_change();
    });

   

    $('#staff').change(function()
    {
        $("#submitbuttn").removeClass('disabledbutton');
        var val = $(this).val();
        $.ajax({
            method: "get",
            url: $('#url').val()+"staff_mobile/"+val,
            cache: false,
            crossDomain: true,
            async: false,
            dataType: 'text',
            success: function (result)
            {
                var json_x = JSON.parse(result);
                if ((json_x.msg) == 'success') {
                    $("#mobile").val(json_x.mobile);
                    $.ajax({
                        method: "post",
                        url: $('#url').val() + "staff_credit_amount",
                        data: {"mobile": json_x.mobile},
                        cache: false,
                        crossDomain: true,
                        async: false,
                        dataType: 'text',
                        success: function (rspnse)
                        {
                            var jsonx = JSON.parse(rspnse);
                            if ((jsonx.msg) == 'exist')
                            {
                                $("#amount").val(jsonx.data.total);
                                $("#submitbuttn").removeClass('disabledbutton');

                            }
                            if ((jsonx.msg) == 'notexist')
                            {
                                $("#amount").val(0);
                                $("#submitbuttn").addClass('disabledbutton');
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            alert(jqXHR.responseText);
                            $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                        }
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert(jqXHR.responseText);
                $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
            }
        });
    });
   
   function submit_category()
        {
            $('.notifyjs-wrapper').remove();
           // var res_id = $("#res_id");
            var cat_name = $("#cat_name");
            //var categrylist     = $("#categrylist");
            var selects    =  $("#category");//var selected_cat=new Array();
            var selected_cat = selects.val();//alert(selected_cat);
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
            var data = {"category" : cat_name.val()};

            $.ajax({
                method: "post",
                url : "api/category_complaints/add",
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
                        //alert(JSON.stringify(json_rslt.category));
                        $.each(json_rslt.category,function(i,indx)
                        {
                                  var count = parseInt(i)+1;
                                  var htm = '';
                                  htm += '<option value="' + indx.cc_id + '">' + indx.cc_name + '</option>';
                                  selects.append(htm);
                                  //selects.multiselect('rebuild');
                        });
//                        var dat = new Array();alert(selected_cat);
//                               dat = selected_cat;
//                             dat= dat.push(toTitleCase(cat_name.val()));
//                              var dataarray = dat;
//                              dataarray = dataarray.filter(function() { return true; });
//                              selects.val(dataarray);
                              //selects.multiselect('refresh');
                              //$("input[name=cat_status]").attr('checked','true');
                              swal({

                               title: "",
                               text: "Added Successfully",
                               timer: 500,
                               showConfirmButton: false
                                    });
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
        
    
</script>
@endsection




