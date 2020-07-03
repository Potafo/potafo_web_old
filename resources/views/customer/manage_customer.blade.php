@extends('layouts.app')
@section('content')
<style>
        .filter_text_box_row{margin-bottom: 6px}
     #example1_wrapper .dt-buttons{position: absolute;right: 80px;top: 15px;}
     #example1_wrapper .dt-buttons .btn-sm { padding: 6px 10px;box-shadow: 2px 3px 5px #d0d0d0;
    font-weight: bold;}
     .pagination_container_sec{width: 100%;height: auto;float: left}
     .pagination_container_sec ul{margin: 0;float: right}
    </style>
<div class="col-sm-12">
        <div class="col-sm-12">
                <ol class="breadcrumb">
						<li>
							<a href="{{url('index')}}">Dashboard</a>
						</li>
						
						<li class="active ms-hover">
							Customer List
						</li>
					</ol>
				</div>
        <div class="card-box table-responsive" style="padding: 8px 10px;">
             
              <div class="box master-add-field-box" >
             <div class="col-md-12 no-pad-left">
                <h3>Customer List  -  Total(<span id="count"></span>)
                    <form style="float: right;"  action="{{ URL::to('excel_download') }}" class="form-horizontal" method="get" enctype="multipart/form-data">

                            <input id="flt_name_xl" name="flt_name_xl" class="form-control" type="hidden">
                        <input id="flt_phone_xl"  name="flt_phone_xl" class="form-control" type="hidden">
                        <input id="flt_from_xl" data-date-format='dd-mm-yyyy'  name="flt_from_xl" class="form-control" type="hidden" >
                         <input id="flt_to_xl" data-date-format='dd-mm-yyyy' name="flt_to_xl" class="form-control" type="hidden">
                         <a href="#"><button class="btn btn-success btn-sm">Excel</button></a>

                        </form>
                </h3>
            </div>
                  
            
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
                       {!! Form::open(['url'=>'filter/customer_list', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'get']) !!}
            
                       <div class="main_inner_class_track" style="width: 20%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Name</label>
                                  <input id="flt_name" name="flt_name" class="form-control" type="text" >
                              </div>
                           </div>
                        </div>
                       <div class="main_inner_class_track" style="width: 20%;">
                           <div class="group">
                               <div style="position: relative">
                                   <label>Mobile</label>
                                   <input id="flt_phone"  name="flt_phone" class="form-control" type="text">
                               </div>
                           </div>
                       </div>
                       
                       
                <div class="main_inner_class_track" style="width: 20%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>From</label>
                                  <input id="flt_from" data-date-format='dd-mm-yyyy'  name="flt_from" class="form-control" type="text" >
                              </div>
                           </div>
                        </div>
                       <div class="main_inner_class_track" style="width: 20%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>To</label>
                                 <input id="flt_to" data-date-format='dd-mm-yyyy' name="flt_to" class="form-control" type="text" >
                              </div>
                           </div>
                        </div>
                       <div id="search_button" class="main_inner_class_track" style="width: 12% !important;float: left">
                           <div class="table-filter-cc" style="margin-top: 22px;">
                               <a href="#" onclick="refresh_filter()" style="margin-left:0;width: 80px " class="on-default followups-popup-btn btn btn-primary">Search</a>
<span id="searchcount"></span>
                            </div>
                        </div>
                        
                       {{ Form::close() }}
                       	

                   </div>  
            </div>
<!--            <div id="loading_gif">
                    <img src="{{asset('public/assets/images/loading.gif')}}">
              </div>-->
            <div class="table_section_scroll" id="customer_list">  
                
                  
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

    


@section('jquery')


    <script type="text/javascript">
        $(document).ready(function()
        {
            $('#flt_from').datepicker({
                       autoclose: true,
                       todayHighlight: true,
                       
    });
             $('#flt_to').datepicker({
                        autoclose: true,
                	todayHighlight: true,
                       
    });
    var date = new Date();
    var before_five = date.getDate()-5;
                        date.setDate(before_five);
                        var sdate = date.getDate();
                        var smonth = date.getMonth()+1;
                        var syear = date.getFullYear();//alert(sdate+"/"+smonth+"/"+syear);
                        var startdata = smonth+"-"+sdate+"-"+syear;
    $("#flt_from").datepicker().datepicker("setDate", new Date(startdata));
    $("#flt_to").datepicker().datepicker("setDate", new Date());
var val = '';
            filter_change();
            $("#current_count").val(1);
            $("#start_count").val(1);
            $("#end_count").val(1);
        
    } );
 
     $('.filter_sec_btn').on('click', function(e)
    {
        $('.filter_box_section_cc').toggleClass("diply_tgl");
        $("#restaurant_name").focus();
    });
</script>
<script>
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
   function refresh_filter() {
                $("#current_count").val(1);
                $("#start_count").val(1);
                $("#end_count").val(1);
                filter_change();
    }
     function filter_change()
    {
        var flt_name = $("#flt_name").val();
        var flt_phone = $("#flt_phone").val();
        var flt_from = $("#flt_from").val();
        var flt_to = $("#flt_to").val();
        
        $("#flt_name_xl").val(flt_name);
        $("#flt_phone_xl").val(flt_phone);
        $("#flt_from_xl").val(flt_from);
        $("#flt_to_xl").val(flt_to);
        
       var start_cnt = $("#start_count").val();
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
          url   : "api/filter/customer_list",
          data  : {"flt_name":flt_name,"flt_phone":flt_phone,"flt_from":flt_from,"flt_to":flt_to,"current_count":current_cnt},
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
              $("#customer_list").html(filter_result.filter_data);
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
              
//            $('#example1').DataTable(
//           {
//            scrollX: false,
//            dom: "Bfrtip",
//            buttons: [{
//                extend: "csv",
//                className: "btn-sm"
//            }, {
//                extend: "excel",
//                className: "btn-sm"
//            }],
//            scrollCollapse: true,
//            "searching": false,
//            "ordering": false,
//            "info": false,
//             "paging": false,
//        } );
//      $("#loading_gif").css('display','none');
//      $("#customer_list").css('display','block');
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




