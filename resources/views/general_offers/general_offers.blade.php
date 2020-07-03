@extends('layouts.app')
@section('title','Potafo - General Offers')
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
    .main_inner_class_track{margin-right: 1.5%;}
    .main_inner_class_track .group{margin-bottom: 0}
    .main_inner_class_track .form-control{resize: none}
      .popover {width: 180px;height: 120px;}.popover img{width:100%}
      .mce-notification-error{ display: none;}   
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
							General Offers
						</li>
					</ol>
				</div>
        <div class="card-box table-responsive" style="padding: 8px 10px;">
              
              <div class="box master-add-field-box" >
             <div class="col-md-6 no-pad-left">
                <h3>General Offers</h3>
            </div> 
               
             <div class="col-md-1 no-pad-left pull-right">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a style="cursor:pointer" class="add_offer_btn_new"> <button type="submit" style="margin-left:0" class="on-default followups-popup-btn btn btn-primary">Add Offer</button></a>
                </div>
                 </div>
            </div>
            <div class=" pull-right">
                <div class="table-filter" style="margin-top: 4px;display:none">
                  <div class="table-filter-cc">
                    <a title="Filter" href="#" onclick="filter_view()"> <button type="submit"  style="margin-right: 10px;" class="on-default followups-popup-btn btn btn-primary filter_sec_btn" ><i class="fa fa-filter" aria-hidden="true"></i></button></a>
                </div>
                   
                 </div>
            </div>      
              <div class="filter_box_section_cc diply_tgl">
<!--                <div class="filter_box_section">FILTER</div>-->
                   <div class="filter_text_box_row">
                       {!! Form::open(['url'=>'filter/genoffer', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'get']) !!}
                       {{ Form::hidden('url',$siteUrl, array ('id'=>'url','name'=>'url')) }}
                       <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Status</label>
                                 <select id="flt_status" name ="flt_status" class="form-control" onchange="return filter_genoffchange(this.value);">
                                     <option value="Y">Active</option>
                                     <option value="N">Inactive</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                       

                       {{ Form::close() }}

                   </div>  
