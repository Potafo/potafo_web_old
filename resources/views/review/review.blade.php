@extends('layouts.app')
@section('content')

 <style>
        .filter_text_box_row{margin-bottom: 6px}
     .rating{pointer-events: none}
     #example1 tbody{min-height: 400px}
        .pagination_container_sec{width: 100%;height: auto;float: left}
        .pagination_container_sec ul{margin: 0;float: right}
    </style>
<div class="col-sm-12">
        <div class="col-sm-12">
                <ol class="breadcrumb">
						<li>
							<a href="{{  url('index') }}">Dashboard</a>
						</li>
                    <li class="active ms-hover">
							Customer Review
						</li>
					</ol>
				</div>
     <div class="card-box table-responsive" style="padding: 8px 10px;">
             
              
                 <div class="col-md-6 no-pad-left">
                <h3>Customer Review</h3>
            </div> 
            
         <div class=" pull-right" style="display:none">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a title="Filter" href="#"> <button type="submit"  style="margin-right: 10px;" class="on-default followups-popup-btn btn btn-primary filter_sec_btn" ><i class="fa fa-filter" aria-hidden="true"></i></button></a>
                </div>
                   
                 </div>
            </div>
                  
            
            
            <div class="filter_box_section_cc">
                   <div class="filter_text_box_row">
                       {!! Form::open(['url'=>'filter/review', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'post']) !!}
                       <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>From</label>
                                  <input id="flt_from" data-date-format='dd-mm-yyyy' onchange="return filter_change(this.value)" name="flt_from" class="form-control" type="text" >
                              </div>
                           </div>
                        </div>
                       <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>To</label>
                                 <input id="flt_to" name="flt_to" data-date-format='dd-mm-yyyy' onchange="return filter_change(this.value)" class="form-control" type="text">
                              </div>
                           </div>
                        </div>
                       <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Name</label>
                                 <input class="form-control" type="text" onkeyup="return filter_change(this.value)" id="flt_name" name="flt_name">
                              </div>
                           </div>
                        </div>
                       <div class="main_inner_class_track" style="width: 25%;">
                           <div class="group">
                               <div style="position: relative">
                                   <label>Restaurant Name</label>
                                   <select id="flt_restname" name="flt_restname" class="form-control" onchange="return filter_change(this.value)" >
                                       <option value="">Select Restaurant</option>
                                       @foreach($restaurant as $key=>$list)
                                           <option value="{{$list->id}}">{{$list->name}}</option>
                                       @endforeach
                                   </select>                               </div>
                               <span id="searchcount"></span>

                           </div>
                       </div>
                       <input type="hidden" name="start_count" id="start_count"  />
                       <input type="hidden" name = "current_count" id="current_count"  />
                       <input type="hidden" name="end_count" id="end_count"  />
                       {{ Form::close() }}

                   </div>  
            </div>
            <div class="table_section_scroll" id="reviewlist">
         {{--   <div class="table-responsive">
                  <table class="table table-hover mails m-0 table table-actions-bar" id="example1">
                       <thead>
						<tr>
							<th style="min-width:50px;">#</th>
                            <th>Restaurant Name</th>
							<th style="min-width:100px;">Name</th>
							<th style="min-width:10px;">Rating</th>
							<th style="min-width:300px;">Review</th>
							<th style="min-width:250px;">Date</th>
                            <th style="min-width:10px;">Status</th>
							</tr>
		        </thead>
											
                        <tbody>--}}
                   {{--@if(isset($details))
                   @if(count($details)>0)
                    @foreach($details as $key=>$item)
                    @if($item->star>0)
                               <tr class="active">
                                   <td style="min-width:50px;">{{ $key+1 }}</td>
                                   <td>@if(isset($item->restaurant)){{title_case($item->restaurant)}}@endif</td>
                                   <td style="min-width:100px;">@if(isset($item->name)) {{ title_case($item->name) }}@endif</td>
                                   <td style="min-width:10px;">@if(isset($item->star)) {{ $item->star }}@endif</td>
                                   <td>@if(isset($item->review)) {{ title_case($item->review) }}@endif</td>
                                   <td style="min-width:250px;">@if(isset($item->entry_date)) {{ $item->entry_date }}@endif</td>
<!--                                    <td style="min-width:10px;"></td>-->
                                   <td>
                                       <div class="status_chck{{ $item->id}}">
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch{{$item->id}}" @if( $item->status == 'Y') checked @endif>
                                                <label class="onoffswitch-label" for="myonoffswitch{{$item->id}}">
                                                    <span class="onoffswitch-inner" onclick="return  statuschange('{{$item->id}}','{{$item->status}}')"></span>
                                                    <span class="onoffswitch-switch" onclick="return  statuschange('{{$item->id}}','{{$item->status}}')"></span>
                                                </label>
                                            </div>
                                       </div>
                                   </td>
                                 </tr>     
                    @endif                           
                   @endforeach
                   @endif
                   @endif--}}
                                          {{--  </tbody>
                                        </table>--}}
                                    </div>

         <div class="pagination_container_sec">
             <ul class="pagination" id="pagination">
                 <li class="paginate_button previous disabled" id="pagn_prev" ><a href="#">Previous</a></li>
                 <li class="paginate_button" id="pagn_start" ><a href="#">1</a></li>
                 <li class="paginate_button " id="pagn_midle" ><a href="#">2</a></li>
                 <li class="paginate_button " id="pagn_end" ><a href="#">3</a></li>
                 <li class="paginate_button next " id="pagn_next" ><a href="#">Next</a></li>
             </ul>
         </div>
