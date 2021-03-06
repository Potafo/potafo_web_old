@extends('layouts.app')
@section('title','Manage Catering City')
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

    </style>

    <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">


    <input type='hidden' id='url' value='{{$url}}' />

    <div class="col-sm-12">

        <div class="card-box table-responsive" style="padding: 8px 10px;">
            <div class="box master-add-field-box" >
                <div class="col-md-6 no-pad-left">
                    <h3>MANAGE CATERING CITY</h3>

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

            <table id="example1" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th style="min-width:30px">Slno</th>
                    <th style="min-width:150px">City</th>
                    <!--<th style="min-width:150px">Status</th>-->
                    <th style="min-width:50px">Action</th>
                </tr>
                </thead>
                <tbody id="arealisting" style="height:390px">
                @if(count($rows)>0)
                    @foreach($rows as $value)
                        <tr>
                            <td style="text-align: left;width:7%">{{ $sl++ }}</td>
                            <td style="text-align: left;width:10%">{{ $value->city}}</td>
                            <!--<td style="text-align: left;width:7%">
                               @if( $value->active == 'Y') Active  @else  Inactive @endif
                            </td>-->
                            <td style="text-align: left;width:10%">
                                <a onclick="return areaedit('{{$value->id}}','{{$value->city}}')" class="btn button_table clear_edit" >
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a class="btn button_table" onclick="deletecity('{{ $value->id }}');" title="Delete"><i class="fa fa-trash-o"></i></a>
                                <span class="add_time_btn" onclick="pincode_details('{{ $value->id }}','{{$value->city}}')" >PINCODE</span>
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

    <div id="add_user" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup">
            <div class="add-work-done-poppup-head">Add/Edit
                <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
            <div style="text-align:center;" id="branchtimezone"></div>
            <div class="add-work-done-poppup-contant">
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">

                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">

                            <input type='hidden' id='url' value='{{$url}}' />
                            <input type='hidden' id='userid' name="userid" />
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>City</label>
                                        {!! Form::text('area',null, ['class'=>'form-control','id'=>'area','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}
                                    </div>
                                </div>
                            </div>

                            <!--<div class="col-xs-3 main_inner_class_track">
                                <div class="form-group" id="status_p" style="display: none">
                                    <label for="status"><span style="color:black">&nbsp;</span></label>
                                    <p style="color: red;margin:0 0 5px"></p>
                                    {{ Form::select('status',['Y' => 'Active','N' => 'Inactive'],null,['id' => 'status', 'class'=>"form-control"])}}
                                </div>
                            </div>-->

                            <div class="main_inner_class_track" style="width:20%">
                                <b><p class="" style="color: #000;float:right;cursor:pointer;display: none;background-color: burlywood;margin-top: 6px;padding: 2px 13px;line-height: 30px;" id="getimage" data-toggle="modal"  data-target="#myModal" data-title=""><a style="color: #000;">View image</a></p></b>

                            </div>

                            <div class="box-footer">
                                <input type="hidden" name="type" id="type" />
                                <a id="inserting" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_area('insert');">Submit</a>
                                <a id="updating" name="updating"  class="staff-add-pop-btn" onclick="submit_area('update');" style="height:40px; bottom: 20px;">Update</a>
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


    <div class="timing_popup_cc" id="pinecode_details_popup">
        <div class="timing_popup" >
            <div class="timing_popup_head">Pincode Details of <span id="pinid"> </span>
                <div onclick='closebutton()' class="timing_popup_cls"><img src="{{asset('public/assets/images/cancel.png') }}"></div>
                <input type="text" id="cityid" name="cityid" hidden="">
            </div>
            <div class="timing_popup_contant">
                <div class="restaurant_more_detail_row">
 {!! Form::open([ 'name'=>'frm_addpin', 'id'=>'frm_addpin','method'=>'get']) !!}
                    <div class="restaurant_more_detail_text" style="width:33%;margin-right:2%">
                        <span class="restaurant_more_detail_text_nm">Pincode</span>
                       <input type="text" id="pincode_code" name="pincode_code" placeholder="Enter pincode">
       
                    </div>
                    <div class="restaurant_more_detail_text" style="width:33%;margin-right:5%">
                        <span class="restaurant_more_detail_text_nm">Place</span>
                        <input type="text" id="pincode_name" name="pincode_name" placeholder="Enter name">
                    </div>
                    
                    <div class="restaurant_more_detail_text" style="width:15%;">
                        <div class="add_time_btn_pop"  onclick="addpincode()" >ADD</div>
                    </div>
                    
                     {{ Form::close() }}
                </div>


                <div class="timing_popup_contant_tabl timing">
                    <table id="listing_pin" class="timing_sel_popop_tbl">
                        <thead>
                        <tr>
                            <th style="width:100px">Slno</th>
                            <th style="width:90px">Pincode</th>
                            <th  style="width:90px">Name</th>
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
        $(".followups-popup-btn").click(function(){

            $("#add_user").show();
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
            $("#status").hide();
        });
        function submit_area(type)
        {
            var table ;
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var  area = $("#area").val();
            var insert = $("#inserting").val();
            var update =$("#updating").val();
            var userid =$("#userid").val();
            var status =$("#status").val();
            if(area == '') {
                $("#area").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter Area.');
                return false;
            }

            if(true)
            {
                table = $('#arealisting');
                table.html('');
                var data= {"area":area,"type":type,"userid":userid};
                $.ajax({
                    method: "post",
                    url : "api/add_city",
                    data : data,
                    cache : false,
                    crossDomain : true,
                    async : false,
                    dataType :'text',
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
                        $.each(json_x.rows,function(i,val)
                        {
                            if( val.active == 'Y')
                            {
                              var status =   'Active';
                            }
                            else
                            {
                                var status =  'Inactive';
                            }
                            table.append('<tr><td style="text-align: left;width:7%">'+i+'</td>'+'<td style="text-align: left;width:10%">'+val.city+'</td>'+
                                         '<td style="text-align: left;width:10%">'+
                                         '<a onclick="return areaedit(\''+val.id+'\',\''+val.city+'\')" class="btn button_table clear_edit" >'+
                                         '<i class="fa fa-pencil"></i></a>'+
                                         '<a class="btn button_table" onclick="deletecity(\''+val.id+'\')" title="Delete"><i class="fa fa-trash-o"></i></a>'+
                                '<span class="add_time_btn" onclick="pincode_details(\''+val.id+'\',\''+val.city+'\')" >PINCODE</span>'+
                             '</td></tr>');
                        });
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
        
         function deletecity(id)
        {
            if(confirm('Are you sure to delete?'))
            {
               // var siteUrl = $("#siteUrl").val();
               // var table = $('#example1').DataTable();
                $.ajax({
                    method: "get",
                    url: "api/cat_city_delete/" + id,
                    cache: false,
                    crossDomain: true,
                    async: false,
                    dataType: 'text',
                    success: function (result) {
                        //var rows = table.rows().remove().draw();
                        table = $('#arealisting');
                table.html('');
                        var json_x = JSON.parse(result);
                        if (json_x.msg == 'deleted') {
                            swal({

                                title: "",
                                text: "Deleted Successfully",
                                timer: 1000,
                                showConfirmButton: false
                            });
                            window.location.reload();
                            /*  $.each(json_x.banners,function(i,index)
                             {
                             var count = parseInt(i) + 1;
                             var appbanner = siteUrl+''+index.app_banners;
                             var webbanner = siteUrl+''+index.web_banners;
                             var newRow = '<tr>'+'<td style="min-width:10px;">'+count+'</td>'+
                                          '<td style="min-width:300px;">'+'<a class="btn" rel="popover" data-img=\"'+appbanner+'\" href="" style="text-decoration: underline;">View</a>'+
                                          '</td>'+'<td style="min-width:300px;">'+'<a class="btn" rel="popover" data-img=\"'+webbanner+'\" href="" style="text-decoration: underline;">View</a>'+
                                          '</td>'+'<td style="min-width:10px;"><a class="btn button_table" onclick="deleteimage(\''+index.id+'\');"><i class="fa fa-trash-o"></i></a></td>'+'</tr>';
                             var rowNode = table.row.add($(newRow)).draw().node();
                             });*/
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
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
    function pincode_details(id,city) 
        {
           $("#pinecode_details_popup").show();
           var cityid = id;
          var cityname =city;
          $("#pinid").html(cityname);
          $("#cityid").html(cityid);
          
          var data = {"cityid": cityid};
           $.ajax({
                method: "get",
                url: "api/view_pincode/" + cityid,
                
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                   $('#listing_pin tbody').html('');
                   $('#listing_pin tbody').html(result);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#errbox").text(jqxhr.responseText);
                }
            });
        }

    function closebutton(){
    $(".timing_popup_cc").hide();
 }
 
 function addpincode()
 {
     
     var pincode= $('#pincode_code').val();
     var pinname= $('#pincode_name').val();
     var cityid=$("#cityid").text();
      if(pincode == '') {
                $("#pincode_code").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter pincode.');
                return false;
            }

            if(true)
            {
                table = $('#listing_pin tbody');
                table.html('');
                var data= {"pincode":pincode,"pinname":pinname,"cityid":cityid};
                $.ajax({
                    method: "post",
                    url : "api/add_pincode",
                    data : data,
                    cache : false,
                    crossDomain : true,
                    async : false,
                    dataType :'text',
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
                        $.each(json_x.rows,function(i,val)
                        {
                            
                            table.append('<tr><td style="text-align: left;width:7%">'+val.sl_no+'</td>'+'<td style="text-align: left;width:10%">'+val.pincode+'</td>'+
                                    '<td style="text-align: left;width:10%">'+val.name+'</td>'+
                                         '<td style="text-align: left;width:10%">'+
                                         '<a onclick="return deletepincode(\''+val.city_id+'\',\''+val.sl_no+'\')" class="btn button_table clear_edit" >'+
                                         '<i class="fa fa-trash-o"></i></a></td></tr>');
                                 
                                 
                        });
                        $("#add_user").hide();
                        $("#area").val('');
                        $("#inserting").css("display",'block');
                        $("#updating").css("display",'none');
                        $('#pincode_code').val("");
                        $('#pincode_name').val("");

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });

            }
     
 }
 
 function deletepincode(cityid,slno)
        {
            if(confirm('Are you sure to delete?'))
            {
               // var siteUrl = $("#siteUrl").val();
               // var table = $('#example1').DataTable();
                $.ajax({
                    method: "get",
                    url: "api/cat_pin_delete/" + cityid+ "/"+slno,
                    cache: false,
                    crossDomain: true,
                    async: false,
                    dataType: 'text',
                    success: function (result) {
                        //var rows = table.rows().remove().draw();
                        table = $('#listing_pin tbody');
                table.html('');
                        var json_x = JSON.parse(result);
                        if (json_x.msg == 'deleted') {
                            swal({

                                title: "",
                                text: "Deleted Successfully",
                                timer: 1000,
                                showConfirmButton: false
                            });
                            
                             $.each(json_x.rows,function(i,val)
                        {
                            
                            table.append('<tr><td style="text-align: left;width:7%">'+val.sl_no+'</td>'+'<td style="text-align: left;width:10%">'+val.pincode+'</td>'+
                                    '<td style="text-align: left;width:10%">'+val.name+'</td>'+
                                         '<td style="text-align: left;width:10%">'+
                                         '<a onclick="return deletepincode(\''+val.city_id+'\',\''+val.sl_no+'\')" class="btn button_table clear_edit" >'+
                                         '<i class="fa fa-trash-o"></i></a></td></tr>');
                                 
                                 
                        });
                            //window.location.reload();
                            /*  $.each(json_x.banners,function(i,index)
                             {
                             var count = parseInt(i) + 1;
                             var appbanner = siteUrl+''+index.app_banners;
                             var webbanner = siteUrl+''+index.web_banners;
                             var newRow = '<tr>'+'<td style="min-width:10px;">'+count+'</td>'+
                                          '<td style="min-width:300px;">'+'<a class="btn" rel="popover" data-img=\"'+appbanner+'\" href="" style="text-decoration: underline;">View</a>'+
                                          '</td>'+'<td style="min-width:300px;">'+'<a class="btn" rel="popover" data-img=\"'+webbanner+'\" href="" style="text-decoration: underline;">View</a>'+
                                          '</td>'+'<td style="min-width:10px;"><a class="btn button_table" onclick="deleteimage(\''+index.id+'\');"><i class="fa fa-trash-o"></i></a></td>'+'</tr>';
                             var rowNode = table.row.add($(newRow)).draw().node();
                             });*/
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
            return true;
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