</div>
           <div class="table_offer_list_cc">
               <div class="row">
                   
            <div class="col-xs-12">
            <div class="table_section_scroll">  
            <div class="table-responsive">
                  <table class="table m-0 table table-striped" id="example1">
                       <thead>
						<tr>
							<th style="min-width:50px;">SlNo</th>
                            <th style="min-width:110px;">Action</th>
                            <th style="min-width:80px;">Status</th>
							<th style="min-width:150px;">Offer Name</th>
							<th style="min-width:80px;">Offer%</th>
                            <th style="min-width:120px;">Coupon code</th>
							<th style="min-width:120px;">Amount Above</th>
							<th style="min-width:100px;">Max Amount</th>
							<th style="min-width:100px;">Usage Limit</th>
							<th style="min-width:100px;">Valid From</th>
							<th style="min-width:100px;">Valid To</th>
							<th style="min-width:100px;">Image</th>
                            <th style="min-width:200px;">Dscription</th>
							</tr>
						</thead>
                      <tbody>
                    @if(isset($details))
                    @if(count($details)>0)
                    @foreach($details as $key=>$item)
                        <tr>
                            
                            <td>{{ $key+1 }}</td>  
                            <td> 
                                <!--<a href="#" class="table-action-btn button_table"><i class="md md-remove-red-eye"></i></a>-->
                                <!--<a href="#" class="table-action-btn button_table"><i class="md md-delete"></i></a>-->
                                <a href="#" onclick="return genofferedit('{{$item->id}}','{{$item->name}}','{{$item->amtabove}}','{{ $item->max_amount }}','{{ $item->valid_to }}','{{$item->offer_per}}','{{ $item->valid_from }}','{{ $item->active }}','{{$item->description}}','{{ $item->coupon_code }}','{{ $item->image }}','{{ $siteUrl }}','{{$item->usage_limit}}')" class="table-action-btn button_table table_edit"><i class="md md-edit"></i></a>
                                <a href="#" onclick="return delete_gen_offer('{{$item->id}}')" class="table-action-btn button_table"><i class="fa fa-trash"></i></a>
                            </td> 
                             <td><div class="onoffswitch">
                                     <input autocomplete="off"   type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch51{{$item->id}}" @if( $item->active == 'Y') checked @endif>
                                     <label class="onoffswitch-label" for="myonoffswitch51{{$item->id}}">
                                       <span class="onoffswitch-inner" onclick="return  statuschange('{{$item->id}}')"></span>
                                       <span class="onoffswitch-switch" onclick="return  statuschange('{{$item->id}}')"></span>
                                      </label>
                                   </div>
                            </td>  
                            <td>@if(isset($item->name)) {{ title_case($item->name) }}@endif</td>  
                            <td>@if(isset($item->offer_per)) {{ $item->offer_per }}@endif</td>  
                           
                            <td>@if(isset($item->coupon_code)) {{ $item->coupon_code }}@endif</td>
                             <td>@if(isset($item->amtabove)) {{ $item->amtabove }}@endif</td>   
                             <td>@if(isset($item->max_amount)) {{ $item->max_amount }}@endif</td>   
                             <td>@if(isset($item->usage_limit)) {{$item->usage_limit}}@endif</td>   
                             <td>@if(isset($item->valid_from)) {{ $item->valid_from }}@endif</td>   
                             <td>@if(isset($item->valid_to)) {{ $item->valid_to }}@endif</td> 
                            <td><a rel="popover" data-img="{{ $siteUrl.$item->image }}" style="text-decoration: underline;"  data-original-title="" href="#">View</a></td> 
                             <td>@if(isset($item->description)) {!! title_case($item->description) !!}@endif</td>
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
                   
            
        </div>    
            </div>
            

            
            
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    <div class="timing_popup_cc add_view_offer_popup" style="display: none;">
    <div class="timing_popup">
        
        <div class="timing_popup_head" style="margin-bottom: -15px;">
            ADD OFFER
            <div class="timing_popup_cls"><img src="{{asset('public/assets/images/cancel.png')}}"></div>
        </div>
        <div class="timing_popup_contant">
            <div class="restaurant_more_detail_row" style="margin-bottom:-18px">
                
                <form enctype="multipart/form-data" id="upload_form" role="form" method="POST" action="" >
                <div class="offer_add_form_section_bx" >
                  <input autocomplete="off"   type='hidden' id="descdetail" name="descdetail" value="">
                <div class="main_inner_class_track" style="width: 43%;">
                          <div class="group">
                             <div style="position: relative">
                                 <label>Offer name*</label>
                                 <input autocomplete="off"   id="o_name" name="o_name" type="text" class="form-control">
                              </div>
                           </div>
                        </div>
                <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Offer %*</label>
                                  <input autocomplete="off"   id="o_perc" name="o_perc" type="text" class="form-control" onkeypress = "return numonly(event);" maxlength="3">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Amount Above*</label>
                                 <input autocomplete="off"   id="amt_abv" name="amt_abv" type="text" class="form-control" onkeypress = "return numonly(event);">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Max Amount*</label>
                                 <input autocomplete="off"   id="max_amt" name="max_amt" type="text" class="form-control" onkeypress = "return numonly(event);">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Usage Limit*</label>
                                 <input autocomplete="off"   id="usage_limit" name="usage_limit" type="text" class="form-control" onkeypress = "return numonly(event);">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Coupon Code</label>
                                 <input autocomplete="off"   id="code" name="code" type="text" class="form-control">
                              </div>
                           </div>
                        </div>
                    
                    <div class="main_inner_class_track" style="width: 31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid From*</label>
                                  <input autocomplete="off"   id="valid_from"  name="valid_from" type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid To*</label>
                                 <input autocomplete="off"   id="valid_to"  name="valid_to" type="text" class="form-control datefield">
                             </div>
                           </div>
                        </div>
                    
                                    
                        <div class="main_inner_class_track" style="width: 31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Image</label>
                                 <input autocomplete="off"   id="offer_image" name="offer_image" type="file" class="form-control" onclick="return Upload()">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 98%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Description</label>
                                 <textarea id="desc" name="desc" class="form-control"></textarea>
                              </div>
                           </div>
                        </div>

            </div>

            <div class="col-sm-12 no-padding">
                <a class="staff-add-pop-btn staff-add-pop-btn-new" onclick="save_genoff();" style="float: right;">ADD</a>
        </div>
                </form> 
        </div>
    </div>
