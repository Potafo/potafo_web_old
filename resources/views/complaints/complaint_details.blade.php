@extends('layouts.app')
@section('title','Potafo - Complaint Details')
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
           .order_dtl_contant_sec_li{padding-left: 7px;}
</style>
    
<div class="dashboard_main_container">
  @if(isset($complaints[0]))
  <div class="col-md-12">
		<h2 style="font-size: 18px;"><a style="color: #fff;background-color: #a98305;width: 30px; height: 30px; float: left; text-align: center;
    border-radius: 50%;" href="{{url('complaints')}}"><i class="ti-arrow-left"></i></a> &nbsp; Complaint No - <strong><?php echo isset($complaints[0]->ct_id)?$complaints[0]->ct_id:null; ?></strong></h2>
  </div>
   <div class="col-md-4">
   		
   		<div class="card-box-sec">
   			<div class="order_dtl_head">Complaints</div>
   			<div class="order_dtl_contant_sec">
   			
                            <div class="order_dtl_contant_sec_li">
   					ID -
   					<?php echo $complaints[0]->ct_id; ?>
                                       
   				</div>
                            <input value="<?php echo $complaints[0]->ct_id; ?>" type="hidden" id="compid" name="compid">
   				<div class="order_dtl_contant_sec_li" >
   					Date -
   					<?php echo date('d-m-Y',strtotime($complaints[0]->ct_date_of_complaint)); ?>
   				</div>
   				<div class="order_dtl_contant_sec_li" >
   					Entry Date -
   					<?php echo date('d-m-Y H:i:s',strtotime($complaints[0]->ct_entry_date)); //fa-credit-card ?>
   				</div>
   				
   			
   			</div>
   		</div>
   	
   </div>
    <div class="col-md-4" >
   		
   		<div class="card-box-sec">
   			<div class="order_dtl_head">Complaint Details</div>
   			<div class="order_dtl_contant_sec">
   			
   				<div class="order_dtl_contant_sec_li">
   					Category -
   					<?php echo isset($complaints[0]->cc_name)?$complaints[0]->cc_name:null; ?>
   				</div>
                            <?php
                            $color='';
                                if($complaints[0]->ct_priority=="Critical")
                                {
                                  $color= "style=' color: #d81414 !important;'";  
                                }  else if($complaints[0]->ct_priority=="Medium"){
                                  $color= "style='color: #f3d604 !important;'";    
                                }else if($complaints[0]->ct_priority=="Low"){
                                  $color= "style='color: #3214d8 !important;'";    
                                }
                                ?>
                                
                            <div class="order_dtl_contant_sec_li" >
                                
   					Priority -
                                        <span <?php echo $color; ?>><?php echo isset($complaints[0]->ct_priority)?$complaints[0]->ct_priority:null; ?></span>
   				</div>
   				<div class="order_dtl_contant_sec_li">
   					File -
                                        <?php if($complaints[0]->ct_images !='') { ?>
                                        <a  href="{{$siteUrl}}<?php echo isset($complaints[0]->ct_images)?$complaints[0]->ct_images:null; ?>" target="_blank" style="margin-left: 25px;">View</a>
                                        <?php }else { ?>
                                        No File
                                        <?php } ?>
   					
   				</div>
   				
   				
   				
   			</div>
   		</div>
   	
   </div>
    <div class="col-md-4" style="float: right;">
   		
   		<div class="card-box-sec">
   			<div class="order_dtl_head">Customer Details</div>
   			<div class="order_dtl_contant_sec">
   			
   				<div class="order_dtl_contant_sec_li">
   					Name -
   					<?php echo isset($complaints[0]->ct_customer_name)?$complaints[0]->ct_customer_name:null; ?>
   				</div>
                            <div class="order_dtl_contant_sec_li">
   					Contact -
   					<?php echo isset($complaints[0]->ct_customer_mobile)?$complaints[0]->ct_customer_mobile:null; ?>
   				</div>
   				<div class="order_dtl_contant_sec_li">
   					Status -
   					<?php echo isset($complaints[0]->ct_status)?$complaints[0]->ct_status:null; ?>
   				</div>
   				
   				
   				
   			</div>
   		</div>
   	
   </div>
   
   
   
   
   
   
   <div class="col-md-12">
  		
  		<div class="card-box-sec" style="margin-top: 20px ">
   			<div class="order_dtl_head">Complaint No:(#<?php echo $complaints[0]->ct_id; ?>) - 
                        <?php echo isset($complaints[0]->ct_heading)?$complaints[0]->ct_heading:null; ?>
                        </div>
   			
   		</div>
   		
   </div>
   
	<div class="col-md-12">
	<div class="card-box-sec" style="padding: 10px;border: 0">
		<table class="table">
			<thead>
				<tr>				
				
				<th>Description</th>
                               
				</tr>
			</thead>
			<tbody>
				<tr>
				
				
				<td><?php echo isset($complaints[0]->ct_descriptions)?$complaints[0]->ct_descriptions:null; ?></td>
                                
				</tr>
				
				
			</tbody>
		</table>
		</div>
	</div>
  

  
  
   <div class="col-md-12">
  		
  		<div class="card-box-sec" style="margin-top: 20px ">
   			<div class="order_dtl_head">Followup's</div>
   			<div class="order_dtl_contant_sec">  
				<div class="card-box-sec" style="padding: 10px;border: 0">
					<table class="table">
						<thead>
							<tr>				
							<th>Date</th>
							<th width="50%">Comments</th>
							<th>Status</th>
							<th>Customer Notified</th>
                                                        <th>Action</th>
							</tr>
						</thead>
						<tbody id="followuplist">
                                     @if(count($followups)>0)
                                        @foreach($followups as $item=>$val)
							<tr>				
							<td><?php echo isset($val->cf_datetime)?date('d-m-Y H:i:s',strtotime($val->cf_datetime)):null; ?></td>
							<td><?php echo isset($val->cf_comments)?$val->cf_comments:null; ?></td>
							<td><?php echo isset($val->cf_status)?$val->cf_status:null; ?></td>
							<td><?php echo isset($val->cf_notify_customer)?$val->cf_notify_customer:null; ?></td>
                                                        <td><a onclick="return compldel(<?php echo $val->cf_id; ?>,<?php echo $val->cf_slno; ?>)" class="btn button_table clear_edit" >
                                        <i class="fa fa-trash-o"></i>
                                    </a></td>
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
   			<div class="order_dtl_head">Add Followup's</div>
                        <input type="hidden" id="checkvalue" name="checkvalue" value="N">
						<div class="card-box-sec" style="padding: 10px;border: 0">
				<table class="table add_his_table">
					<tbody>
					<tr>				
						<td width="20%">Complaint Status</td>
							<td style="width:150px"><div class="main_inner_class_track " style="width: 100%"> 
                                                                <select class="form-control" name="compt_status" id="compt_status" <?php if($changpermision[0]->complaint_status_change == "N"){ ?> disabled="true"<?php } ?>>
                                                                    <option value="Active">Active</option>
								    <option value="Closed">Closed</option>
								</select>
							</div></td>
							<td width="20%" style="text-align:right; display:none;">Notify Customer</td>
                            <td ><div class="main_inner_class_track " style="width: 100%;display:none;">  <input style="width: 15px;height: 15px" type="checkbox" name="yes" onclick="checkvalue(this.value)" id= "yes" value="Y"></div></td>
						</tr>
						
					

						<tr>				
						<td valign="top" width="20%">Comments</td>
						<td colspan="3"><div class="main_inner_class_track " style="width: 100%">  <textarea class="form-control" id="comment" name="comment" ></textarea></div></td>
						</tr>
						<tr>	
						<td colspan="5"><a style="float: right" class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submitcomment()">Submit</a></td>
						</tr>	

					</tbody>
				</table>
			</div>
   		</div>
   </div>
   

  
@endif
 
</div>



    
@section('jquery')

   
     
<script>
    $(document).ready(function() {
         $('a[rel=popover]').popover({
              html: true,
              trigger: 'hover',
              placement: 'bottom',
              content: function(){return '<img src="'+$(this).data('img') + '" width="200" height="100"/>';}
            });
        });
        
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
  function submitcomment()
	{
		var status = $("#compt_status option:selected").val();
		var comment = $("#comment").val();
		var checkvalue ="N";// $("#checkvalue").val();
		var compid = $("#compid").val();
		if(!comment)
		{
			$("#comment").focus();
			$.Notification.autoHideNotify('error', 'bottom right','Enter Comment.');
			return false;
		}
		$.ajax({
			method: "post",
			url: "../api/submitcomment_cpl",
			data:{"status":status,"comment":comment,"compid":compid,"checkvalue":checkvalue},
			cache: false,
			crossDomain: true,
			async: false,
			dataType: 'text',
			success: function (result)
			{
				$("#followuplist").html(result);
				$('#comment').val('');
				if(document.getElementById('compt_status').options[0].value != status){
                                document.getElementById('compt_status').options[0].remove();}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
			}
		});
		return true;
	}
        function compldel(flid,slno)
        {
            if(confirm('Are you sure to delete?'))
            {
                //var siteUrl = $("#siteUrl").val();
                //var table = $('#example1').DataTable();
                $.ajax({
                    method: "get",
                    url: "../api/followup_delete/" + flid + "/" + slno,
                    cache: false,
                    crossDomain: true,
                    async: false,
                    dataType: 'text',
                    success: function (result) {
                       // var rows = table.rows().remove().draw();
                        var json_x = JSON.parse(result);
                        if (json_x.msg == 'deleted') {
                            swal({

                                title: "",
                                text: "Deleted Successfully",
                                timer: 1000,
                                showConfirmButton: false
                            });
                            window.location.reload();
                           
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
            return true;
        }
    </script>

 
@stop



@endsection





