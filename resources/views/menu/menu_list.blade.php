@extends('layouts.app')
@section('title','Potafo - Menu')
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
   .popover {width: 180px;height: 120px;}.popover img{width:100%}
.timing_popup_cc_pop{width: 100%;
    height: 100%;
    position: fixed;
    left: 0;
    top: 0;
    background-color: rgba(0,0,0,0.7);
    z-index: 999;
    display: none;
    overflow: auto;}
    .loader_staff_sec{position: absolute; width: 100%; height: 100%; left: 0; top: 0; background-color: rgba(255, 255, 255, 0.62);
    text-align: center; padding-top: 20%;} .loader_staff_sec img{width:185px} 
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
						
						<li class="active ms-hover">
							<a href="{{  url('manage_restaurant') }}">{{$restaurant_name[0]->name}}</a>
						</li>
                    <li class="active ms-hover">
							Menu
						</li>
					</ol>
		</div>
        <div class="col-sm-12">
            <a href="{{ url('restaurant_edit/'.$restaurant_id) }}"><div class="potafo_top_menu_sec">About</div></a>
            <a ><div class="potafo_top_menu_sec potafo_top_menu_act">Menu</div></a>
            <a href="{{ url('category/list/'.$restaurant_id) }}"><div class="potafo_top_menu_sec">Category</div></a>
            <a href="{{ url('menu/review/'.$restaurant_id) }}"><div class="potafo_top_menu_sec">Review</div></a>
            <a href="{{ url('menu/tax/'.$restaurant_id) }}"><div class="potafo_top_menu_sec">Tax %</div></a>
          </div>
        <div class="card-box table-responsive" style="padding: 8px 10px;">
             
              <div class="box master-add-field-box" >
             <div class="col-md-6 no-pad-left">
                <h3>Menu</h3>
            </div>    
                  
            <div class="col-md-1 no-pad-left pull-right">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a href="{{ url('menu/add/'.$restaurant_id) }}"> <button type="submit" style="margin-left:0" class="on-default followups-popup-btn btn btn-primary" >Add New</button></a>
                </div>
                 </div>
            </div>
               <div class=" pull-right">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a title="Filter" href="#"> <button type="submit"  style="margin-right: 10px;" class="on-default followups-popup-btn btn btn-primary filter_sec_btn" ><i class="fa fa-filter" aria-hidden="true"></i></button></a>

                </div>
                   
                 </div>
            </div>
             <div class=" pull-right">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a style="background-color: #90921b !important;border: 1px #90921b solid !important;" title="Menu Upload" href="#" class="on-default mn_uplod btn btn-primary"><i class="fa fa-upload" aria-hidden="true"></i></a>
                </div>
                    
                 </div>
            </div>
                  <div class=" pull-right">
                <div class="table-filter" style="margin-top: 4px;">

                    <div class="table-filter-cc">
                    <a style="background-color: #90921b !important;border: 1px #90921b solid !important;" title="Menu Download" onclick="return menudownload('{{ $restaurant_id }}');" class="on-default followups-popup-btn btn btn-primary"><i class="fa fa-download" aria-hidden="true"></i></a>
                </div>
                 </div>
            </div>

            </div>
            <div class="timing_popup_cc_pop loadin_popup_loader " id="loadin_popup_loader" style="display: none;background-color:rgba(255,255,255,0.5)">
                <div class="loader_staff_sec" id='loadingmessage' style="display:block;" >
                                    <img src='../../public/assets/images/main-loader.gif'/>
            </div> 
            </div>
            <div class="filter_box_section_cc diply_tgl">
