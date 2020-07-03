@extends('layouts.app')
@section('content')
<style>
        .filter_text_box_row{margin-bottom: 6px}
     #example1_wrapper .dt-buttons{position: absolute;right: 80px;top: 15px;}
     #example1_wrapper .dt-buttons .btn-sm { padding: 6px 10px;box-shadow: 2px 3px 5px #d0d0d0;
    font-weight: bold;}
     .pagination_container_sec{width: 100%;height: auto;float: left}
     .pagination_container_sec ul{margin: 0;float: right}
     .tooltips {
        position: relative;
        display: inline-block;
       // border-bottom: 1px dotted black;
    }
        .filter_text_box_row{margin-bottom: 6px}
        #example1_wrapper .dt-buttons{position: absolute;right: 80px;top: 15px;}
        #example1_wrapper .dt-buttons .btn-sm { padding: 6px 10px;box-shadow: 2px 3px 5px #d0d0d0;
            font-weight: bold;}
        .pagination_container_sec{width: 100%;height: auto;float: left}
        .pagination_container_sec ul{margin: 0;float: right}
        .main_inner_class_track .form-control{margin-top:0}
        .add-work-done-poppup-textbox-box label{font-weight:lighter;}.inner-textbox-cc input:focus ~ label, input:valid ~ label{font-size:13px;top: -10px;}.group{margin-bottom: 14px}.add-work-done-poppup{height: auto;} div.dataTables_wrapper div.dataTables_filter{float: right;top: 4px;position: relative;}.dataTables_length{top: 7px;position: relative;float: left}
        .add-work-done-poppup-textbox-box label{font-weight:lighter;}

    .tooltips .tooltiptext {
        visibility: hidden;
        width: 100%;
        background-color: #555;
        color: #fff;
        text-align:justify;
        float: right;
        border-radius: 6px;
        padding: 5px 0;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -60px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltips .tooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }

    .tooltips:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