</div>

        		</div>
    
    
    <div class="timing_popup_cc edit_view_offer_popup" style="display: none;">
    <div class="timing_popup">
        <div class="timing_popup_head" style="margin-bottom: -15px;">
            Edit Offer
            <div class="timing_popup_cls"><img src="{{asset('public/assets/images/cancel.png')}}"></div>
        </div>
        <div class="timing_popup_contant">
            <div class="restaurant_more_detail_row" style="margin-bottom:-18px">
                
                <form enctype="multipart/form-data" id="upload_edform" role="form" method="POST" action="" >
                <div class="offer_add_form_section_bx" >
                  <input autocomplete="off"   type='hidden' id="ed_descdetail" name="ed_descdetail" value="">
                  <input autocomplete="off"   type='hidden' id="edid" name="edid" value="">
                  <input autocomplete="off"   type="hidden" name="oldimg" id="oldimg" >
                  <input autocomplete="off"   type="hidden" name="editoldimg" id="editoldimg" >
                <div class="main_inner_class_track" style="width: 43%;">
                          <div class="group">
                             <div style="position: relative">
                                 <label>Offer name*</label>
                                 <input autocomplete="off"   id="ed_o_name" name="ed_o_name" type="text" class="form-control">
                              </div>
                           </div>
                        </div>
                <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Offer %*</label>
                                  <input autocomplete="off"   id="ed_o_perc" name="ed_o_perc" type="text" class="form-control" onkeypress = "return numonly(event);" maxlength="3">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Amount Above*</label>
                                 <input autocomplete="off"   id="ed_amt_abv" name="ed_amt_abv" type="text" class="form-control" onkeypress = "return numonly(event);">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Max Amount*</label>
                                 <input autocomplete="off"   id="ed_max_amt" name="ed_max_amt" type="text" class="form-control" onkeypress = "return numonly(event);">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Usage Limit*</label>
                                 <input autocomplete="off"   id="ed_usage_limit" name="ed_usage_limit" type="text" class="form-control" onkeypress = "return numonly(event);">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Coupon Code</label>
                                 <input autocomplete="off"   id="ed_code" name="ed_code" type="text" class="form-control">
                              </div>
                           </div>
                        </div>
                    
                    <div class="main_inner_class_track" style="width:31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid From*</label>
                                 <input autocomplete="off"   id="ed_valid_from"  name="ed_valid_from" type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid To*</label>
                                 <input autocomplete="off"   id="ed_valid_to"  name="ed_valid_to" type="text" class="form-control datefield">
                             </div>
                           </div>
                        </div>
                    
                                    
                        <div class="main_inner_class_track" style="width: 31%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Image</label><a rel="popover" id="imgoffer" name="imgoffer"  href="#" style="margin-left: 25px;">View</a>
                                 <input autocomplete="off"   id="ed_offer_image" name="ed_offer_image" type="file" class="form-control" onclick="return Upload()">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 98%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Description</label>
                                 <textarea id="ed_desc" name="ed_desc" class="form-control"></textarea>
                              </div>
                           </div>
                        </div>

            </div>

            <div class="col-sm-12 no-padding">
                <a class="staff-add-pop-btn staff-add-pop-btn-new" onclick="update_genoff();" style="float: right;">Update</a>
        </div>
                </form> 
        </div>
    </div>
</div>

</div>


     <link href="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css">
        <script src="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
        <script src="{{ asset('public/assets/pages/jquery.sweet-alert.init.js') }}"></script>
         <script src="{{ asset('public/assets/admin/plugins/tinymce/js/tinymce/jquery.tinymce.min.js') }}"></script>
        <script src="{{ asset('public/assets/admin/plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>
        <script src="{{ asset('public/assets/admin/plugins/tinymce/js/tinymce/tinymce.js') }}"></script>
         <style>#datatable-fixed-col_filter{display:none}table.dataTable thead th{white-space:nowrap;padding-right: 20px;}
        .on-default	{margin-left:10px;}div.dataTables_info {padding-top:13px;}
    </style>
    
