@extends('layouts.app')
@section('title','Potafo - Category Type')
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
        .tbl_view_sec_btn{width: auto;padding: 5px;border-radius: 5px;border-bottom: solid 3px #ececec;color: #666; text-decoration: none !important; box-shadow: 0px 3px 7px #cac7c7;}

        .main_inner_class_track .bootstrap-select{border: solid 1px #ccc;}
        .table_staff_scr_scr thead{ display: inline-block;width: 100%;}
        .table_staff_scr_scr tbody{ display: inline-block;width: 100%;max-height:  390px;overflow: auto   }
        .table_staff_scr_scr tr{ display: inline-block;width: 100%;}
        .table_staff_scr_scr td{ width: 100px;}
        .table_staff_scr_scr th{ width: 100px;}
        .pagination_total_showing{float: left;width: auto;padding-top: 12px;padding-left: 10px;color: #000000;}
        .add-work-done-poppup-textbox-box label{font-weight:lighter;}.inner-textbox-cc input:focus ~ label, input:valid ~ label{font-size:13px;top: -10px;}.group{margin-bottom: 14px}.add-work-done-poppup{height: auto;} div.dataTables_wrapper div.dataTables_filter{float: right;top: 4px;position: relative;}.dataTables_length{top: 7px;position: relative;float: left}
        .dataTables_scrollHeadInner{width: 100% !important}.dataTables_scrollHeadInner table{width: 100% !important}.dataTables_scrollBody table{width: 100% !important} .dataTables_scrollBody {  height: 350px;}
        .popover {width: 180px;height: 120px;}.popover img{width:100%}

    </style>
    <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{asset('public/assets/admin/script/menu.js') }}" type="text/javascript"></script>
    <script src="{{asset('public/assets/script/common.js') }}" type="text/javascript"></script>
    <div class="col-sm-12">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li>
                    <a href="{{  url('index') }}">Dashboard</a>
                </li>

                <li class="active ms-hover">
                    <a href="{{  url('manage_restaurant') }}">{{$restaurant_name}}</a>
                </li>
                <li class="active ms-hover">
                    Category
                </li>

            </ol>
        </div>

        <div class="col-sm-12">
            <a href="{{ url('cat_restaurant_edit/'.$restaurant_id) }}"><div class="potafo_top_menu_sec">About</div></a>
            <a href="{{ url('menu/types/'.$restaurant_id) }}"><div class="potafo_top_menu_sec">Type</div></a>
            <a ><div class="potafo_top_menu_sec potafo_top_menu_act">Type Category</div></a>

        </div>

        <div class="card-box table-responsive" style="padding: 8px 10px;">

            <div class="box master-add-field-box" >
                <div class="col-md-6 no-pad-left">
                    <h3>Menu Type Category</h3>
                </div>
                <div class="col-md-1 no-pad-left pull-right">
                    <div class="table-filter" style="margin-top: 4px;">
                        <div class="table-filter-cc">
                            <a style="cursor:pointer;"> <button type="submit" style="margin-top: px; border-radius: 4px;margin-left: 0;" class="on-default followups-popup-btn btn btn-primary ad-work-clear-btn" >Add New</button></a>

                        </div>

                    </div>
                </div>

            </div>


            <div >
                <div class="filter_box_section_cc diply_tgl" style="display: block">
                    <!--                <div class="filter_box_section">FILTER</div>-->
                    <div class="filter_text_box_row">
                        {!! Form::open(['url'=>'filter/restaurant', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'get']) !!}
                        <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>
                        <input type="hidden" id="logingroup" name="logingroup" value="{{ Session::get('logingroup')}}"/>
                        <div class="main_inner_class_track" style="width: 25%;">
                            <div class="group">
                                <div style="position: relative">
                                    <label> Name</label>
                                    <input id="filter_name" onkeyup="return filter_change(this.value)" name="filter_name" class="form-control" type="text">
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>

{!! Form::close() !!}
                        <table id="datatable-1" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th style="min-width:3px">Slno</th>
                        <th style="min-width:130px">Name</th>
                        <th style="min-width:130px">Max Item Count</th>
                        <th style="min-width:100px">Display Order</th>
                        <th style="min-width:100px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($details)>0)
                        @foreach($details as $key=>$item)
                            <tr>
                                <td  style="min-width:3px !important;">{{ $key+1 }}</td>
                                <td style="min-width:130px !important;">{{ title_case($item->mtc_name) }}</td>
                                <td style="min-width:130px !important;">{{ $item->mtc_max_item_count }}</td>
                                <td style="min-width:100px;">
                                    <input type="textbox" onkeypress="return isNumberKey(event)" value="{{ $item->mtc_display_order }}" title="Edit Order" name="order_no" id="order_no" onkeyup="return changeorderno('{{ $item->mtc_id }}','{{ $item->mtc_menu_type_id }}',this.value)">
                                </td>
                                <td style="text-align: left;min-width:130px !important;">
                                    <a onclick="return categryedit('{{$item->mtc_id}}','{{ $item->mtc_name }}','{{ $item->mtc_max_item_count }}','{{ $item->mtc_status }}')" class="btn button_table clear_edit" >
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <div id="add_user" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup">
            <div class="add-work-done-poppup-head">Add/Edit
                <a style="cursor:pointer;"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>

            <div style="text-align:center;" id="branchtimezone"></div>

            <div class="add-work-done-poppup-contant" >

                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">

                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">
                            <form enctype="multipart/form-data" id="cat_category_form" role="form" method="POST" action="" >

                                <input type='hidden' id='url' value='{{$url}}' />
                                <input type="hidden" value="{{ $restaurant_id }}" id="res_id" name="res_id">
                                <input type="hidden" value="{{ $type_id }}" id="menutype_id" name="menutype_id">
                                <input type='hidden' id='type_id' name="type_id" />
                                <input type='hidden' id='userid' name="userid" value="{{$user_id}}" />

                                <div class="main_inner_class_track ">
                                    <div class="group">
                                        <div style="position: relative">
                                            <label>Name *</label>
                                            {!! Form::text('name',null, ['class'=>'form-control','id'=>'name','name'=>'name','required','style'=>"background-color:transparent;"]) !!}

                                        </div>
                                    </div>
                                </div>
                                <div class="main_inner_class_track ">
                                    <div class="group">
                                        <div style="position: relative">
                                            <label>Max Item Count *</label>
                                            {!! Form::text('count',null, ['class'=>'form-control','id'=>'count','name'=>'count','onkeypress' => 'return numonly(event);','required','style'=>"background-color:transparent;"]) !!}

                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-3 main_inner_class_track statusfield"  style="display: none">
                                    <div class="form-group" id="status_p">
                                        <label for="status"><span style="color:black">&nbsp;</span></label>
                                        <p style="color: red;margin:0 0 5px"></p>
                                        {{ Form::select('status',['Active' => 'Active','Inactive' => 'Inactive'],null,['id' => 'status', 'class'=>"form-control"])}}
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <input type="hidden" name="type" id="type" value="insert"/>
                                    <a id="inserting" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_category('insert');">Submit</a>
                                    <a id="updating" name="updating"  class="staff-add-pop-btn" onclick="submit_category('update');" style="height:40px; bottom: 20px;">Update</a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div><!--add-work-done-poppup-textbox-cc-->
            </div>
            <div class="add-work-list-cc">
                <!--<div class="add-work-list-head">LIST</div>-->


            </div><!--add-work-done-poppup-->

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
            var t = $('#datatable-1').DataTable({
                scrollX: false,
                scrollCollapse: true,
                "searching": false,
                "ordering": false,
                "info": false,
                columnDefs: [
                    { width: '20%', targets: 0 }
                ],
                "deferLoading": 0,
                "lengthChange": false,
                "columnDefs": [{
                    paging: false
                } ],
            } );
        } );
    </script>
    <script>

        $(document).ready(function()
        {
            $('a[rel=popover]').popover({
                html: true,
                trigger: 'hover',
                placement: 'right',
                content: function(){return '<img src="../../'+$(this).data('img') + '" width="200" height="100"/>';}
            });
        });
        function numonly(evt)
        {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31
                    && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

        function charonly(evt)
        {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
                return false;
            return true;
        }

        function changeorderno(id,menutypeid,val)
        {
            $.ajax({
                method: "get",
                url: "../../api/categorytype_order/"+ id+ "/"+ menutypeid+ "/"+val,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                    var json_x = JSON.parse(result);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
            return true;
        }

        $(".followups-popup-btn").click(function(){
            $("#type").val('insert');
            $("#add_user").show();
            $(".statusfield").hide();
            $("#inserting").css("display",'block');
            $("#updating").css("display",'none');
        });

        $(".ad-work-close-btn").click(function(){
            $("#reset_pasword").css("display",'none');
            $("#add_user").hide();
            $('#view_all_categ').hide();
            $("#new_password").html('');
            $("#confirm_password").html('');
            $("#userid_reset").html('');
            $("#username_reset").html('');
        });

        function submit_category(type)
        {
            var table ;
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var  name = $("#name").val();
            var count = $("#count").val();
            var update =$("#updating").val();
            var userid =$("#userid").val();
            var status =$("#status").val();
            if(name == '') {
                $("#name").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter  Name.');
                return false;
            }
            if(count == '') {
                $("#count").focus();
                $.Notification.autoHideNotify('error', 'bottom right', 'Enter Item Max Count.');
                return false;
            }
            if(true) {
                var formdata = new FormData($('#cat_category_form')[0]);
                table = $('#categorylisting');
                table.html('');
                var i = 1;
                $.ajax({
                    method: "post",
                    url: "../../api/add_categorytype",
                    data: formdata,
                    cache: false,
                    crossDomain: true,
                    async: false,
                    processData: false,
                    contentType: false,
                    dataType: 'text',
                    success: function (result) {
                        var json_x = JSON.parse(result);
                        if ((json_x.msg) == 'success') {
                            window.location.href = "../../menu/category/" + $("#menutype_id").val();
                            swal({

                                title: "",
                                text: "Added Successfully",
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }
                        else if ((json_x.msg) == 'already exist') {
                            window.location.href = "../../menu/category/" + $("#menutype_id").val();
                            swal({

                                title: "",
                                text: "Already Exist",
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }
                        else if ((json_x.msg) == 'done') {
                            window.location.href = "../../menu/category/" + $("#menutype_id").val();
                            swal({

                                title: "",
                                text: "Updated Successfully",
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }
                        else if ((json_x.msg) == 'exist') {
                            swal({

                                title: "",
                                text: "Already Exist",
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }
                        $("#add_user").hide();
                        $("#area").val('');
                        $("#inserting").css("display", 'block');
                        $("#updating").css("display", 'none');

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
        }
        function categryedit(id,name,count,status)
        {
            $("#type").val('update');
            $("#add_user").show();
            $(".statusfield").show();
            $("#name").val(name);
            $("#count").val(count);
            $("#type_id").val(id);
            $("#status").val(status);
            $("#inserting").css("display",'none');
            $("#updating").css("display",'block');
        }

        function filter_change(val)
        {
            var frm = $('#frm_filter');
            var table = $('#datatable-1').DataTable();
            $.ajax({
                method: "post",
                url   : "../../api/filter/category_type",
                data  : frm.serialize(),
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(result)
                {
                    var rows = table.rows().remove().draw();
                    var json_x= JSON.parse(result);
                    if(parseInt(json_x.length) > 0) {
                        $.each(json_x, function (i, val)
                        {
                            var count = i + 1;
                            var name = val.mtc_name;
                            var id = val.mtc_id;
                            var typeid = val.mtc_menu_type_id;
                            var mtc_max_item_count = val.mtc_max_item_count;
                            var displayorder = val.mtc_display_order;
                            var newRow = '<tr><td style="min-width:3px;">'+count+'</td>'+'<td style="min-width:160px;text-align: left;">'+name+'</td>'+
                                    '<td style="min-width:80px;text-align: left;">'+mtc_max_item_count+'</td>'+
                                    '<td style="width:70px;"><input style="width:70px;" class="form-control" type="textbox" onkeypress="return isNumberKey(event)" value='+displayorder+' title="Edit Order" name="order_no" id="order_no" onkeyup="return changeorderno('+id+','+typeid+',this.value)"></td>'+
                                    '<td> <a onclick="return categryedit(\''+val.mtc_id+'\',\''+val.mtc_name+'\',\''+val.mtc_max_item_count+'\',\''+val.mtc_status+'\')" class="btn button_table clear_edit" >'+
                                    '<i class="fa fa-pencil"></i></a></td>'+ '</tr>';
                            var rowNode = table.row.add($(newRow)).draw().node();
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
            return true;
        }
    </script>

@stop

@endsection