<!--                <div class="filter_box_section">FILTER</div>-->
                   <div class="filter_text_box_row">
                       {!! Form::open(['url'=>'filter/menu', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'post']) !!}
                      <input type="hidden" name="resid" id="resid" value="{{ $restaurant_id }}">
                       <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Name</label>
                                  <input class="form-control" id="menuname" name="menuname" onkeyup="return filter_change(this.value)" type="text">
                              </div>
                           </div>
                        </div>
                       <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Category</label>
                                  <input class="form-control" id="menu_categry" name="menu_categry" onkeyup="return filter_change(this.value)" type="text">
                              </div>
                           </div>
                        </div>
                       <div class="main_inner_class_track" onkeyup="return filter_change(this.value)" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Subcategory</label>
                                  <input class="form-control" id="menu_subcat" name="menu_subcat" onkeyup="return filter_change(this.value)" type="text">
                              </div>
                           </div>
                        </div>
      <!--             <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Final Rate</label>
                                  <input class="form-control" type="text">
                              </div>
                           </div>
                        </div> -->
                       {{ Form::close() }}
                   </div>
            </div>
            <div >
                <table id="datatable-1" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th style="min-width:3px">Slno</th>
                    <th style="min-width:130px">Name</th>
                    <th style="min-width:130px">Category</th>
                    <th style="min-width:120px">Subcategory</th>
      <!--         <th style="min-width:100px">Status</th>-->
                    <th style="min-width:60px">Most Selling</th>
                    <th style="min-width:100px">Time of Avl</th>
                    <th style="min-width:40px">Status</th>
					<th style="min-width:40px">Image</th>
                    <th style="min-width:10px"></th>
                </tr>
                </thead>
                <tbody>
                @if(count($details)>0)
                    @foreach($details as $key=>$item)
                    <tr>
                    <td  style="min-width:3px !important;">{{ $key+1 }}</td>
                    <td style="min-width:130px !important;">{{ title_case($item->name) }}</td>
                    <td style="min-width:130px !important;white-space:nowrap;">@if(isset($item->category) && $item->category != 'null'){{ mb_strimwidth(implode(",",json_decode($item->category)), 0, 25, "...") }}@endif</td>
                    <td style="min-width:120px !important;white-space:nowrap;">@if(isset($item->subcategory) &&  $item->subcategory != 'null'){{ mb_strimwidth(implode(",",json_decode($item->subcategory)),0,25,"...") }}@endif</td>
     <!--           <td>Yes</td>  -->
                    <!--<td style="min-width:60px !important;">@if(isset($item->days) && $item->days != 'null'){{ mb_strimwidth(implode(",",json_decode($item->days)),0,15,"...") }}@endif</td>-->
                    <td style="text-align: left;width:7%">
                        <div class="status_chck{{ $item->menu_id}},{{ $item->rest_id}}">
                            <div class="onoffswitch">
                                <input type="checkbox" name="onoffswitch2" class="onoffswitch-checkbox" id="myonoffswitch2{{ $item->menu_id}},{{ $item->rest_id}}" @if( $item->m_most_selling == 'Y') checked @endif>
                                    <label class="onoffswitch-label" for="myonoffswitch2{{ $item->menu_id}},{{ $item->rest_id}}">
                                        <span class="onoffswitch-inner" onclick="return  statuschange('{{ $item->menu_id}}','{{ $item->rest_id}}','MS')"></span>
                                        <span class="onoffswitch-switch" onclick="return  statuschange('{{ $item->menu_id}}','{{ $item->rest_id}}','MS')"></span>
                                    </label>
                            </div>
                        </div>
                    </td>
                    
                    <td style="min-width:100px !important;">@if($item->from_time != '' || $item->to_time !='' ){{ $item->from_time.' - '.$item->to_time }}@endif</td>
