@extends('layouts.app')
@section('title','Manage Staff')
@section('content')

<style>
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
                <h3>MANAGE STAFF </h3>
                
            </div>         
            <div class="col-md-1 no-pad-left pull-right">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a href="#"> <button type="submit" style="margin-top: px; border-radius: 4px;margin-left: 0;" class="on-default followups-popup-btn btn btn-primary ad-work-clear-btn add_new_btn" >Add New</button></a>

                </div>
                   
                 </div>
            </div>
                  <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>
                  <div class=" pull-right" style="display:none">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a title="Filter" href="#"> <button type="submit"  style="margin-right: 10px;" class="on-default followups-popup-btn btn btn-primary filter_sec_btn" ><i class="fa fa-filter" aria-hidden="true"></i></button></a>
                </div>
                   
                 </div>
            </div>
            </div>
            <div class="filter_box_section_cc diply_tgl" style="display:block">
<!--                <div class="filter_box_section">FILTER</div>-->
                   <div class="filter_text_box_row">
                       {!! Form::open(['url'=>'manage_staff', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'get','onkeypress'=>"return event.keyCode != 13;"]) !!}
            
                       <div class="main_inner_class_track" style="width: 20%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Name</label>
                                  <input id="flt_name" name="flt_name" class="form-control" type="text">
                              </div>
                           </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 20%;">
                            <div class="group">
                                <label for="status">Status<span style="color:black">&nbsp;</span></label>
                                <p style="color: red;margin:1px"></p>
                                {{ Form::select('flt_status',['Y' => 'Active','N' => 'Inactive'],null,['id' => 'flt_status', 'class'=>"form-control"])}}
                            </div>
                           </div>
                      <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Designation </label>
                                {{ Form::select('designation',['Select' => 'Select Designation']+$designationlist,null,['id' => 'flt_designation','name' => 'flt_designation', 'class'=>"form-control",'onchange' => 'cat_listsel(this.value)'])}}        
                                    </div>
                                </div>
                            </div>
                       <div id="search_button" class="main_inner_class_track" style="width: 12% !important;float: left">
                           <div class="table-filter-cc" style="margin-top: 22px;">
                               <a href="#" onclick="refresh_filter()" style="margin-left:0;width: 80px " class="on-default btn btn-primary">Search</a>
                                <span id="searchcount"></span>
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
  
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    <div id="add_user" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup" style="height: auto;pading:-bottom:20px">
            <div class="add-work-done-poppup-head">Add/Edit
                <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>

                <div style="text-align:center;" id="branchtimezone"></div>

            <div class="add-work-done-poppup-contant" >
              
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">
                       
                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">
                                          
                                            <input type='hidden' id='url' value='{{$url}}' />
                                             <input type='hidden' id='userid' name="userid" />
                            
                           <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Name *</label>
                                       {!! Form::text('fname',null, ['class'=>'form-control','id'=>'fname','name'=>'fname','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                                    </div>
                                </div>
                            </div>
                             
                            <div class="main_inner_class_track" >
                                <div class="group">
                                    <div style="position: relative">
                                       <label>Last Name</label>          
                                       {!! Form::text('lastname',null, ['class'=>'form-control','id'=>'lastname','name'=>'lastname','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                                   </div>
                                </div>
                            </div>
                            
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Mobile Number</label>
                                       {!! Form::text('mobile_number',null, ['class'=>'form-control','id'=>'mobile_number','name'=>'mobile_number','required','onkeypress' => 'return numonly(event);','style'=>"background-color:transparent;"]) !!}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Alternate Number</label>
                                       {!! Form::text('alternate_number',null, ['class'=>'form-control','id'=>'alternate_number','name'=>'alternate_number','required','onkeypress' => 'return numonly(event);','style'=>"background-color:transparent;"]) !!}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Designation *</label>
                                {{ Form::select('designation',['Select' => 'Select Designation']+$designationlist,null,['id' => 'designation','name' => 'designation', 'class'=>"form-control",'onchange' => 'cat_listsel(this.value)'])}}        
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Category *</label>
                                        <?php
                                            $catlist['restaurant']="Restaurant";
                                            $catlist['potafo_mart']="Potafo Mart";
                                             
                                            ?>
                                        
                                        
                                {{ Form::select('category',['all' => 'All']+$catlist,null,['id' => 'staff_cat','name' => 'staff_cat', 'class'=>"form-control"])}}        
                                    </div>
                                </div>
                            </div>

                            <div class="main_inner_class_track  disable_field" id="staffcreditlimit_field">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Staff Credit Limit *</label>
                                        {!! Form::text('staff_credit_limit',null, ['class'=>'form-control','id'=>'staff_credit_limit','name'=>'staff_credit_limit','onkeypress' => 'return numonly(event);','style'=>"background-color:transparent;"]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group" id="changefs_p" >
                                    <div style="position: relative">
                                        <label>Complaint Followup's *</label>
                                        <?php
                                            
                                            $stalist['N']="No";
                                             $stalist['Y']="Yes";
                                            ?>
                                        
                                        
                                {{ Form::select('changefs',$stalist,null,['id' => 'changefs','name' => 'changefs', 'class'=>"form-control"])}}        
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group" id="auth_p" style="display: none">
                                    <div style="position: relative">
                                        <label>Auth Code *</label>
                                       {!! Form::text('auth_code',null, ['class'=>'form-control','id'=>'auth_code','maxlength' => '4','minlength' => '4','name'=>'auth_code','required','onkeypress' => 'return numonly(event);','style'=>"background-color:transparent;"]) !!}
                                    </div>
                                </div>
                            </div>

                           <div class="col-xs-3 main_inner_class_track">
                            <div class="form-group" id="status_p" style="display: none">
                                <label for="status">Status<span style="color:black">&nbsp;</span></label>
                                <p style="color: red;margin:0 0 5px"></p>
                                {{ Form::select('status',['Y' => 'Active','N' => 'Inactive'],null,['id' => 'status', 'class'=>"form-control"])}}
                            </div>
                           </div>
                                             
                            <div class="col-xs-3 main_inner_class_track">
                            <div class="form-group" id="permission_p" style="display: none">
                                <label for="permission">Confirm Authorize<span style="color:black">&nbsp;</span></label>
                                <p style="color: red;margin:0 0 5px"></p>
                                {{ Form::select('permission',['Y' => 'Active','N' => 'Inactive'],null,['id' => 'permission', 'class'=>"form-control"])}}
                            </div>
                           </div>                  
                            <div class="col-xs-3 main_inner_class_track">
                            <div class="form-group" id="canc_permission" style="display: none">
                                <label for="permission">Cancel Authorize<span style="color:black">&nbsp;</span></label>
                                <p style="color: red;margin:0 0 5px"></p>
                                {{ Form::select('can_permission',['Y' => 'Active','N' => 'Inactive'],null,['id' => 'can_permission', 'class'=>"form-control"])}}
                            </div>
                           </div>
                            
                            <div class="main_inner_class_track" style="width:20%">
                            <b><p class="" style="color: #000;float:right;cursor:pointer;display: none;background-color: burlywood;margin-top: 6px;padding: 2px 13px;line-height: 30px;" id="getimage" data-toggle="modal"  data-target="#myModal" data-title=""><a style="color: #000;">View image</a></p></b>
                            </div>
                        
                             <div class="box-footer">
                                 <input type="hidden" name="type" id="type" />
                                 <a id="inserting" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_staff('insert');">Submit</a>
                               <a id="updating" name="updating"  class="staff-add-pop-btn" onclick="submit_staff('update');" style="height:40px; bottom: 20px;">Update</a>
                              </div>
                        </div>
                         
                            
                        </div>
                    </div>
                </div><!--add-work-done-poppup-textbox-cc-->
            </div>
            
        
    </div>

<div class="timing_popup_cc">
    <div class="timing_popup">
        <div class="timing_popup_head">Manage Area
            <div class="timing_popup_cls"><img src="public/assets/images/cancel.png"></div>
        </div>
        <div class="timing_popup_contant">
            <div class="restaurant_more_detail_row">
                <div class="restaurant_more_detail_text" style="width:40%;margin-right:2%">
                    <input type="hidden" id="staffid" name="">
                    <span class="restaurant_more_detail_text_nm">Area Select</span>
                    <div class="restaurant_more_detail_box" style="border:0">
                        {{ Form::select('area',['' => 'Select Area']+$citylist,null,['id' => 'area', 'class'=>"form-control"])}}
                    </div>
                </div>
                <div class="restaurant_more_detail_text" onclick="areaadd()" style="width:15%;">
                    <span id="select_all" class="add_time_btn_pop">ADD</span>
                </div>
            </div>
            <div class="timing_popup_contant_tabl">
                <table id="tableid" class="timing_sel_popop_tbl">
                    <thead>
                    <tr>
                        <th style="width:100px">Area</th>
                        <th  style="width:40px">Action</th>
                    </tr>
                    </thead>
                    <tbody id="tbodyhtml">
                    <tr>
                        <td  style="width:100px">Kozhikode</td>
                        <td  style="width:40px"><a class="btn button_table"><i class="fa fa-trash"></i></a></td>
                    </tr>
                    </tbody>
                </table>
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
        $.fn.dataTable.ext.errMode = 'none';
    </script>
<script>
    
//         $(document).ready(function() {
//    var t = $('#example1').DataTable( {
//         scrollY: "380px",
//            scrollX: true,
//            scrollCollapse: true,
//        "columnDefs": [ {
//            paging: false
//        } ],
//        "searching": true,
//        "ordering": false,
//        "iDisplayLength": 5
//    } );
//
//} );

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
              var start_cnt = $("#start_count").val();
              var staff_id = $("#staff_id").val();
              var flt_designation = $("#flt_designation").val();
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
          url   : "api/filter/staff_list",
          data  : {"flt_name":flt_name,"flt_status":flt_status,"current_count":current_cnt,"staff_id":staff_id,"flt_designation":flt_designation},
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
              $("#staff_list").html(filter_result.filter_data);
                  if(filter_result.data_count==0)
                  {
                      end_cnt=1;
                  }
                  else
                  {
                     end_cnt =  filter_result.data_count;
                  }
                 $("#end_count").val(end_cnt);
                  if(current_cnt=='')
                  {
                      current_cnt=1;
                  }
                  if(start_cnt==''){
                      start_cnt=1;
                  }
              $(".paginate_button").removeClass("active");
              $(".paginate_button").removeClass("disabled");
              if(current_cnt == start_cnt && end_cnt==1) {
                   $("#pagination").html(' <li class="paginate_button previous disabled" id="pagn_1" ><a href="#">Previous</a></li>'+
                        '<li class="paginate_button active" id="pagn_2" onclick="search_filter(1)"><a href="#">1</a></li>'+
                        '<li class="paginate_button disabled" id="pagn_3" ><a href="#">2</a></li>'+
                        '<li class="paginate_button disabled" id="pagn_4" ><a href="#">3</a></li>'+
                        '<li class="paginate_button next disabled" id="pagn_5" ><a href="#">Next</a></li>');
              } 
             else if(current_cnt == start_cnt && end_cnt==2) {
                   $("#pagination").html(' <li class="paginate_button previous disabled" id="pagn_1" ><a href="#">Previous</a></li>'+
                        '<li class="paginate_button active" id="pagn_2" onclick="search_filter(1)"><a href="#">1</a></li>'+
                        '<li class="paginate_button " id="pagn_3" onclick="search_filter(2)"><a href="#">2</a></li>'+
                        '<li class="paginate_button disabled" id="pagn_4" ><a href="#">3</a></li>'+
                        '<li class="paginate_button next " id="pagn_5" onclick="search_filter_btn(2)"><a href="#">Next</a></li>');
              } 
             else if(current_cnt == 2 && end_cnt==2) {
                   $("#pagination").html(' <li class="paginate_button previous " id="pagn_1" onclick="search_filter_btn(1)" ><a href="#">Previous</a></li>'+
                        '<li class="paginate_button " id="pagn_2" onclick="search_filter(1)"><a href="#">1</a></li>'+
                        '<li class="paginate_button active" id="pagn_3" onclick="search_filter(2)"><a href="#">2</a></li>'+
                        '<li class="paginate_button disabled" id="pagn_4" ><a href="#">3</a></li>'+
                        '<li class="paginate_button next disabled" id="pagn_5" ><a href="#">Next</a></li>');
              } 
              else if(current_cnt == start_cnt){
                  $("#pagination").html(' <li class="paginate_button previous disabled" id="pagn_1"  ><a href="#">Previous</a></li>'+
                        '<li class="paginate_button active" id="pagn_2" onclick="search_filter(1)" ><a href="#">1</a></li>'+
                        '<li class="paginate_button " id="pagn_3" onclick="search_filter(2)"><a href="#">2</a></li>'+
                        '<li class="paginate_button " id="pagn_4" onclick="search_filter(3)"><a href="#">3</a></li>'+
                        '<li class="paginate_button next " id="pagn_5" onclick="search_filter_btn(2)"><a href="#">Next</a></li>');
              } else if(current_cnt == end_cnt)
              {
                        s = parseInt(current_cnt)-2;
                        m=parseInt(current_cnt)-1;
                        e=current_cnt;
                   $("#pagination").html(' <li class="paginate_button previous " id="pagn_1" onclick="search_filter_btn(1)" ><a href="#">Previous</a></li>'+
                        '<li class="paginate_button" id="pagn_2" onclick="search_filter('+s+')"><a href="#">'+s+'</a></li>'+
                        '<li class="paginate_button " id="pagn_3" onclick="search_filter('+m+')"><a href="#">'+m+'</a></li>'+
                        '<li class="paginate_button active" id="pagn_4" onclick="search_filter('+e+')"><a href="#">'+e+'</a></li>'+
                        '<li class="paginate_button next disabled" id="pagn_5" ><a href="#">Next</a></li>');
              } 
               else{
                        s = parseInt(current_cnt)-1;
                        m=parseInt(current_cnt);
                        e=parseInt(current_cnt)+1;
                   $("#pagination").html(' <li class="paginate_button previous " id="pagn_1" onclick="search_filter_btn(1)"><a href="#">Previous</a></li>'+
                        '<li class="paginate_button" id="pagn_2" onclick="search_filter('+s+')"><a href="#">'+s+'</a></li>'+
                        '<li class="paginate_button active" id="pagn_3" onclick="search_filter('+m+')"><a href="#">'+m+'</a></li>'+
                        '<li class="paginate_button " id="pagn_4" onclick="search_filter('+e+')"><a href="#">'+e+'</a></li>'+
                        '<li class="paginate_button next " id="pagn_5" onclick="search_filter_btn(2)"><a href="#">Next</a></li>');
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
<script>
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
             
              $("#status").show();
        });
         $(".ad-work-clear-btn").click(function(){
             $("#fname").val(''); 
             $("#lastname").val(''); 
             $("#mobile_number").val(''); 
             $("#alternate_number").val(''); 
             $("#designation").val('Select'); 
             $("#auth_code").val(''); 
             $("#fname").focus(); 
              $("#status_p").hide();
              $("#auth_p").hide();
               $("#permission_p").hide();
               $("#canc_permission").hide();
              
        });
        function submit_staff(types)
        {
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var p1 = document.getElementById("auth_code").value;
            var min = 4
            var fname = $("#fname").val();
            var lastname = $("#lastname").val();
            var mobile_number =$("#mobile_number").val();
            var alternate_number =$("#alternate_number").val();
            var designation =$("#designation").val();
            var auth_code =$("#auth_code").val();
            var status =$("#status").val();
            var permission =$("#permission").val();
            var can_permission =$("#can_permission").val();
            var userid =$("#userid").val();
            var insert = $("#inserting").val();
            var update =$("#updating").val();
            var staff_id = $("#staff_id").val();
            var changefs = $("#changefs").val();
            
            var staff_credit_limit = $("#staff_credit_limit").val();
            var category="";
            category =$("#staff_cat").val();
//            if(designation === "Delivery staff")
//                {
//                     category ="all";
//                }else
//                {
//                     category =$("#staff_cat").val();
//                }
          // alert(category)     
            if(fname == '')
            {
              $("#fname").focus();
               $.Notification.autoHideNotify('error', 'bottom right','Enter Name');
            return false;
            }
            
            if(designation == 'Select') 
            {
              $("#designation").focus();
              $.Notification.autoHideNotify('error', 'bottom right','Select Designation');
            return false;
            }
            
//              if(auth_code == '') 
//            {
//              $("#auth_code").focus();
//               $.Notification.autoHideNotify('error', 'bottom right','Enter Auth Code');
//            return false;
//            }
//           if(p1.length != min) 
//            {
//                 $.Notification.autoHideNotify('error', 'bottom right','Enter atleast 4 digits for AuthCode');
//              
//            return false;
//            }
        if(true)
        {
            var data= {"staffid":staff_id,"staff_credit_limit":staff_credit_limit,"fname":fname,"lastname":lastname,"mobile_number":mobile_number,"alternate_number":alternate_number,"designation":designation,"status":status,"types":types,"userid":userid,"auth_code":auth_code,"permission":permission,"can_permission":can_permission,"category":category,"changefs":changefs};
            
            $.ajax({
                method: "post",
                url : "api/add_staff",
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
               
               
                    if((json_x.msg)=='done')
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
            return true;
        }

        $(".add_new_btn").click(function()
        {
            $('#staffcreditlimit_field').addClass('disable_field');
            $.ajax({
                method: "get",
                url : "api/get_staff_credit_limit",
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(result)
                {
                    var json_x= JSON.parse(result);
                    if((json_x.msg)=='Exist')
                    {
                       $("#staff_credit_limit").val(json_x.credit);
                    }
                 },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        });
        
        function staffedit(id,fname,lname,mobile,altnumber,designation,status,authcode,permission,can_permision,stff_mx_credit,category,complaint_status_change)
        {
         $('#staffcreditlimit_field').removeClass('disable_field');
         $('#staff_credit_limit').val(stff_mx_credit);
         $("#userid").val(id);
         $("#status").val(status);
         $("#permission").val(permission);
         $("#can_permission").val(can_permision);
         $("#designation").val(designation);
         $("#fname").val(fname);
         $("#lastname").val(lname);
         $("#mobile_number").val(mobile);
         $("#alternate_number").val(altnumber);
         $("#auth_code").val(authcode);
         $("#changefs").val(complaint_status_change);
         $("#add_user").css("display",'block');
         $("#status_p").css("display",'block');
         $("#permission_p").css("display",'block');
         $("#canc_permission").css("display",'block');
         $("#password_user").css("display",'none');
         $("#inserting").css("display",'none');
         $("#updating").css("display",'block');
         $("#auth_p").css("display",'block');
         $("#staff_cat").val(category);
         if(designation=="Delivery staff")
         {
             //document.getElementById("staff_cat").disabled=true;
         }else
         {
             //document.getElementById("staff_cat").disabled=false;
         }
        }
        function staffareaadd(id)
        {
            $(".timing_popup_cc").show();
            $("#staffid").val(id);
            if(area != 'null')
            {
                var div = $("#tbodyhtml").html('');
                $.ajax({
                    method: "post",
                    url: "api/staffarea_list",
                    data: {'id':id},
                    cache: false,
                    crossDomain: true,
                    async: false,
                    dataType: 'text',
                    success: function (result)
                    {
                        div.html(result);
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        alert(jqXHR.responseText);
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
            return true;
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
    $(".timing_popup_cls").click(function()
    {
         $(".timing_popup_cc").hide();
        filter_change();
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

    function areaadd()
    {
        $('.notifyjs-wrapper').remove();
        var id = $("#staffid").val();
        var area = $("#area");
        if(area.val() =='')
        {
            area.focus();
            $.Notification.autoHideNotify('error', 'bottom right','Select Area');
            return false;
        }
        if(true)
        {
            var div = $("#tbodyhtml").html('');
            var reset_data = {"area":area.val(),"id":id};
            $.ajax({
                method: "post",
                url : "api/staff_area",
                data : reset_data,
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(datas)
                {
                    var jsonx = JSON.parse(datas);
                    if(jsonx['msg'] == 'success')
                    {
                        swal({
                              title: "",
                              text: "Added Successfully",
                              timer: 1000,
                              showConfirmButton: false
                          });
                    }
                    else
                    {
                        swal({
                            title: "",
                            text: jsonx['msg'],
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }
                    staffareaadd(id);
                    $('#area').val('');
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('error');
                    $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }
        return true;
    }

    function area_delete(id,val)
    {
        var length = $('#tableid').find('tr').length - 1;
        if(length > 1)
        {
        var reset_data = {"area":val,"id":id};
        if(confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                method: "post",
                url: "api/staff_area_delete",
                data: reset_data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (datas) {
                    var jsonx = JSON.parse(datas);
                    if (jsonx['msg'] == 'success') {
                        swal({
                            title: "",
                            text: "Deleted Successfully",
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }
                    staffareaadd(id);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }
        }
        else
        {
            swal({
                title: "",
                text: "Cannot Delete",
                timer: 1000,
                showConfirmButton: false
            });
        }
        return true;
    }
    function cat_listsel(des)
    {
        if(des=="Delivery staff")
        {
           // document.getElementById("staff_cat").disabled=true;
            //$("#staff_cat").css("disabled",'true');
        
        }else
        {
       // document.getElementById("staff_cat").disabled=false;
        }
    
    }

</script>
@endsection




