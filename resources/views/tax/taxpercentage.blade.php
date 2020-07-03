@extends('layouts.app')
@section('title','Manage Tax')
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
.add-work-done-poppup-textbox-box .main_inner_class_track	{
    width: 30%;
    margin-right: 3%;
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
         <div class="col-sm-12">
                <ol class="breadcrumb">
						<li>
							<a href="{{  url('index') }}">Dashboard</a>
						</li>
						
						<li class="active ms-hover">
							<a href="{{  url('manage_restaurant') }}">{{$restaurant_name[0]->name}}</a>
						</li>
                    <li class="active ms-hover">
							Manage Tax
						</li>
					</ol>
				</div>
        
        <div class="col-sm-12">
            <a href="{{ url('restaurant_edit/'.$resid) }}"><div class="potafo_top_menu_sec">About</div></a>
            <a href="{{ url('menu/list/'.$resid) }}"><div class="potafo_top_menu_sec">Menu</div></a>
            <a href="{{ url('category/list/'.$resid) }}"><div class="potafo_top_menu_sec">Category</div></a>
            <a href="{{ url('menu/review/'.$resid) }}"><div class="potafo_top_menu_sec">Review</div></a>
            <a ><div class="potafo_top_menu_sec potafo_top_menu_act">Tax %</div></a>
          </div>  
        
        <div class="card-box table-responsive" style="padding: 8px 10px;">
              <div class="box master-add-field-box" >
             <div class="col-md-6 no-pad-left">
                <h3>MANAGE TAX</h3>
                
            </div>         
            <div class="col-md-1 no-pad-left pull-right">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a style="cursor:pointer;" onclick="cleardisabled()"> <button type="submit" style="margin-top: px; border-radius: 4px;margin-left: 0;" class="on-default followups-popup-btn btn btn-primary ad-work-clear-btn" >Add New</button></a>

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
                    <th style="min-width:150px">Tax</th>
                    <th style="min-width:150px">Value</th>
                    <th style="min-width:150px">Status</th>
                    <th style="min-width:50px">Action</th>
                </tr>
                </thead>
                <tbody  style="height:390px">
                            @if(count($rows)>0)
                            @foreach($rows as $value)
                            <tr>
                            <td style="text-align: left;width:7%">{{ $value->t_slno }}</td>
                            <td style="text-align: left;width:10%">{{ $value->t_name}}</td>
                            <td style="text-align: left;width:10%">{{ $value->t_value}}</td>
                            <!--<td style="text-align: left;width:10%">@if( $value->t_status == 'Y') Active  @else  Inactive @endif</td>-->
                            <td style="text-align: left;width:7%">
                                        <div class="status_chck{{ $value->id}}">
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch{{$value->restaurant_id}},{{$value->t_slno}}" @if( $value->t_status == 'Y') checked @endif>
                                                <label class="onoffswitch-label" for="myonoffswitch{{$value->restaurant_id}},{{$value->t_slno}}">
                                                    <span class="onoffswitch-inner" onclick="return  statuschange('{{$value->restaurant_id}}','{{$value->t_slno}}')"></span>
                                                    <span class="onoffswitch-switch" onclick="return  statuschange('{{$value->restaurant_id}}','{{$value->t_slno}}')"></span>
                                                </label>
                                            </div>
                                        </div>
                                    
                                    </td>
                            
                            <td style="text-align: left;width:13%">
                                <a onclick="return taxedit('{{$value->restaurant_id}}','{{$value->t_slno}}','{{$value->t_name}}','{{ $value->t_value }}','{{ $value->t_status }}')" class="btn button_table clear_edit" >               
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a style="cursor:pointer;" onclick="return view_all_category('{{$value->restaurant_id}}','{{$value->t_name}}','{{$value->t_slno}}')" style="margin-right: 7px; float: right;">
                                    <button style="margin-top: 0px; border-radius: 4px;margin-left: 0;    background-color: #dc5718 !important;border: 1px solid #dc5718 !important;" class="on-default followups-popup-btn btn btn-primary ad-work-clear-btn">Apply Tax</button>
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
            <div class="add-work-done-poppup-head">Add/Edit
                <a style="cursor:pointer;"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>

                <div style="text-align:center;" id="branchtimezone"></div>

            <div class="add-work-done-poppup-contant" >
              
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">
                       
                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">
                                          
                                            <input type='hidden' id='url' value='{{$url}}' />
                                             <input type="hidden" value="{{ $resid }}" id="res_id" name="res_id">
                                             <input type='hidden' id='slno' name="slno" />
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Tax</label>
                                       {!! Form::text('tax',null, ['class'=>'form-control','id'=>'tax','name'=>'tax','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Value</label>
                                       {!! Form::text('value',null, ['class'=>'form-control','id'=>'value','name'=>'value','onkeypress' => 'return numonly(event);','required','style'=>"background-color:transparent;"]) !!}
                                        
                                    </div>
                                </div>
                            </div>

                           <div class="col-xs-3 main_inner_class_track">
                            <div class="form-group" id="status_p" style="display: none">
                                <label for="status"><span style="color:black">&nbsp;</span></label>
                                <p style="color: red;margin:0 0 5px"></p>
                                {{ Form::select('status',['Y' => 'Active','N' => 'Inactive'],null,['id' => 'status', 'class'=>"form-control"])}}
                            </div>
                           </div>
                             
                                    <div class="main_inner_class_track" style="width:20%">
                                    	 <b><p class="" style="color: #000;float:right;cursor:pointer;display: none;background-color: burlywood;margin-top: 6px;padding: 2px 13px;line-height: 30px;" id="getimage" data-toggle="modal"  data-target="#myModal" data-title=""><a style="color: #000;">View image</a></p></b>
                                       
                                    </div>
                        
                             <div class="box-footer">
                                 <input type="hidden" name="type" id="type" />
                                 <a id="inserting" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_tax('insert');">Submit</a>
                               <a id="updating" name="updating"  class="staff-add-pop-btn" onclick="submit_tax('update');" style="height:40px; bottom: 20px;">Update</a>
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
    
    <div id="view_all_categ" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup" style="width: 500px">
            <div class="add-work-done-poppup-head">Apply Tax
                <a style="cursor:pointer;"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>

                <div style="text-align:center;" id="branchtimezone"></div>

            <div class="add-work-done-poppup-contant" >
              
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">
                       
                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">
                                          
                                            <input type='hidden' id='url' value='{{$url}}' />
                                             <input type="hidden" value="{{ $resid }}" id="res_id" name="res_id">
                                             <input type='hidden' id='slno' name="slno" />
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <input type="hidden" id="tax_name_under">
                                    <input type="hidden" id="restname_under">
                                    <div style="position: relative ; width: 211px;">
                                        <label>Category</label>
                                        <select id="res_categories" class="form-control" >
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                           
                        
                             <div class="box-footer">
                                 <input type="hidden" name="type" id="type" />
                                 <a style="margin-top: 20px; float: left;margin-left: 15%;height: 35px;" id="insert_value" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="return submit_category();">Submit</a>
                              </div>
                        </div>
                         
                            
                        </div>
                    </div>
                </div><!--add-work-done-poppup-textbox-cc-->
            </div>
          
        
    </div>
    
 
     <div id="reset_pasword" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup"  >
            <div class="add-work-done-poppup-head">Edit
                <a style="cursor:pointer;"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>

                <div style="text-align:center;" id="branchtimezone"></div>

           
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
        function view_all_category(restaurant_id,t_name,t_slno)
        {
            $("#add_user").hide();
            $('#view_all_categ').show();
            $('#slno').val(t_slno);
            $('#tax_name_under').val(t_name);
            $('#restname_under').val(restaurant_id);
             $('#res_categories').html('');
             $.ajax({
                method: "get",
                url : "../../api/get_restraurent_category",
                cache : false,
                data : {'id':restaurant_id},
                crossDomain : true,
                async : false,
                success : function(result)
                {
                    $('#res_categories').append('<option value="">Select</option>');
                    $('#res_categories').append( '<option value="all">All</option>');
                    for(var i=0; i<result.length;i++){
                        
                        $('#res_categories').append('<option value="'+result[i].name+'">'+result[i].name+'</option>');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }
        
        function submit_category()
        {
            var category = $('#res_categories').val();
            var t_slno   = $("#slno").val();
            var t_name   = $('#tax_name_under').val();
            var rest_id  = $('#restname_under').val();
            
            if(category == '')
            {
                swal({
                    title: "",
                    text: "Select Category !",
                    timer: 2000,
                    showConfirmButton: false
                });
                return false;
            }
            var data = {'category':category,'t_name':t_name,'t_slno':t_slno,'rest_id':rest_id};
                $.ajax({
                    method: "post",
                    url : "../../api/insert_restraurent_category",
                    cache : false,
                    data : data,
                    crossDomain : true,
                    async : false,
                    success : function(result)
                    {
                       if(result == 'exist'){
                            swal({
                                title: "",
                                text: "Already Exist",
                                timer: 2000,
                                showConfirmButton: false
                            });
                       }else{
                           swal({
                                title: "",
                                text: "Added Successfully",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#view_all_categ').hide();
                       }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
        }
    
    
         $(document).ready(function()
         {
         var t = $('#example1').DataTable({
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
        });

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
               $('#view_all_categ').hide();
               $("#new_password").html('');
               $("#confirm_password").html('');
               $("#userid_reset").html('');
               $("#username_reset").html('');
        });
        $(".clear_edit").click(function(){
              $("#tax").val(); 
              $("#value").val(); 
              $("#status").show();
        });
        $(".ad-work-clear-btn").click(function(){
              $("#tax").val(''); 
              $("#value").val(''); 
              $("#tax").focus(); 
               
              $("#status").hide();
        });
        function submit_tax(type)
        {
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var  tax = $("#tax").val();
            var  value = $("#value").val();
            var insert = $("#inserting").val();
            var update =$("#updating").val();
            var res_id =$("#res_id").val();
            var status =$("#status").val();
            var slno =$("#slno").val();
            if(tax == '') 
            {
              $("#tax").focus();
              $.Notification.autoHideNotify('error', 'bottom right','Enter Tax.');
              return false;
            }
            if(value == '')
            {
              $("#value").focus();
              $.Notification.autoHideNotify('error', 'bottom right','Enter Tax Value.');
              return false;
            }

        if(true)
        {
            var data= {"tax":tax,"status":status,"type":type,"res_id":res_id,"value":value,"slno":slno};
            $.ajax({
                method: "post",
                url : "../../api/add_tax",
                cache : false,
                data : data,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(result)
                {
                    var json_x= JSON.parse(result);
                    
                    if((json_x.msg)=='success')
                    {
                        location.reload();
                         swal({
							
                            title: "",
                            text: "Added Successfully",
                            timer: 4000,
                            showConfirmButton: false
                        });

                    }
                    else if((json_x.msg)=='already exist')
                    {
                       location.reload();
                        swal({
							
                            title: "",
                            text: "Already Exist",
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
               
                 
                   else if((json_x.msg)=='done')
                    {
                        location.reload();
                         swal({
							
                            title: "",
                            text: "Updated Successfully",
                            timer: 4000,
                            showConfirmButton: false
                        });

                    }
                    else if((json_x.msg)=='exist')
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
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }
        }
        
        function taxedit(rid,slno,tax,value,status)
        {
         $("#userid").val(rid);
         $("#slno").val(slno);
         $("#status").val(status);
         $("#tax").val(tax);
         $("#value").val(value);
         $("#add_user").css("display",'block');
         $("#status_p").css("display",'block');
         $("#password_user").css("display",'none');
         $("#inserting").css("display",'none');
         $("#updating").css("display",'block');
         document.getElementById('tax').disabled = true;
        }
        function cleardisabled()
        {
            document.getElementById('tax').disabled = false;
        }
        
         function statuschange(rid,slno) {
            var rid = rid;
            var slno = slno;
            var data = {"rid": rid,"slno":slno};

            $.ajax({
                method: "get",
                url: "../../tax_status",
                cache : false,
                data : data,
                crossDomain : true,
                async : false,
                dataType :'text',
                success: function (result)
                {
//                    alert (result);
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
//                    alert(errorThrown);
                    $("#errbox").text(jqxhr.responseText);
                }
            });
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
        $(document).ready(function()
        {
        $('#example1').DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        } );
         } );

    </script>

@endsection




