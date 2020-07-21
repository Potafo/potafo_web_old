    <!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ csrf_token() }}">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="{{asset('public/assets/images/favicon_1.png')}}">
    <title>@yield('title')</title>

	@yield('css')
    <!--Morris Chart CSS -->    
    <link rel="stylesheet" href="{{asset('public/assets/plugins/morris/morris.css')}}">

     <link href="{{asset('public/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet" media="screen"> 
 <link href="{{asset('public/assets/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="{{asset('public/assets/css/bootstrap-multiselect.css')}}" type="text/css">
       
<!--     <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">-->
     
        <link href="{{asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('public/assets/plugins/datatables/fixedColumns.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    

    <link href="{{asset('public/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('public/assets/css/core.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('public/assets/css/components.css')}}" rel="stylesheet" type="text/css" />
    <noscript id=deferred-styles>   
    <link href="{{asset('public/assets/css/icons.css')}}" rel="stylesheet" type="text/css" />
    </noscript>
    <link href="{{asset('public/assets/css/pages.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('public/assets/css/responsive.css')}}" rel="stylesheet" type="text/css" />
    <script src="{{asset('public/assets/js/jquery-3.2.1.min.js')}}"
              integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
              crossorigin="anonymous"> </script>
    
<script src="{{asset('public/assets/js/modernizr.min.js')}}"></script>
    <script src="{{asset('public/assets/script/common.js') }}" type="text/javascript"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googlekey }}&libraries=places&language=en"></script>
<style>
  
  .not-active {
    pointer-events: none;
    cursor: default;
  }
</style>

</head>


<body class="fixed-left">

<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">
        
        <!-- LOGO -->
        <div class="topbar-left">
            <div class="text-center">
                <a href="" class="logo"><img src="{{asset('public/assets/images/comp_logo.png') }}"></a>
             
            </div>
        </div>
        <div class="top_sm_anylt_sec">
    <div class="col-xs-6 col-sm-4 col-lg-2 col-xl-3 div_center sm_anlyt">
          <div class="card gradient-purpink">
            <div class="card-body">
              <div class="media">
              <div class="media-body text-center">
                  <input type="hidden" id="site_url" value="<?=$siteUrl?>">
                  
                  <h4 class="text-white" id="total_orders"></h4>
                <span class="text-white">Total Order</span>
              </div>
			  <div class="align-self-center"><span id="dash-chart-1"></span></div>
            </div>
            </div>
          </div>
        </div>
    <div class="col-xs-6 col-sm-4 col-lg-2 col-xl-3 div_center sm_anlyt">
          <div class="card gradient-ohhappiness">
            <div class="card-body">
              <div class="media">
              <div class="media-body text-center">
                <h4 class="text-white" id="unassigned_orders"></h4>
                <span class="text-white">Unassigned orders</span>
              </div>
			  <div class="align-self-center"><span id="dash-chart-1"></span></div>
            </div>
            </div>
          </div>
        </div>
    <div class="col-xs-6 col-sm-4 col-lg-2 col-xl-3 div_center sm_anlyt">
          <div class="card gradient-ibiza">
            <div class="card-body">
              <div class="media">
              <div class="media-body text-center">
                <h4 class="text-white" id="delivery_pending_orders"></h4>
                <span class="text-white">Delivery Pending</span>
              </div>
			  <div class="align-self-center"><span id="dash-chart-1"></span></div>
            </div>
            </div>
          </div>
        </div>
    
    
    
