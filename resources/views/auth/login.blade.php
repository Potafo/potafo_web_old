<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EXPODINE</title>
           <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

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
        <span class="report_text">Reports</span>
    </div>

    <div  >
    <form class="form-horizontal simform" method="POST" action="{{ route('login') }}"  >
 {{ csrf_field() }}
        <input type="hidden" name="ip" id="ip" value=""/>

        <div class="simform-inner">
            <ol class="login">
                <li class="current">
                    <div class="group form-group{{ $errors->has('email') ? ' has-error' : '' }}">
<!--                        <input type="email" class="form-control"onkeyup="myfunc()"  id="email" name="email" value="" placeholder="Email" autocomplete="off">-->
                      <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus >

                                @if ($errors->has('email'))
                                    <span class="help-block" style="font-size: 16px;color: red">
                                       
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                      <label style="top: -10px;font-size: 14px;color: #5264AE;" class="label user-name" for="username">USER NAME</label>
                    </div>
                </li>
                <li class="current">
                    <div class="group form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                              
                                    <span class="help-block">
                                        
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                        
                        <label style="top: -10px;font-size: 14px;color: #5264AE;"  class="label" for="password">PASSWORD</label>
                    </div>
                </li>

            </ol><!-- /login -->
            <div class="controls">
                <button type="submit" class="next next_btn" onclick="return submit_login();">Login</button>
                    		<span class="number" style="display:none;">
								<span id="count" class="number-current"></span>
								<span class="number-total"></span>
							</span>
                <span class="error-message"></span>
            </div>
            
            <div class="loader_img" id="loginloader">
                <img src="{{ asset("public/assets/images/ajax-loader.gif") }}">
            </div>

            <div ng-model="PostDataResponse"></div>
            <div ng-model="ResponseDetails"></div>
        </div>
    </form>



    </div>
        <span class="final-message"></span>
</div>
<script src="{{ asset('public/assets/js/classie.js') }}"></script>
<script src="{{ asset('public/assets/angular/app.js') }}"></script>
<script src="{{ asset('public/assets/js/stepsForm.js') }}"></script>

<script>
    
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
   
</script>
<script type="text/javascript" src='https://rawgithub.com/gsklee/ngStorage/master/ngStorage.js'></script>

</body>
</html>
