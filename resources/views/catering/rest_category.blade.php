@extends('layouts.app')
@section('title','Potafo - Category')
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

                <li class=" ms-hover">
                    <a href="{{  url('manage_restaurant') }}">{{$restaurant_name[0]->name}}</a>
                </li>
                <li class="active ms-hover">
                    Category
                </li>
               
            </ol>
        </div>

        <div class="col-sm-12">
            <a href="{{ url('cat_restaurant_edit/'.$restaurant_id) }}"><div class="potafo_top_menu_sec">About</div></a>
            <a href="{{ url('menu/types/'.$restaurant_id) }}"><div class="potafo_top_menu_sec ">Type</div></a>
            <a ><div class="potafo_top_menu_sec potafo_top_menu_act">Category</div></a>
            <a href="{{ url('cat_restaurant_pincode/'.$restaurant_id) }}"><div class="potafo_top_menu_sec ">Pincode</div></a>
            <a href="{{ url('cat_restaurant_tax/'.$restaurant_id) }}"><div class="potafo_top_menu_sec ">Tax</div></a>
        </div>

        <div class="card-box table-responsive" style="padding: 8px 10px;">

            <div class="box master-add-field-box" >
                <div class="col-md-6 no-pad-left">
                    <h3>{{$restaurant_name[0]->name}} Category</h3>
                </div>
               

            </div>
            