<!--                    <td style="min-width:40px;position:relative">
                        @if(isset($item->img) && $item->img != '')
                        <a class="btn" rel="popover" data-img="{{ $siteUrl.$item->img }}" style="text-decoration: underline;">View</a>
                        @endif
                    </td>-->
                    <td style="text-align: left;width:7%">
                        <div class="status_chck{{ $item->menu_id}},{{ $item->rest_id}}">
                            <div class="onoffswitch">
                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch{{ $item->menu_id}},{{ $item->rest_id}}" @if( $item->status == 'Y') checked @endif>
                                    <label class="onoffswitch-label" for="myonoffswitch{{ $item->menu_id}},{{ $item->rest_id}}">
                                        <span class="onoffswitch-inner" onclick="return  statuschange('{{ $item->menu_id}}','{{ $item->rest_id}}','S')"></span>
                                        <span class="onoffswitch-switch" onclick="return  statuschange('{{ $item->menu_id}}','{{ $item->rest_id}}','S')"></span>
                                    </label>
                            </div>
                        </div>
                    </td>
					<td  style="min-width:10px">
						<a class="btn tbl_view_sec_btn" rel="popover" data-img="@if(isset($item->m_image) && $item->m_image != ''){{  $siteUrl.$item->m_image }}@endif" href="" style="text-decoration: underline;">View</a>
					</td>
					<td  style="min-width:10px"><a  class="btn button_table"  onclick="popupimageupload('{{ $restaurant_id}}','{{$item->menu_id}}')"><i class="fa fa-upload"></i></a></td>
                    <td  style="min-width:10px"><a  class="btn button_table" href="{{ url('menu/edit/'.$restaurant_id.'/'.$item->menu_id) }}" onclick="oneditclick()"><i class="fa fa-pencil"></i></a></td>
                    </tr>
                    @endforeach
                @endif
                </tbody>


            </table>
           </div>  
        </div>
    </div>

<div id="urls"></div>


  
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    
 <div class="timing_popup_cc menu_upload_popup" style="display: none;">
    <div class="timing_popup" style="width: 390px;">
        <div class="timing_popup_head">Upload Menu
            <div class="timing_popup_cls"><img src="{{ asset('public/assets/images/cancel.png') }}"></div>
        </div>
        {!! Form::open(['enctype'=>'multipart/form-data','url'=>'api/menu/upload', 'name'=>'frm_upload', 'id'=>'frm_upload','method'=>'post',]) !!}
        <input type="hidden" name="resid" id="resid" value="{{ $restaurant_id }}">
        <div class="timing_popup_contant">
            <div class="restaurant_more_detail_row">
                    <div class="restaurant_more_detail_text" style="width:83%;margin-right:2%">
                        <span class="restaurant_more_detail_text_nm" style="margin-bottom:5px;">Upload Menu</span>
                        <input style="padding-left:5px;" type="file" id="upld_file" name="upld_file">
                    </div>

                <div class="restaurant_more_detail_text" style="width:15%;">
                    <span style="margin-top: 23px;" class="add_time_btn_pop"><i class="fa fa-upload" aria-hidden="true"></i></span>
                </div>    
                </div>
            {!! Form::close() !!}
            
            
            
            <div class="col-sm-12" style="display: none;">
             <a class="staff-add-pop-btn staff-add-pop-btn-new" style="float: right;">SAVE</a>
            
        </div>
            
        </div>
    </div>
</div>
    <div id="urls"></div>
    
