<script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZoizgDw3-h_w_SJ3IlNjBAcnsFuco8Bw"></script>
<!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"> </script>-->
<link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<script src="{{asset('public/assets/script/common.js') }}" type="text/javascript"></script>

<div id="googleMap_click" >Click Here To Load Map</div>

        <div id="googleMap" style="width:100%;height:100%;"></div>

        <input type="hidden" id="la" name="la" value="<?=$latitude?>">
        <input type="hidden" id="lo" name="lo" value="<?=$longitude?>">

      <!--<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?v=3&amp;sensor=false"></script>-->


        <input type="text" id="latitude" name="latitude" value="<?=$latitude?>"  hidden=""/>
        <input type="text" id="longitude" name="longitude" value="<?=$longitude?>" hidden="" />   

<script type="text/javascript">
   
  
   // A $( document ).ready() block.
  // document.addEventListener("DOMContentLoaded", function(){
   
   function initialize() {
   //console.log( "init!" );
   directionsService = new google.maps.DirectionsService();
  directionsDisplay = new google.maps.DirectionsRenderer();
 var latitude1=parseFloat(document.getElementById('latitude').value);
   var longitude1=parseFloat(document.getElementById('longitude').value);
    var e = new google.maps.LatLng(latitude1,longitude1), t = {
            zoom: 15,
            center: e,
            panControl: !0,
            scrollwheel: 1,
            scaleControl: !0,
            overviewMapControl: !0,
            overviewMapControlOptions: {opened: !0},
            mapTypeId: google.maps.MapTypeId.terrain
        };
        map = new google.maps.Map(document.getElementById("googleMap"), t)
        geocoder = new google.maps.Geocoder
        marker = new google.maps.Marker({
            position: e,
            map: map
        });
        map.streetViewControl = false
        infowindow = new google.maps.InfoWindow({
            content: "(" + latitude1 + "," + longitude1 + ")"
        });
        google.maps.event.addListener(map, "click", function (e) {
            marker.setPosition(e.latLng);
            var t = e.latLng
            o = "(" + t.lat().toFixed(6) + ", " + t.lng().toFixed(6) + ")";
            infowindow.setContent(o),
            document.getElementById("lat").value = t.lat().toFixed(6),
            document.getElementById("lng").value = t.lng().toFixed(6)
        });
        directionsDisplay.setMap(map);
}
 

/* var interval = setInterval(function() {
    if(document.readyState == 'complete') {
        clearInterval(interval);
        initialize();
         google.maps.event.addDomListener(window, 'load', initialize);
    }    
}, 100);*/
       // console.log( "ready!" );
      
   // });

 var mapDiv = document.getElementById('googleMap_click');
           
          // google.maps.event.addDomListener(mapDiv, 'click', initialize);
google.maps.event.addDomListener(mapDiv, 'click', initialize);
   
   </script>
 
<script>
 //document.addEventListener("DOMContentLoaded", function(){
 //window.addEventListener("load", function(){  
    // google.maps.event.addDomListener(window, 'load', initialize);

</script>           
     <link href="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css">
        <script src="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
        <script src="{{ asset('public/assets/pages/jquery.sweet-alert.init.js') }}"></script>
         <style>#datatable-fixed-col_filter{display:none}table.dataTable thead th{white-space:nowrap;padding-right: 20px;}
        .on-default	{margin-left:10px;}div.dataTables_info {padding-top:13px;}
    </style>
    
@section('jquery')

     
    {{--<script src="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.js') }}"></script>--}}
    <link href="{{ asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('public/assets/dark/plugins/custombox/css/custombox.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <!--<script src="{{ asset('public/assets/js/angular.min.js') }}"></script>-->
    <script src="{{asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />

    

@stop



   