<div class="filter_box_section_cc diply_tgl" style="display: block">
<!--                <div class="filter_box_section">FILTER</div>-->
                   <div class="filter_text_box_row">
                       
                       <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>
                       <input type="hidden" id="logingroup" name="logingroup" value="{{ Session::get('logingroup')}}"/>
                       <input type="hidden" id="restid" name="restid" value="{{$restaurant_id}}"/>
                       <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Add Category</label>
                                 <select id="category" name ="category" class="form-control" >
                                     <option value="">Select Category</option>
                                    @foreach($category as $item)
                                        @if($item->cc_status == 'Active')
                                          <option value="{{ $item->cc_id }}">{{ title_case($item->cc_name) }}</option>
                                          @endif
                                      @endforeach
                                 </select>
                              </div>
                           </div>
                        </div>
                       <div class="col-md-1 no-pad-left ">
                    <div class="table-filter" style="margin-top: 4px;">
                        <div class="table-filter-cc">
                            <a style="cursor:pointer;" onclick="submitcategory({{$restaurant_id}})">submit</a>

                        </div>

                    </div>
                </div>
                       

                   </div>  
            </div>
        
            <div >
                <table id="datatable-1" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th style="min-width:3px">Slno</th>
                        <th style="min-width:130px">Category</th>
                        <!--<th style="min-width:130px">Image View</th>
                        <th style="min-width:100px">Display Order</th>-->
                        <th style="min-width:100px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($details)>0)
                        @foreach($details as $key=>$item)
                            <tr>
                                <td  style="min-width:3px !important;">{{ $key+1 }}</td>
                                <td style="min-width:130px !important;">{{ title_case($item->categoryname) }}</td>
                               
                                <td style="text-align: left;min-width:130px !important;">
                                    <a onclick="return categrydel('{{$item->cid}}','{{$restaurant_id}}')" class="btn button_table clear_edit" >
                                        <i class="fa fa-trash-o"></i>
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
                                        <label>Rate *</label>
                                        {!! Form::text('rate',null, ['class'=>'form-control','id'=>'rate','name'=>'rate','onkeypress' => 'return numonly(event);','required','style'=>"background-color:transparent;"]) !!}

                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Min Pax *</label>
                                        {!! Form::text('min_pax',null, ['class'=>'form-control','id'=>'min_pax','name'=>'min_pax','onkeypress' => 'return numonly(event);','required','style'=>"background-color:transparent;"]) !!}

                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Max Pax *</label>
                                        {!! Form::text('max_pax',null, ['class'=>'form-control','id'=>'max_pax','name'=>'max_pax','onkeypress' => 'return numonly(event);','required','style'=>"background-color:transparent;"]) !!}

                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Upload *</label>
                                        <input autocomplete="off"   id="type_image" name="type_image" type="file" class="form-control" onclick="return Upload()">
                                        <a style="display:none;" class="btn tbl_view_sec_btn" id="view_cat_icon" rel="popover"  href="" style="text-decoration: underline;">View</a>
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

                            <div class="main_inner_class_track" style="width:20%">
                                <b><p class="" style="color: #000;float:right;cursor:pointer;display: none;background-color: burlywood;margin-top: 6px;padding: 2px 13px;line-height: 30px;" id="getimage" data-toggle="modal"  data-target="#myModal" data-title=""><a style="color: #000;">View image</a></p></b>

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
        function statuschange(id,slno,optn) {

            var ids = id;
            var slno = slno;
            var catstatus = $("#status"+id+slno).prop('checked');
            var imgstatus = $("#myonoffswitch"+id+slno).prop('checked');
            if(imgstatus == true){
                imgstatus = 'N';
            }
            else{
                imgstatus = 'Y'
            }
            if(catstatus == true){
                catstatus = 'N';
            }
            else{
                catstatus = 'Y'
            }
            var data = {"ids":ids,"slno":slno,"optn":optn,"catstatus":catstatus,"imgstatus":imgstatus};
            $.ajax({
                method: "get",
                url: "../../category_imgview",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
//                    alert(result);
//                  location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
//                    alert(errorThrown);
                    $("#errbox").text(jqxhr.responseText);
                }
            });
        }

        function changeorderno(resid,id,val)
        {
            $.ajax({
                method: "get",
                url: "../../api/type_order/" + resid+ "/"+ id+ "/"+val,
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

            $("#cl_id").val();
            $("#group_id").val('');
            $("#group_id").removeClass('not-active');
            $("#Currency").val('');
            $("#reg_email").val('');
            $("#host_name").text('');
            $("#port_no").val('');
            $("#user_name").val('');
            $("#password").val('');
            $("#db_name").val('');
            $("#phone_number").val('');
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

       

        function categrydel(catid,restid)
        {
            if(confirm('Are you sure to delete?'))
            {
                //var siteUrl = $("#siteUrl").val();
                //var table = $('#example1').DataTable();
                $.ajax({
                    method: "get",
                    url: "../api/cat_restcatgy_delete/" + catid + "/" + restid,
                    cache: false,
                    crossDomain: true,
                    async: false,
                    dataType: 'text',
                    success: function (result) {
                       // var rows = table.rows().remove().draw();
                        var json_x = JSON.parse(result);
                        if (json_x.msg == 'deleted') {
                            swal({

                                title: "",
                                text: "Deleted Successfully",
                                timer: 1000,
                                showConfirmButton: false
                            });
                            window.location.reload();
                           
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
            return true;
        }
function submitcategory(restid)
{
  var  category = $("#category").val(); 
  if(category == '') 
            {
                $("#category").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Select Category.');
                return false;
            }
            
            if(true)
            {
                var formdata = new FormData($('#cat_restcategory')[0]);
                //table = $('#categorylisting');
                //table.html('');
                var i=1;
                $.ajax({
                    method: "get",
                    url : "../api/add_restcategory/" + category + "/" + restid,
                    cache: false,
                    crossDomain: true,
                    async: false,
                    dataType: 'text',
                    success : function(result)
                    {
                        var json_x= JSON.parse(result);

                        if((json_x.msg)=='success')
                        {
                            
                            swal({

                                title: "",
                                text: "Added Successfully",
                                timer: 4000,
                                showConfirmButton: false
                            });
                           // window.location.href = "../cat_restaurant_category/" + $('#restid').val();
window.location.reload();
                        }
                        else if((json_x.msg)=='already exist')
                        {
                            
                            swal({

                                title: "",
                                text: "Already Exist",
                                timer: 4000,
                                showConfirmButton: false
                            });
                            window.location.href = "../cat_restaurant_category/" + $('#restid').val();
                        }


                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });

            }
}
    </script>

@stop

@endsection




