@extends('layouts.app')
@section('content')
 <style>
    .filter_text_box_row{margin-bottom: 6px}
    .dt-buttons{position: absolute;right: 0px;top: -130px;font-weight: bold;}
    .dt-buttons .btn-sm { padding: 6px 10px;box-shadow: 2px 3px 5px #d0d0d0;}
    .dt-buttons .btn-sm { padding: 6px 10px;box-shadow: 2px 3px 5px #d0d0d0;  font-weight: bold; }
    .content{padding: 10px !important;}
    .searchlist{top:29px;    max-height: 142px;}
    </style>
        <div class="card-box table-responsive" style="padding: 8px 10px;height: 561px;overflow:hidden">
             
              <div class="box master-add-field-box" >
             <div class="col-md-6 no-pad-left">
                <h3>GENERAL REPORT</h3>
            </div>    
                  
            
                <div class=" pull-right">
                <div class="table-filter" style="margin-top: 4px;">
                </div>
                </div>
                  
            </div>
            
            <div class="filter_box_section_cc ">
                   <div class="filter_text_box_row">
                       {!! Form::open(['name'=>'frm_filter', 'id'=>'frm_filter','method'=>'get']) !!}
                       <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>
                       <input type="hidden" name="reporttype" id="reporttype" value="">
                       <div class="main_inner_class_track" style="width: 15%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Reports</label>
                                  <select class="form-control" id="reports_id" onchange="reports_change(this.value);">
                                      @foreach($reports_all as $val)
                                      <option value='{{$val->report_name}}'><?= ucwords(str_replace('_', ' ', $val->report_name));?></option>
                                      @endforeach
                                  </select>
                              </div>
                           </div>
                        </div>
                       
                       <div id="date_from_id" class="main_inner_class_track" style="width: 10%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>From Date</label>
                                  <input id="flt_from" data-date-format='dd-mm-yyyy' name="flt_from" class="form-control" type="text" value="{{$datetime}}">
                              </div>
                           </div>
                        </div>
                       <div id="date_to_id" class="main_inner_class_track" style="width: 10%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>To Date</label>
                                 <input id="flt_to" name="flt_to" data-date-format='dd-mm-yyyy'  class="form-control" type="text" value="{{$datetime}}">
                              </div>
                           </div>
                        </div>
                       <div id="one_date_id" class="main_inner_class_track" style="width: 10%;display: none">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Date</label>
                                 <input id="one_date" name="one_date" data-date-format='dd-mm-yyyy'  class="form-control" type="text" value="{{$datetime}}">
                              </div>
                           </div>
                        </div>
                       <div id="category_div" class="main_inner_class_track" style="width: 12%;display:block;">
                           <div class="group">
                               <div style="position: relative">
                                   <label>Category</label>
                                   @foreach($order_cat as $cats)

                                       @if($cats->order_list_cat == 'all')
                                           <select class="form-control" id="order_cat_filter" onchange="return change_filter(this.value)">
                                               <option value="All">All</option>
                                               <option value="Restaurant">Restaurant</option>
                                               <option value="Potafo Mart">Potafo Mart</option>

                                           </select>
                                       @endif
                                       @if($cats->order_list_cat == 'restaurant')
                                           <select class="form-control" id="order_cat_filter" onchange="return change_filter(this.value)">

                                               <option value="Restaurant">Restaurant</option>


                                           </select>
                                       @endif
                                       @if($cats->order_list_cat == 'potafo_mart')
                                           <select class="form-control" id="order_cat_filter" onchange="return change_filter(this.value)">

                                               <option value="Potafo Mart">Potafo Mart</option>


                                           </select>

                                       @endif
                                   @endforeach
                               </div>
                           </div>
                       </div>
                       <div id="restraurents_id" class="main_inner_class_track" style="width: 25%;display:block;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Restaurants/Shop</label>
                                  <select class="form-control" id="rest_id_fil" onchange="return change_restraurents(this.value);">
                                      <option value='select' id="option_values_change">All</option>
                                      @foreach($restraurents as $val)
                                      <option value='{{$val->id}}'>{{$val->rest_name}}</option>
                                      @endforeach
                                  </select>
                              </div>
                           </div>
                        </div>
                       <div id="paymode_div" class="main_inner_class_track" style="width: 10%;display:block;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Paymode</label>
                                  <select class="form-control" id="payment_mode">
                                      <option value='all' id="option_values_change">All</option>
                                      @foreach($paymode as $val)
                                      <option value='{{$val->name}}'>{{$val->name}}</option>
                                      @endforeach
                                  </select>
                              </div>
                           </div>
                        </div>
                       <div id="menu_item_id" class="main_inner_class_track" style="width: 15%;display: none">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Item</label>
                                  <input class="form-control" type='text' name = 'menu_name' id='menu_name' placeholder='Enter Menu Name' onKeyUp = 'return menunamechange(this.value)'>
                                  <div class="searchlist" style="display:none;width: 130%;background:#FFF;float:left;margin-top: 31px;width: 221px !important;" id="suggesstionsmenu"  onMouseOut="mouseoutfnctn(this);">
                              </div>
                           </div>
                        </div>
                        </div>
                       <div id="search_button" class="main_inner_class_track" style="width: 12% !important;float: left">
                           <div class="table-filter-cc" style="margin-top: 22px;">
                               <a href="#" onclick="submit_filter();" style="margin-left:0;width: 80px " class="on-default followups-popup-btn btn btn-primary">Search</a>
                            </div>
                        </div>
                       {{ Form::close() }}
                       <span id="test"></span>
                   </div>  
            </div>
             <div id="append">
                     <div class="full_loading" style="display:none;" id="full_loading"></div>
                </div>
            <div class="table_section_scroll" id="append_div" style="position:relative;">
               
               
                
           </div>
        </div>

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

     $('#one_date').datepicker({
         autoclose: true,
         todayHighlight: true,

     });
    
    });

     $('.filter_sec_btn').on('click', function(e)
    {
        $('.filter_box_section_cc').toggleClass("diply_tgl");
        $("#restaurant_name").focus();
    }); 