<div class="timing_popup_cc" id="imageupload_details_popup">
        <div class="timing_popup" >
            <div class="timing_popup_head">Image Upload <span id=""> </span>
                <div onclick='closebutton()' class="timing_popup_cls_f"><img src="{{asset('public/assets/images/cancel.png') }}"></div>
                
            </div>
            <div class="timing_popup_contant">
                <div class="restaurant_more_detail_row">
 {!! Form::open([ 'enctype'=>'multipart/form-data','name'=>'frm_upload_f', 'id'=>'frm_upload_f','method'=>'get']) !!}
                    <div class="restaurant_more_detail_text" style="width:33%;margin-right:2%">
                        <span class="restaurant_more_detail_text_nm">Image</span>
                       <input style="padding-left:5px;" type="file" id="upld_file_f" name="upld_file_f" class="form-control">
       
                    </div>

                    <input type="text" id="menuid" name="menuid" hidden="">
					<input type="text" id="restaurant_id" name="restaurant_id" hidden="">
                    <div class="restaurant_more_detail_text" style="width:15%;">
                        <div class="add_time_btn_pop_f"  onclick="addimage()" >ADD</div>
                    </div>
                    
                     {{ Form::close() }}
                </div>




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
    <link href="{{ asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('public/assets/dark/plugins/custombox/css/custombox.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script>
	function addimage()
    { var upload_file = $("#upld_file_f");
		 if(upload_file.val() != '')
        {
            if (!hasExtension('upld_file_f', ['.jpg','.png','.jpeg','.pdf'])) {
                upload_file.addClass('input_focus');
                $.Notification.autoHideNotify('error', 'bottom right','File Format Not Supported');
                return false;
            }
        }
		var formdata = new FormData($('#frm_upload_f')[0]);
             try
             {
                 
            $.ajax({
                type: "POST",
                url: "api/add_menu_image",
                data: formdata,
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                processData: false,
                contentType: false,
                success: function (result) {
//                   / var json_x = JSON.parse(result);
                    //alert(result);
                    if ((result) == 'success') {
                        swal({

                            title: "",
                            text: "Added Successfully",
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
                    else if ((result) == 'already exist') {
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
        }catch(e)
        {
          alert("Error Name: " + e.name + ' Error Message: ' + e);  
        }
	}
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
                "bStateSave": true,
                "lengthChange": false,
                "columnDefs": [{
                    paging: false
                }],
            });
            t.draw( false );
        } );
    </script>
<script>
    
    $(".mn_uplod").click(function(){
        $(".menu_upload_popup").show();
    });

    $(".timing_popup_cls").click(function()
    {
        $(".menu_upload_popup").hide();
    });
	$(".timing_popup_cls_f").click(function()
    {
        $(".imageupload_details_popup").hide();
    });

    
   /* $(document).ready(function()
    {
        var t = $('#datatable-1').DataTable({
            scrollX: true,
            scrollCollapse: true,
            "searching": false,
            "ordering": false,
            "info": false,
            "deferLoading": 0,
            "lengthChange": false,
            "columnDefs": [{
                paging: false
            } ],
        });
       
    } );*/

    $('.filter_sec_btn').on('click', function(e)
    {
        $('.filter_box_section_cc').toggleClass("diply_tgl");
    });

    function filter_change(val)
    {
        var frm = $('#frm_filter');
        var table = $('#datatable-1').DataTable();
        $.ajax({
            method: "post",
            url   : "../../api/filter/menu",
            data  : frm.serialize(),
            cache : false,
            crossDomain : true,
            async : false,
            dataType :'text',
            success : function(result)
            {
                 var rows = table.rows().remove().draw();
                var json_x= JSON.parse(result);
                if(parseInt(json_x.length) > 0)
                {
                    $.each(json_x, function (i, val)
                    {
                        var count = i + 1;
                        var menuname = toTitleCase(val.name);
                        var carray=  JSON.parse(val.category);
                        var result = carray.join(",");
                        var category = add3Dots(JSON.stringify(result),25);
                        var subcatarr= JSON.parse(val.subcategory);
                        var rsltsub = subcatarr.join(",");
                        var subcategory = add3Dots(JSON.stringify(rsltsub),25);
                        var daysarr = JSON.parse(val.days);
                        var rsltday = daysarr.join(",");
                        var days = add3Dots(JSON.stringify(rsltday),25);
                        var from_time = val.from_time;
                        var to_time = val.to_time;
                        var time = from_time+'-'+to_time
                        var phoneno = val.code+''+val.mob;
                        var m_rest_id = val.m_rest_id;
                        var menuid = val.menuid;
                        var status = val.status;
                        var m_most_selling = val.m_most_selling;
                        if(val.m_most_selling == 'Y')
                        {
                            var m_most_selling = 'checked';
                        }
                        if(val.status == 'Y')
                        {
                            var status = 'checked';
                        }
                        var newRow = '<tr><td style="min-width:3px;">'+count+'</td>'+
                                '<td style="min-width:130px;text-align: left;">'+menuname+'</td>'+
                                '<td style="min-width:100px;text-align: left;">'+category+'</td>'+
                                '<td style="min-width:100px;text-align: left;">'+subcategory+'</td>'+                                                                                                                                                                                                                                                                                                    
                                '<td style="text-align: left;width:7%"><div class="status_chck'+menuid+','+m_rest_id+'"><div class="onoffswitch"> <input type="checkbox" name="onoffswitch2" class="onoffswitch-checkbox" id="myonoffswitch2'+menuid+','+m_rest_id+'" '+m_most_selling+'> <label class="onoffswitch-label" for="myonoffswitch2'+menuid+','+m_rest_id+'"> <span class="onoffswitch-inner" onclick="return  statuschange(\''+menuid+'\',\''+m_rest_id+'\',\'MS\')"></span><span class="onoffswitch-switch" onclick="return  statuschange(\''+menuid+'\',\''+m_rest_id+'\',\'MS\')"></span> </label></div></div></td>'+
                                '<td style="min-width:100px;">'+time+'</td>'+
                                '<td style="text-align: left;width:7%"><div class="status_chck'+menuid+','+m_rest_id+'"><div class="onoffswitch"> <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch'+menuid+','+m_rest_id+'" '+status+'> <label class="onoffswitch-label" for="myonoffswitch'+menuid+','+m_rest_id+'"> <span class="onoffswitch-inner" onclick="return  statuschange(\''+menuid+'\',\''+m_rest_id+'\',\'S\')"></span><span class="onoffswitch-switch" onclick="return  statuschange(\''+menuid+'\',\''+m_rest_id+'\',\'S\')"></span> </label></div></div></td>'+
                                '<td  style="min-width:10px"><a  class="btn button_table" onclick="menueditview('+m_rest_id+','+menuid+')"><i class="fa fa-pencil"></i></a></td>'+
                                '</tr>';
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
    function menueditview(restid,menuid)
    {
        window.location.href = "../edit/"+restid+"/"+menuid;
    }

    //Upload Excel file for menu upload

    $(".add_time_btn_pop").click(function()
    {
        $('.notifyjs-wrapper').remove();
        var upload_file = $("#upld_file");
        upload_file.removeClass('input_focus');
        if(upload_file.val() == '')
        {
            upload_file.addClass('input_focus');
            $.Notification.autoHideNotify('error', 'bottom right','Upload File.');
            return false;
        }
        else if(upload_file.val() != '')
        {
            if (!hasExtension('upld_file', ['.xls', '.csv'])) {
                upload_file.addClass('input_focus');
                $.Notification.autoHideNotify('error', 'bottom right','Upload Excel File Only.');
                return false;
            }
        }
        $(".menu_upload_popup").hide();
            $('.loadin_popup_loader').css("display", "block");
        if(true)
        {
            
            
            var formdata = new FormData($('#frm_upload')[0]);
            $.ajax({
                method: "post",
                url: "../../api/menu/upload",
                data: formdata,
                cache: false,
                crossDomain: true,
                async: true,
                processData: false,
                contentType: false,
                dataType: 'text',
                success: function (result)
                {
                    var json_x = JSON.parse(result);
                    if(json_x.msg ==  'success')
                    {
                        swal({

                            title: "",
                            text: "Menu Uploaded Successfully",
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $(".menu_upload_popup").hide();
                        $('.loadin_popup_loader').css("display", "none");
                        window.location.reload();
                    }
                    else if ((json_x.msg) == 'error')
                    {
                        $.Notification.autoHideNotify('error', 'bottom right',json_x.data);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                     $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }
        return true;
    });
</script>
<script>
   $(document).ready(function()
    {
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
   
   function statuschange(menuid,restid,type) {
            var data = {"menuid": menuid,'restid':restid,'type':type};
            $.ajax({
                method: "get",
                url: "../../most_selling",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
//                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
//                    alert(errorThrown);
                    $("#errbox").text(jqxhr.responseText);
                }
            });
        }

    function oneditclick()
    {
        var info = $('#datatable-1').DataTable().page.info();
       // alert(info.page);
    }
	 function popupimageupload(restaurant,menuid) 
        {
           $("#imageupload_details_popup").show();
           var restaurant = restaurant;
          var menuid =menuid;
          $("#restaurant_id").html(restaurant);
          $("#menuid").html(menuid);
          
         /* var data = {"menuid": cityid};
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
            });*/
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

</script>

@stop

@endsection




