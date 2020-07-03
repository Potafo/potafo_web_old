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
.calendar {
	text-align: center;
}

.calendar header {
	position: relative;
}

.calendar h2 {
	text-transform: uppercase;
	font-size: 21px;
}

.calendar thead {
	font-weight: 600;
	text-transform: uppercase;
}

.calendar tbody {
	color: #7c8a95;
}

.calendar tbody td:hover {
	border: 2px solid #00addf;
}

.calendar td {
	border: 2px solid transparent;
	border-radius: 50%;
	display: inline-block;
	    height: 2.3em;
    line-height: 2.3em;
    text-align: center;
    width: 3em;
}

.calendar .prev-month,
.calendar .next-month {
	color: #cbd1d2;
}

.calendar .prev-month:hover,
.calendar .next-month:hover {
	border: 2px solid #cbd1d2;
}

.current-day {
	background: #00addf;
	color: #f9f9f9;
}

.event {
	cursor: pointer;
	position: relative;
}
	.widget-chart{width: 100%;float:left;    padding: 11px;}
	
	.card-box{margin-bottom: 20px}

/*
.event:after {
	background: #00addf;
	border-radius: 50%;
	bottom: .5em;
	display: block;
	content: '';
	height: .5em;
	left: 50%;
	margin: -.25em 0 0 -.25em;
	position: absolute;
	width: .5em;
}
*/
	.widget-chart{height: 272px;overflow: auto}
	.card-box{    border-radius: 10px;overflow: hidden}

