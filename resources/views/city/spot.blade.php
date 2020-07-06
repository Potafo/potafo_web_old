@extends('layouts.app')
@section('title','Manage Spot')
@section('content')
    <?php
    $pg=app('request')->input('page') ;
    if($pg==''){
        $pg=1;
    }
    $sl=($pg * 25)-24;
    $p=1;
    ?>

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
        .Location_btn{
            width: auto;
    padding:2px 15px;
    background-color: #4CAF50 !important;
    border: 1px solid #4caf50 !important;
    box-shadow: 5px 3px 12px #bdbdbd;
    border-bottom: 3px solid #197b1d !important;
    font-weight: bold;
    float: right;
    color: #fff;
    border-radius: 20px;
    margin: 8px 3px;
    cursor:pointer;
        }
        .Location_btn:hover{    background-color: #10bb17 !important;}
    </style>

    <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">


    <input type='hidden' id='url' value='{{$url}}' />
   
    <div class="col-sm-12">
    <div class="col-sm-12">
                <ol class="breadcrumb">
						<li>
							<a href="{{ url('index') }}">Dashboard</a>
						</li>
						
						<li class="active ms-hover">
                        <a href="{{ url('area') }}">Manage Area</a>
						</li>
                        <li class="active ms-hover">
                        Manage Spot
						</li>
					</ol>
				</div>
        <div class="card-box table-responsive" style="padding: 8px 10px;">
            <div class="box master-add-field-box" >
                <div class="col-md-6 no-pad-left">
                    <h3>MANAGE SPOT</h3>

                </div>
                <div class="col-md-1 no-pad-left pull-right">
                    <div class="table-filter" style="margin-top: 4px;">
                        <div class="table-filter-cc">
                            <a href="#"> <button type="submit" style="margin-top: px; border-radius: 4px;margin-left: 0;" class="on-default followups-popup-btn btn btn-primary ad-work-clear-btn" >Add New</button></a>

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12">

            </div>
            <input type='hidden' id='cityid' value='{{$cityid}}' />
            <table id="example1" class="table table-striped table-bordered">
                <thead>
                <tr>
                   <th style="min-width:30px">Slno</th>
                    <th style="min-width:150px">Name</th>
                     <!--<th style="min-width:150px">Latitude</th>
                    <th style="min-width:50px">Longitude</th>-->
                    <th style="min-width:150px">Address</th>
                    <th style="min-width:50px">Radius (M)</th>
                     <!--<th style="min-width:150px">Map Link</th>-->
                    <th style="min-width:50px">Status</th>
                    <th style="min-width:150px">Actions</th>
                </tr>
                </thead>
                <tbody id="arealisting" style="height:390px">
                @if(count($rows)>0)
                    @foreach($rows as $value)
                        <tr>
                            <td style="text-align: left;width:7%">{{ $sl++ }}</td>
                            <td style="text-align: left;width:10%">{{ $value->name}}</td>
                             <!--<td style="text-align: left;width:10%">{{ $value->latitude}}</td>
                            <td style="text-align: left;width:10%">{{ $value->longitude}}</td>-->
                            <td style="text-align: left;width:10%">{{ $value->address}}</td>
                            <td style="text-align: left;width:10%">{{ $value->radius}}</td>
                            <!-- <td style="text-align: left;width:10%">{{ $value->maplink}}</td>-->

                            <td style="text-align: left;width:7%">

                            <div class="onoffswitch">
                                     <input autocomplete="off"   type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch51{{$value->id}}" @if( $value->status == '1') checked @endif>
                                     <label class="onoffswitch-label" for="myonoffswitch51{{$value->id}}">
                                       <span class="onoffswitch-inner" onclick="return  statuschange('{{$value->id}}','{{$value->city_id}}')"></span>
                                       <span class="onoffswitch-switch" onclick="return  statuschange('{{$value->id}}','{{$value->city_id}}')"></span>
                                      </label>
                                   </div>
                              <!-- @if( $value->status == '1') Active  @else  Inactive @endif -->
                            </td>
                            <td style="text-align: left;width:7%">
                            <a onclick="return edit_spot('{{$value->id}}','{{$value->city_id}}','{{ $value->name}}','{{ $value->latitude}}','{{ $value->longitude}}','{{ $value->address}}','{{ $value->maplink}}','{{$value->status}}','{{$value->radius}}')" class="btn button_table clear_edit" >
                                    <i class="fa fa-pencil"></i>
                                </a> 
                                <a onclick="return delete_spot('{{$value->id}}','{{$value->city_id}}')" class="btn button_table clear_edit" >
                                    <i class="fa fa-trash"></i>
                                </a>
                                <a href="{{$value->maplink}}"  class="Location_btn" target="_blank"  id="locationview" style="display: block"><p style="margin-bottom: 0;">Map</p></a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>

            </table>
        </div>
    </div>


    <div id="url"></div>

    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    
    
<!-- ---------------------------------   Add spot --------------------------->
    
    <div id="add_spot" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup">
            <div class="add-work-done-poppup-head">Add/Edit Spot
                <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn close_addspot"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
            <div style="text-align:center;" id="branchtimezone"></div>
            <div class="add-work-done-poppup-contant">
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">

                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">

                            <input type='hidden' id='url' value='{{$url}}' />
                            <input type='hidden' id='userid' name="userid" />
                            <input type='hidden' id='cityid_edit' name="cityid_edit" />
                            <input type='hidden' id='spotid_edit' name="spotid_edit" />
                            <div class="main_inner_class_track " style="width: 98%">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Spot Name</label>
                                        {!! Form::text('spot_name',null, ['class'=>'form-control','id'=>'spot_name','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Latitude</label>
                                        {!! Form::text('spot_latitude',null, ['class'=>'form-control','id'=>'spot_latitude','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Longitude</label>
                                        {!! Form::text('spot_longitude',null, ['class'=>'form-control','id'=>'spot_longitude','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track " style="width: 98%">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Address</label>
                                        {!! Form::text('spot_address',null, ['class'=>'form-control','id'=>'spot_address','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track "  style="width: 98%">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Map Link</label>
                                        {!! Form::text('spot_map_link',null, ['class'=>'form-control','id'=>'spot_map_link','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}
                                    </div>
                                </div>
                            </div>
                           
                            <div class="col-xs-3 main_inner_class_track status_set" style="display:none">
                                <div class="group" >
                                     <label>Spot Status</label>
                                    
                                    {{ Form::select('spot_status',['1' => 'Active','0' => 'Inactive'],null,['id' => 'spot_status', 'class'=>"form-control"])}}
                                </div>
                            </div>
                            <div class="col-xs-3 main_inner_class_track radius_cov" style="display:none">
                                <div class="group" >
                                     <label>Radius</label>
                                    
                                     {!! Form::text('spot_radius',null, ['class'=>'form-control','id'=>'spot_radius','onkeypress' => 'return numonly(event);','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}
                                </div>
                            </div>

                            <div class="main_inner_class_track" style="width:20%">
                                <b><p class="" style="color: #000;float:right;cursor:pointer;display: none;background-color: burlywood;margin-top: 6px;padding: 2px 13px;line-height: 30px;" id="getimage" data-toggle="modal"  data-target="#myModal" data-title=""><a style="color: #000;">View image</a></p></b>

                            </div>

                            <div class="box-footer">
                                <input type="hidden" name="type" id="type" />
                                <a id="inserting_spot" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_spot('insert');">Submit</a>
                                <a id="updating_spot" name="updating"  class="staff-add-pop-btn" onclick="submit_spot('update');" style="height:40px; bottom: 20px;">Update</a>
                            </div>
                        </div>


                    </div>
                </div>
            </div><!--add-work-done-poppup-textbox-cc-->
        </div>
        <div class="add-work-list-cc">
            <!--<div class="add-work-list-head">LIST</div>-->


        </div><!--add-work-done-poppup-->

    </div>
    <div id="edit_load">
    </div>



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
    <script>
function edit_spot(id,cityid,name,lat,long,address, link,status,radius)
{
    $("#add_spot").show();
            $("#inserting_spot").css("display",'none');
            $("#updating_spot").css("display",'block');
            
            $(".radius_cov").css("display",'block');
            $(".status_set").css("display",'block');
            $("#spot_radius").val(radius);
            $("#spotid_edit").val(id);
            $("#cityid_edit").val(cityid);
            $("#spot_map_link").val(link);
            $("#spot_address").val(address);
            $("#spot_longitude").val(long);
            $("#spot_latitude").val(lat);
            $("#spot_name").val(name);
            $("#spot_status").val(status);

}
        $(document).ready(function() {
            var t = $('#example1').DataTable( {
                scrollY: "380px",
                scrollX: true,
                scrollCollapse: true,
                "columnDefs": [ {

                    paging: false


                } ],
                "searching": true,
                "ordering": false,
                "iDisplayLength": 5
            } );
        } );
        $(document).ready(function(){
            $(".alert").delay(600).slideUp(300);
        });
    </script>
    <script>
        $(document).ready(function()
        {
            $(".input-sm").focus();
        });
    </script>
    <script>
        $(document).ready(function() {

            $('#myModal').on("show.bs.modal", function (e) {
                $("#fav-title").attr("src",$(e.relatedTarget).data('title'));
            });
        });
        function hasExtension(inputID, exts) {
            var fileName = document.getElementById(inputID).value;
            return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
        }
        $(".addspot").click(function(){

            $("#add_spot").show();
            $("#inserting_spot").css("display",'block');
            $("#updating_spot").css("display",'none');
        });
        $(".close_addspot").click(function(){

            $("#add_spot").hide();
        });
        $(".followups-popup-btn").click(function(){
 //spot_map_link spot_address spot_longitude spot_latitude spot_name spot_status
            $("#add_spot").show();
            $("#inserting_spot").css("display",'block');
            $("#updating_spot").css("display",'none');

            $("#cl_id").val();
           // $("#group_id").val('');
           // $("#group_id").removeClass('not-active');
            $("#spot_map_link").val('');
            $("#spot_address").val('');
            $("#spot_longitude").val('');
            $("#spot_latitude").val('');
            $("#spot_name").val('');
            $("#spot_radius").val('');
           

        });
        $(".ad-work-close-btn").click(function(){
            $("#reset_pasword").css("display",'none');
            $("#add_user").hide();
            $("#new_password").html('');
            $("#confirm_password").html('');
            $("#userid_reset").html('');
            $("#username_reset").html('');
        });
        $(".clear_edit").click(function(){
            $("#area").val();
            $("#status").show();
        });
        $(".ad-work-clear-btn").click(function(){
            $("#area").val('');
            $("#area").focus();
            //$("#status").hide();
        });
        function submit_spot(type)
        {
            //spot_map_link spot_address spot_longitude spot_latitude spot_name spot_status
            var table ;
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var  spot_name = $("#spot_name").val();
            var  spot_latitude = $("#spot_latitude").val();
            var  spot_longitude = $("#spot_longitude").val();
            var  spot_address = $("#spot_address").val();
            var  spot_map_link = $("#spot_map_link").val();
            
            var  spot_id=0;
            var  city_id=0;
            var spot_radius="";
            var  spot_status='';
            if(type=="insert")
            {
                //var  spot_id = $("#cityid").val();
              city_id = $("#cityid").val();
            }else{
                  city_id = $("#cityid_edit").val();
                  spot_id = $("#spotid_edit").val();
                  spot_radius = $("#spot_radius").val();
                  spot_status = $("#spot_status").val();
            }
           
            
            var insert = $("#inserting").val();
            var update =$("#updating").val();
            var userid =$("#userid").val();
            //var status =$("#status").val();
            //alert(userid)
            if(spot_name == '') {
                $("#spot_name").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter Spot name.');
                return false;
            }
             if(spot_longitude == '') {
                $("#spot_longitude").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter Spot Longitude.');
                return false;
            }
             if(spot_latitude == '') {
                $("#spot_latitude").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter Spot Latitude.');
                return false;
            }
            if(spot_address == '') {
                $("#spot_address").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter Spot Address.');
                return false;
            }
            if(spot_map_link == '') {
                $("#spot_map_link").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter Spot Map Link.');
                return false;
            }
            if(true)
            {
                //table = $('#arealisting');
                //table.html('');
                
                var data= {"type":type,"spot_id":spot_id,"city_id":city_id,"spot_radius":spot_radius,"spot_map_link":spot_map_link,"spot_address":spot_address,"spot_longitude":spot_longitude,"spot_latitude":spot_latitude,"spot_name":spot_name,"spot_status":spot_status};
                $.ajax({
                    method: "post",
                    url : "../api/add_spot",
                    data : data,
                    cache : false,
                    crossDomain : true,
                    async : false,
                    dataType :'text',
                    success : function(result)
                    {//alert(result);
                        var json_x= JSON.parse(result);

                        if((json_x.msg)=='success')
                        {
                            swal({

                                title: "",
                                text: "Added Successfully",
                                timer: 4000,
                                showConfirmButton: false
                            });
                            location.reload();

                        }
                        else if((json_x.msg)=='already exist')
                        {
                            swal({

                                title: "",
                                text: "Already Exist",
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }


                        else if((json_x.msg)=='done')
                        {
                            swal({

                                title: "",
                                text: "Updated Successfully",
                                timer: 4000,
                                showConfirmButton: false
                            });
                            location.reload();
                        }
                        else if((json_x.msg)=='exist')
                        {
                            swal({

                                title: "",
                                text: "Already Exist",
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }
                       
                        $("#add_user").hide();
                        $("#area").val('');
                        $("#inserting").css("display",'block');
                        $("#updating").css("display",'none');

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });

            }
        }
        

       
        function delete_spot(spot_id,city_id)
        {
            if(confirm('Are you sure to delete?'))
            {
                //var siteUrl = $("#spot").val();
                var table = $('#example1').DataTable();
                $.ajax({
                    method: "get",
                    url: "../api/spot_delete/" + spot_id + "/" + city_id,
                    cache: false,
                    crossDomain: true,
                    async: false,
                    dataType: 'text',
                    success: function (result) {
                        //var rows = table.rows().remove().draw();
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
        function areaedit(id,area,status)
        {
            $("#userid").val(id);
            $("#status").val(status);
            $("#area").val(area);
            $("#password_user").css("display",'none');
            $("#inserting").css("display",'none');
            $("#updating").css("display",'block');
            $("#add_user").css("display",'block');
            $("#status_p").css("display",'block');
            $("#status").show();

        }
          </script>
    <script>
        const openMap = (lat, long) => {
            const base_url = "https://www.google.com/maps/@";
            var map_link = base_url + lat + ',' + long + ',15z';

            //location.href = map_link;
            window.open(map_link, '_blank');

        }
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
        if (!(charCode >= 65 && charCode <= 120) && (charCode != 32 && charCode != 0))
            return false;
        return true;
    }
    function statuschange(spotid,cityid) {
           // var ids = id;

          
           
            var data = {"spot": spotid,"city": cityid};
            $.ajax({
                method: "get",
                url: "{{ route('spot.change_status') }}",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {//alert(result)
                    //var json_x = JSON.parse(result);
                    //    alert(json_x.msg);

                    // location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
//                    alert(errorThrown);
                    $("#errbox").text(jqxhr.responseText);
                }
            });
        }
</script>
<script>
    $(document).ready(function() {
        $('#example1').DataTable( {
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        } );
    } );

</script>

@endsection




