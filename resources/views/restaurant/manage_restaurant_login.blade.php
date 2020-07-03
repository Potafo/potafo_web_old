@extends('layouts.app')
@section('title','Potafo - Manage Restaurant Login')
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
        .onoffswitch{width: 70px;}.onoffswitch-switch{right: 40px;}
    </style>

    <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{asset('public/assets/script/common.js') }}" type="text/javascript"></script>
    <div class="col-sm-12">

        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('index') }}">Dashboard</a>
                </li>

                <li href="{{ url('manage_restaurant') }}" class="active ms-hover">
                    Restaurants
                </li>
            </ol>
        </div>
        <div class="card-box table-responsive" style="padding: 8px 10px;">

            <div class="box master-add-field-box" >
                <div class="col-md-6 no-pad-left">
                    <h3>Manage Restaurant Login</h3>
                </div>
                <div class="col-md-1 no-pad-left pull-right">
                    <div class="table-filter" style="margin-top: 4px;">
                        <div class="table-filter-cc">
                            <a href="#"> <button type="submit" style="margin-top: px; border-radius: 4px;margin-left: 0;" class="on-default followups-popup-btn btn btn-primary ad-work-clear-btn" >Add New</button></a>

                        </div>

                    </div>
            </div>
                <div class="filter_box_section_cc diy_tgl" style="display: none">
                <!--                <div class="filter_box_seplction">FILTER</div>-->
                <div class="filter_text_box_row">
                    {!! Form::open(['url'=>'filter/restaurant', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'get']) !!}
                    <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>
                    <div class="main_inner_class_track" style="width: 25%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Restaurant Name</label>
                                <input id="restaurant_name" onkeyup="return filter_change(this.value)" name="restaurant_name" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Diet</label>
                                <select id="diet" name ="diet" class="form-control" onchange="return filter_change(this.value);">
                                    <option value="">All</option>
                                    <option value="N">Non Veg</option>
                                    <option value="Y">Veg</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Phone</label>
                                <input id="phone" name="phone"  onkeyup="return filter_change(this.value)" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}

                </div>
            </div>

            <div class="table_section_scroll">
                <table id="example1" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th style="min-width:3px">Slno</th>
                        <th style="min-width:250px">Name</th>
                        <th style="min-width:100px">Role</th>
                        <th style="min-width:5px">Active</th>
                        <th style="min-width:10px"></th>
                    </tr>
                    </thead>
                    <tbody id="loginlisting">
                    @if(isset($rows))
                        @if(count($rows)>0)
                            @foreach($rows as $key=>$item)
                                <tr>
                                    <td style="min-width:3px;">{{ $key+1 }}</td>
                                    <td style="min-width:100px;text-align: left;">@if(isset($item['name'])) {{ title_case($item['name']) }}@endif</td>
                                    <td style="min-width:100px;text-align: left;">@if(isset($item['role'])) {{ title_case($item['role']) }}@endif</td>
                                    <td style="min-width:5px;">   <div class="status_chck{{ $item['id']}}">
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch{{$item['id']}}" @if($item['active'] == 'Y') checked @endif>
                                                <label class="onoffswitch-label" for="myonoffswitch{{$item['id']}}">
                                                    <span class="onoffswitch-inner" onclick="return  statuschange('{{$item['id']}}')"></span>
                                                    <span class="onoffswitch-switch" onclick="return  statuschange('{{$item['id']}}')"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: left;width:10%">
                                        <a onclick="return loginedit('{{$item['id']}}','{{$item['name']}}','{{$item['password']}}','{{$item['role']}}')" class="btn button_table clear_edit" >
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="#" onclick="return del_restlog('{{$item['id']}}')" class="table-action-btn button_table"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    <div id="rest_auth_sec" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup">
            <div class="add-work-done-poppup-head">Login Details
                <a href="#" onclick="close_aut_log()"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
            <div style="text-align:center;" id="branchtimezone"></div>
            <div class="add-work-done-poppup-contant" >

                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">

                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">

                            <input type='hidden' id='url' value='{{$url}}' />
                            <input type='hidden' id='restid' value="{{$restid}}" name="restid" />
                            <input type='hidden' id='userid'  name="userid" />
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Role</label>
                                        {{ Form::select('role',[''=>'Select','Manager' => 'Manager','Staff'=>'Staff'],null,['id' => 'role','autocomplete'=>'off','class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Login Name</label>
                                        {!! Form::text('rest_name',null, ['class'=>'form-control','id'=>'rest_name','name'=>'rest_name','autocomplete'=>'off','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}

                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Password</label>
                                        <input style="padding-right:35px;" class="form-control" id="rest_pasw" name="rest_pasw" type="password" autocomplete='off',>

                                        <div class="ion-ios7-eye pass_show" onmouseover="mouseoverPass();" onmouseout="mouseoutPass();" />
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track" style="width:20%">
                                <b><p class="" style="color: #000;float:right;cursor:pointer;display: none;background-color: burlywood;margin-top: 6px;padding: 2px 13px;line-height: 30px;" id="getimage" data-toggle="modal"  data-target="#myModal" data-title=""><a style="color: #000;">View image</a></p></b>

                            </div>

                            <div class="box-footer">
                                <input type="hidden" name="type" id="type" />
                                <a id="inserting" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="add_auth('insert');">Submit</a>
                                <a id="updating" name="updating"  class="staff-add-pop-btn" onclick="add_auth('update');" style="height:40px; bottom: 20px;">Update</a>
                            </div>
                        </div>


                    </div>
                </div>
            </div><!--add-work-done-poppup-textbox-cc-->
        </div>
    </div>
    </div>
    <div class="add-work-list-cc">
        <!--<div class="add-work-list-head">LIST</div>-->


    </div><!--add-work-done-poppup-->


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
    <script type="text/javascript">
        function del_restlog(id)
  {
       var data = {
                "id": id
                            };
  
            if(confirm('Are you sure to delete?'))
            {                    
                        $.ajax({
                        method: "post",
                        url: "../../api/remove_rest_login",
                        data: data,
                        cache: false,
                        crossDomain: true,
                        async: false,
                        dataType: 'text',
                        success: function (result)
                        {//alert(result)
                            //console.log(result)
                           location.reload(true);
                                swal({

                                   title: "",
                                   text: "Deleted Successfully",
                                   timer: 2000,
                                   showConfirmButton: false
                               });

                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                           $("#errbox").text(jqXHR.responseText);
                       }
                       });
                     }
                  
  }
     $(document).ready(function()
        {
            var t = $('#example1').DataTable({
                scrollX: false,
                scrollCollapse: false,
                "searching": false,
                "ordering": false,
                "info": false,
                "bPaginate": false,
                columnDefs: [
                    { width: '20%', targets: 0 }
                ],
                "deferLoading": 0,
                "lengthChange": false,
                "columnDefs": [{
                    paging: false
                } ],
            } );
        });

        $('.filter_sec_btn').on('click', function(e)
        {
            $('.filter_box_section_cc').toggleClass("diply_tgl");
            $("#restaurant_name").focus();
        });
    </script>
    <script>
        $(document).ready(function () {
            $('input').attr('autocomplete', 'false');
        });
    </script>
    <script>
        function mouseoverPass(obj) {
            var obj = document.getElementById('rest_pasw');
            obj.type = "text";
        }
        function mouseoutPass(obj) {
            var obj = document.getElementById('rest_pasw');
            obj.type = "password";
        }
    </script>
    <script>
        $(".followups-popup-btn").click(function(){
                  $('#rest_name').val('');
                  $("#rest_pasw").val('');
                  $("#rest_auth_sec").css("display","block");
                  $("#inserting").css("display",'block');
                  $("#updating").css("display",'none');

        });
        function close_aut_log()
        {
            $("#rest_auth_sec").css("display","none");
            $("#inserting").css("display",'block');
            $("#updating").css("display",'none');
            $("#rest_name").val('');
            $("#rest_pasw").val('');

        }

        function add_auth(type) {
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var role = $("#role").val();
            var rest_name = $("#rest_name").val();
            var rest_pasw = $("#rest_pasw").val();
            var update = $("#updating").val();
            var resid = $("#restid").val();
            var active = $("#active").val();
            if (role == '') {
                $("#role").focus();
                $.Notification.autoHideNotify('error', 'bottom right', 'Select Role.');
                return false;
            }
            if (rest_name == '') {
                $("#rest_name").focus();
                $.Notification.autoHideNotify('error', 'bottom right', 'Enter Login Name.');
                return false;
            }
            if (rest_pasw == '') {
                $("#rest_pasw").focus();
                $.Notification.autoHideNotify('error', 'bottom right', 'Enter Password.');
                return false;
            }
            if (true) {
                table = $('#loginlisting');
                table.html('');
                var data = {
                    "userid":$("#userid").val(),
                    "role": role,
                    "restname": rest_name,
                    "restpasw": rest_pasw,
                    "resid": resid,
                    "active": active,
                    "type": type
                };
                $.ajax({
                    method: "post",
                    url: "../../api/add_restaurantlogin",
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
                        else if ((json_x.msg) == 'done') {
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
                        location.reload();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });

            }
        }

        function loginedit(id,name,password,role)
        {
            $("#rest_name").val(name);
            $("#rest_pasw").val(password);
            $("#role").val(role);
            $("#userid").val(id);
            $("#inserting").css("display",'none');
            $("#updating").css("display",'block');
            $("#rest_auth_sec").css("display",'block');
        }

        function statuschange(id) {
            var ids = id;
            var data = {"ids": ids};
            $.ajax({
                method: "get",
                url: "../../restaurantlogin_status",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                   // location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
//                    alert(errorThrown);
                    $("#errbox").text(jqxhr.responseText);
                }
            });
        }

    </script>
@stop
@endsection