</div>

        <!-- Button mobile view to collapse sidebar menu -->
        <div class="navbar navbar-default" role="navigation" >
            <div class="container">
                <div class="">
                    <div class="pull-left">
                        <button class="button-menu-mobile open-left waves-effect waves-light">
                            <i class="md md-menu"></i>
                        </button>
                        <span class="clearfix"></span>
                    </div>
                    
                    <ul class="nav navbar-nav navbar-right pull-right">
                        
                        <li class="hidden-xs">
                            <a href="#" id="btn-fullscreen" class="waves-effect waves-light"><i class="icon-size-fullscreen"></i></a>
                        </li>
                        
                        <li class="{{ Request::is('logout')? 'active' : '' }}"><a  href="{{ url('logout') }}"  onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="ti-power-off m-r-10 text-danger"></i> Logout</a></li>

                        
                    </ul>
                    
                </div>
                
                <!--/.nav-collapse -->
            </div>
        </div>
        
        <div id="SecondsUntilExpire" style="display:none">
            
        </div>
    </div>
    <!-- Top Bar End -->
    
 <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ url('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
    <!-- ========== Left Sidebar Start ========== -->
    <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>
    <div class="left side-menu">
        <div class="sidebar-inner slimscrollleft">
            <!--- Divider -->
            <div id="sidebar-menu">
               
                <ul>
                  @if($designation_logged[0]->designation !="Admin" || $designation_logged[0]->designation !="Super_Admin")
                  <li>
                    <a href="{{ url('dashboard') }}" class="waves-effect"><i class="fa fa-dashboard"></i><span>Dashboard</span></a>
                    </li>
                  @else
                    <li>
                    <a href="{{ url('index') }}" class="waves-effect"><i class="fa fa-dashboard"></i><span>Dashboard</span></a>
                    </li>
                    @endif
                 @foreach($module as $moduleshow)
                 @if($moduleshow->count>1)
            
                  <li>
                           <a href="#" class="waves-effect"><i class="ti-dropbox-alt"></i>
                            <span>{{str_replace('_', ' ', $moduleshow->module_name)}}</span>
                            <span class="menu-arrow"></span>
                           </a>
                      <ul>
                          @foreach($modulesublist as $sub)
                          @if($moduleshow->module_name == $sub->module_name)                          
                              <li><a href="{{url($sub->page_link)}}">{{str_replace('_', ' ',$sub->sub_module)}}</a></li>
                           @endif
                           @endforeach
                      </ul>
                   </li>
               @else
                 <li>
                      <a href="{{ url($moduleshow->page_link) }}"class="waves-effect"><i class="ti-dropbox-alt"></i><span>{{str_replace('_',' ' ,$moduleshow->module_name)}}</span></a>
                 </li>
               @endif
                
               
                 @endforeach
                </ul>
                  
                   
                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ url('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>

                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <!-- Left Sidebar End -->
    <div class="content-wrapper">
    	<div class="content-wrapper">

        	<div class="content-page">
        		<div class="content">

        			@yield('content')

        		</div>
        	</div>
        </div>

    </div>
    <!-- Scripts -->
    <!-- Right Sidebar -->
    
    <!-- /Right-bar -->

</div>
<!-- END wrapper -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">You have been idle for a while...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Do you want to stay on the page or logout?
        
        You will be logged out within <span id="popupseconds"></span> seconds.          
        
      </div>
        
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" href="{{ url('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Logout</button>
        <button type="button" class="btn btn-primary" onclick="stay_fn()">Stay here</button>
      </div>
    </div>
  </div>
</div>
    
    

    
           

    
    
<!--<script src="{{asset('public/assets1/js/jquery.min.js') }}"></script>-->
<script src="{{ asset('js/app.js') }}"></script>

<script>
    var resizefunc = [];
</script>
<link href="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
<script src="{{ asset('public/assets/pages/jquery.sweet-alert.init.js') }}"></script>
<!-- <script src="{{ asset('public/assets/picker/jquery-ui.js') }}"></script>-->

<!-- jQuery  -->




<!--<script src="public/assets/js/jquery.min.js"></script>-->
<!--<script src="public/assets/js/bootstrap.min.js"></script>-->
<script src="{{asset('public/assets/js/detect.js') }}"></script>
<script src="{{asset('public/assets/js/fastclick.js') }}"></script>

<script src="{{asset('public/assets/js/jquery.slimscroll.js') }}"></script>
<script src="{{asset('public/assets/js/jquery.blockUI.js') }}"></script>
<script src="{{asset('public/assets/js/waves.js') }}"></script>
<script src="{{asset('public/assets/js/wow.min.js') }}"></script>
<script src="{{asset('public/assets/js/jquery.nicescroll.js') }}"></script>
<script src="{{asset('public/assets/js/jquery.scrollTo.min.js') }}"></script>

<!--   <script src="{{asset('public/assets/js/jquery.js') }}"></script>-->
    <script src="{{asset('public/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>


<!-- jQuery  -->
    <!--
<script src="{{asset('public/assets/plugins/waypoints/lib/jquery.waypoints.js') }}"></script>
<script src="{{asset('public/assets/plugins/counterup/jquery.counterup.min.js') }}"></script>


<script src="{{asset('public/assets/plugins/peity/jquery.peity.min.js') }}"></script>

<script src="{{asset('public/assets/plugins/morris/morris.min.js') }}"></script>
<script src="{{asset('public/assets/plugins/raphael/raphael-min.js') }}"></script>

<script src="{{asset('public/assets/plugins/jquery-knob/jquery.knob.js') }}"></script>

<script src="{{asset('public/assets/pages/jquery.dashboard.js') }}"></script>
-->

    
<script src="{{asset('public/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>

    <script src="{{asset('public/assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{asset('public/assets/plugins/datatables/buttons.bootstrap.min.js') }}"></script>
    <script src="{{asset('public/assets/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{asset('public/assets/plugins/datatables/pdfmake.min.js') }}"></script>
<script src="{{asset('public/assets/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{asset('public/assets/plugins/datatables/buttons.html5.min.js') }}"></script>
<script src="{{asset('public/assets/plugins/datatables/buttons.print.min.js') }}"></script>
<script src="{{asset('public/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>

<script src="{{asset('public/assets/plugins/datatables/dataTables.bootstrap.js') }}"></script>
<!--
<script src="{{asset('public/assets/js/chosen.jquery.js" type="text/javascript') }}"></script>
<script src="{{asset('public/assets/js/docsupport/init.js" type="text/javascript') }}" charset="utf-8"></script>
-->

<script type="text/javascript" src="{{asset('public/assets/js/bootstrap-datetimepicker.js') }}" charset="UTF-8"></script>
<!--<script type="text/javascript" src="{{asset('public/assets/js/locales/bootstrap-datetimepicker.fr.js') }}" charset="UTF-8"></script>-->





<script src="{{asset('public/assets/pages/datatables.init.js') }}"></script>
    
<script src="{{asset('public/assets/js/jquery.core.js') }}"></script>
<script src="{{asset('public/assets/js/jquery.app.js') }}"></script>
<script type="text/javascript" src="{{asset('public/assets/js/prettify.min.js') }}"></script>
 <script type="text/javascript" src="{{asset('public/assets/js/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('public/assets/plugins/notifyjs/js/notify.js') }}"></script>
<script src="{{ asset('public/assets/plugins/notifications/notify-metro.js') }}"></script>
    
    
    
    
<script type="text/javascript">
            $(document).ready(function() {
                window.prettyPrint() && prettyPrint();
            });
        </script>
    
 <script type="text/javascript">
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
             });
        </script>


@yield('jquery')

<script>
    $(document).ready(function(){
            var site_url = $('#site_url').val();
            var staffid  =   $("#staff_id").val();

        $.ajax({
               method: "get",
               url : site_url+'api/view_order_details_all',
               cache : false,
               data : {'staffid' : staffid},
               crossDomain : true,
               async : false,
               dataType :'text',
               success : function(result)
               { 
                   var objc = JSON.parse(result);
                  $('#delivery_pending_orders').html(objc.total_del_pen);
                  $('#unassigned_orders').html(objc.total_unasgnd);
                  $('#total_orders').html(objc.total_orders_det);
//                  
                             
               },
               error: function (jqXHR, textStatus, errorThrown) {
                   alert(errorThrown);
                   $("#errbox").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
               }
           });
            
    });
</script>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.counter').counterUp({
            delay: 100,
            time: 1200
        });

        $(".knob").knob();

    });
