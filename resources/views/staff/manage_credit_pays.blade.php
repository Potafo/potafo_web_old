@extends('layouts.app')
@section('title','Manage Credit Pays')
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
    </style>

    <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <input type='hidden' id='url' value='{{$url}}' />

    <div class="col-sm-12">

        <div class="card-box table-responsive" style="padding: 8px 10px;">
            <div class="box master-add-field-box" >
                <div class="col-md-6 no-pad-left">
                    <h3>MANAGE CREDITS  PAYS </h3>
                </div>
                <form style="float: right;"  action="{{ URL::to('excel_download_creditpay') }}" class="form-horizontal" method="get" enctype="multipart/form-data">

                            <input id="flt_name_xl" name="flt_name_xl" class="form-control" type="hidden">
                        
                        <input id="flt_from_xl" data-date-format='dd-mm-yyyy'  name="flt_from_xl" class="form-control" type="hidden" >
                         <input id="flt_to_xl" data-date-format='dd-mm-yyyy'  name="flt_to_xl" class="form-control" type="hidden" >
                         <a href="#"><button class="btn btn-success btn-sm">Excel</button></a>

                        </form>
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
                    {!! Form::open(['url'=>'manage_creditpays', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'get','onkeypress'=>"return event.keyCode != 13;"]) !!}

                    <div class="main_inner_class_track" style="width: 20%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>From</label>
                                <input id="flt_from" data-date-format='dd-mm-yyyy'  name="flt_from" class="form-control" type="text"  >
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 20%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>To</label>
                                <input id="flt_to" data-date-format='dd-mm-yyyy' name="flt_to" class="form-control" type="text" >
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 20%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Staff Name</label>
                                <select id="flt_name" name="flt_name" class="form-control">
                                    <option value="">Select Staff</option>
                                    @foreach($staff as $key=>$list)
                                        <option value="{{$key}}">{{$list}}</option>
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
    <div id="rest_auth_sec" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup" style="height: 49% !important;">
            <div class="add-work-done-poppup-head">Credit Pays
                <a href="#" onclick="close_aut_log()"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
            <div style="text-align:center;" id="branchtimezone"></div>
            <div class="add-work-done-poppup-contant" >

                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">

                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">

                            <input type='hidden' id='url' value='{{$url}}' />
                            <input type='hidden' id='userid'  name="userid" />
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Staff</label>
                                        {{ Form::select('staff',[''=>'Select']+$staff,null,['id' => 'staff','autocomplete'=>'off','class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track " style="pointer-events:none;">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Mobile No</label>
                                        {!! Form::text('mobile',null, ['class'=>'form-control','id'=>'mobile','name'=>'mobile','autocomplete'=>'off','required','style'=>"background-color:transparent;"]) !!}

                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Amount</label>
                                        {!! Form::text('amount',null, ['class'=>'form-control','id'=>'amount','name'=>'amount','autocomplete'=>'off','required','style'=>"background-color:transparent;"]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Transfer Date</label>
                                        <input id="trans_date" data-date-format='dd-mm-yyyy'  name="trans_date" class="form-control" type="text"  >
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Remarks</label>
                                        {!! Form::textarea('description',null, ['class'=>'form-control','id'=>'description','required','style'=>"background-color:transparent;",'placeholder'=>"","rows"=>"2","cols"=>"80",'maxlength' => '50']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer" id="submitbuttn">
                                <input type="hidden" name="type" id="type" />
                                <a id="inserting" name="inserting" style="margin-top: 8%;" class="staff-add-pop-btn staff-add-pop-btn-new" onclick="add_credit();">Submit</a>
                            </div>
                        </div>


                    </div>
                </div>
            </div><!--add-work-done-poppup-textbox-cc-->
        </div>
    </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->


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

            $("#flt_from").datepicker().datepicker("setDate", new Date());

            $("#flt_to").datepicker().datepicker("setDate", new Date());
            $('#trans_date').datepicker({
                autoclose: true,
                todayHighlight: true,

            });

            $("#trans_date").datepicker().datepicker("setDate", new Date());


        } );
        $(".followups-popup-btn").click(function(){
            $("#rest_auth_sec").css("display","block");
        });

        $(".close-pop-ad-work-cc").click(function(){
            $("#rest_auth_sec").css("display","none");
        });

    </script>
    <script>

        $(document).ready(function()
        {

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
        function refresh_filter()
        {
            $("#current_count").val(1);
            $("#start_count").val(1);
            $("#end_count").val(1);
            filter_change();
        }
        function filter_change()
        {
            var flt_name = $("#flt_name").val();
            var flt_status = $("#flt_status").val();
            var flt_from = $("#flt_from").val();
            var flt_to = $("#flt_to").val();
            
             $("#flt_name_xl").val(flt_name);
        $("#flt_to_xl").val(flt_to);
        $("#flt_from_xl").val(flt_from);
            
            var start_cnt = $("#start_count").val();
            var staff_id = $("#staff_id").val();
            var current_cnt = $("#current_count").val();
            var end_cnt = $("#end_count").val();
            var s='';
            var m ='';
            var e='';
            var prev='p';
            var next="n";
            var frm = $('#frm_filter');
            var table = $('#example1').DataTable();
            $.ajax({
                method: "post",
                url   : "api/filter/creditpays_list",
                data  : {"flt_name":flt_name,"flt_status":flt_status,"flt_to":flt_to,"flt_from":flt_from,"current_count":current_cnt,"staff_id":staff_id},
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(result)
                {
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

    function add_credit()
    {
        $('.notifyjs-wrapper').remove();
        $('input').removeClass('input_focus');
        $('select').removeClass('input_focus');
        var staff = $("#staff").val();
        var mobile = $("#mobile").val();
        var trans_date = $("#trans_date").val();
        var amount = $("#amount").val();
        var description = $("#description").val();
        if (staff == '') {
            $("#role").focus();
            $.Notification.autoHideNotify('error', 'bottom right', 'Select Staff.');
            return false;
        }
        if (trans_date == '') {
            $("#rest_name").focus();
            $.Notification.autoHideNotify('error', 'bottom right', 'Select Date.');
            return false;
        }
        if (amount == '') {
            $("#rest_pasw").focus();
            $.Notification.autoHideNotify('error', 'bottom right', 'Enter Amount.');
            return false;
        }
        if (true) {
            table = $('#loginlisting');
            table.html('');
            var data = {
                "staff": staff,
                "mobile": mobile,
                "depositor_mobile":mobile,
                "transfer_date": trans_date,
                "product_description": description,
                "transactionSource":"Potafo",
                "PNBTransactionID":null,
                "customer_name":null,
                "paymode":"CASH",
                "amount":amount,

            };



            console.log(data);
            $.ajax({
                method: "post",
                url:  $('#url').val()+"credit_pay_post",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result) {
                    var json_x = JSON.parse(result);
                    if ((json_x.msg) == 'success') {
                        swal({

                            title: "",
                            text: "Added Successfully",
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
                    else if ((json_x.msg) == 'already exist') {
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

        }
    }

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
							document.getElementById("amount").disabled=true;
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
    })
</script>
@endsection




