@extends('layouts.app')
@section('content')

 <style>
        .filter_text_box_row{margin-bottom: 6px}
     .rating{pointer-events: none}
     #example1 tbody{min-height: 400px}
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
                           </div>
                       </div>

                       {{ Form::close() }}

                   </div>  
            </div>
            <div class="table_section_scroll">  
            <div class="table-responsive">
                  <table class="table table-hover mails m-0 table table-actions-bar" id="example1">
                       <thead>
						<tr>
							<th style="min-width:50px;">#</th>
                                                        <th style="min-width:10px;">Status</th>
                                                        <th style="min-width:250px;">Date</th>
                                                         <th>Restaurant Name</th>
							<th style="min-width:100px;">Cust Name</th>
							<th style="min-width:10px;">Rating</th>
							<th style="min-width:300px;">Review</th>
							
                            
							</tr>
		        </thead>
											
                        <tbody>
                   @if(isset($details))
                   @if(count($details)>0)
                    @foreach($details as $key=>$item)
                    @if($item->star>0)
                               <tr class="active">
                                   <td style="min-width:50px;">{{ $key+1 }}</td>
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
                                   <td style="min-width:250px;">@if(isset($item->entry_date)) {{ $item->entry_date }}@endif</td>
                                   <td>@if(isset($item->restaurant)){{title_case($item->restaurant)}}@endif</td>
                                   <td style="min-width:100px;">@if(isset($item->name)) {{ title_case($item->name) }}@endif</td>
                                   <td style="min-width:10px;">@if(isset($item->star)) {{ $item->star }}@endif</td>
                                   <td>@if(isset($item->review)) {{ title_case($item->review) }}@endif</td>
                                   
<!--                                    <td style="min-width:10px;"></td>-->
                                   
                                 </tr>     
                    @endif                           
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
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    


@section('jquery')

     
<script>
    function filter_change(val)
  {
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
               $("#urls").text(result);
                var rows = table.rows().remove().draw();
                var json_x= JSON.parse(result);
                if(parseInt(json_x.length) > 0) {
                $.each(json_x, function (i, val)
               {
                         var count = i + 1;
                        var fltname = toTitleCase(val.name);
                        var rating = val.star;
                        var id = val.id;
                        var review = val.review;
                        var edate =  val.entry_date;

                      var myDate = new Date(edate);
                      console.log(myDate);
                      var d = myDate.getDate();
                      var m =  myDate.getMonth();
                      m += 1;
                      var y = myDate.getFullYear();
                      var newdate=(d+ "-" + m + "-" + y);
//
                      if(val.status == 'Y')
                        {
                            var status = 'checked';
                        }

                      var newRow = '<tr><td style="min-width:50px;">'+count+'</td>'+
                              '<td style="min-width:10px;><div class="status_chck'+id+'"><div class="onoffswitch"> <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch'+id+'"  '+status+'> <label class="onoffswitch-label" for="myonoffswitch'+id+'"> <span class="onoffswitch-inner" onclick="return  statuschange('+id+')"></span><span class="onoffswitch-switch" onclick="return  statuschange('+id+')"></span> </label></div></div></td>'+
                              '<td style="min-width:10px;">'+newdate+'</td>'+
                          '<td  style="min-width:120px";>'+val.restaurant+'</td>'+
                          '<td style="min-width:120px;">'+fltname+'</td>'+
                          '<td style="min-width:10px;">'+rating+'</td>'+
                          '<td style="min-width:70px;">'+review+'</td>'+'</tr>';
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
  
  function statuschange(id,status) {
            var ids = id;
            var data = {"ids": ids,"status": status,"resid" : $("#resid").val()};
            $.ajax({
                method: "get",
                url: "../../review_status",
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


    } );
</script>

@stop



   

@endsection




