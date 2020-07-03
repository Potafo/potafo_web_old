@extends('layouts.app')
@section('title','Potafo - Manage Restaurant')
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
						
						<li class="active ms-hover">
							Catering Restaurants
						</li>
					</ol>
				</div>
        <div class="card-box table-responsive" style="padding: 8px 10px;">
             
              <div class="box master-add-field-box" >
             <div class="col-md-6 no-pad-left">
                <h3>Manage Restaurant</h3>
                
            </div>    
                  
            <div class="col-md-1 no-pad-left pull-right">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a href="cat_restaurant_details"> <button type="submit" style="margin-left:0" class="on-default followups-popup-btn btn btn-primary" >Add New</button></a>
                </div>

                 </div>
            </div>
                  <div class=" pull-right" style="display: none">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a title="Filter" href="#" onclick="filter_view()"> <button type="submit"  style="margin-right: 10px;" class="on-default followups-popup-btn btn btn-primary filter_sec_btn" ><i class="fa fa-filter" aria-hidden="true"></i></button></a>
                </div>
                   
                 </div>
            </div>
                  
            </div>
            
            <div class="filter_box_section_cc diply_tgl" style="display: block">
<!--                <div class="filter_box_section">FILTER</div>-->
                   <div class="filter_text_box_row">
                       {!! Form::open(['url'=>'filter/cat_restaurant', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'post']) !!}
                       <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>
                       <input type="hidden" id="logingroup" name="logingroup" value="{{ Session::get('logingroup')}}"/>
                       <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Restaurant Name</label>
                                  <input id="restaurant_name" onkeyup="return filter_change(this.value)" name="restaurant_name" class="form-control" type="text" >
                              </div>
                           </div>
                        </div>
                       <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Category</label>
                                 <select id="category" name ="category" class="form-control" onchange="return filter_change(this.value);">
                                     <option value="">All</option>
                                    @foreach($category as $item)
                                        @if($item->cc_status == 'Active')
                                          <option value="{{ $item->cc_id }}">{{ title_case($item->cc_name) }}</option>
                                          @endif
                                      @endforeach
                                 </select>
                              </div>
                           </div>
                        </div>
                       <div class="main_inner_class_track" style="width: 25%;display: none">
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
                    <th style="min-width:100px">Restaurant Name</th>
                    <th style="width:80px">Dis Ordr</th>
        <!--        <th style="min-width:50px">Point Of Contact</th>  -->
                    <th style="min-width:80px">Min Pax</th>
                    <th style="min-width:10px">Status</th>
              <!--  <th style="min-width:7px">Extra Rate %</th>  -->
        <!--        <th style="min-width:20px">Min Cart Val</th>  -->
                    {{--<th style="min-width:5px">Diet</th>--}}
                    <th style="min-width:5px">Min Rate</th>
               <th style="min-width:20px">Max Rate</th>
                    <th style="min-width:10px"></th>
                </tr>
                </thead>
                
                <tbody>
                    <!--`cr_id`, `cr_name`, `cr_cusines`, `cr_custom_message`, `cr_min_rate`, `cr_max_rate`, 
                    `cr_min_pax`, `cr_avg_rating`, `cr_veg_only`, `cr_pic`, `cr_dlv_range`, `cr_city`, `cr_address`,
                    `cr_contact_person`, `cr_dlv_charge`,
                    `cr_packing_charge`, `cr_registeration_date`, `cr_display_order`, `cr_status` FROM `cat_restaurants`-->
                @if(isset($details))
                @if(count($details)>0)
                    @foreach($details as $key=>$item)
                    <tr>
                    <td style="min-width:3px;">{{ $key+1 }}</td>
                    <td style="min-width:100px;text-align: left;">@if(isset($item->cr_name)) {{ title_case($item->cr_name) }}@endif</td>
                    <td style="width:70px;">
                      <input style="width:70px;" class="form-control" type="textbox" onkeypress="return isNumberKey(event)" value="{{ $item->cr_display_order }}" title="Edit Order" name="order_no" id="order_no" onkeyup="return changeorderno('{{ $item->cr_id }}',this.value)">
                    </td>
    <!--            <td style="min-width:50px;text-align: left;">@if(isset($item->point_of_contact)) {{ title_case($item->point_of_contact) }}@endif</td>  -->
                    <td style="min-width:80px;text-align: left;">@if(isset($item->cr_min_pax)){{ $item->cr_min_pax }}@endif</td>
                     <td style="min-width:5px;">
                        
                        <div class="status_chck{{ $item->cr_id}}">
                            <div class="onoffswitch">
                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch{{$item->cr_id}}" @if( $item->cr_status == 'Active') checked @endif>
                                <label class="onoffswitch-label" for="myonoffswitch{{$item->cr_id}}">
                                    <span class="onoffswitch-inner" onclick="return  statuschange('{{$item->cr_id}}')"></span>
                                    <span class="onoffswitch-switch" onclick="return  statuschange('{{$item->cr_id}}')"></span>
                                </label>
                            </div>
                        </div>
                        
                    </td>
                    <td style="min-width:10px;text-align: center;">@if(isset($item->cr_min_rate)){{ $item->cr_min_rate }}@endif</td>
                     <td style="min-width:10px;text-align: center;">@if(isset($item->cr_max_rate)){{ $item->cr_max_rate }}@endif</td>
        <!--        <td style="min-width:7px;text-align: left;">@if(isset($item->extra_rate_percent) && $item->extra_rate_percent >0){{ $item->extra_rate_percent }}@endif</td>     -->
        <!--        <td style="min-width:20px;text-align: center;">@if(isset($item->min_cart_value) && $item->min_cart_value>0){{ $item->min_cart_value }}@endif</td> -->
                    {{--<td style="min-width:5px;"><img width="15px" src="@if(isset($item->pure_veg) && $item->pure_veg == 'Y'){{ asset('public/assets/images/veg_ico.png') }} @else {{ asset('public/assets/images/non_veg_ico.png') }} @endif"></td>--}}
                   
                
                    <td style="min-width:10px">