@section('jquery')
<script>
        $(document).ready(function()
        {
             tinymce.init({
                selector: 'textarea',
                height: 150,
                menubar: false,
                branding: false,
                forced_root_block: false,
                theme: 'modern',
                plugins: 'autolink directionality advcode visualblocks visualchars fullscreen  link media template codesample  charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount tinymcespellchecker a11ychecker imagetools mediaembed  linkchecker contextmenu colorpicker textpattern help',
                toolbar: 'formatselect | bold italic backcolor strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help | underline  ',
                //toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
            });
            
            $('a[rel=popover]').popover({
                html: true,
                trigger: 'hover',
                placement: 'left',
                content: function()
                {
                    return '<img src="'+$(this).data('img') + '" width="200" height="100"/>';
                }
            });
            
    
        });
    </script>
<script>
     
    $(".add_offer_btn_new").click(function(){
        $(".add_view_offer_popup").show();
        $("#o_name").focus();
    });
    $(".timing_popup_cls").click(function(){
        $(".add_view_offer_popup").hide();
        $(".edit_view_offer_popup").hide();
    });
    $(".table_edit").click(function(){
        $(".edit_view_offer_popup").show();
        $("#o_name").focus();
    });
    
    
    
</script>
</script>
<script type="text/javascript">
 $('.datefield').datetimepicker({
               format: "yyyy-mm-dd h:i:00",
        showMeridian: true,
        autoclose: true,
        todayBtn: true
            });
</script>

    <script type="text/javascript">

     $('.filter_sec_btn').on('click', function(e)
    {
        $('.filter_box_section_cc').toggleClass("diply_tgl");
        $("#restaurant_name").focus();
    });
        
//    $('#valid_to').datepicker({
//                        autoclose: true,
//                        format: "yyyy-mm-dd h:i:00",
//                	todayHighlight: true,
//    });
//     $('#valid_from').datepicker({
//                        autoclose: true,
//                        format: "yyyy-mm-dd h:i:00",
//                	todayHighlight: true,
//    });
//    
//    $('#ed_valid_from').datepicker({
//                        autoclose: true,
//                        format: "yyyy-mm-dd h:i:00",
//                	todayHighlight: true,
//    });
//     $('#ed_valid_to').datepicker({
//                        autoclose: true,
//                        format: "yyyy-mm-dd h:i:00",
//                	todayHighlight: true,
//    });
</script>