</script>

<script>
    function reports_change(values)
    {
        $('#append_div').html('');
        $('#reporttype').val(values);
        $('#rest_id_fil').val('select');
        $("#payment_mode").val('all');
        if(values == 'select'){
            $('#date_from_id').hide();
            $('#date_to_id').hide();
            $('#restraurents_id').hide();
            $('#search_button').hide();
            $('#one_date_id').hide();
            $('#paymode_div').hide();
        }else if(values == 'Sales_reports'){
            $('#date_from_id').show();
            $('#date_to_id').show();
            $('#one_date_id').hide();
            $('#restraurents_id').show();
            $('#search_button').show();
            $('#menu_item_id').hide();
            $('#paymode_div').show();
            $('#option_values_change').html('All');
        }else if(values == 'Item_sale_report'){
            $('#date_from_id').show();
            $('#date_to_id').show();
            $('#one_date_id').hide();
            $('#allstaffs_id').show();
            $('#search_button').show();
            $('#menu_item_id').hide();
            $('#restraurents_id').show();
            $('#paymode_div').hide();
            $('#option_values_change').html('Select');
        }else if(values == 'Order_summary'){
            $('#date_from_id').show();
            $('#date_to_id').show();
            $('#one_date_id').hide();
            $('#allstaffs_id').show();
            $('#search_button').show();
            $('#menu_item_id').hide();
            $('#restraurents_id').show();
            $('#paymode_div').show();
            $('#option_values_change').html('Select');
        }else if(values == 'Cancellation_report'){
            $('#date_from_id').show();
            $('#date_to_id').show();
            $('#one_date_id').hide();
            $('#allstaffs_id').show();
            $('#search_button').show();
            $('#menu_item_id').hide();
            $('#restraurents_id').hide();
            $('#option_values_change').html('Select');
            $('#paymode_div').hide();

        }
       
    }

    function change_filter(type)
    {
        $('#rest_id_fil').html('');
        $.ajax({
            method: "get",
            url: "get_restaurants_category/"+type,
            async:false,
            success: function (result)
            {
                $('#rest_id_fil').html(result);

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $("#errbox").text(jqXHR.responseText);
            }
        });

    }

    function change_restraurents(val)
    {
      var res =  $('#rest_id_fil').val();
      var type =  $('#reports_id').val();//reporttype
      if(res == 'select')
      {
          $('#menu_item_id').hide();
      }else{
         
          
      }
       if(type == 'Order_summary')
          {
               $('#menu_item_id').hide();
          }
          else if(type == 'Sales_reports')
          {
              $('#menu_item_id').hide();
          }else if(type == 'Cancellation_report')
          {
              $('#menu_item_id').hide();
          }else
          {
           $('#menu_item_id').show();
          }
     localStorage.restruarents = val;
    }
    
    function submit_filter()
    {
    // $("#full_loading").show();
        var restraurent     =   $('#rest_id_fil').val();
        var reports_name    =   $('#reports_id').val(); 
        var one_date        =   $('#one_date').val();
        var date_from       =   $('#flt_from').val();
        var date_to         =   $('#flt_to').val();
        var menu_name       =   $('#menu_name').val();
        var staff_id       =   $('#staff_id').val();
        var order_cat_filter       =   $('#order_cat_filter').val();
        var payment_mode       =   $('#payment_mode').val();
        $('#append_div').html('');
        if(reports_name == 'Sales_reports')
        {
               var data = {"staff_id":staff_id,"order_cat_filter":order_cat_filter,"date_from":date_from,"payment_mode":payment_mode,"date_to":date_to,"restraurent":restraurent,"reports_name":reports_name,'menu_name':menu_name};
                if(date_from == '' || date_to == '')
                {
                    alert('Select From Date And To Date');
                    if(date_from == ''){
                        $('#flt_from').focus();
                    }else if(date_to == ''){
                         $('#flt_to').focus();
                     }   
                    return false;
                }

              $.ajax({
                method: "get",
                url: "api/filter_general_reports",
                data: data,
                async:false,
                success: function (result)
                {
                    //alert(result)
                   $('#append_div').html(result);
                // $("#full_loading").hide();

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#errbox").text(jqXHR.responseText);
                }
            });
        }
        else if(reports_name == 'Item_sale_report')
        {
            var data = {"staff_id":staff_id,"order_cat_filter":order_cat_filter,"date_from":date_from,"date_to":date_to,"restraurent":restraurent,"reports_name":reports_name,'menu_name':menu_name};
                if(date_from == '' || date_to == ''){
          
                    alert('Select From Date And To Date');

                    if(date_from == ''){
                        $('#flt_from').focus();
                    }else if(date_to == ''){
                         $('#flt_to').focus();
                     }   
                    return false;
                }

                if(restraurent == 'select')
                {
                    alert("Select Restaurant / Shop ");
                    $('#rest_id_fil').focus();
                    return false;
                }
               $.ajax({
                method: "get",
                url: "api/filter_general_reports",
                data: data,
                async:false,
                success: function (result)
                {
                   $('#append_div').html(result);
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
            }
            else if(reports_name == 'Order_summary'){
            var data = {"staff_id":staff_id,"order_cat_filter":order_cat_filter,"date_from":date_from,"payment_mode":payment_mode,"date_to":date_to,"restraurent":restraurent,"reports_name":reports_name};
                if(date_from == '' || date_to == ''){
          
                    alert('Select From Date And To Date');

                    if(date_from == ''){
                        $('#flt_from').focus();
                    }else if(date_to == ''){
                         $('#flt_to').focus();
                     }   
                    return false;
                }
             //   $("#full_loading").css("display","block");
               $.ajax({
                method: "get",
                url: "api/filter_general_reports",
                data: data,
                async:false,
                   success: function (result)
                {
                   $('#append_div').html(result);
                 //   $("#full_loading").css("display","none");
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
            }
            else if(reports_name == 'Cancellation_report'){
            var data = {"staff_id":staff_id,"order_cat_filter":order_cat_filter,"date_from":date_from,"date_to":date_to,"reports_name":reports_name};
                if(date_from == '' || date_to == ''){
          
                    alert('Select From Date And To Date');

                    if(date_from == ''){
                        $('#flt_from').focus();
                    }else if(date_to == ''){
                         $('#flt_to').focus();
                     }   
                    return false;
                }
             //   $("#full_loading").css("display","block");
               $.ajax({
                method: "get",
                url: "api/filter_general_reports",
                data: data,
                async:false,
                success: function (result)
                {
//                    alert (result);
                   $('#append_div').html(result);
                 //  $("#full_loading").css("display","none");
                   
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
            }
            var rp_name = reports_name;
            var replaced = rp_name.replace(' ', '_');
//          Order_summary
                   var t = $('#'+replaced).DataTable({
                        scrollY: "340px",
                        scrollX: true,
                        dom: "Bfrtip",
                        paging: false,
                        serverSide: true,
                        bProcessing: true,
                        buttons: [{
                            text: 'Excel',
                            extend: "csv",
                            filename: replaced,
                            className: "btn-sm"
                        }],
                        scrollCollapse: true,
                        searching: false,
                        ordering: false,
                         info: false,
                         columnDefs: [
                        { width: '20%', targets: 0 }
                    ],
                        "deferLoading": 0,
                          "lengthChange": false,
                       "columnDefs": [{
                            paging: false
                        } ],
                    });
        return true;
    }

    function menunamechange(val)
    {
        var staff_id = $("#staff_id").val();
        var count = val.length;
        if(parseInt(count)>= 3)
        {
            $.ajax({

                method: "get",
                url : "api/search_item_name",
                data : {'menu':val,'staff_id':staff_id,'rest_id':localStorage.restruarents},
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success: function (data)
                {
                    $("#suggesstionsmenu").empty();
                    $("#suggesstionsmenu").show();
                    $.each(JSON.parse(data), function (i, indx)
                    {
                        $.each(JSON.parse(indx.portion), function (n, val)
                        {
                         var search_id = indx.m_menu_id+'_'+val.portion;
                        if ($("#search_" + search_id).length == 0)
                        {
                            var menu_name = indx.name+' , '+val.portion;
                            $("#suggesstionsmenu").show();
                            $("#suggesstionsmenu").append('<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="search_' + search_id + '" onclick=\'selectname("' + indx.name + '")\'>' + '<p>' + menu_name + '</p></div>');
                        }
                    });
                    });
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }else{
            $("#suggesstionsmenu").html('');
            $("#suggesstionsmenu").hide();
        }
    }
    
    function selectname(name)
    {
        $('#menu_name').val(name);
        $("#suggesstionsmenu").hide();
    }
     
</script>

@stop
@endsection




