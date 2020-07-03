@extends('layouts.app')
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

</style>

          <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">


                                            <input type='hidden' id='url' value='{{$url}}' />

    <div class="col-sm-12">
        
        <div class="card-box table-responsive" style="padding: 8px 10px;">
              <div class="box master-add-field-box" >
             <div class="col-md-6 no-pad-left">
                <h3>MANAGE Users</h3>
                
            </div>         
            <div class="col-md-1 no-pad-left pull-right">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a href="#"> <button type="submit" style="margin-top: px; border-radius: 4px;margin-left: 0;" class="on-default followups-popup-btn btn btn-primary" >Add New</button></a>

                </div>
                   
                 </div>
            </div>
            </div>

            <table id="example1" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th style="min-width:30px">Slno</th>
                    <th style="min-width:50px">Action</th>
                    <th style="min-width:150px">Userid</th>
                    <th style="min-width:150px">User Name</th>
                    <th style="min-width:120px">Phone</th>
                    <th style="min-width:120px">Designation</th>
                    <th style="min-width:120px">Group Name</th>
                    <th style="min-width:80px">Branch</th>
                    <th style="min-width:80px">Reports</th>
                    <th style="min-width:250px;">Registered Email</th>
                    <th>Password </th>
                    <th>Status</th>
                    <th>CS App</th>
                    <th>Online App</th>
                  
                </tr>
                </thead>


            </table>
        </div>
    </div>

