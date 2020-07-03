@extends('layouts.app')
@section('title','Manage Category')
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
        
        .tbl_view_sec_btn{width: auto;padding: 5px;border-radius: 5px;border-bottom: solid 3px #ececec;color: #666; text-decoration: none !important; box-shadow: 0px 3px 7px #cac7c7;}
    </style>
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
                    <h3>MANAGE CATEGORY</h3>

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
                    <th style="min-width:150px">Name</th>
                    <th style="min-width:150px">Icon</th>
                    <th style="min-width:150px">Status</th>
                    <th style="min-width:50px">Action</th>
                </tr>
                </thead>
                <tbody id="categorylisting" style="height:390px">
                @if(count($rows)>0)
                    @foreach($rows as $value)
                        <tr>
                            <td style="text-align: left;width:7%">{{ $sl++ }}</td>
                            <td style="text-align: left;width:10%">{{ $value->cc_name}}</td>
                            <td style="text-align: left;width:10%">
                                 <a class="btn tbl_view_sec_btn" rel="popover" data-img="@if(isset($value->cc_icon) && $value->cc_icon != ''){{  $value->cc_icon }}@endif" href="" style="text-decoration: underline;">View</a>
                                </td>
                            <td style="text-align: left;width:7%">
                               @if( $value->cc_status == 'Active') Active  @else  Inactive @endif
                            </td>
                            <td style="text-align: left;width:10%">
                                <a onclick="return cat_edit('{{$value->cc_id}}','{{$value->cc_name}}','{{ $value->cc_icon}}','{{$value->cc_status}}')" class="btn button_table clear_edit" >
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


    <div id="url"></div>

    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    <div id="add_user" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup">
            <div class="add-work-done-poppup-head">Add Category
                <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
            <div style="text-align:center;" id="branchtimezone"></div>
            <div class="add-work-done-poppup-contant">
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">

                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">
                            <form enctype="multipart/form-data" id="cat_category_form" role="form" method="POST" action="" >
                            <input type='hidden' id='url' value='{{$url}}' />
                            <input type='hidden' id='userid' name="userid" />
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                         
                                        <label>Category Name</label>
                                       <!-- {!! Form::text('cat_category',null, ['class'=>'form-control','id'=>'cat_cat','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}-->
                                        <input autocomplete="off"   id="cat_cat" name="cat_cat" type="text" class="form-control" onkeypress="return charonly(event);" style="background-color:transparent;" autofocus="true" required="">
                                        <label>Category Icon</label>
                                        <!--{!! Form::text('cat_category',null, ['class'=>'form-control','id'=>'cat_icon','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}-->
                                        <input autocomplete="off"   id="cat_icon" name="cat_icon" type="file" class="form-control" onclick="return Upload()">
                                        

                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-3 main_inner_class_track">
                                <div class="form-group" id="status_p" style="display: none">
                                    <label for="status"><span style="color:black">&nbsp;</span></label>
                                    <p style="color: red;margin:0 0 5px"></p>
                                    <select name="status" id="status" class="form-control">
                                            <option value="Active" selected='selected'>Active</option>
                                            <option value="Inactive" >Inactive</option>
                                    </select>
                                    <!--{{ Form::select('status',['Active' => 'Active','Inactive' => 'Inactive'],null,['id' => 'status', 'class'=>"form-control"])}}-->
                                </div>
                            </div>

                            <div class="main_inner_class_track" style="width:20%">
                                <b><p class="" style="color: #000;float:right;cursor:pointer;display:none;background-color: burlywood;margin-top: 6px;padding: 2px 13px;line-height: 30px;" id="getimage" data-toggle="modal"  data-target="#myModal" data-title=""><a style="color: #000;">View image</a></p></b>

                            </div>

                            <div class="box-footer">
                                <input type="hidden" name="type" id="type" value="insert" />
                                <a id="inserting" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_category('insert');">Submit</a>
                                <!--<a id="updating" name="updating"  class="staff-add-pop-btn" onclick="submit_category('update');" style="height:40px; bottom: 20px;">Update</a>-->
                            </div>
                             </form>
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
    
    <!--edit category-->
    
    <div id="edit_category" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup">
            <div class="add-work-done-poppup-head">Edit Category
                <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
            <div style="text-align:center;" id="branchtimezone"></div>
            <div class="add-work-done-poppup-contant">
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">

                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">
                            <form enctype="multipart/form-data" id="cat_category_form_edit" role="form" method="POST" action="" >
                            <input type='hidden' id='url_edit' value='{{$url}}' />
                            <input type='hidden' id='userid_edit' name="userid_edit" />
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                         
                                        <label>Category Name</label>
                                       <!-- {!! Form::text('cat_category',null, ['class'=>'form-control','id'=>'cat_cat','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}-->
                                        <input autocomplete="off"   id="cat_cat_edit" name="cat_cat_edit" type="text" class="form-control" onkeypress="return charonly(event);" style="background-color:transparent;" autofocus="true" required="">
                                        <label>Category Icon</label>
                                        <!--{!! Form::text('cat_category',null, ['class'=>'form-control','id'=>'cat_icon','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}-->
                                        <input autocomplete="off"   id="cat_icon_edit" name="cat_icon_edit" type="file" class="form-control" onclick="return Upload()">
                                        <a class="btn tbl_view_sec_btn" id="view_cat_icon" rel="popover"  href="" style="text-decoration: underline;">View</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-3 main_inner_class_track">
                                <div class="form-group" id="status_block" style="display: none">
                                    <label for="status"><span style="color:black">&nbsp;</span></label>
                                    <p style="color: red;margin:0 0 5px"></p>
                                    <select name="status_edit" id="status_edit" class="form-control">
                                            <option value="Active" selected='selected'>Active</option>
                                            <option value="Inactive" >Inactive</option>
                                    </select>
                                    <!--{{ Form::select('status',['Active' => 'Active','Inactive' => 'Inactive'],null,['id' => 'status', 'class'=>"form-control"])}}-->
                                </div>
                            </div>

                            <div class="main_inner_class_track" style="width:20%">
                                <b><p class="" style="color: #000;float:right;cursor:pointer;display:none;background-color: burlywood;margin-top: 6px;padding: 2px 13px;line-height: 30px;" id="getimage" data-toggle="modal"  data-target="#myModal" data-title=""><a style="color: #000;">View image</a></p></b>

                            </div>

                            <div class="box-footer">
                                <input type="hidden" name="type_edit" id="type_edit" value="update" />
                                <!--<a id="inserting" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_category('insert');">Submit</a>-->
                                <a id="updating" name="updating"  class="staff-add-pop-btn" onclick="submit_category_edit('update');" style="height:40px; bottom: 20px;">Update</a>
                            </div>
                             </form>
                        </div>


                    </div>
                </div>
            </div><!--add-work-done-poppup-textbox-cc-->
        </div>
        <div class="add-work-list-cc">
            <!--<div class="add-work-list-head">LIST</div>-->


        </div><!--add-work-done-poppup-->

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
            $("#edit_category").hide();
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
        
        function submit_category_edit(type)
        {
            var table ;
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var  cat_name = $("#cat_cat_edit").val();
            var  cat_icon = $("#cat_icon_edit").val();
            //var insert = $("#inserting").val();
            var update =$("#updating").val();
            var userid =$("#userid_edit").val();
            var status =$("#status_edit").val();
            if(cat_name == '') {
                $("#cat_cat_edit").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter category Name.');
                return false;
            }/*else if(cat_icon == '') {
                $("#cat_icon").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter Category Icon.');
                return false;
            }*/
        if(cat_icon != '')
          {
          if (!hasExtension('cat_icon_edit', ['.jpg','.jpeg', '.gif', '.png','.JPG', '.JPEG', '.GIF', '.PNG'])) {
          $("#cat_icon_edit").focus();
          $("#cat_icon_edit").addClass('input_focus');
          $.Notification.autoHideNotify('error', 'bottom right','Upload Gif or Jpg Images Only.');
          return false;
          }
          }

            if(true)
            {
                var formdata = new FormData($('#cat_category_form_edit')[0]);
                table = $('#categorylisting');
                table.html('');
               // var data= {"cat_cat":cat_name,"cat_icon":cat_icon,"status":status,"type":type,"userid":userid};
                var i=1;
                $.ajax({
                    method: "post",
                    url : "api/edit_category",
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
                            window.location.href = "manage_category";
                            swal({

                                title: "",
                                text: "Added Successfully",
                                timer: 4000,
                                showConfirmButton: false
                            });

                        }
                        else if((json_x.msg)=='already exist')
                        {
                            window.location.href = "manage_category";
                            swal({

                                title: "",
                                text: "Already Exist",
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }


                        else if((json_x.msg)=='done')
                        {window.location.href = "manage_category";
                            swal({

                                title: "",
                                text: "Updated Successfully",
                                timer: 4000,
                                showConfirmButton: false
                            });

                        }
                        else if((json_x.msg)=='exist')
                        {window.location.href = "manage_category";
                            swal({

                                title: "",
                                text: "Already Exist",
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }
                        $.each(json_x.rows,function(i,val)
                        {
                            if( val.cc_status == 'Active')
                            {
                              var status =   'Active';
                            }
                            else
                            {
                                var status =  'Inactive';
                            }
                            table.append('<tr><td style="text-align: left;width:7%">'+i+'</td>'+'<td style="text-align: left;width:10%">'+val.cc_name+'</td>'+
                                         '<td style="text-align: left;width:7%"><a class="btn tbl_view_sec_btn" rel="popover" data-img="'+   val.cc_icon   +'" href="" style="text-decoration: underline;">View</a></td>'+
                                         '<td style="text-align: left;width:7%">'+status+'</td>'+'<td style="text-align: left;width:10%">'+
                                         '<a onclick="return categoryedit(\''+val.cc_id+'\',\''+val.cc_name+'\',\''+val.cc_active+'\')" class="btn button_table clear_edit" >'+
                                         '<i class="fa fa-pencil"></i></a></td></tr>');
                        });
                        $("#edit_category").hide();
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
        function submit_category(type)
        {
            var table ;
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var  cat_name = $("#cat_cat").val();
            var  cat_icon = $("#cat_icon").val();
            var insert = $("#inserting").val();
            var update =$("#updating").val();
            var userid =$("#userid").val();
            var status =$("#status").val();
            if(cat_name == '') {
                $("#cat_cat").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter category Name.');
                return false;
            }/*else if(cat_icon == '') {
                $("#cat_icon").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Enter Category Icon.');
                return false;
            }*/
        if(cat_icon != '')
          {
          if (!hasExtension('cat_icon', ['.jpg','.jpeg', '.gif', '.png','.JPG', '.JPEG', '.GIF', '.PNG'])) {
          $("#cat_icon").focus();
          $("#cat_icon").addClass('input_focus');
          $.Notification.autoHideNotify('error', 'bottom right','Upload Gif or Jpg Images Only.');
          return false;
          }
          }

            if(true)
            {
                var formdata = new FormData($('#cat_category_form')[0]);
                table = $('#categorylisting');
                table.html('');
               // var data= {"cat_cat":cat_name,"cat_icon":cat_icon,"status":status,"type":type,"userid":userid};
                var i=1;
                $.ajax({
                    method: "post",
                    url : "api/add_category",
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
                            window.location.href = "manage_category";
                            swal({

                                title: "",
                                text: "Added Successfully",
                                timer: 4000,
                                showConfirmButton: false
                            });

                        }
                        else if((json_x.msg)=='already exist')
                        {
                            window.location.href = "manage_category";
                            swal({

                                title: "",
                                text: "Already Exist",
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }


                        else if((json_x.msg)=='done')
                        {window.location.href = "manage_category";
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
                            if( val.cc_status == 'Active')
                            {
                              var status =   'Active';
                            }
                            else
                            {
                                var status =  'Inactive';
                            }
                            table.append('<tr><td style="text-align: left;width:7%">'+i+'</td>'+'<td style="text-align: left;width:10%">'+val.cc_name+'</td>'+
                                         '<td style="text-align: left;width:7%"><a class="btn tbl_view_sec_btn" rel="popover" data-img="'+   val.cc_icon   +'" href="" style="text-decoration: underline;">View</a></td>'+
                                         '<td style="text-align: left;width:7%">'+status+'</td>'+'<td style="text-align: left;width:10%">'+
                                         '<a onclick="return categoryedit(\''+val.cc_id+'\',\''+val.cc_name+'\',\''+val.cc_active+'\')" class="btn button_table clear_edit" >'+
                                         '<i class="fa fa-pencil"></i></a></td></tr>');
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

$(document).ready(function()
        {
            $('a[rel=popover]').popover({
                html: true,
                trigger: 'hover',
                placement: 'right',
                content: function(){return '<img src="'+$(this).data('img') + '" width="200" height="100"/>';}
            });
        });
        function cat_edit(id,name,icon,status)
        {
            $("#edit_category").css("display",'block');
            $("#status_edit").css("display",'block');
            $("#status_block").css("display",'block');
            $("#userid_edit").val(id);
            $("#status_edit").val(status);
            $("#cat_cat_edit").val(name);
            document.getElementById("view_cat_icon").setAttribute('data-img', icon);
            //$("#view_cat_icon").data-img(icon);
            //$("#password_user").css("display",'none');
            $("#inserting").css("display",'none');
            $("#updating").css("display",'block');
            
            
            //$("#status").show();

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
</script>
<script>
    $(document).ready(function() {
        $('#example1').DataTable( {
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        } );
    } );

</script>

@endsection