<input type="hidden" id="url" name="url">

     </div>
          </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    


@section('jquery')

     
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
    function filter_change(val)
  {


      var start_cnt = $("#start_count").val();
      var current_cnt = $("#current_count").val();
      var end_cnt = $("#end_count").val();
      var s='';
      var m ='';
      var e='';
      var prev='p';
      var next="n";
      $("#pagination").html('');
      $("#reviewlist").html('');
      var frm = $('#frm_filter');
      var table = $('#example1').DataTable();
      $.ajax({
          method: "post",
          url   : "api/filter/review",
          data  : frm.serialize(),
          cache : false,
          crossDomain : true,
          async : false,
          dataType :'text',
          success : function(result)
          {
              $("#current_count").val('');
              var filter_result = JSON.parse(result);
              var cust_count = filter_result.count;
              $("#count").html(cust_count);
              $("#searchcount").html('&nbsp;<b>'+filter_result.searchcount+'</b>');
              if(parseInt(filter_result.searchcount)>0) {
                  $("#reviewlist").html(filter_result.filter_data);
                  if (filter_result.data_count == 0) {
                      end_cnt = 1;
                  }
                  else {
                      end_cnt = filter_result.data_count;
                  }
                  $("#end_count").val(end_cnt);
                  if (current_cnt == '') {
                      current_cnt = 1;
                  }
                  if (start_cnt == '') {
                      start_cnt = 1;
                  }
                  $(".paginate_button").removeClass("active");
                  $(".paginate_button").removeClass("disabled");
                  if (current_cnt == start_cnt && end_cnt == 1) {
                      $("#pagination").html(' <li class="paginate_button previous disabled" id="pagn_1" ><a href="#">Previous</a></li>' +
                              '<li class="paginate_button active" id="pagn_2" onclick="search_filter(1)"><a href="#">1</a></li>' +
                              '<li class="paginate_button disabled" id="pagn_3" ><a href="#">2</a></li>' +
                              '<li class="paginate_button disabled" id="pagn_4" ><a href="#">3</a></li>' +
                              '<li class="paginate_button next disabled" id="pagn_5" ><a href="#">Next</a></li>');
                  }
                  else if (current_cnt == start_cnt && end_cnt == 2) {
                      $("#pagination").html(' <li class="paginate_button previous disabled" id="pagn_1" ><a href="#">Previous</a></li>' +
                              '<li class="paginate_button active" id="pagn_2" onclick="search_filter(1)"><a href="#">1</a></li>' +
                              '<li class="paginate_button " id="pagn_3" onclick="search_filter(2)"><a href="#">2</a></li>' +
                              '<li class="paginate_button disabled" id="pagn_4" ><a href="#">3</a></li>' +
                              '<li class="paginate_button next " id="pagn_5" onclick="search_filter_btn(2)"><a href="#">Next</a></li>');
                  }
                  else if (current_cnt == 2 && end_cnt == 2) {
                      $("#pagination").html(' <li class="paginate_button previous " id="pagn_1" onclick="search_filter_btn(1)" ><a href="#">Previous</a></li>' +
                              '<li class="paginate_button " id="pagn_2" onclick="search_filter(1)"><a href="#">1</a></li>' +
                              '<li class="paginate_button active" id="pagn_3" onclick="search_filter(2)"><a href="#">2</a></li>' +
                              '<li class="paginate_button disabled" id="pagn_4" ><a href="#">3</a></li>' +
                              '<li class="paginate_button next disabled" id="pagn_5" ><a href="#">Next</a></li>');
                  }
                  else if (current_cnt == start_cnt) {
                      $("#pagination").html(' <li class="paginate_button previous disabled" id="pagn_1"  ><a href="#">Previous</a></li>' +
                              '<li class="paginate_button active" id="pagn_2" onclick="search_filter(1)" ><a href="#">1</a></li>' +
                              '<li class="paginate_button " id="pagn_3" onclick="search_filter(2)"><a href="#">2</a></li>' +
                              '<li class="paginate_button " id="pagn_4" onclick="search_filter(3)"><a href="#">3</a></li>' +
                              '<li class="paginate_button next " id="pagn_5" onclick="search_filter_btn(2)"><a href="#">Next</a></li>');
                  } else if (current_cnt == end_cnt) {
                      s = parseInt(current_cnt) - 2;
                      m = parseInt(current_cnt) - 1;
                      e = current_cnt;
                      $("#pagination").html(' <li class="paginate_button previous " id="pagn_1" onclick="search_filter_btn(1)" ><a href="#">Previous</a></li>' +
                              '<li class="paginate_button" id="pagn_2" onclick="search_filter(' + s + ')"><a href="#">' + s + '</a></li>' +
                              '<li class="paginate_button " id="pagn_3" onclick="search_filter(' + m + ')"><a href="#">' + m + '</a></li>' +
                              '<li class="paginate_button active" id="pagn_4" onclick="search_filter(' + e + ')"><a href="#">' + e + '</a></li>' +
                              '<li class="paginate_button next disabled" id="pagn_5" ><a href="#">Next</a></li>');
                  }
                  else {
                      s = parseInt(current_cnt) - 1;
                      m = parseInt(current_cnt);
                      e = parseInt(current_cnt) + 1;
                      $("#pagination").html(' <li class="paginate_button previous " id="pagn_1" onclick="search_filter_btn(1)"><a href="#">Previous</a></li>' +
                              '<li class="paginate_button" id="pagn_2" onclick="search_filter(' + s + ')"><a href="#">' + s + '</a></li>' +
                              '<li class="paginate_button active" id="pagn_3" onclick="search_filter(' + m + ')"><a href="#">' + m + '</a></li>' +
                              '<li class="paginate_button " id="pagn_4" onclick="search_filter(' + e + ')"><a href="#">' + e + '</a></li>' +
                              '<li class="paginate_button next " id="pagn_5" onclick="search_filter_btn(2)"><a href="#">Next</a></li>');
                  }
              }
              //search_filter(1);
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
          }
      });
      return true;
  }
  
  function statuschange(id,status) {
      var url = $('#url').val();
            var ids = id;
            var data = {"ids": ids,"status": status};
            $.ajax({
                method: "get",
                url: "review_status",
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

    </script>

    <script type="text/javascript">
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


      $('#flt_from').datepicker({
                       autoclose: true,
                       todayHighlight: true,

orientation: "bottom left"

    });
             $('#flt_to').datepicker({
                        autoclose: true,
                	todayHighlight: true,

orientation: "bottom left"

    });
            $("#flt_from").datepicker().datepicker("setDate", new Date());
            $("#flt_to").datepicker().datepicker("setDate", new Date());

            filter_change();

    } );
</script>

@stop



   

@endsection