.btn-prev,
.btn-next {
	border: 2px solid #cbd1d2;
	border-radius: 50%;
	color: #cbd1d2;
	height: 2em;
	font-size: .75em;
	line-height: 2em;
	margin: -1em;
	position: absolute;
	top: 50%;
	width: 2em;
}
	
	.modal-title{color: #242424}

.btn-prev:hover,
.btn-next:hover {
	background: #cbd1d2;
	color: #f9f9f9;
}

.btn-prev {
	left: 6em;
}

.btn-next {
	right: 6em;
}
	.card-box {
		padding: 0px !important;width: 100%;float: left;}
	.table td{text-align: left}
	.header-title{padding: 14px;
    background-color: #001029;
    margin-bottom: 10px !important;
    color: #ffffff !important;}
</style>
<div class="dashboard_main_container">
    
    <div class="row">
    
       <div class="col-lg-4">
            <div class="card-box">
                <h4 style="background: linear-gradient(45deg, #40abab, #425aca)!important;" class="text-dark header-title m-t-0 m-b-30">Calandar</h4>
                <div class="widget-chart text-center">
                <div class="calendar">

			<header>				

				<h2>April</h2>

				<a class="btn-prev fontawesome-angle-left" href="#"></a>
				<a class="btn-next fontawesome-angle-right" href="#"></a>

			</header>
			
			<table>
			
				<thead>
					
					<tr>
						
						<td>Mo</td>
						<td>Tu</td>
						<td>We</td>
						<td>Th</td>
						<td>Fr</td>
						<td>Sa</td>
						<td>Su</td>

					</tr>

				</thead>

				<tbody>
					
					<tr>
						<td class="prev-month">29</td>
						<td class="prev-month">30</td>
						<td class="prev-month">31</td>
					
				<td>1</td>
<td>2</td>
						<td>3</td>
						<td>4</td>
					</tr>
					<tr>
						


						<td>5</td>
						<td>6</td>
						<td>7</td>
						<td>8</td>
<td class="event">9</td>
						<td class="current-day event">10</td>
						<td>11</td>
					</tr>
					<tr>
						<td>12</td>
						<td>13</td>
						<td>14</td>
						<td>15</td>
<td>16</td>
						<td>17</td>
						<td>18</td>
					</tr>
					<tr>
				
				<td>19</td>
						<td>20</td>
						<td>21</td>
						<td class="event">22</td>
<td>23</td>
						<td>24</td>
						<td>25</td>
					</tr>

					<tr>
						
					<td>26</td>
						<td>27</td>
						<td>28</td>
						<td>29</td>
<td>30</td>
						<td class="next-month">1</td>
<td class="next-month">2</td>
					</tr>
					

				</tbody>

			</table>

		</div> <!-- end calendar -->
                </div>
             </div>
		</div>	
        
        
        <div class="col-lg-4">
            <div class="card-box">
                <h4 style="background: linear-gradient(45deg, #173bea, #6078ea)!important;" class="text-dark header-title m-t-0 m-b-30">Next 5 Upcoming Events</h4>
                <div class="widget-chart text-center">
                
                <table class="table">
                       <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                 <th>Category</th>
                                 <th>Loc</th>
                             </tr>
                        </thead>
                        <tbody>
                                                                                                                                                                                                    
                              <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>Categ 1</td>
                                <td>Nadkav...</td>
                             </tr>
                             <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>Categ 1</td>
                                <td>Nadkav...</td>
                             </tr>
                             <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>Categ 1</td>
                                <td>Nadkav...</td>
                             </tr>
                              <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>Categ 1</td>
                                <td>Nadkav...</td>
                             </tr>
                              <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>Categ 1</td>
                                <td>Nadkav...</td>
                             </tr>
                                                       
                                                        
						</tbody>
					</table>
                
				</div>
     		</div>
     </div>
     
     
       <div class="col-lg-4">
            <div class="card-box">
                <h4 style="    background: linear-gradient(45deg, #31169e, #6078ea)!important;" class="text-dark header-title m-t-0 m-b-30">First Advance Collected</h4>
                <div class="widget-chart text-center">
                
                <table class="table">
                       <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                 <th>Advance</th>
                                 <th>Place</th>
                             </tr>
                        </thead>
                        <tbody>
                                                                                                                                                                                                    
                              <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>32000</td>
                                <td>Nadakkav</td>
                             </tr>
                             <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>32000</td>
                                <td>Nadakkav</td>
                             </tr>
                             <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>32000</td>
                                <td>Nadakkav</td>
                             </tr>
                             <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>32000</td>
                                <td>Nadakkav</td>
                             </tr>
                             <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>32000</td>
                                <td>Nadakkav</td>
                             </tr>
                             
						</tbody>
					</table>    
                             
      </div>
       </div>
       </div>
       
       
       <div class="col-lg-4">
            <div class="card-box">
                <h4 style="background: linear-gradient(45deg, #7f00ff, #001029)!important;" class="text-dark header-title m-t-0 m-b-30">New 5 Orders</h4>
                <div class="widget-chart text-center">
                
                <table class="table">
                       <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                 <th>Mob</th>
                             </tr>
                        </thead>
                        <tbody>
                                                                                                                                                                                                    
                              <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>+919876543210</td>
                             </tr>
                              <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>+919876543210</td>
                             </tr>
                              <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>+919876543210</td>
                             </tr>
                              <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>+919876543210</td>
                             </tr>
                              <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>+919876543210</td>
                             </tr>
                             
					</tbody>
      			</table>
       
        
    </div>
       </div>
          </div>
          
           <div class="col-lg-8">
            <div class="card-box">
                <h4 style="    background: linear-gradient(45deg, #260c69, #607D8B)!important;" class="text-dark header-title m-t-0 m-b-30">Follow Ups</h4>
                <div class="widget-chart text-center">
                
                <table class="table">
                       <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Rate</th>
                                <th>Categ</th>
                                 <th>Mob</th>
                                 <th>Follow up</th>
                             </tr>
                        </thead>
                        <tbody>
                                                                                                                                                                                                    
                              <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>35000</td>
                                <td>Categ</td>
                                <td>+919876543210</td>
                                <td><a href="#" data-toggle="modal" data-target=".bs-example-modal-sm"><span class="label label-success">Follow Up</span></a></td>
                             </tr>
                               <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>35000</td>
                                <td>Categ</td>
                                <td>+919876543210</td>
                                <td><a href="#" data-toggle="modal" data-target=".bs-example-modal-sm"><span class="label label-success">Follow Up</span></a></td>
                             </tr>
                               <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>35000</td>
                                <td>Categ</td>
                                <td>+919876543210</td>
                                <td><a href="#" data-toggle="modal" data-target=".bs-example-modal-sm"><span class="label label-success">Follow Up</span></a></td>
                             </tr>
                               <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>35000</td>
                                <td>Categ</td>
                                <td>+919876543210</td>
                                <td><a href="#" data-toggle="modal" data-target=".bs-example-modal-sm"><span class="label label-success">Follow Up</span></a></td>
                             </tr>
                               <tr>
                                <td>1/02/2020</td>
                                <td>Shahul</td>
                                <td>35000</td>
                                <td>Categ</td>
                                <td>+919876543210</td>
                                <td><a href="#" data-toggle="modal" data-target=".bs-example-modal-sm"><span class="label label-success">Follow Up</span></a></td>
                             </tr>
                              
                             
					</tbody>
      			</table>
       
        
    </div>
       </div>
          </div>
    
    
    
    
    

</div><!--dashboard_main_container-->

  

 
    
<div class="modal fade bs-example-modal-sm " tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="padding-right: 17px;">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title" id="mySmallModalLabel">Small modal</h4>
                                                </div>
                                                <div class="modal-body">
                                                 
                                                 <div class="form-group">
	                                                <label for="exampleInputEmail1">Rate</label>
	                                                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Rate">
	                                            </div>
                                                  <div class="form-group">
	                                                <label for="exampleInputEmail1">Text</label>
	                                                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Tex">
	                                            </div>
                                                 <button style="float: right" type="submit" class="btn btn-purple waves-effect waves-light">Submit</button>
                                                  
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div>



    
@section('jquery')

   
     
    

 
@stop



@endsection





