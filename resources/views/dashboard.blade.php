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
    
    


    Welcome 
    
    
    
    
    

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