</style>
<div class="col-sm-12">
        <div class="col-sm-12">
                <ol class="breadcrumb">
						<li>
							<a href="{{url('index')}}">Dashboard</a>
						</li>
						
						<li class="active ms-hover">
                        Send Notification
						</li>
					</ol>
				</div>
        <div class="card-box table-responsive" style="padding: 8px 10px;">
             
              <div class="box master-add-field-box">
                  <div class="col-md-6 no-pad-left">
                      <h3>Notification</h3>
                  </div>
                  <div class="col-md-1 no-pad-left pull-right">
                      <div class="table-filter" style="margin-top: 4px;">
                          <div class="table-filter-cc">
                              <a onclick="viewadd()"> <button type="submit" style="margin-left:0" class="on-default followups-popup-btn btn btn-primary" >Add New</button></a>
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
              </div>

            <div id="add_notification" class="filter_box_section_cc diply_tgl">

                <div class="filter_text_box_row" style="display:block">
                    <div class="full_loading" style="display:none;"></div>
                    <div class="main_inner_class_track" style="width: 40%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Group</label>
                                  <select  class="form-control" id="group" name="group">
                                     <option value="all">All</option>
                                      @foreach($group as $item)
                                          <option value="{{ $item->g_id }}">{{ title_case($item->g_name) }}</option>
                                      @endforeach
                                  </select>
                              </div>
                           </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 30%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Expiry Date</label>
                                 <input id="expiry_date" data-date-format='dd-mm-yyyy'  name="expiry_date" class="form-control" type="text" >
                              </div>
                           </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 30%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Title</label>
                                  <input class="form-control" name="title" id="title" type="text">
                              </div>
                           </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 89%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Message</label>
                                 <textarea id="message" name="message" class="form-control" style="width: 99%;"></textarea>
                              </div>
                           </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 11% !important;float: left">
                           <div class="table-filter-cc" style="margin-top: 22px;">
                               <a onclick="sendnotification()"  style="margin-left:0;width: 80px " class="on-default followups-popup-btn btn btn-primary">SEND</a>
                            </div>
                        </div>
            </div>
            </div>
            <div id="filter_notification" class="filter_box_section_cc diply_tgl">
                   <div class="filter_text_box_row">

                       <div class="main_inner_class_track" style="width: 20%;">
                          <div class="group">
                            <div style="position: relative">
                                  <label>Group</label>
                                  <select  class="form-control" id="groupsearch" name="groupsearch" onchange="filter_change()">
                                     <option value="all">All</option>
                                      @foreach($group as $item)
                                          <option value="{{ $item->g_id }}">{{ $item->g_name }}</option>
                                      @endforeach
                                  </select>
                              </div>
                           </div>
                        </div>
                       
                <div class="main_inner_class_track" style="width: 20%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>From</label>
                                  <input id="flt_from" data-date-format="dd-mm-yyyy" value="<?=date('d-m-Y', strtotime('-10 days') )?>" name="flt_from" class="form-control" type="text">
                              </div>
                           </div>
                        </div>
                       <div class="main_inner_class_track" style="width: 20%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>To</label>
                                 <input id="flt_to" data-date-format="dd-mm-yyyy" value="<?=date('d-m-Y')?>" name="flt_to" class="form-control" type="text">
                              </div>
                           </div>
                        </div>
                       <div id="search_button" class="main_inner_class_track" style="width: 12% !important;float: left">
                           <div class="table-filter-cc" style="margin-top: 22px;">
                               <a onclick="refresh_filter()" style="cursor:pointer;margin-left:0;width: 80px " class="on-default followups-popup-btn btn btn-primary">Search</a>

                            </div>
                        </div>
                        
                   </div>  
            </div>

            <div class="table_section_scroll" id="notification_list"></div>
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
<div id="add_types" class="add-work-done-poppup-cc" style="display: none;">
    <div class="add-work-done-poppup" style="    width: 400px;top: 33%;">
        <div class="add-work-done-poppup-head"><span id="title_type"></span>
            <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
        </div>
        <input type='hidden' id='selecttype' name="selecttype"/>
        <input type='hidden' id='order_amount' name="order_amount"/>
        <input type='hidden' id='order_restaurant' name="order_restaurant"/>
        <input type='hidden' id='orderno' name="orderno" />
        <div style="text-align:center;" id="branchtimezone"></div>
        <div class="add-work-done-poppup-contant">
            <div class="add-work-done-poppup-textbox-cc">
                <div class="add-work-done-poppup-textbox-box">

                    <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">
                        <div class="main_inner_class_track " style="width: 65%;margin-right:0">
                            <div id="amount_div"  class="group"  style="display:none;">
                                <div style="position: relative">
                                    <label>Amount</label>
                                    {!! Form::text('amount',null, ['class'=>'form-control','id'=>'amount','onkeypress' => 'return numonly(event);submit_type();','required','style'=>"background-color:transparent;"]) !!}
                                </div>
                            </div>
                            <div id="restaurants_div" class="group" style="display:none;">
                                <div style="position: relative">
                                    <label>Restaurants</label>
                                    {!! Form::select('restaurants',['Select restaurants'],null,['id' => 'restaurants','class'=>'form-control','required','style'=>"background-color:transparent;"]) !!}
                                </div>
                            </div>
                            <div id="orderno_div" class="group" style="display:none;">
                                <div style="position: relative">
                                    <label>Order Number</label>
                                    {!! Form::text('order_no',null, ['class'=>'form-control','id'=>'order_no','onkeypress' => 'return numonly(event);submit_type();','required','style'=>"background-color:transparent;"]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <input type="hidden" name="type" id="type" />
                            <a id="inserting" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_type();">Submit</a>
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

    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->
@section('jquery')
    <script>
        $(document).ready(function()
        {
            $('#expiry_date').datepicker({
                autoclose: true,
                todayHighlight: true,
            });
        });
        var val = '';
        filter_change();
        $("#current_count").val(1);
        $("#start_count").val(1);
        $("#end_count").val(1);
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
            var groupid = $("#groupsearch  option:selected").val();
            var flt_from = $("#flt_from").val();
            var flt_to = $("#flt_to").val();
            var start_cnt = $("#start_count").val();
            var current_cnt = $("#current_count").val();
            var end_cnt = $("#end_count").val();
            var s='';
            var m ='';
            var e='';
            var prev='p';
            var next="n";
            var pgntn = $("#notification_list");
            var table = $('#example1').DataTable();

          $.ajax({
          method: "post",
          url   : "api/filter/notification_list",
          data  : {"groupid":groupid,"flt_from":flt_from,"flt_to":flt_to,"current_count":current_cnt},
          cache : false,
          crossDomain : true,
          async : false,
          dataType :'text',
          success : function(result)
          {
              var filter_result = JSON.parse(result);
              pgntn.html(filter_result.filter_data);
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
              else
              {
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
                      });
              $.fn.dataTableExt.sErrMode = 'throw';
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              
          }
        });
        }

        function notificationdelete(id)
        {
            $('.notifyjs-wrapper').remove();
            if(confirm('Are you sure to delete?'))
            {
                $.ajax({
                    method: "get",
                    url: "api/notification_delete/" + id,
                    cache: false,
                    crossDomain: true,
                    async: false,
                    dataType: 'text',
                    success: function (result)
                    {
                        var json_x = JSON.parse(result);
                        if (json_x.msg == 'success')
                        {
                            swal({

                                title: "",
                                text: "Deleted Successfully",
                                timer: 1000,
                                showConfirmButton: false
                            });
                            filter_change();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
        }
        function sendnotification()
        {
            $('.notifyjs-wrapper').remove();
            var group = $("#group");
            var expiry_date = $("#expiry_date");
            var title = $("#title");
            var message = $("#message");
            var groupname = group.find( "option:selected" ).text();
            if(expiry_date.val() == '')
            {
                $.Notification.autoHideNotify('error', 'bottom right','Enter Expiry Date');
                $("#fname").focus();
                return false;
            }
            if(title.val() == '')
            {
                $.Notification.autoHideNotify('error', 'bottom right','Enter Title');
                $("#title").focus();
                return false;
            }
            if(message.val() == '')
            {
                $.Notification.autoHideNotify('error', 'bottom right','Enter Message');
                $("#message").focus();
                return false;
            }
            if(groupname.toUpperCase() == 'ORDERED AMOUNT ABOVE')
            {
                $("#add_types").show();
                $("#amount_div").show();
                $("#restaurants_div").hide();
                $("#orderno_div").hide();
                $("#selecttype").val(groupname.toUpperCase());
                $("#title_type").html(groupname.toUpperCase());
//              submit_type(group.val(),expiry_date.val(),title.val(),message.val());
            }
            if(groupname.toUpperCase() == 'ORDER NUMBERS ABOVE')
            {
                $("#add_types").show();
                $("#orderno_div").show();
                $("#restaurants_div").hide();
                $("#amount_div").hide();
                $("#title_type").html(groupname.toUpperCase());
                $("#selecttype").val(groupname.toUpperCase());
            }
            else if(groupname.toUpperCase() == 'RESTAURANTS')
            {
                $("#add_types").show();
                $("#restaurants_div").show();
                $("#amount_div").hide();
                $("#orderno_div").hide();
                $("#selecttype").val(groupname.toUpperCase());
                $("#title_type").html(groupname.toUpperCase());
               // submit_type(group.val(),expiry_date.val(),title.val(),message.val());
                $('#restaurants').html('');
                $.ajax({
                    method: "get",
                    url: "api/restaurantlist",
                    cache: false,
                    crossDomain: true,
                    async: false,
                    dataType: 'text',
                    success: function (result)
                    {
                        var select = JSON.parse(result);
                        $('#restaurants').html('<option value="Select">Select</option>');
                        $.each(select, function(key, value)
                        {
                            $('#restaurants')
                                    .append($("<option></option>")
                                            .attr("value",value.id)
                                            .text(value.name));
                        });
                        $("#add_type").show();
                        $("#amount_div").hide();
                        $("#restaurants_div").show();
                        $("#title_type").html(val.toUpperCase());
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });

            }
            else
            {
                $("#selecttype").val(groupname.toUpperCase());
                var datas = {"group" :group.val(),"expiry_date":expiry_date.val(),"type":groupname.toUpperCase(),"title":title.val(),"message": message.val()};
                if(true)
                {
                    $(".full_loading").show();
                    notify(datas);
                }
            }
            return false;
        }

        function notify(datas)
        {
            var group = $("#group");
            var expiry_date = $("#expiry_date");
            var title = $("#title");
            var message = $("#message");
                $.ajax({
                method: "post",
                url: "api/notificationsubmit",
                data:datas,
                cache: false,
                crossDomain: true,
                async: false,
                success: function(result)
                {
                    if(result.toLowerCase() == 'success')
                    {
                        group.val('all');
                        expiry_date.val('');
                        title.val('');
                        message.val('');
                        setTimeout(function ()
                        {
                            $(".full_loading").hide();
                          //  location.reload();
                        }, 4000);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#errbox").text(jqXHR.responseText);
                }
            });
            return true;
        }

        function submit_type(group,expiry,title,message)
        {
            $("#add_types").hide();
            var group = $("#group");
            var expiry_date = $("#expiry_date");
            var title = $("#title");
            var message = $("#message");
            var type = $("#selecttype");
            var id = $("#q_id");
            var orderamt = $("#order_amount");
            var orderno = $("#orderno");
            var orderrestaurant = $("#order_restaurant");
            orderamt.val('');
            orderno.val('');
            if(type.val() == 'ORDERED AMOUNT ABOVE')
            {
                var amount = $("#amount").val();
                orderamt.val(amount);
                if(amount == '')
                {
                    $("#amount").focus();
                    $("#amount").addClass('input_focus');
                    $.Notification.autoHideNotify('error', 'bottom right','Enter Amount');
                    return false;
                }
                $(".full_loading").show();
                var datas = {'amount' : amount,'type' :type.val(),'id':id.val(),"group" :group.val(),"expiry_date" :expiry_date.val(),"title" :title.val(),"message" :message.val()};
                notify(datas);
            }
            if(type.val() == 'ORDER NUMBERS ABOVE')
            {
                var order_no = $("#order_no").val();
                orderno.val(order_no);
                if(order_no == '')
                {
                    $("#order_no").focus();
                    $("#order_no").addClass('input_focus');
                    $.Notification.autoHideNotify('error', 'bottom right','Enter Order Number');
                    return false;
                }
                $(".full_loading").show();
                var datas = {'order_no' : order_no,'type' :type.val(),'id':id.val(),"group" :group.val(),"expiry_date" :expiry_date.val(),"title" :title.val(),"message" :message.val()};
                notify(datas);
            }
            if(type.val() == 'RESTAURANTS')
            {
                var restaurants = $("#restaurants").val();
                orderrestaurant.val(restaurants);
                if(restaurants == 'Select')
                {
                    $("#restaurants").focus();
                    $("#restaurants").addClass('input_focus');
                    $.Notification.autoHideNotify('error', 'bottom right','Select Restaurants');
                    return false;
                }
                $(".full_loading").show();
                var datas = {'restaurantid' : restaurants,'type' :type.val(),'id':id.val(),"group" :group.val(),"expiry_date" :expiry_date.val(),"title" :title.val(),"message" :message.val()};
                notify(datas);
            }
            $("#add_types").hide();
            return true;
        }

    </script>
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
   /* var date = new Date();
    var before_five = date.getDate()-10;
                        date.setDate(before_five);
                        var sdate = date.getDate();
                        var smonth = date.getMonth()+1;
                        var syear = date.getFullYear();//alert(sdate+"/"+smonth+"/"+syear);
                        var startdata = smonth+"-"+sdate+"-"+syear;
    $("#flt_from").datepicker().datepicker("setDate", new Date(startdata));
    $("#flt_to").datepicker().datepicker("setDate", new Date());*/
    $.fn.dataTableExt.sErrMode = 'throw';
    });
    $.fn.dataTableExt.sErrMode = 'throw';
    $('.filter_sec_btn').on('click', function(e)
    {
        $('#filter_notification').toggleClass("diply_tgl");
    });

    $(".ad-work-close-btn").click(function() {
            $("#add_type").css("display", 'none');
    });
    function viewadd()
    {
        $('#add_notification').toggleClass("diply_tgl");
    }
        $(".close-pop-ad-work-cc").click(function()
        {
            $("#add_types").hide();

        });
</script>
 @stop
@endsection