<!--                        <a href="{{ url('restaurant_edit/'.$item->cr_id) }}" class="btn button_table" ><i class="fa fa-pencil"></i></a>-->
                        <a href="#" class="btn button_table" >
                            <i class="fa fa-cog"></i>
                            <div class="other_button_section">
                                <div class="oth_btn_1"  onclick="viewlink('{{ $item->cr_id }}','about')">
                                     <i class="fa fa-cutlery"></i>  <br>
                                    About
                                </div>
                                <div class="oth_btn_1"  onclick="viewlink('{{ $item->cr_id }}','types')">
                                   <i class="fa fa-info"></i> <br>
                                    Types
                                </div>
                                 <div class="oth_btn_1"  onclick="viewlink('{{ $item->cr_id }}','category')">
                                     <i class="fa fa-info"></i>  <br>
                                   Category
                                </div>
                                 <div class="oth_btn_1"  onclick="viewlink('{{ $item->cr_id }}','pincode')">
                                     <i class="fa fa-info"></i>  <br>
                                   Pincodes
                                </div>
                                <div class="oth_btn_1"  onclick="viewlink('{{ $item->cr_id }}','tax')">
                                     <i class="fa fa-info"></i>  <br>
                                   Tax
                                </div>
                               <!-- 
                               
                                <div class="oth_btn_1"  onclick="viewlink('{{ $item->cr_id }}','offer')">
                                    <i class="fa fa-star-o "></i>  <br>
                                   Offer
                                </div>
                                <div class="oth_btn_1"  onclick="viewlink('{{ $item->cr_id }}','login')">
                                    <i class="fa fa-star-o "></i>  <br>
                                   Login
                                </div>-->
                            </div>
                        </a>
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
                                             <input type='hidden' id='restid' name="restid" />
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Login Name</label>
                                       {!! Form::text('rest_name',null, ['class'=>'form-control','id'=>'rest_name','name'=>'rest_name','onkeypress' => 'return charonly(event);','required','style'=>"background-color:transparent;"]) !!}
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Password</label>
                                          <input style="padding-right:35px;" class="form-control" id="rest_pasw" name="rest_pasw" type="password">

                                         <div class="ion-ios7-eye pass_show" onmouseover="mouseoverPass();" onmouseout="mouseoutPass();" />
                                    </div>
                                </div>
                            </div>
                                    <div class="main_inner_class_track" style="width:20%">
                                    	 <b><p class="" style="color: #000;float:right;cursor:pointer;display: none;background-color: burlywood;margin-top: 6px;padding: 2px 13px;line-height: 30px;" id="getimage" data-toggle="modal"  data-target="#myModal" data-title=""><a style="color: #000;">View image</a></p></b>
                                       
                                    </div>
                        
                             <div class="box-footer">
                                 <input type="hidden" name="type" id="type" />
                               <a id="updating" name="updating"  class="staff-add-pop-btn" onclick="update_auth();" style="height:40px; bottom: 20px;">Update</a>
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

     
    {{--<script src="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.js') }}"></script>--}}
    <link href="{{ asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('public/assets/dark/plugins/custombox/css/custombox.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <!--<script src="{{ asset('public/assets/js/angular.min.js') }}"></script>-->
    <script src="{{asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />

    <script type="text/javascript">
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
function filter_change(val)
{
      var frm = $('#frm_filter');
      var table = $('#example1').DataTable();
      $.ajax({
          method: "post",
          url   : "api/filter/cat_restaurant",
          data  : frm.serialize(),
          cache : false,
          crossDomain : true,
          async : false,
          dataType :'text',
          success : function(result)
          {
            //$("#urls").text(result);
              var rows = table.rows().remove().draw();
              var json_x= JSON.parse(result);
              if(parseInt(json_x.length) > 0) {
                  $.each(json_x, function (i, val)
                  {
                      var count = i + 1;
                      var status="";
                      if(val.cr_status == 'Active')
                        {
                           // var status_val = 'checked';
                             status = '<div class="status_chck'+val.cr_id+'"><div class="onoffswitch"> <input type="checkbox" name="onoffswitchs" class="onoffswitch-checkbox" id="myonoffswitchs'+val.cr_id+'"  checked> <label class="onoffswitch-label" for="myonoffswitchs'+val.cr_id+'"> <span class="onoffswitch-inner" onclick="return  statuschange('+val.cr_id+')"></span><span class="onoffswitch-switch" onclick="return  statuschange('+val.cr_id+')"></span> </label></div></div>';
                        }else
                        {
                            status='<div class="status_chck'+val.cr_id+'"><div class="onoffswitch"> <input type="checkbox" name="onoffswitchs" class="onoffswitch-checkbox" id="myonoffswitchs'+val.cr_id+'"  > <label class="onoffswitch-label" for="myonoffswitchs'+val.cr_id+'"> <span class="onoffswitch-inner" onclick="return  statuschange('+val.cr_id+')"></span><span class="onoffswitch-switch" onclick="return  statuschange('+val.cr_id+')"></span> </label></div></div>';
                        }
                      
                     //alert(status)
                       var newRow = '<tr><td style="min-width:3px;">'+count+'</td>'+
                          '<td style="min-width:160px;text-align: left;">'+val.cr_name+'</td>'+
                          '<td style="width:70px;"><input style="width:70px;" class="form-control" type="textbox" onkeypress="return isNumberKey(event)" value="'+val.cr_display_order+'"  onkeyup="return changeorderno('+val.cr_id+',this.value)"></td>'+
                          '<td style="min-width:80px;text-align: left;">'+val.cr_min_pax+'</td>'+                         
                          '<td>'+status+'</td>'+
                         '<td>'+val.cr_min_rate+'</td>'+
                         '<td>'+val.cr_max_rate+'</td>'+
                          '<td  style="min-width:10px"> <a href="#" class="btn button_table" >'+
                          '<i class="fa fa-cog"></i>'+'<div class="other_button_section">'+
                          '<div class="oth_btn_1"  onclick="viewlink('+val.cr_id+',\'about\')"><i class="fa fa-cutlery"></i> <br>About</div>'+
                          '<div class="oth_btn_1"  onclick="viewlink('+val.cr_id+',\'types\')"><i class="fa fa-info"></i><br>Types</div>'+
                          '<div class="oth_btn_1"  onclick="viewlink('+val.cr_id+',\'category\')"><i class="fa fa-info">%</i><br>Category</div>'+
                          '<div class="oth_btn_1"  onclick="viewlink('+val.cr_id+',\'pincode\')"><i class="fa fa-info"></i><br>Pincodes</div>'+
                          
                          '</div></a></td>'+'</tr>';
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
function close_aut_log()
{
     $("#rest_auth_sec").css("display","none");
}
function update_auth() {
    var restname = $("#rest_name").val();
    var restpasw = $("#rest_pasw").val();
    var restid   = $("#restid").val();
    if(restname==''){
          swal({
							
                title: "",
                text: "Please Enter Name",
                timer: 2000,
                showConfirmButton: false
            });
    }
    else if(restpasw==''){
          swal({
							
                title: "",
                text: "Please Enter Password",
                timer: 2000,
                showConfirmButton: false
            });
    }
    else {
                 $.ajax({
                        method: "post",
                        url: "api/update_rest_auth",
                        data:{"restname":restname,"restpasw":restpasw,"restid":restid},
                        success: function (result)
                        {
                          if(result=='insert'){
                              
                            swal({

                                  title: "",
                                  text: "Login Added Succesfully",
                                  timer: 4000,
                                  showConfirmButton: false
                              });
                              location.reload();
                      }
                      else{
                            swal({

                                  title: "",
                                  text: "Login Updated Succesfully",
                                  timer: 4000,
                                  showConfirmButton: false
                              });
                              location.reload();
                      }  
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
        //                    alert(errorThrown);
                            $("#errbox").text(jqxhr.responseText);
                        }
                    });

            }
    }
 function viewlink(id,link)
    {
        if(link == 'menu')
        {
            window.location.href="menu/list/"+id;
        }
        else if(link == 'about')
        {
            window.location.href="cat_restaurant_edit/"+id;
        }
        else if(link == 'category')
        {
            window.location.href="cat_restaurant_category/"+id;
        }
        else if(link == 'pincode')
        {
            window.location.href="cat_restaurant_pincode/"+id;
        }
else if(link == 'tax')
        {
            window.location.href="cat_restaurant_tax/"+id;
        }
        else if(link == 'types')
        {
            window.location.href="menu/types/"+id;
        }
        return true;
    }
    
     function statuschange(id) {
            var ids = id;
            var data = {"ids": ids};
            $.ajax({
                method: "get",
                url: "catrestaurant_status",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
//                    alert (result);
//                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
//                    alert(errorThrown);
                    $("#errbox").text(jqxhr.responseText);
                }
            });
        }

        function restaurantstatuschange(id) {
            var ids = id;
            var data = {"ids": ids};
            $.ajax({
                method: "get",
                url: "restaurantclose_status",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
        //                    alert (result);
        //                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
        //                    alert(errorThrown);
                    $("#errbox").text(jqxhr.responseText);
                }
            });
        }
        
  function changeorderno(id,val)
        {
            $.ajax({
                method: "get",
                url: "api/rest_disporder/" + id+ "/"+val,
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
        
</script>

@stop



   

@endsection




