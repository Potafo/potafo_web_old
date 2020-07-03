@extends('layouts.app')
@section('content')
<style>
        .filter_text_box_row{margin-bottom: 6px}
     #example1_wrapper .dt-buttons{position: absolute;right: 80px;top: 15px;}
     #example1_wrapper .dt-buttons .btn-sm { padding: 6px 10px;box-shadow: 2px 3px 5px #d0d0d0;
    font-weight: bold;}
     .pagination_container_sec{width: 100%;height: auto;float: left}
     .pagination_container_sec ul{margin: 0;float: right}
     .main_inner_class_track .form-control{margin-top:0}
     .add-work-done-poppup-textbox-box label{font-weight:lighter;}.inner-textbox-cc input:focus ~ label, input:valid ~ label{font-size:13px;top: -10px;}.group{margin-bottom: 14px}.add-work-done-poppup{height: auto;} div.dataTables_wrapper div.dataTables_filter{float: right;top: 4px;position: relative;}.dataTables_length{top: 7px;position: relative;float: left}
     .add-work-done-poppup-textbox-box label{font-weight:lighter;}

</style>
<div class="col-sm-12">
        <div class="col-sm-12">
                <ol class="breadcrumb">
						<li>
							<a href="{{url('index')}}">Dashboard</a>
						</li>
						<li class="active ms-hover">
                        Notification Group
						</li>
					</ol>
				</div>
        <div class="card-box table-responsive" style="padding: 8px 10px;">
              <div class="box master-add-field-box" >
             <div class="col-md-6 no-pad-left">
                <h3>Notification Group</h3>
            </div>                      
            </div>
            @if(strtoupper(Session::get('logingroup')) == 'SA')
            <div class="filter_box_section_cc diply_tgl" style="display:block">
                 <input type="hidden" id="flt_id" name="flt_id">
                   <div class="filter_text_box_row">
                       <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Group Name</label>
                                  <input id="flt_name" name="flt_name" class="form-control" type="text">
                              </div>
                           </div>
                        </div>
                       <div class="main_inner_class_track" style="width: 63%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Query  ( Name, Last Name, Mobile Contact )</label>
                                  <input id="flt_query" name="flt_query" class="form-control" type="text">
                              </div>
                           </div>
                        </div>
                       <div id="search_button" class="main_inner_class_track" style="width: 12% !important;float: left">
                           <div class="table-filter-cc" style="margin-top: 22px;">
                               <a id="addshow" style="margin-left:0;width: 80px " class="on-default followups-popup-btn btn btn-primary" onclick="add_notification();">ADD</a>
                               <a id="updateshow" style="display:none; margin-left:0;width: 80px;" class="on-default followups-popup-btn btn btn-primary" onclick="update_notification();">UPDATE</a>
                           </div>
                        </div>
                   </div>  
            </div>
            @endif
        </div>
        <div class="table_section_scroll" style="margin-top:10px">
            <div class="card-box table-responsive" style="padding: 8px 10px;">
                <div class="full_loading" style="display:none;"></div>
                <div class="col-md-5">
                        <div class="group_box_section_cc">
                            <div class="group_box_head">
                                Groups
                            </div>
                            <div class="group_box_section_contant">
                                <table class="table table-bordered dataTable no-footer group_table_lft" id="example1">
                                  @if(count($rows)>0)
                                  @foreach($rows as $key=>$value)
                                  <tr class="add slno_{{$key}}">
                                     <!--<td style="width: 66% !important;"  onclick="return customer_list('{{$value->g_id}}','{{$key}}','{{$value->g_name}}')">{{ ucfirst($value->g_name)}}</td>-->
                                     <td style="width: 66% !important;"  onclick="return customer_list('{{$value->g_id}}','{{$key}}','{{$value->g_name}}')">{{ ucfirst($value->g_name)}}</td>
                                      @if(strtoupper(Session::get('logingroup')) == 'SA')
                                      <td style="width: 34% !important;">
                                      <a class="btn button_table clear_edit" onclick='return edit_notification("{{$value->g_id}}","{{$value->g_name}}","{{$value->g_query}}")'><i class="fa fa-pencil"></i></a>
                                      <a class="btn button_table clear_edit" onclick="return delete_notification('{{$value->g_id}}')"><i class="fa fa-trash"></i></a>
                                          @if(ucfirst($value->g_name) == 'DELIVERY STAFF')
                                             {{--<a class="btn button_table clear_edit" style="pointer-events:none;"><i class="fa fa-list"></i></a>--}}
                                          @else
                                              <a class="btn button_table clear_edit"   onclick="return listget('{{$value->g_id}}','{{$key}}','{{$value->g_name}}')"><i class="fa fa-list"></i></a>
                                          @endif
                                      </td>
                                     @endif
                                  </tr>
                                 @endforeach
                                 @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="group_box_section_cc">
                                <div class="group_box_head" style="padding:0">
                                    {!! Form::open(['url'=>'filter/customer', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'get']) !!}
                                    <input type="hidden" id="q_id" name="q_id">
                                    <input type='hidden' id='selecttype' name="selecttype" />
                                    <input type='hidden' id='order_amount' name="order_amount" />
                                    <input type='hidden' id='order_restaurant' name="order_restaurant" />
                                    <input type='hidden' id='orderno' name="orderno" />
                                    <div class="main_inner_class_track" style="width:30%;margin-bottom:3px">
                                        <div class="group">
                                            <div style="position: relative">
                                                <p style="margin:-3px 0 0 0;font-size:11px"> Name</p>
                                                <input style="height: 27px;" name="cstname" id='cstname' class="form-control" type="text" onkeyup="return filter_change(this.value)">
                                            </div>
                                        </div>
                                     </div>
                                     <div class="main_inner_class_track" style="width:30%;margin-left:2%;">
                                        <div class="group">
                                            <div style="position: relative">
                                                <p style="margin:-3px 0 0 0;font-size:11px">Mobile</p>
                                                <input style="height: 27px;" name="cstmob" id='cstmob' onkeypress = 'return numonly(event);' class="form-control" type="text" onkeyup="return filter_change(this.value)">
                                            </div>
                                        </div>
                                     </div>

                                     {{ Form::close() }}
                                </div>
                                <div class="group_box_head" style="padding:2px;border:0">
                                        <span style="padding-left: 8px;margin-top: 6px;display: inline-block;"> Customer List</span>&nbsp;<span id="countcustomer"></span>
                                        <!--<a href="#" style="margin:3px 3px 0 0 ;width: 60px;float:right;font-size:12px;height: 28px;" class="on-default followups-popup-btn btn btn-primary">ADD+</a>-->
                                    </div>
                                <div class="group_box_section_contant">
                                    <table class="table table-bordered dataTable no-footer" id='tableexample'>
                                         <thead>
                                          <tr>
                                            <th style="min-width:3px"></th>
                                            <th style="min-width:130px"></th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>  
                         </div>
                    </div>
                </div>
            </div>
    <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>
    <div id="add_type" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup" style="    width: 400px;top: 33%;">
            <div class="add-work-done-poppup-head"><span id="title_type"></span>
                <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
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
    </div>

    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

         <script>
             function numonly(evt)
             {
                 var charCode = (evt.which) ? evt.which : evt.keyCode;
                 if (charCode != 46 && charCode > 31
                         && (charCode < 48 || charCode > 57))
                     return false;
                 return true;
             }
             function listget(id,key,val)
             {
                 $(".full_loading").show();
                 $('.add').removeClass('group_table_lft_tr_act');
                 $(".slno_" + key).addClass('group_table_lft_tr_act');
                 if(val == 'DELIVERY STAFF')
                 {
                     staff_list(id,key,val);
                 }
                 else
                 {
                     customer_list(id, key, val)
                 }
                 return true;
             }
        function add_notification()
        {
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var gname = $("#flt_name").val();
            var gquery = $("#flt_query").val();
            if(gname == '') 
            {
               $("#flt_name").focus();
               $.Notification.autoHideNotify('error', 'bottom right','Enter Group Name');
               return false;
            }
            if(gquery == '') 
            {
              $("#flt_query").focus();
              $.Notification.autoHideNotify('error', 'bottom right','Enter Query');
            return false;
            }
        if(true)
        {
            var data= {"gname":gname,"gquery":gquery};
            $.ajax({
                method: "post",
                url : "api/add_notification",
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
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });

        }
        }
        
        function delete_notification(id)
        {
            if(confirm('Are you sure to delete?'))
            {
                var data= {"id":id};
                $.ajax({
                method: "post",
                url : "api/group_delete",
                data : data,
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                    success: function (result) {
                    var json_x= JSON.parse(result);
                    if((json_x.msg)=='deleted')
                    {
                        location.reload();
                         swal({
                            title: "",
                            text: "Successfully Deleted",
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
            return true;
        }
        
        function edit_notification(id,name,query)
        {
            $("#flt_id").val(id);
            $("#flt_name").val(name);
            $("#flt_query").val(query);
            $("#updateshow").css("display",'block');
            $("#addshow").css("display",'none');
			$('.add').removeClass('group_table_lft_tr_act');
        }

       /*  $('.add').click(function()
         {
                 $(this).addClass('group_table_lft_tr_act');
                 $(".full_loading").show();
         });*/

         function update_notification()
         {
            var gid = $("#flt_id").val();
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var gname = $("#flt_name").val();
            var gquery = $("#flt_query").val();
            if(gname == '') 
            {
               $("#flt_name").focus();
               $.Notification.autoHideNotify('error', 'bottom right','Enter Group Name');
               return false;
            }
            if(gquery == '') 
            {
              $("#flt_query").focus();
              $.Notification.autoHideNotify('error', 'bottom right','Enter Query');
            return false;
            }
        if(true)
        {
            var data= {"gname":gname,"gquery":gquery,"gid":gid};
            $.ajax({
                method: "post",
                url : "api/update_notification",
                data : data,
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(result)
                {
                    var json_x= JSON.parse(result);
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
                    $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }
        }

        function staff_list(id,key,val)
        {
            var data = {"id":id};
            $.ajax({
                method: "post",
                url: "api/staff_list",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                    var jsonx = JSON.parse(result);
                    if (parseInt(jsonx[1]) >= 0)
                    {
                        $("#countcustomer").html('('+jsonx[1]+')');
                        $('#tableexample').html(jsonx[0]);
                    }
                    if (parseInt(jsonx[1]) > 0)
                    {
                        setTimeout(function ()
                        {
                            $(".full_loading").hide();
                        }, 4000);
                    }
                    else
                    {
                        $(".full_loading").hide();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
            return true;
        }


        function customer_list(id,key,val)
        {
            var staff_id = $("#staff_id").val();
          /*  $('.add').removeClass('group_table_lft_tr_act');
            $(".slno_" + key).addClass('group_table_lft_tr_act');*/
            $("#selecttype").val(val.toUpperCase());
            $('#tableexample').html('');
            $("#amount").val('');
            $("#restaurants").val('');
            $("#order_no").val('');
            if(val.toUpperCase() == 'ORDERED AMOUNT ABOVE')
            {
                $("#add_type").show();
                $("#amount_div").show();
                $("#restaurants_div").hide();
                $("#orderno_div").hide();
                $("#title_type").html(val.toUpperCase());
            }
            if(val.toUpperCase() == 'DELIVERY STAFF')
            {
                $("#add_type").show();
                $("#amount_div").show();
                $("#restaurants_div").hide();
                $("#orderno_div").hide();
                $("#title_type").html(val.toUpperCase());
            }
            if(val.toUpperCase() == 'ORDER NUMBERS ABOVE')
            {
                $("#add_type").show();
                $("#orderno_div").show();
                $("#restaurants_div").hide();
                $("#amount_div").hide();
                $("#title_type").html(val.toUpperCase());
            }
            else if(val.toUpperCase() == 'RESTAURANTS')
            {
                $('#restaurants').html('');
                $.ajax({
                    method: "get",
                    url: "api/restaurantlist",
                    data:{"staff_id":staff_id},
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
                        $("#orderno_div").hide();
                        $("#title_type").html(val.toUpperCase());
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                       // $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });

            }
            else if(val.toUpperCase() == 'ANDROID'|| val.toUpperCase() == 'IOS'|| val.toUpperCase() == 'WEB')
            {
                var data = {"id": id};
                customerfilter(data);
            }
            $("#q_id").val(id);
            $("#cstname").val('');
            $("#cstmob").val('');
            return true;
        }

             function customerfilter(data)
             {
                 $.ajax({
                     method: "post",
                     url: "api/customer_list",
                     data: data,
                     cache: false,
                     crossDomain: true,
                     async: false,
                     dataType: 'text',
                     success: function (result)
                     {
                         var jsonx = JSON.parse(result);
                         if (parseInt(jsonx[1]) >= 0)
                         {
                             $("#countcustomer").html('('+jsonx[1]+')');
                             $('#tableexample').html(jsonx[0]);
                         }
                         if (parseInt(jsonx[1]) > 0)
                         {
                             setTimeout(function ()
                             {
                                 $(".full_loading").hide();
                             }, 4000);
                         }
                         else
                         {
                             $(".full_loading").hide();
                         }
                     },
                     error: function (jqXHR, textStatus, errorThrown) {
                         $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                     }
                 });
                 return true;
             }

             function submit_type()
             {
                 var type = $("#selecttype");
                 var id = $("#q_id");
                 var orderamt = $("#order_amount");
                 var orderrestaurant = $("#order_restaurant");
                 orderamt.val('');
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
                     var datas = {'amount' : amount,'type' :type.val(),'id':id.val()};
                     customerfilter(datas);
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
                     var datas = {'restaurantid' : restaurants,'type' :type.val(),'id':id.val()};
                     customerfilter(datas);
                 }
                 if(type.val() == 'ORDER NUMBERS ABOVE') {
                     var order_no = $("#order_no").val();
                     $("#orderno").val(order_no);
                     if (order_no == '') {
                         $("#order_no").focus();
                         $("#order_no").addClass('input_focus');
                         $.Notification.autoHideNotify('error', 'bottom right', 'Enter Order Number');
                         return false;
                     }
                     $(".full_loading").show();
                     var datas = {'type' :type.val(),'id':id.val(),'order_no' :order_no};
                     customerfilter(datas);
                 }
                 $("#add_type").hide();
                 return true;
             }
        
        function filter_change(val)
        {
            $(".full_loading").show();
            var frm = $('#frm_filter');
            $('#tableexample').html(''); 
            var name = $('#cstname').val(); 
            var mob = $('#cstmob').val(); 
            var id = $('#q_id').val();
            var namecount = name.length;
            var mobcount = mob.length;
           if(namecount >=4 || mobcount >=4)
           {
            $.ajax({
                method: "post",
                url   : "api/filter/customer",
                data  : frm.serialize(),
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(result)
                {
                    var jsonx = JSON.parse(result);
                    if (parseInt(jsonx[1]) >= 0)
                    {
                        $("#countcustomer").html('('+jsonx[1]+')');
                        $('#tableexample').html(jsonx[0]);
                    }
                    if (parseInt(jsonx[1]) > 0)
                    {
                        setTimeout(function ()
                        {
                            $(".full_loading").hide();
                        }, 4000);
                    }
                    else
                    {
                        $(".full_loading").hide();
                    }
                    $('#tableexample').html(jsonxs[0]);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }
        else 
        {
            var type = $("#selecttype");
            if(type.val() == 'ORDERED AMOUNT ABOVE')
            {
                var amount =   $("#order_amount").val();
                var datas = {'amount' : amount,'type' :type.val(),'id':id};
            }
            else if(type.val() == 'ORDER NUMBERS ABOVE')
            {

                var orderno =   $("#orderno").val();
                var datas = {'order_no' : orderno,'type' :type.val(),'id':id};
            }
            else if(type.val() == 'RESTAURANTS')
            {

                var restaurants =   $("#restaurants").val();
                var datas = {'restaurantid' : restaurants,'type' :type.val(),'id':id};
            }
            else
            {

                var datas = {'id':id};
            }
                $(".full_loading").show();
                $.ajax({
                method: "post",
                url   : "api/customer_list",
                data  : datas,
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(result)
                {
                var jsonx = JSON.parse(result);
                    if (parseInt(jsonx[1]) >= 0)
                {
                    $("#countcustomer").html('('+jsonx[1]+')');

                    $('#tableexample').html(jsonx[0]);
                }
                    if (parseInt(jsonx[1]) > 0)
                    {
                        setTimeout(function ()
                        {
                            $(".full_loading").hide();
                        }, 4000);
                    }
                    else
                    {
                        $(".full_loading").hide();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }
        return true;
      }

            $(".ad-work-close-btn").click(function() {
                 $("#add_type").css("display", 'none');
                 $(".full_loading").css("display", 'none');
                 $('.add').removeClass('group_table_lft_tr_act');
            });

             function numonly(evt)
             {
                 var charCode = (evt.which) ? evt.which : evt.keyCode;
                 if (charCode != 46 && charCode > 31
                         && (charCode < 48 || charCode > 57))
                     return false;
                 return true;
             }


    </script>


@section('jquery')


  

@stop
@endsection