<div class="add-work-done-poppup-cc" style="display: none;" id="update_branch_show_pop">
    <div class="add-work-done-poppup-branch">
        <div class="add-work-done-poppup-head">Branch Permission
            <a href="" onclick="close_update_branch_show_pop();"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
        </div>

        <div class="add-work-list-cc">
            <div class="add-work-list-table-cc" style="height:auto;padding-bottom:0">

                <table id="followuptable"  cellspacing="0" class="trackorder-detail-table staff_master_tbl" width="100%" border="0">
                    <thead>
                    <tr>

                        <th style="min-width:60%;">BRANCH</th>
                        <th style="min-width:10%;">Status</th>
                        <th style="min-width:10%;">ACTION</th>

                    </tr>
                    </thead>
                </table>   
                <div class="staff_master_tbl_tbody">
               
                <table id="followuptable"  cellspacing="0" class="trackorder-detail-table staff_master_tbl" width="100%" border="0">
                    <tbody id="append_data">
                        
                    </tbody>
                    
                </table>
                 </div>

            </div>


        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog" style=" padding-right: 69px;margin-bottom:10px;";
{{-- margin-top: 56px;
 margin-bottom: 20px;
 margin-left: 12px;"--}}>


    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="background-color: #c5bcb1;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Image View</h4>
            </div>
            <img src="">
            <div class="modal-body" style="text-align:center">
                <img src="" id="fav-title" width="300px">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
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

            <div class="add-work-done-poppup-contant" >
              
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">
                       
                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">
                            {!! Form::open(['enctype'=>"multipart/form-data",'id' =>'frm_add','name' => 'frm_add', 'ng-submit'=>'staffmaster()', 'ng-controller'=>'staffmastercontroller']) !!}
                                          
                                            <input type='hidden' id='url' value='{{$url}}' />
                                             <input type='hidden' id='userid' name="userid" />
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>User Name</label>
                                       {!! Form::text('user name',null, ['class'=>'form-control','id'=>'user_name','name'=>'user_name','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                                        
                                    </div>
                                </div>
                            </div>

                             
                            <div class="main_inner_class_track" >
                                    <div class="group">
                                        <div style="position: relative">
                                              <label>Group Name</label>          
                                        {!! Form::select('group_id',[null=>'Select group'],null,['id' => 'group_id','name' => 'group_id','style'=>"background-color:transparent;height: 39px",'class'=>' staff-master-select form-control']) !!}
                                   </div>
                                    </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Registered Email</label>
                                       {!! Form::text('reg_email',null, ['class'=>'form-control','id'=>'reg_email','name'=>'reg_email','required','style'=>"background-color:transparent;",'onchange'=>"ValidateEmail()"]) !!}
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Phone Number</label>
                                       {!! Form::text('phone_number',null, ['class'=>'form-control','id'=>'phone_number','name'=>'phone_number','required','onkeypress' => 'return numonly(event);','style'=>"background-color:transparent;"]) !!}
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Designation</label>
                                {!! Form::select('designation',['select' => 'Select Designation','SuperAdmin' => 'Super Admin','Admin' => 'Admin','SubUser'=>'Sub User'],NULL,['id' => 'designation','name' => 'designation','class'=>'form-control']) !!}
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track " >
                                <div class="group" id="password_user">
                                    <div style="position: relative">
                                        <label>Password</label>
                                       {!! Form::text('password',null, ['class'=>'form-control','id'=>'password','name'=>'password','required','style'=>"background-color:transparent;"]) !!}
                                        
                                    </div>
                                </div>
                            </div>
                           <div class="col-xs-3 main_inner_class_track">
                            <div class="form-group" >
                                <label>CS Status</label>
                                {!! Form::select('cs_status',['Y' => 'Active','N' => 'InActive'],NULL,['id' => 'cs_status','name' => 'cs_status','class'=>'form-control']) !!}
                                
                            </div>
                        </div>
                           <div class="col-xs-3 main_inner_class_track">
                            <div class="form-group" >
                               <label>Online Status</label>
                                {!! Form::select('onl_status',['Y' => 'Active','N' => 'InActive'],NULL,['id' => 'onl_status','name' => 'onl_status','class'=>'form-control']) !!}
                                
                            </div>
                        </div>
                           <div class="col-xs-3 main_inner_class_track">
                            <div class="form-group" id="status_p" style="display: none">
                                <label for="status"><span style="color:black">&nbsp;</span></label>
                                <p style="color: red;margin:0 0 5px"></p>
                                {!! Form::select('status',['Select'=>'Select Status','Active' => 'Active','InActive' => 'InActive'],NULL,['id' => 'status','name' => 'status','class'=>'form-control']) !!}
                                
                            </div>
                        </div>
                             
                                            <div class="main_inner_class_track" style="width:20%">
                                    	 <b><p class="" style="color: #000;float:right;cursor:pointer;display: none;background-color: burlywood;margin-top: 6px;padding: 2px 13px;line-height: 30px;" id="getimage" data-toggle="modal"  data-target="#myModal" data-title=""><a style="color: #000;"">View image</a></p></b>
                                       
                                       
                                    </div>
                        
                             <div class="box-footer">
                                 <a id="inserting" class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_client();">Submit</a>
                               <a id="updating" class="staff-add-pop-btn" onclick="submit_client();" style="height:40px; bottom: 20px;">Update</a>
                              </div>
                        {!! Form::close() !!}   
                        </div>
                         
                            
                        </div>
                    </div>
                </div><!--add-work-done-poppup-textbox-cc-->
            </div>
            <div class="add-work-list-cc">
                <!--<div class="add-work-list-head">LIST</div>-->
                
              
        </div><!--add-work-done-poppup-->
        
    </div>
 
     <div id="reset_pasword" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup"  >
            <div class="add-work-done-poppup-head">Edit
                <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>

                <div style="text-align:center;" id="branchtimezone"></div>

            <div class="add-work-done-poppup-contant" >
              
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">
                       
                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">
                            {!! Form::open(['enctype'=>"multipart/form-data",'id' =>'frm_reset','name' => 'frm_reset', 'ng-submit'=>'staffmaster()', 'ng-controller'=>'staffmastercontroller']) !!}
                                          
                                            <input type='hidden' id='url' value='{{$url}}' />
                                             <input type='hidden' id='userid_reset' name="userid_reset" />
                                             <input type='hidden' id='username_reset' name="username_reset" />
                            <div class="main_inner_class_track " >
                                <div class="group" id="">
                                    <div style="position: relative">
                                        <label>Password</label>
                                       {!! Form::text('new password',null, ['class'=>'form-control','id'=>'new_password','name'=>'new_password','required','style'=>"background-color:transparent;"]) !!}
                                        
                                    </div>
                                </div>
                            </div>
                           <div class="main_inner_class_track " >
                                <div class="group" id="">
                                    <div style="position: relative">
                                        <label>Confirm Password</label>
                                       {!! Form::text('confirm password',null, ['class'=>'form-control','id'=>'confirm_password','name'=>'confirm_password','required','style'=>"background-color:transparent;"]) !!}
                                        
                                    </div>
                                </div>
                            </div>      
                             
                                           
                        
                             <div class="box-footer">
                                 <a id="inserting" class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_new_password();">Submit</a>
                                 <a id="generate" class="staff-add-pop-btn staff-add-pop-btn-new" onclick="generate_password();">GenerateNew</a>
                              </div>
                        {!! Form::close() !!}   
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
        var url = window.location.href;var sp_url = url.split("&pass=");
        if(sp_url[1] == '1'){
            alert("inserted");
             swal({
							
                            title: "",
                            text: "Please Enter Name",
                            timer: 1000,
                            showConfirmButton: false
                          });
                          $("#user_name").focus();
             
        }
        if(sp_url[1] == '2'){
             alert("updated");
             
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
    function ValidateEmail(mail) 
{
 if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(myForm.emailAddr.value))
  {
    return (true)
  }
    alert("You have entered an invalid email address!")
    return (false)
}
} );
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
        
        
        function submit_client(){
            var routeurl     = $("#url").val();
            var group_id     = $("#group_id").val();
            var userid       = $("#userid").val();
            var user_name    = $("#user_name").val();
            var reg_email    = $("#reg_email").val();
            var status       = $("#status").val();
            var cs_status    = $("#cs_status").val();
            var onl_status   = $("#onl_status").val();
            var password     = $("#password").val();
            var phone_number = $("#phone_number").val();
            var designation  = $("#designation").val();
            var token     = $("#csrf_token").val();
            var user_data = {"group_id":group_id,"userid":userid,"user_name":user_name,"reg_email":reg_email,"phone_number":phone_number,"designation":designation,"status":status,'cs_status':cs_status,'onl_status':onl_status,"password":password};
            var url       = routeurl+'add_client';
            if(user_name==''){
                            swal({
							
                            title: "",
                            text: "Please Enter Name",
                            timer: 1000,
                            showConfirmButton: false
                          });
                          $("#user_name").focus();
            }
            else if(reg_email==''){
                            swal({
							
                            title: "",
                            text: "Please Enter email",
                            timer: 1000,
                            showConfirmButton: false
                          });
                          $("#reg_email").focus();
            }
            
            else if(group_id==''){
                $("#group_id").focus();
                            swal({
							
                            title: "",
                            text: "Please Select group",
                            timer: 1000,
                            showConfirmButton: false
                          });
                          
            } 
            else if(designation=='select' || designation ==null){
                designation = '';
                $("#designation").focus();
                            swal({
							
                            title: "",
                            text: "Please Select Designation",
                            timer: 1000,
                            showConfirmButton: false
                          });
                          
            }
            else if(password==''&& userid==''){
                $("#password").focus();
                            swal({
							
                            title: "",
                            text: "Please Enter Password",
                            timer: 1000,
                            showConfirmButton: false
                          });
                          
            }
            if(user_name&&reg_email&&group_id&&password&&designation){
                var email = document.getElementById('reg_email');
                var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                if (!filter.test(email.value)) {
                    swal({

                                        title: "",
                                        text: "Please provide a valid email address",
                                        timer: 1000,
                                        showConfirmButton: false
                                      });

                         $("#reg_email").focus();
                        return false;
                }
                else{
                    $.ajax({
            type: "POST",
            url: "api/insert_new_user",
            data:user_data,
            success: function (data) {
               
            
               if(data=='inserted'){
                    $("#add_user").hide();
                   swal({
							
                            title: "",
                            text: "inserted",
                            timer: 2000,
                            showConfirmButton: false
                          });
                          window.location.reload();
               }
               if(data=='usercountExceed'){
                   
                   swal({
							
                            title: "",
                            text: "Sorry User Limit Exceeded",
                            timer: 3000,
                            showConfirmButton: false
                          });
               }
               if(data=='updated'){
                    $("#add_user").hide();
                   swal({
							
                            title: "",
                            text: "updated",
                            timer: 2000,
                            showConfirmButton: false
                          });
                          window.location.reload();
               }
                  
                         

            },
            error: function (jqXHR, textStatus, errorThrown) {
               alert('error');
                $("#errbox").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
            }
        });
                }
                 
//        
//            var thisform = document.frm_add;
//            thisform.submit();
//            return false;
        }
        }
        function edit_user(id,name,phone,designation,groupid,email,status,cs_status,onl_status,password){
               
         $("#userid").val(id);
         $("#group_id").val(groupid);
         $("#group_id").addClass('not-active');
         $("#user_name").val(name);
         $("#reg_email").val(email);
         $("#status").val(status);
         $("#cs_status").val(cs_status);
         $("#onl_status").val(onl_status);
         $("#password").val(password);
         $("#phone_number").val(phone);
          $("#designation").val(designation);
         $("#add_user").css("display",'block');
         $("#status_p").css("display",'block');
         $("#password_user").css("display",'none');
         $("#inserting").css("display",'none');
         $("#updating").css("display",'block');
        }
                    function reset_password(id,name){
                                $("#reset_pasword").css("display",'block')
                                 var text = "";
                                    var possible = id+name;

                                    for (var i = 0; i < 7; i++)
                                      text += possible.charAt(Math.floor(Math.random() * possible.length));
                                 
                                  $("#new_password").val(text)
                                  $("#confirm_password").val(text)
                                  $("#username_reset").val(name)
                                  $("#userid_reset").val(id)
                     }
                      function generate_password(){
                          var name =  $("#username_reset").val();
                            var id =    $("#userid_reset").val();
                           var text = "";
                                var possible = id+name;

                                for (var i = 0; i < 7; i++)
                                  text += possible.charAt(Math.floor(Math.random() * possible.length));
                              
                              $("#new_password").val(text)
                              $("#confirm_password").val(text)
                            
                      }
      function submit_new_password(){
           var new_password      = $("#new_password").val();
           var conf_pass = $("#confirm_password").val();
           var userid_reset = $("#userid_reset").val();
           var username_reset = $("#username_reset").val();
           var reset_data ={"new_password":new_password,"conf_pass":conf_pass,"userid_reset":userid_reset,"username_reset":username_reset}
           if(new_password==''){
               swal({
							
                            title: "",
                            text: "Please Generate New Password",
                            timer: 1000,
                            showConfirmButton: false
                          });
           }
           else if(conf_pass==''){
                swal({
							
                            title: "",
                            text: "Please Confirm Paswword",
                            timer: 1000,
                            showConfirmButton: false
                          });
           }
           else if(conf_pass!=new_password){
                swal({
							
                            title: "",
                            text: "Password and confirm password must be equal",
                            timer: 1000,
                            showConfirmButton: false
                          });
           }
           else{
//               change_password
        $.ajax({
            type: "POST",
            url: "api/change_password",
            data:reset_data,
            success: function (data) {
                 $("#new_password").html('');
                          $("#confirm_password").html('');
                          $("#reset_pasword").hide('');
                          
                  swal({
							
                            title: "",
                            text: "Updated",
                            timer: 1000,
                            showConfirmButton: false
                          });
                         

            },
            error: function (jqXHR, textStatus, errorThrown) {
               alert('error');
                $("#errbox").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
            }
        });
//              var thisform = document.frm_reset;
//            thisform.submit();
//            return false; 
           }
      }
      function get_branch(id){
          $("#update_branch_show_pop").show();
          var routeurl = $("#url").val();
       
         $.ajax({
            type: "GET",
            url: routeurl+"get_branch_by_user/"+id,

            success: function (data) {
                //$("#bdm").append(data);
                    var user_id = id;$('#append_data').html('');
                $.each(data, function (i, indx)                {
                    if(indx.active=='Y'){
                       
                        $('#append_data').append('<tr>'+
                                             '<td style="width: 60%; text-align: left; color: black; font-size: 16px;">'+ indx.branch_name +'</td>'+
                        
                                              "<td  style='color: black'>Active</td>" + 
                                              "<td><a href='#' style='color: red' onclick=\"reset_branch('"+indx.branchid+"','"+user_id+"','"+indx.active+"')\"><span class='act-btn-add-wrk-pop '>Disable</span></a></td>" + 
                                               '</tr>');
                    }
                    else{
                        
                        $('#append_data').append('<tr>'+
                                             '<td style="width: 60%; text-align: left;  color: black; font-size: 16px;">'+ indx.branch_name +'</td>'+

                                              "<td style='color: black'>In Active</td>" + 
                                              "<td><a href='#'  style='color: green' onclick=\"reset_branch('"+indx.branchid+"','"+user_id+"','"+indx.active+"')\"><span class='act-btn-add-wrk-pop'>Enable</span></a></td>" + 
                                               '</tr>');
                    }
                  
                      
                });

            },
            error: function (jqXHR, textStatus, errorThrown) {
               alert('error');
                $("#errbox").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
            }
        });
      }
      function close_update_branch_show_pop(){
      $("#update_branch_show_pop").hide();
      }
        function reset_branch(branchid,userid,status){
        var routeurl = $("#url").val();
        $.ajax({
            type: "POST",
            url: routeurl+"update_branch/"+userid+"/"+branchid+"/"+status,

            success: function (data) {
                  swal({
							
                            title: "",
                            text: "Updated",
                            timer: 1000,
                            showConfirmButton: false
                          });
                get_branch(userid);
            },
            error: function (jqXHR, textStatus, errorThrown) {
               alert('error');
                $("#errbox").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
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
   

@endsection