<script>
    function save_genoff()
{
 $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
  var o_name = $("#o_name").val();
  var o_perc = $("#o_perc").val();
  var amt_abv = $("#amt_abv").val();
  var max_amt = $("#max_amt").val();
  var usage_limit = $("#usage_limit").val();
  var code = $("#code").val();
  var valid_from = $("#valid_from").val();
  var valid_to = $("#valid_to").val();
  var offer_image = $("#offer_image").val();
  var desc = tinyMCE.get('desc').getContent();
  var firstDate = valid_from;
  var secondDate = valid_to;
  var date2 = secondDate.split("-").reverse().join("-");
  var date1 = firstDate.split("-").reverse().join("-");
  $("#descdetail").val(desc);
    if(o_name == '') {
       
         $("#o_name").addClass('input_focus');
          $("#o_name").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Offer Name');
        return false;
    }
     if(o_perc == '') {
        $("#o_perc").focus();
        $("#o_perc").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Offer %');
        return false;
    }
     if(o_perc != '' && parseFloat(o_perc) > 100)
         {
             $("#o_perc").focus();
              $("#o_perc").addClass('input_focus');
             $.Notification.autoHideNotify('error', 'bottom right','Invalid Offer Percent.');
             return false;
         }
     if(amt_abv == '') {
        $("#amt_abv").focus();
         $("#amt_abv").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Amount Above');
        return false;
    }
      if(max_amt == '') {
        $("#max_amt").focus();
        $("#max_amt").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Maximum Amount');
        return false;
    }
      if(usage_limit == '') {
        $("#usage_limit").focus();
        $("#usage_limit").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter No.Of Usage');
        return false;
    }
      if(valid_from == '') {
        $("#valid_from").focus();
         $("#valid_from").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Select Valid From Date');
        return false;
    }
     if(valid_to == '') {
        $("#valid_to").focus();
         $("#valid_to").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Select Valid To Date');
        return false;
    }
    
//    if(date1 > date2)
//        {
//            $("#valid_from").addClass('input_focus');
//            $("#valid_to").addClass('input_focus');
//            $.Notification.autoHideNotify('error', 'bottom right','Invalid Date Range.');
//             return false;
//         }

         if(offer_image != '')
          {
          if (!hasExtension('offer_image', ['.jpg','.jpeg', '.gif', '.png','.JPG', '.JPEG', '.GIF', '.PNG'])) {
          $("#offer_image").focus();
          $("#offer_image").addClass('input_focus');
          $.Notification.autoHideNotify('error', 'bottom right','Upload Gif or Jpg Images Only.');
          return false;
          }
          }
//         else
//          {
//              $("#offer_image").focus();
//              $("#offer_image").addClass('input_focus');
//              $.Notification.autoHideNotify('error', 'bottom right','Upload Offer Image.');
//              return false;
//          }

//     if(desc == '') {
//        $("#desc").focus();
//        $("#desc").addClass('input_focus');
//        $.Notification.autoHideNotify('error', 'bottom right','Enter Description ');
//        return false;
//    }
    
    if(true)
    {
        var formdata = new FormData($('#upload_form')[0]);

        $.ajax({
            method: "post",
            url : "api/add_gen_offers",
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
                   window.location.href = "general_offers";
                    swal({

                        title: "",
                        text: "Added Successfully",
                        timer: 4000,
                        showConfirmButton: false
                    });

                }
                else if((json_x.msg)=='already exist')
                {
                    window.location.href = "general_offers";
                    swal({

                        title: "",
                        text: "Already Exist",
                        timer: 4000,
                        showConfirmButton: false
                    });
                }
                else if((json_x.msg)=='invaliddate_range')
                {
                            $("#valid_from").addClass('input_focus');
                            $("#valid_to").addClass('input_focus');
                            $.Notification.autoHideNotify('error', 'bottom right','Invalid Date Range.');
                             return false;
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
//                alert(textStatus);
                $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
            }
        });
    }
    return true;

}

    function update_genoff()
{ 
 $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
  var edid = $("#edid").val();     
  var ed_o_name = $("#ed_o_name").val();
  var ed_o_perc = $("#ed_o_perc").val();
  var ed_amt_abv = $("#ed_amt_abv").val();
  var ed_max_amt = $("#ed_max_amt").val();
  var ed_usage_limit = $("#ed_usage_limit").val();
  var ed_code = $("#ed_code").val();
  var ed_valid_from = $("#ed_valid_from").val();
  var ed_valid_to = $("#ed_valid_to").val();
  var ed_offer_image = $("#ed_offer_image").val();
  var ed_desc = tinyMCE.get('ed_desc').getContent();
  var firstDate = ed_valid_from;
  var secondDate = ed_valid_to;
  var date2 = secondDate.split("-").reverse().join("-");
  var date1 = firstDate.split("-").reverse().join("-");

  $("#ed_descdetail").val(ed_desc);
    if(ed_o_name == '') {
       
         $("#ed_o_name").addClass('input_focus');
          $("#ed_o_name").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Offer Name');
        return false;
    }
     if(ed_o_perc == '') {
        $("#ed_o_perc").focus();
        $("#ed_o_perc").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Offer %');
        return false;
    }
     if(ed_o_perc != '' && parseFloat(ed_o_perc) > 100)
         {
             $("#ed_o_perc").focus();
              $("#ed_o_perc").addClass('input_focus');
             $.Notification.autoHideNotify('error', 'bottom right','Invalid Offer Percent.');
             return false;
         }
     if(ed_amt_abv == '') {
        $("#ed_amt_abv").focus();
         $("#ed_amt_abv").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Amount Above');
        return false;
    }
      if(ed_usage_limit == '') {
        $("#ed_usage_limit").focus();
        $("#ed_usage_limit").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Usage Limit');
        return false;
    }
      if(ed_max_amt == '') {
        $("#ed_max_amt").focus();
        $("#ed_max_amt").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Maximum Amount');
        return false;
    }
      if(ed_valid_from == '') {
        $("#ed_valid_from").focus();
         $("#ed_valid_from").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Select Valid From Date');
        return false;
    }
     if(ed_valid_to == '') {
        $("#ed_valid_to").focus();
         $("#ed_valid_to").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Select Valid To Date');
        return false;
    }
 

//     if(ed_desc == '') {
//        $("#ed_desc").focus();
//        $("#ed_desc").addClass('input_focus');
//        $.Notification.autoHideNotify('error', 'bottom right','Enter Description ');
//        return false;
//    }
    
    if(true)
    {
        var formdata = new FormData($('#upload_edform')[0]);

        $.ajax({
            method: "post",
            url : "api/edit_gen_offers",
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
                   window.location.href = "general_offers";
                    swal({

                        title: "",
                        text: "Updated Successfully",
                        timer: 4000,
                        showConfirmButton: false
                    });

                }
                else if((json_x.msg)=='already exist')
                {
                    //window.location.href = "general_offers";
                    swal({

                        title: "",
                        text: "Already Exist",
                        timer: 4000,
                        showConfirmButton: false
                    });
                }
                else if((json_x.msg)=='invaliddate_range')
                {
                            $("#ed_valid_from").addClass('input_focus');
            $("#ed_valid_to").addClass('input_focus');
            $.Notification.autoHideNotify('error', 'bottom right','Invalid Date Range.');
             return false;
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
//                alert(textStatus);
                $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
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

    </script>
    <script>
        var _URL = window.URL;
    $("#offer_image").change(function (e) {
         $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
    var file, img;

    if ((file = this.files[0])) {
        img = new Image();
        img.onload = function () {
            if (this.height > 225 || this.width > 225) 
            {
                $.Notification.autoHideNotify('error', 'bottom right','Height and Width must not exceed 225px.');
                $("#offer_image").val('');
                return false;
            }
            if (this.height < 225 || this.width < 225) 
            {
                $.Notification.autoHideNotify('error', 'bottom right','Height and Width must not be less than 225px.');
                $("#offer_image").val('');
                return false;
            }
            
//            alert("Width:" + this.width + "   Height: " + this.height);//this will give you image width and height and you can easily validate here....
        };
        img.src = _URL.createObjectURL(file);
    }
});


 $("#ed_offer_image").change(function (e) {
         $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
    var file, img;

    if ((file = this.files[0])) {
        img = new Image();
        img.onload = function () {
            if (this.height > 225 || this.width > 225) 
            {
                $.Notification.autoHideNotify('error', 'bottom right','Height and Width must not exceed 225px.');
                $("#ed_offer_image").val('');
                return false;
            }
            if (this.height < 225 || this.width < 225) 
            {
                $.Notification.autoHideNotify('error', 'bottom right','Height and Width must not be less than 225px.');
                $("#ed_offer_image").val('');
                return false;
            }
            
//            alert("Width:" + this.width + "   Height: " + this.height);//this will give you image width and height and you can easily validate here....
        };
        img.src = _URL.createObjectURL(file);
    }
});

function genofferedit(id,name,amtabove,max_amount,valid_to,offer_per,valid_from,active,description,coupon_code,image,url,usagelimit)
        {
         $("#edid").val(id);
         $("#ed_o_name").val(name);
         $("#ed_o_perc").val(offer_per);
         $("#ed_amt_abv").val(amtabove);
         $("#ed_max_amt").val(max_amount);
         $("#ed_usage_limit").val(usagelimit);
         $("#ed_code").val(coupon_code);
         $("#ed_valid_from").val(valid_from);
         $("#ed_valid_to").val(valid_to);
         tinymce.get("ed_desc").setContent(description);
         $("#editoldimg").val(image);
         $("#oldimg").val(url+''+image);
         $("#imgoffer").attr('data-img',url+''+image);
         $(".edit_view_offer_popup").show();
        }
     
function statuschange(id) {
            var ids = id;
            var data = {"ids": ids};
            $.ajax({
                method: "get",
                url: "genoffer_status",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
//                    alert(errorThrown);
                    $("#errbox").text(jqxhr.responseText);
                }
            });
        }
        
        function filter_genoffchange(val)
  {
      var frm = $('#frm_filter');
      var table = $('#example1').DataTable();
      $.ajax({
          method: "post",
          url   : "api/filter/genoffer",
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
                      var offername = toTitleCase(val.name);
                      var offerper = val.offer_per;
                      var status = val.active;
                      var code = val.coupon_code;
                      var amtabv = val.amtabove;
                      var maxamt = val.max_amount;
                      var valid_from = val.valid_from;
                      var valid_to = val.valid_to;
                      var image = val.image;
                      //var description = val.description;
                      var ed_desc = tinyMCE.get('ed_desc').getContent();

                      var url = $("#url").val();
                      var imgurl = url+image;
                      var id = val.id;
                      if(status == 'Y')
                        {
                            var statuss = 'checked';
                        }
                        
                        var newRow = '<tr><td>'+count+'</td>'+
                                   '<td><a href="#" class="table-action-btn button_table table_edit" onclick=\"return genofferedit(\''+id+'\',\''+offername+'\',\''+amtabv+'\',\''+maxamt+'\',\''+valid_to+'\',\''+offerper+'\',\''+valid_from+'\',\''+status+'\',\''+description+'\',\''+code+'\',\''+image+'\',\''+url+'\')\"><i class="md md-edit"></i></a></td>'+
                                   '<td>'+offername+'</td>'+
                                   '<td>'+offerper+'</td>'+
                                   '<td><div class="status_chck'+id+'"><div class="onoffswitch"> <input autocomplete="off"   type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch'+id+'"  '+statuss+'> <label class="onoffswitch-label" for="myonoffswitch'+id+'"> <span class="onoffswitch-inner" onclick="return  statuschange('+id+')"></span><span class="onoffswitch-switch" onclick="return  statuschange('+id+')"></span> </label></div></div></td>'+
                                   '<td>'+code+'</td>'+
                                   '<td>'+amtabv+'</td>'+
                                   '<td>'+maxamt+'</td>'+
                                   '<td>'+valid_from+'</td>'+
                                   '<td>'+valid_to+'</td>'+
                                   '<td><a rel="pop" data-img="' +imgurl+'" style="text-decoration: underline;"  data-original-title="" href="#">View</a></td>'+
                                   '<td>'+description+'</td>'+'</tr>';
                      var rowNode = table.row.add($(newRow)).draw().node();
                          $('a[rel=pop]').popover({
                html: true,
                trigger: 'hover',
                placement: 'left',
                content: function()
                {
                    return '<img src="'+$(this).data('img') + '" width="200" height="100"/>';
                }
            }); 
                       
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
        
       function delete_gen_offer(offerid)
         {
             swal({
                    title: "",
                    text: "Are you sure you want to delete?",
                    type: "info",
                    showCancelButton: true,
                    cancelButtonClass: 'btn-white btn-md waves-effect',
                    confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
                    confirmButtonText: 'Delete',
                    closeOnConfirm: false
                    }, function (isConfirm)
                   {
                     if (isConfirm)
                     {
                        $.ajax({
                        method: "post",
                        url: "api/remove_gen_offer/"+offerid,
                        cache: false,
                        crossDomain: true,
                        async: false,
                        success: function (result)
                        {
                           location.reload(true);
                                swal({

                                   title: "",
                                   text: "Deleted Successfully",
                                   timer: 4000,
                                   showConfirmButton: false
                               });

                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                           $("#errbox").text(jqXHR.responseText);
                       }
                       });
                     }
                   }); 
        } 
        
        
        
            </script>
@stop



   

@endsection




