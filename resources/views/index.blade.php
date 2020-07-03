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
}
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
                                
                                
                            </div>
                            <div id="portlet2" class="panel-collapse collapse in">
                                <div class="portlet-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Pending Order</th>
                                                    <th>Total Orders</th>
                                                    <th>Number</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($all)>0)
                                                <?php $i = 0; ?>
                                                  @foreach($all as $value)
                                               <?php $i++;  ?>   
                                                        <tr>
                                                            <td><?=$i?></td>
                                                            <td><?=$value['name']?></td>
                                                            <td><span class="label label-purple"><?=$value['pending']?></span></td>
                                                            <td><?=$value['all_order']?></td>
                                                            <td><?=$value['mobile']?></td>
                                                        </tr>
                                                  @endforeach
                                                @endif
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

   
     
    

 
@stop



@endsection





