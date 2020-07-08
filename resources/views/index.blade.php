@extends('layouts.app')
@section('title','Potafo - Admin Dashboard')
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
    .content-page > .content{padding: 20px;}.table-responsive{float: left;width: 100%;}.portlet{float: left;width: 100%;box-shadow: 0px 1px 20px rgba(0, 0, 0, 0.10);}.portlet .portlet-heading .portlet-title{font-size: 19px; margin-bottom: 17px;}
    .table td{font-size: 14px;padding: 10px 8px !important;}
     .card:before {
    position: absolute;
    bottom: 0;
    left: -55px;
    z-index: 1;
    display: block;
    width: 60px;
    height: 75px;
    background-color: rgba(0, 0, 0, 0.10);
    content: "";
    -webkit-transform: skewX(40deg);
    -moz-transform: skewX(40deg);
    -ms-transform: skewX(40deg);
    -o-transform: skewX(40deg);
    transform: skewX(40deg);
}  .Location_btn{
            width: auto;
    padding:2px 15px;
    background-color: #4CAF50 !important;
    border: 1px solid #4caf50 !important;
    box-shadow: 5px 3px 12px #bdbdbd;
    border-bottom: 3px solid #197b1d !important;
    font-weight: bold;
    float: left;
    color: #fff;
    border-radius: 20px;
    margin: 8px 3px;
    cursor:pointer;
        }
        .Location_btn:hover{    background-color: #10bb17 !important;}
    .top_sm_anylt_sec{display: none}
	.Location_btn_red{
            width: auto;
    padding:2px 15px;
    background-color: #f91200 !important;
    border: 1px solid #f91200 !important;
    box-shadow: 5px 3px 12px #bdbdbd;
    border-bottom: 3px solid #690e0e !important;
    font-weight: bold;
    float: left;
    color: #fff;
    border-radius: 20px;
    margin: 8px 3px;
    cursor:pointer;
        }
        .Location_btn_red:hover{    background-color: #4CAF50 !important;}
    .top_sm_anylt_sec{display: none}
</style>
<div class="dashboard_main_container">
    
    <div class="row">
    
        <div class="col-md-3 stretch-card grid-margin">
              <div class="card bg-gradient-anth card-img-holder text-white">
                <div class="card-body">
                  <img src="public/assets/images/circle.svg" class="card-img-absolute" alt="circle-image">
                    <h6 class="card-text">All</h6>
                  <h4 class="font-weight-normal mb-3">Pending Order
                    <i class="fa fa-line-chart mdi-24px float-right"></i>
                  </h4>
                  <h2 class="mb-5"><?=$pending_orders[0]->total?></h2>
                  
                </div>
              </div>
         </div>
        <div class="col-md-3 stretch-card grid-margin">
              <div class="card bg-gradient-info card-img-holder text-white">
                <div class="card-body">
                  <img src="public/assets/images/circle.svg" class="card-img-absolute" alt="circle-image">
                    <h6 class="card-text">Today</h6>
                  <h4 class="font-weight-normal mb-3">Completed Order
                    <i class="fa fa-bar-chart  mdi-24px float-right"></i>
                  </h4>
                  <h2 class="mb-5"><?=$completed_orders[0]->total?></h2>
                  
                </div>
              </div>
         </div>
        <div class="col-md-3 stretch-card grid-margin">
              <div class="card bg-gradient-success card-img-holder text-white">
                <div class="card-body">
                  <img src="public/assets/images/circle.svg" class="card-img-absolute" alt="circle-image">
                    <h6 class="card-text">Today</h6>
                  <h4 class="font-weight-normal mb-3">Total Order
                    <i class="fa fa-signal mdi-24px float-right"></i>
                  </h4>
                  <h2 class="mb-5"><?=$total_orders[0]->total?></h2>
                  
                </div>
              </div>
         </div>
        <div class="col-md-3 stretch-card grid-margin">
              <div class="card bg-gradient-danger card-img-holder text-white">
                <div class="card-body">
                  <img src="public/assets/images/circle.svg" class="card-img-absolute" alt="circle-image">
                    <h6 class="card-text">Today</h6>
                  <h4 class="font-weight-normal mb-3">Cancelled Order
                    <i class="fa fa-pie-chart  mdi-24px float-right"></i>
                  </h4>
                  <h2 class="mb-5"><?=$cancelled_orders[0]->total?></h2>
                  
                </div>
              </div>
         </div>
        
    </div>
    
    <div class="row">

                    <div class="col-lg-12">

                        <div class="portlet"><!-- /primary heading -->
                            <div class="portlet-heading">
                                <h3 class="portlet-title text-dark text-uppercase">
                                    Today's Delivery Staffs
                                </h3>
                                <a onclick="reload_orderlist()" style='float:right'><i class="fa fa-refresh fa-spin" style="font-size:30px;color:red;cursor: pointer;"></i></span>
                                
                            </div>
                            <div id="portlet2" class="panel-collapse collapse in">
                                <div class="portlet-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th style='width: 10%;'>#</th>
                                                    <th style='width: 10%;'>Name</th>
                                                    <th style='width: 10%;'>Pending Order</th>
                                                    <th style='width: 10%;'>Total Orders</th>
                                                    <th style='width: 10%;'>Number</th>
                                                    <th style='width: 10%;'>Action</th>
													<th style='width: 10%;'>Location</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_fullist">
                                              <!--  @if(count($all)>0)
                                                <?php $i = 0; ?>
                                                  @foreach($all as $value)
                                               <?php $i++;  ?>   
                                                        <tr>
                                                            <td style='width: 10%;'><?=$i?></td>
                                                            <td style='width: 10%;'><?=$value['name']?></td>
                                                            <td style='width: 10%;'><span class="label label-purple"><?=$value['pending']?></span></td>
                                                            <td style='width: 10%;'><?=$value['all_order']?></td>
                                                            <td style='width: 10%;'><?=$value['mobile']?></td>
							    <td style="width: 10%;"><a class="Location_btn_red" onclick="stop_service_staff(<?=$value['staffid_on']?>)" style='float: left;'><p style="margin-bottom: 0;">stop</p></a></td>
								<td style="width: 10%;"><a class="Location_btn" onclick="openMap(<?=$value['latitude']?>,<?=$value['longitude']?>)";   style="float: left;"><p style="margin-bottom: 0;">Location</p></a></td>
                                                        </tr>
                                                  @endforeach
                                                @endif-->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
        
        <div class="col-lg-6" style="display:none">

                        <div class="portlet"><!-- /primary heading -->
                            <div class="portlet-heading">
                                <h3 class="portlet-title text-dark text-uppercase">
                                    Today's Most Ordered Restaurant
                                </h3>
                            </div>
                            <div id="portlet2" class="panel-collapse collapse in">
                                <div class="portlet-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Place</th>
                                                    <th>Ttl Orders</th>
                                                    <th>Number</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Rahamath</td>
                                                    <td>2nd Gate</td>
                                                    <td>30</td>
                                                    <td>9876543210</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Bun Club</td>
                                                    <td>Mavoor Road</td>
                                                    <td>28</td>
                                                    <td>9876543210</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>3</td>
                                                    <td>Adaminte Chay...</td>
                                                    <td>Beach</td>
                                                    <td>25</td>
                                                    <td>9876543210</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>4</td>
                                                    <td>Ojin</td>
                                                    <td>Nadakav</td>
                                                    <td>20</td>
                                                    <td>9876543210</td>
                                                </tr>
                                                <tr>
                                                    <td>5</td>
                                                    <td>Biriyani sulaim...</td>
                                                    <td>Nadakav</td>
                                                    <td>18</td>
                                                    <td>9876543210</td>
                                                </tr>
                                                

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
        
        


                </div>
    
    
    

</div><!--dashboard_main_container-->

  

 
    




    
@section('jquery')

 <script>
  $(document).ready(function()
    {reload_orderlist();
      
    });
  const openMap = (lat, long) => {
            const base_url = "https://www.google.com/maps/@";
            var map_link = base_url + lat + ',' + long + ',15z';

            //location.href = map_link;
            window.open(map_link, '_blank');

        }
 
function stop_service_staff(staffid)
{
	 swal({
                title: "Warning",
                text: "Are you sure you want to end service of this staff?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-white btn-md waves-effect',
                confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
                confirmButtonText: 'Yes',
                closeOnConfirm: false
            }, function (isConfirm)
            {
                if (isConfirm)
                {
	$.ajax({
                method: "post",
                url: "api/deliverystaff_attendance/"+staffid,
                data: {"by_admin":"1"},
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
				}
			}
			);
}
function reload_orderlist()
{
	$.ajax({
                method: "get",
                url: "load_todays_staff",
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (response)
                {
                    $('#table_fullist').html(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
}
</script> 
     
    

 
@stop



@endsection





