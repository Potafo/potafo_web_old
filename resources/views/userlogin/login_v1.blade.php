<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ app()->getLocale() }}">
<head>
    <?php echo
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: text/html');?>
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potafo | Login</title>
<!--           <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">-->

    <style>
        .loader_img{
            position: absolute;
            margin: auto;
            top: 100px;
            bottom: 0;
            width: 40px;
            height: 40px;
            right: 0;
            left: 0;
            display:none;
            z-index:1;
        }
        .sweet-alert .btn-lg {
            font-size: 15px !important;
        }
        .btn.focus, .btn:focus, .btn:hover {
            color: #333;
            text-decoration: none;
        }
        .btn-primary, .btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .btn-primary.focus, .btn-primary:active, .btn-primary:focus, .btn-primary:hover, .open > .dropdown-toggle.btn-primary {
            background-color: #5d9cec !important;
            border: 1px solid #5d9cec !important
        }
        .btn-group-lg>.btn, .btn-lg {
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.3333333;
            border-radius: 6px;
        }
        button {
            overflow: visible;
        }
        .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
            font-family: inherit;
            font-weight: 500;
            line-height: 1.1;
            color: inherit;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #505458;
            font-family: "Source Sans Pro", "Helvetica Neue", Helvetica, Arial, sans-serif;
            margin: 10px 0;
        }
        button, select {
            text-transform: none;
        }
        .sweet-alert .btn-lg {
            font-size: 15px !important;
        }
        button, html input[type=button], input[type=reset], input[type=submit] {
            -webkit-appearance: button;
            cursor: pointer;
        }
        .btn-primary, .btn-success, .btn-default, .btn-info, .btn-warning, .btn-danger, .btn-inverse, .btn-purple, .btn-pink {
            color: #ffffff !important;
        }

        .btn
        {
            border-radius: 3px;
            outline: none !important;
        }
        .btn-group-lg>.btn, .btn-lg {
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.3333333;
            border-radius: 6px;
        }
        
        .btn-primary{background-color: transparent !important;color: #757575 !important;border: solid 1px #ccc !important}
        .showSweetAlert .btn-primary:hover{background-color: transparent !important;color: #757575 !important;border: solid 1px #ccc !important}
        .sweet-alert{width: 285px !important; height: 101px; padding-top: 10px !important;    box-shadow: 1px 4px 15px #ccc;}
        .sweet-overlay {background-color: rgba(247, 247, 247, 0.7) !important;}
        .sweet-alert h2{    font-size: 17px;}
    </style>
</head>
<link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/component.css') }}" />
<link href="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('public/assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/assets/css/style.css') }}" rel="stylesheet" />
<script src="{{ asset('public/assets/script/jquery.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('public/assets/js/angular.min.js') }}"></script>
<script src="{{ asset('public/assets/js/modernizr.custom.js') }}"></script>
<script src="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
<script src="{{ asset('public/assets/pages/jquery.sweet-alert.init.js') }}"></script>

<body onload="load_ip()">

<div class="container" id="divID" ng-controller="myCtrl">

   <div class="login_top_header">
        <span><img src="{{ asset('public/assets/images/comp_logo.png') }}" /></span>
    </div>

    <div  >
    <form class="form-horizontal simform" method="POST" action="{{ route('login') }}"  >
 {{ csrf_field() }}
     

      <form id="frm_login" name="frm_login" class="simform" >
        <input type="hidden" name="url" id="url" value="{{ $url }}" />
        <input type="hidden" name="ip" id="ip" value=""/>
     {!! Form::open(['url'=>'userexistcheck
        ', 'name'=>'theForm','id'=>'theForm','class'=>'simform','method'=>'get']) !!}
        <div class="simform-inner">
            <ol class="login">
                <li class="current">
                    <div class="group">
                        <input type="text" class="form-control"onkeyup="myfunc()" id="username" name="username" value="" placeholder="Username" autocomplete="off">
                 
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <div id="emailcheck" class="textbox_icon_box">
                        <span style="color: green; display: none;" id="checksuccess">


                            <i class="fa fa-check" aria-hidden="true" ></i> </span>
                            <span id="check"  style="display: none;"><i class="fa fa-times" aria-hidden="true"></i></span>
                        </div>
                      <label style="top: -10px;font-size: 14px;color: #5264AE;" class="label user-name" for="username">USER NAME</label>
                    </div>
                </li>
                <li class="current">
                    <div class="group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">

                        <span class="highlight"></span>
                        <span class="bar"></span>
						<div class="textbox_icon_box">
                        	<i class="fa fa-lock" aria-hidden="true"></i>
                        </div>
                        <label style="top: -10px;font-size: 14px;color: #5264AE;"  class="label" for="password">PASSWORD</label>
                    </div>
                </li>

            </ol><!-- /login -->
            <div class="controls">
                
                <button type="button" class="next next_btn" onclick="return submit_login();">Login</button>
                    		<span class="number" style="display:none;">
								<span id="count" class="number-current"></span>
								<span class="number-total"></span>
							</span>
                <span class="error-message"></span>
                
            </div>
            <p id="warning_msg" style="font-size: 12px;color: red;float:left;width:100%;    margin-top: -11px;"></p>
            <div class="loader_img" id="loginloader">
                <img src="{{ asset("public/assets/images/ajax-loader.gif") }}">
            </div>

            <div ng-model="PostDataResponse"></div>
            <div ng-model="ResponseDetails"></div>
        </div>
        {!! Form::close() !!}
    </form>
    </form>



    </div>
        <span class="final-message"></span>
</div>
    

<div class="paymnet_section_alert_popup_cc paymnet_alert" id="paymnet_alert" style="display:none">
    <div class="paymnet_section_alert_popup">
        <div class="paymnet_section_alert_popup_head_img"><img src="{{asset ('public/assets/images/alert-ico.png') }}"></div>
        <div class="paymnet_section_alert_popup_head">ALERT</div>
        <div class="paymnet_section_alert_popup_contant">
            Please renew your subscription to enjoy your Live Reports.  Please contact Expodine Team for further assistance
        </div>
        <div class="paymnet_section_alert_popup_close" onclick="subs_pop_up()">CLOSE</div>
    </div>
</div><!--paymnet_section_alert_popup_cc-->
<div class="paymnet_section_alert_popup_cc branch_permission_alert"  style="display:none">
    <div class="paymnet_section_alert_popup">
        <div class="paymnet_section_alert_popup_head_img"><img src="{{asset ('public/assets/images/alert-ico.png') }}"></div>
        <div class="paymnet_section_alert_popup_head">ALERT</div>
        <div class="paymnet_section_alert_popup_contant">
            Sorry Branch Permission is not defined for this User
        </div>
        <div class="paymnet_section_alert_popup_close" onclick="subs_pop_up()">CLOSE</div>
    </div>
</div><!--paymnet_section_alert_popup_cc-->
    
    
<script src="{{ asset('public/assets/js/classie.js') }}"></script>
<script src="{{ asset('public/assets/angular/app.js') }}"></script>
<script src="{{ asset('public/assets/js/stepsForm.js') }}"></script>

<script>
    $(document).ready(function()
    {
        $("#username").focus();
    });
    function load_ip(){
        $.ajax({
                        method:"get",
                        url: "https://geoip-db.com/jsonp",
                        jsonpCallback: "callback",
                        dataType: "jsonp",
                        success:function(result){                            
                            $('#ip').val(result.IPv4);
                        },
                        error:function(jqxhr, textStatus, errorThrown){
                            $("#errbox").text(jqxhr.responseText); // @text = response error, it is will be errors: 324, 500, 404 or anythings else
                        }
                    });
    }
    function myfunc()
    {
        var routeurl = $("#url").val();
        var email    = $('#username').val(); //alert(email);
        
        //alert(routeurl);
        var data= { "email":email};


             $.ajax({
            type: "get",
            url: routeurl+'emailcheck',
            data: data,
            success: function (result)
            {
        if(result =='exist')
                { 
                    if(email == '')
                    {
                         $('#checksuccess').css('display','none');
                    }
                    else{
                    $('#checksuccess').css('display','block');
                    $('#check').css('display','none');
                    
                    }
                  
                }else{
                    if(email == '')
                {
                    $('#check').css('display','none');
                }
                else{
                    $('#check').css('display','block');
                   $('#checksuccess').css('display','none');
                }
                   
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#errbox").text(jqXHR.responseText);
                alert(error)
            }
        });
    }

    function subs_pop_up(){
            $('.paymnet_alert').css('display','none');
            $('.branch_permission_alert').css('display','none');
    }
            $('#password').on("keypress", function(e) {
        if (e.keyCode == 13) {
             var email    = $('#email').val();
             if(email!=''){
                return submit_login();
             }
             else{
                  $("#warning_msg").html("Please Enter UserName");
             }
            alert("Enter pressed");
            return false; // prevent the button click from happening
        }
});
    function submit_login()
    {
        localStorage.clear();
        var email = document.getElementById('username').value;
        var passwrd = document.getElementById('password').value;
        if(email == '')
        {
            swal("Please Enter the Details");
            $("#username").focus();
        }
        else if (passwrd == '')
        {
            swal("Please enter Password!");
            $("#password").focus();
        }
        if(email!='')
        {
            if (passwrd == '') {
            document.getElementById('password').focus();
        }
        else 
        {
                $.ajax({
                    type: "get",
                    url: "userexistcheck",
                    data: {"email": email, "password": passwrd},
                    dataType: 'text',
                    success: function (data) {
                        localStorage.clear();
                        var result = JSON.parse(data);
                        $("#loginloader").hide();
                        if (result.msg == 'User Exist')
                        {
                             localStorage.staffid = result.staffid;
                             localStorage.setuserid = result.setuserid;
								 var usr = {'staffid': result.staffid,"logingroup":result.login_group,"setuserid":result.setuserid,
                                 'designation':result.designation};
                            $.ajax({
                            type: "GET",
                            url: "session/set",
                            data:usr,
                            success: function (data) {
                                if(result.login_group =='H'){
                                    window.location.href='welcome_restaurant';
                                }
                                else{
                                    designarray = new Array("MANAGER", "SUPER_ADMIN","ADMIN");
                                    var designation = result.designation;
                                    if(designation)
                                    {
/*                                        if( $.inArray(designation.toUpperCase(), designarray) !== -1 ) {
                                            window.location.href='index';
                                        }
                                        else
                                        {*/
                                            window.location.href='manage_credits';
//                                        }
                                    }
                                }

                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                $("#errbox").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                            }
                            }); 
                        }
                        else {
                            swal("Please enter valid login details!");
                            $("#password").focus();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#test").text(jqXHR.responseText);
                    }
                });
            }
        }
        else
            {
                $("#username").focus();
            }
        
    }
</script>
<script type="text/javascript" src='https://rawgithub.com/gsklee/ngStorage/master/ngStorage.js'></script>

</body>
</html>