</script>
<script>
    var email = localStorage.email;
    $("#email").val(email);
</script>
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#datatable-fixed-col').DataTable({
            scrollY: "400px",
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            fixedColumns: {
                leftColumns: 1,
                rightColumns: 1
            }
        });
    });
    TableManageButtons.init();

    function logout_fn()
    {
        
    }

</script>

 <script>
	function changepassword()
	{
	    var oldpsw = $("#password").val();
            var newpsw = $("#newpsw").val();
            var conpsw = $("#conpsw").val();
            var routeurl = $("#url").val();
            var token = $("#token").val();
            var url =routeurl+'changing_password?token='+token;
            var email = localStorage.email; 
            var pass  = localStorage.pass;
            //alert(email);
            //alert(routeurl);
            //alert(url);    
                
        if(oldpsw =='')
        {
             swal({
							
                            title: "",
                            text: "Please Enter Old Password",
                            timer: 1000,
                            showConfirmButton: false
                        });
            $("#password").focus();
            return false;
        }
        if(newpsw =='')
        {
            swal({
							
                            title: "",
                            text: "Please Enter New Password",
                            timer: 1000,
                            showConfirmButton: false
                        });
            $("#newpsw").focus();
            return false;
        }
        if(conpsw =='')
        {
            swal({			
                            title: "",
                            text: "Please Enter Confirm Password",
                            timer: 1000,
                            showConfirmButton: false
                        });
            $("#conpsw").focus();
            return false;
        }
        if(newpsw!=conpsw)
        {
             swal({
							
                            title: "",
                            text: "Password Do Not Match! Please Re-Enter",
                            timer: 1000,
                            showConfirmButton: false
                        });
            $("#conpsw").focus();
           return false;
        }

       if(true)
       {
           var data= {"token" :token,"oldpassword":oldpsw,"new_password":newpsw,"con_password":conpsw,"email":email,"pass":pass};
           
           $.ajax({
               method: "post",
               url : url,
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
                       
                        swal({
							
                            title: "",
                            text: "Password Successfully Successfully",
                            timer: 1000,
                            showConfirmButton: false
                        });
                        window.location.href="{{url('logout')}}";
                    }
                    else if((json_x.msg)=='Wrong Old Password')
                    {
                        swal({
							
                            title: "",
                            text: "Please Check Your Old Password",
                            timer: 1000,
                            showConfirmButton: false
                        });
                        return false;

                    }
                   
               },
               error: function (jqXHR, textStatus, errorThrown) {
                   alert(errorThrown);
                   $("#errbox").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
               }
           });

      }
        return false;

	  }
	
	
 </script>
 
 <script>
   /* var IDLE_TIMEOUT =6000;//seconds
    var _idleSecondsTimer = null;
    var _idleSecondsCounter = 0;

    document.onclick = function() {
        _idleSecondsCounter = 0;
    };

    document.onmousemove = function() {
        _idleSecondsCounter = 0;
    };  

    document.onkeypress = function() {
        _idleSecondsCounter = 0;
    };

    _idleSecondsTimer = window.setInterval(CheckIdleTime, 1000);

    function CheckIdleTime() {
        _idleSecondsCounter++;
            var oPanel = document.getElementById("SecondsUntilExpire");
            if (oPanel)
                oPanel.innerHTML = (IDLE_TIMEOUT - _idleSecondsCounter) + "";

            if (_idleSecondsCounter >= IDLE_TIMEOUT) {
                window.clearInterval(_idleSecondsTimer);
                idle_func();   
            }
    }
    function idle_func(){
        
        $('#exampleModal').modal('show'); 
        var popup_timeout=3;//timeout for popup in seconds
        var popup_timer=null;
        var popup_counter=0;
        var path = window.location.pathname;
        var url_count = path.split('/').length -3;
        var path_url = "../";
        var str = path_url.repeat(parseInt(url_count));
        popup_timer = window.setInterval(CheckIdlePopup, 1000);
        function CheckIdlePopup()
        {
        popup_counter++;
            var oPanel = document.getElementById("popupseconds");
            if (oPanel)
                oPanel.innerHTML = (popup_timeout - popup_counter) + "";
            if (popup_counter >= popup_timeout)
            {
                window.clearInterval(_idleSecondsTimer);
                window.location.href=str+'logout';
            }
    }
        
    }
    */
    function logout_btn(){
        window.location.href='logout';
    }
    
    function stay_fn(){
        location.reload();
    }
     </script>
<script>var loadDeferredStyles=function(){var b=document.getElementById("deferred-styles");var a=document.createElement("div");a.innerHTML=b.textContent;document.body.appendChild(a);b.parentElement.removeChild(b)};var raf=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||window.msRequestAnimationFrame;if(raf){raf(function(){window.setTimeout(loadDeferredStyles,0)})}else{window.addEventListener("load",loadDeferredStyles)};</script>

</body>

</html>