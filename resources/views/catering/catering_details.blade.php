@extends('layouts.app')
@section('title','Potafo - Catering Details')
@section('content')
<?php
        $pg=app('request')->input('page') ;
        if($pg==''){
            $pg=1;
        }
        $sl=($pg * 25)-24;
        $p=1;
?>
   <style>
	   .content-page > .content{    padding: 10px;}
	   .card-box-sec{width: 100%;height: auto;float: left;background-color: #fff;border: solid 1px #e5e5e5}
	   .order_dtl_head{width: 100%;height: auto;float: left;padding: 10px;background-color: #f3f3f3;color: #242424;font-size: 16px;}
	   .order_dtl_contant_sec{width: 100%;height: auto;float: left;background-color: #fff;font-family: Gill Sans, Gill Sans MT, Myriad Pro, DejaVu Sans Condensed, Helvetica, Arial," sans-serif";}
	   .order_dtl_contant_sec_li{color: #001029;font-size: 16px;width: 100%;height: auto;float: left;line-height: 22px;padding:10px 5px;padding-left: 35px;border-bottom: 1px #efefef solid;position: relative}
	   .order_dtl_contant_sec_li_ico{width: 30px;position: absolute;left: 10px;top: 10px;color: #001029}
	   .table td{border: solid 1px #e5e5e5;padding: 7px !important}
	   .table th{border: solid 1px #e5e5e5 !important;padding: 7px !important}
	   .add_his_table td{padding: 7px !important;border: 0 !important}
</style>
    
<div class="dashboard_main_container">
    
   <div class="col-md-4">
   		
   		<div class="card-box-sec">
   			<div class="order_dtl_head">Order Details</div>
   			<div class="order_dtl_contant_sec">
   			
   				<div class="order_dtl_contant_sec_li">
   					<div class="order_dtl_contant_sec_li_ico"><i class="fa fa-cart-plus"></i></div>
					@if(isset($orders[0]->com_order_id))
						{{$orders[0]->com_order_id}}
					@endif   				</div>
				<input value="{{$orders[0]->com_order_id}}" type="hidden" id="orderno" name="orderno">
   				<div class="order_dtl_contant_sec_li">
   					<div class="order_dtl_contant_sec_li_ico"><i class="fa fa-calendar"></i></div>
					@if(isset($orders[0]->com_order_date))
          {{date('Y-m-d H:i:s',strtotime($orders[0]->com_order_date))}}
						@endif
            				</div>
   				<div class="order_dtl_contant_sec_li">
   					<div class="order_dtl_contant_sec_li_ico"><i class="fa fa-credit-card"></i></div>
					@if(isset($orders[0]->com_scheduled_date))
						{{$orders[0]->com_scheduled_date. ' '.$orders[0]->com_scheduled_time}}
					@endif
				</div>
   				<div class="order_dtl_contant_sec_li">
   					<div class="order_dtl_contant_sec_li_ico"><i class="fa md-local-shipping"></i></div>
					@if(isset($orders[0]->com_menu_type_name))
						{{$orders[0]->com_menu_type_name. ' '.$orders[0]->com_menu_type_name}}
						@endif
   				</div>
   				
   			</div>
   		</div>
   	
   </div>
   
   <div class="col-md-4">
   		
   		<div class="card-box-sec">
   			<div class="order_dtl_head">Customer Details</div>
   			<div class="order_dtl_contant_sec">
   			
   				<div class="order_dtl_contant_sec_li">
   					<div class="order_dtl_contant_sec_li_ico"><i class="fa fa-user"></i></div>
					{{isset($orders[0]->customername)?$orders[0]->customername:null}}<br/>
   				</div>
   				<div class="order_dtl_contant_sec_li">
   					<div class="order_dtl_contant_sec_li_ico"><i class="fa fa-group"></i></div>
					{{isset($orders[0]->com_reg_number)?$orders[0]->com_reg_number:null}}<br/>

				</div>
   				<div class="order_dtl_contant_sec_li">
   					<div class="order_dtl_contant_sec_li_ico"><i class="fa fa-envelope-o"></i></div>
					{{isset($orders[0]->customermobile)?$orders[0]->customermobile:null}}<br/>
   				</div>
   				<div class="order_dtl_contant_sec_li">
   					<div class="order_dtl_contant_sec_li_ico"><i class="fa fa-mobile-phone"></i></div>
					{{isset($orders[0]->cityname)?$orders[0]->cityname:null}}<br/>
   				</div>
   				
   			</div>
   		</div>
   	
   </div>
   
   
   <div class="col-md-4">
   		
   		<div class="card-box-sec">
   			<div class="order_dtl_head">Options</div>
   			<div class="order_dtl_contant_sec">
   			
   				<div class="order_dtl_contant_sec_li" style="padding: 10px">
   					Pax <span style="float: right">@if(isset($orders[0]->com_pax))
							{{$orders[0]->com_pax}}
						@endif</span>
   				</div>
   				<div class="order_dtl_contant_sec_li" style="padding: 10px">
   					Single Rate <span style="float: right">@if(isset($orders[0]->com_single_rate	))
							{{$orders[0]->com_single_rate}}
						@endif</span>
   				</div>
   				<div class="order_dtl_contant_sec_li" style="padding: 10px">
   					Final Rate <span style="float: right">@if(isset($orders[0]->com_final_rate))
							{{$orders[0]->com_final_rate}}
						@endif</span>
   				</div>
   				<div class="order_dtl_contant_sec_li" style="padding: 10px">
   					&nbsp;
   				</div>
   				
   				
   			</div>
   		</div>
   	
   </div>
   
   
   
   <div class="col-md-12">
  		
  		<div class="card-box-sec" style="margin-top: 20px ">
   			<div class="order_dtl_head">Orders({{$orders[0]->com_order_id}})</div>
   			<div class="order_dtl_contant_sec">  
   				
   				<div class="col-md-6">
   				<div class="card-box-sec" style="margin: 20px 0">
   					<div style="background-color: #fff;    border-bottom: 1px #ececec solid;" class="order_dtl_head">Restaurant Address</div>
   					<div class="order_dtl_contant_sec_li" style="padding-left: 10px">
   						{{$orders[0]->cr_name}}<br>
{{$orders[0]->cr_address}}
    					</div>
   				</div>
   				</div>
   				
   				<div class="col-md-6">
   				<div class="card-box-sec" style="margin: 20px 0">
   					<div style="background-color: #fff;    border-bottom: 1px #ececec solid;" class="order_dtl_head">Delivery Address</div>
   					<div class="order_dtl_contant_sec_li" style="padding-left: 10px">
 {{isset($orders[0]->com_delivery_location)?$orders[0]->com_delivery_location:null}}<br/>
						 {{ isset($orders[0]->cityname)?$orders[0]->cityname:null }},
						 {{ isset($orders[0]->com_pincode)?$orders[0]->com_pincode:null }}
   					</div>
   				</div>
   				</div>
   				
   			</div>
   		</div>
   		
   </div>
   
	<div class="col-md-12">
	<div class="card-box-sec" style="padding: 10px;border: 0">
		<table class="table">
			<thead>
				<tr>				
				<th width="30%">Items</th>
				<th width=20%">Category Name</th>
				<th  width=20%">Description</th>
				<th  width=10%">Diet</th>
				</tr>
			</thead>
			<tbody>
			@if(count($details)>0)
				@foreach($details as $key=>$value)
				<tr>
				<td>{{$value->cod_menu_name}}</td>
				<td>{{$value->cod_menu_name}}</td>
				<td>{{$value->cod_menu_details}}</td>
				<td>{{$value->cod_diet}}</td>
				</tr>
				@endforeach
				@endif
			</tbody>
		</table>
		</div>
	</div>
  
  <div class="col-md-12">
	<div class="card-box-sec" style="padding: 10px;border: 0">
		<table class="table">
			<tbody>
				<tr>
				<td ><b>Single Rate</b> </td>
				<td ><b><div class="main_inner_class_track " style="width: 100%" id="single_rate" >{{$orders[0]->com_single_rate}}</div></b> </td>
				</tr>
				<tr>
				<td ><b>Pax</b> </td>
				<td ><b><div class="main_inner_class_track " style="width: 100%" id="pax">{{$orders[0]->com_pax}}</div></b> </td>
				</tr>
				<tr>
				<td ><b>Sub Total</b> </td>
				<td ><b><div class="main_inner_class_track " style="width: 100%" id="subtotal">{{$orders[0]->com_single_rate*$orders[0]->com_pax}}</div></b> </td>
				</tr><tr>
				<td ><b>Tax</b> </td>
				<td ><b><div class="main_inner_class_track " style="width: 100%" id="tax">{{isset($orders[0]->tax_rate)?$orders[0]->tax_rate:0}}</div></b> </td>
				</tr>
				<tr>
				<td ><b>Other Amount</b> </td>
				<td ><b><div class="main_inner_class_track " style="width: 100%" id="others">{{$extrasum[0]['total']}}</div></b> </td>
				</tr>
				<tr>
				<td ><b>Final Rate</b> </td>
				<td ><b><div class="main_inner_class_track " style="width: 100%" id="final_rate">{{ (($orders[0]->com_single_rate*$orders[0]->com_pax)+$orders[0]->tax_rate+$extrasum[0]['total']) }}</div></b> </td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>
  
  
   <div class="col-md-12">
  		
  		<div class="card-box-sec" style="margin-top: 20px ">
   			<div class="order_dtl_head">Extra Charges <a href="#"> <button type="submit" style="margin-top: px; border-radius: 4px;margin-left: 0;" class="on-default followups-popup-btn btn btn-primary ad-work-clear-btn" >Add New</button></a>
			</div>
   			<div class="order_dtl_contant_sec">  
				<div class="card-box-sec" style="padding: 10px;border: 0">
					<table class="table">
						<thead>
							<tr>				
							<th>Name</th>
							<th width="50%">Final Rate</th>
							<th>Action</th>
							</tr>
						</thead>
						<tbody id="chargelist">
                        @if(count($extradetail)>0)
		                     @foreach($extradetail as $item=>$val)
							   <tr>
								   <td>{{title_case($val->attribute_name)}}</td>
								   <td>{{ number_format($val->final_rate,2) }}</td>
								   <td><a onclick="deleteextracharge('{{$val->order_no}}','{{$val->slno}}')" class="btn button_table"><i class="fa fa-trash"></i></a></td>
							   </tr>
							  @endforeach
						@endif
						</tbody>
					</table>
			</div>
   			</div>
   		</div>
   	</div>
   	 <div class="col-md-12">

  		<div class="card-box-sec" style="margin-top: 20px ">
   			<div class="order_dtl_head">FollowUps</div>
   			<div class="order_dtl_contant_sec">
				<div class="card-box-sec" style="padding: 10px;border: 0">
					<table class="table">
						<thead>
							<tr>
							<th>Date Added</th>
							<th width="50%">Comment</th>
							<th>Status</th>
							</tr>
						</thead>
						<tbody id="followuplist">
                        @if(count($FollowUpsdetail)>0)
		                     @foreach($FollowUpsdetail as $item=>$val)
							   <tr>
								   <td>{{$val->datetime}}</td>
								   <td>{{ $val->comment }}</td>
								   <td>@if($val->status == 'P')Order Placed @elseif($val->status == 'C') Confirmed @elseif($val->status =='D') Delivered @else Cancelld @endif</td>
							   </tr>
							  @endforeach
						@endif
						</tbody>
					</table>
			</div>
   			</div>
   		</div>
   	</div>

   	<div class="col-md-12">
  		
  		<div class="card-box-sec" style="margin-top: 20px ">
   			<div class="order_dtl_head">Add Followup</div>
			<input type="hidden" id="checkvalue" name="checkvalue" value="N">
   			<div class="card-box-sec" style="padding: 10px;border: 0">
				<table class="table add_his_table">
					<tbody>
					<tr>				
						<td width="20%">Order Status</td>
							<td ><div class="main_inner_class_track " style="width: 100%"> 
								<select class="form-control" name="status" id="status">
									@foreach($CategoryOrderStatus as $key=>$val)

										<option value="{{$val->code}}">{{$val->status}}</option>
									@endforeach
								</select>
							</div></td>
						</tr>
						<tr>				
						<td valign="top" width="20%">Notify Customer</td>
						<td ><div class="main_inner_class_track " style="width: 100%"><input type="checkbox" name="yes" onclick="checkvalue(this.value)" id= "yes" value="Y"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
						</tr>
					    <tr>
						<td valign="top" width="20%">Comment</td>
						<td ><div class="main_inner_class_track " style="width: 100%"><textarea id="comment" name="comment" class="form-control"></textarea></div></td>
						</tr>
						<tr>	
						<td colspan="2"><a style="float: right" class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submitcomment()">Submit</a></td>
						</tr>	
					</tbody>
				</table>
			</div>
   		</div>
   </div>
	<div id="add_user" class="add-work-done-poppup-cc" style="display: none;">
		<div class="add-work-done-poppup">
			<div class="add-work-done-poppup-head">Add Extra Charges
				<a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
			</div>
			<div style="text-align:center;" id="branchtimezone"></div>
			<div class="add-work-done-poppup-contant">
				<div class="add-work-done-poppup-textbox-cc">
					<div class="add-work-done-poppup-textbox-box">

						<div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">
							<form enctype="multipart/form-data" id="extracharge_form" role="form" method="POST" action="" >
								<input type='hidden' id='url' value='{{$url}}' />
								<input type='hidden' id='userid' name="userid" />
								<input value="{{$orders[0]->com_order_id}}" type="hidden" id="order_no" name="order_no">
								<div class="main_inner_class_track ">
									<div class="group">
										<div style="position: relative">
											<label>Name</label>
											<input autocomplete="off"   id="name" name="name" type="text" class="form-control" onkeypress="return charonly(event);" style="background-color:transparent;" autofocus="true" required="">
										</div>
									</div>
								</div>

								<div class="col-xs-3 main_inner_class_track">
									<div class="form-group" id="status_p" style="display: block">
										<label>Final Rate</label>
										<input autocomplete="off"   id="fnalrate" name="fnalrate" type="text" class="form-control"  style="background-color:transparent;" autofocus="true" required="">
									</div>
								</div>

								<div class="main_inner_class_track" style="width:20%">
									<b><p class="" style="color: #000;float:right;cursor:pointer;display:none;background-color: burlywood;margin-top: 6px;padding: 2px 13px;line-height: 30px;" id="getimage" data-toggle="modal"  data-target="#myModal" data-title=""><a style="color: #000;">View image</a></p></b>

								</div>

								<div class="box-footer">
									<input type="hidden" name="type" id="type" value="insert" />
									<a id="inserting" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_charge('insert');">Submit</a>
								</div>
							</form>
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



    
@section('jquery')
<script>
  function checkvalue(val)
  {
      if($("#yes").is(":checked"))
	  {
		  $("#checkvalue").val('Y');
	  }
	  else{
		  $("#checkvalue").val('N');
	  }
  }
	function deleteextracharge(order_no,slno)
	{
		if(confirm('Are You Sure You Want To Continue?'))
		{
			$.ajax({
				method: "post",
				url: "../api/deleteextracharge",
				data: {"slno": slno, "order_no": order_no},
				cache: false,
				crossDomain: true,
				async: false,
				dataType: 'text',
				success: function (result) {
					var json_x = JSON.parse(result);
					$("#chargelist").html(json_x.result);
					if(json_x.sum)
					{
						var otheramount = json_x.sum;
					}
					else{
						var otheramount = 0;
					}
					$("#others").html(otheramount);
					var subtotal = $("#subtotal").html();
					var tax = $("#tax").html();
					$("#final_rate").html(parseFloat(subtotal)+parseFloat(tax)+parseFloat(otheramount));
					$('#name').val('');
					$('#fnalrate').val('');
				},
				error: function (jqXHR, textStatus, errorThrown) {
					$("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
				}
			});
		}
 	}
	$(".ad-work-close-btn").click(function()
	{
		$("#add_user").hide();
		$("#name").val();
		$("#finalrate").val('');
	});
	$(".followups-popup-btn").click(function() {
		$("#add_user").show();
		$("#name").val();
		$("#finalrate").val('');
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
	  if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
		  return false;
	  return true;
  }
	function submit_charge(type)
	{
		var table ;
		$('.notifyjs-wrapper').remove();
		$('input').removeClass('input_focus');
		$('select').removeClass('input_focus');
		var  name = $("#name").val();
		var  fnalrate = $("#fnalrate").val();
		var userid =$("#userid").val();
		var status =$("#status").val();
		var orderno = $("#orderno").val();

		if(name == '') {
			$("#name").focus();
			$.Notification.autoHideNotify('error', 'bottom right','Enter  Name.');
			return false;
		}
		if(fnalrate == '')
		{
			$("#fnalrate").focus();
			$.Notification.autoHideNotify('error', 'bottom right','Enter  Final Rate.');
			return false;
		}

		if(true)
		{
			var formdata = new FormData($('#extracharge_form')[0]);
			table = $('#chargelist');
			table.html('');
			var i=1;
			$.ajax({
				method: "post",
				url : "../api/submitextracharge",
				data : formdata,
				cache : false,
				crossDomain : true,
				async : false,
				processData : false,
				contentType: false,
				dataType :'text',
				success : function(result)
				{
					var json_x = JSON.parse(result);
					$("#chargelist").html(json_x.result);
					$("#others").html(json_x.sum);
					var subtotal = $("#subtotal").html();
					var tax = $("#tax").html();
					$("#final_rate").html(parseFloat(subtotal)+parseFloat(tax)+parseFloat(json_x.sum));
					$('#name').val('');
					$('#fnalrate').val('');
					$("#add_user").hide();
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(jqXHR.responseText);
					$("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
				}
			});
		}
	}
	function submitcomment()
	{
		var status = $("#status option:selected").val();
		var comment = $("#comment").val();
		var checkvalue = $("#checkvalue").val();
		var orderno = $("#orderno").val();
		if(!comment)
		{
			$("#comment").focus();
			$.Notification.autoHideNotify('error', 'bottom right','Enter Comment.');
			return false;
		}
		$.ajax({
			method: "post",
			url: "../api/submitcomment",
			data:{"status":status,"comment":comment,"order_no":orderno,"checkvalue":checkvalue},
			cache: false,
			crossDomain: true,
			async: false,
			dataType: 'text',
			success: function (result)
			{
				$("#followuplist").html(result);
				$('#comment').val('');
				if(document.getElementById('status').options[0].value != status){
                 document.getElementById('status').options[0].remove();}
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





