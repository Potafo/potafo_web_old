@extends('layouts.app')
@section('title','Potafo - Add Web Banners')
@section('content')
    <style>
        .not-active {
            pointer-events: none;
            cursor: default;opacity: 0.5;
            font-weight: bold;
        }
        label.cabinet{display: block;cursor: pointer;}
label.cabinet input.file{position: relative;height: 100%;width: auto;opacity: 0;
	-moz-opacity: 0;  filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0); margin-top:-30px;}
#upload-demo{width:100%;height: 400px; padding-bottom:25px;}
figure figcaption {   position: absolute; bottom: 0; color: #fff; width: 100%; padding-left: 9px;
    padding-bottom: 5px; text-shadow: 0 0 10px #000;}
        label.cabinet{text-align: center}
        .modal-dialog {width: 770px;}
        
/*
        @media (max-width:991px){
             .modal-dialog {width: 770px;}
        }
*/
        
    </style>
    <script src="{{asset('public/assets/script/common.js') }}" type="text/javascript"></script>
    <div class="col-sm-12 col-xs-12 mob_nopad">
        <div class="col-sm-12 col-xs-12">
        </div>

        <div class="col-md-12 col-xs-12 text-center mob_nopad">
            <div class="col-md-9 col-xs-12 add_menu_cc mob_nopad">
                <div class="card-box table-responsive" style="padding: 8px 10px;">
                    <h3 style="margin-bottom:40px;text-align: center;">ADD APP BANNER</h3>
                    {!! Form::open(['enctype'=>'multipart/form-data','url'=>'api/catering/app_banner_submit', 'name'=>'frm_add', 'id'=>'frm_add','method'=>'post',]) !!}
                    <div class="container">
	                <div class="row">
								<div class="col-xs-12">
                                    <span class="restaurant_more_detail_text_nm" style="display:none;">App Banner</span>
                                    <label class="cabinet center-block">
									<figure>
											<img src="" class="gambar img-responsive img-thumbnail" id="item-img-output" />
										  <figcaption><i class="fa fa-camera"></i></figcaption>
								    </figure>
										<input type="file" class="item-img file center-block" id="file_photo" name="file_photo"/>
                                        <input type="hidden" id="img1" name="img1">
                                        <input type="hidden" id="img2" name="img2">
									</label>
								</div>
                        <div class="col-xs-8"  style="margin-top: 15px;">
                            <div class="restaurant_more_detail_text">
                                <span class="restaurant_more_detail_text_nm">Google Address *</span>
                                <input type='hidden' id="lat" name="lat" value="">
                                <input type='hidden' id="long" name="long" value="">
                                <input type="text" style="margin-top:10px;" id="geo_location" name="geo_location" value="">
                            </div>
                        </div>
                        <div class="col-xs-4"  style="margin-top: 13px;">
                            <div class="restaurant_more_detail_text" style="width:80%">
                                <span class="restaurant_more_detail_text_nm">Distance Radius (KM) * </span>
                                <input style="width:50%;margin-top:10px;" type="text"   id="range" name="range" value="0" onkeypress = "return numonly(event)">
                                <!--<div onclick="checkradius()" class="check_radius_btn">Check</div>-->
                            </div>
                        </div>
							</div>

                    </div>
{{--
              <div class="container" style="margin-top: 15px;">
	                <div class="row">
								<div class="col-xs-12">
								   <span class="restaurant_more_detail_text_nm">Web Banner</span>
									<label class="cabinet center-block">
										<figure>
											<img src="" class="gambar img-responsive img-thumbnail" id="item-img-output" />
										  <figcaption><i class="fa fa-camera"></i></figcaption>
								    </figure>
										<input type="file" class="item-img file center-block" name="file_photo" id="file_photo"/>
                                      --}}{{--  <input type="hidden" id="img11" name="img1">
                                        <input type="hidden" id="img21" name="img2">--}}{{--
									</label>
								</div>
							</div>
                    </div>--}}

                    {{ Form::close() }}
                    <div class="table_section_scroll" style="margin-top:20px;text-align:center"><div class="table-filter-cc">
                    <a href=""> <a onclick="return banner_add()" style="margin-left:0;cursor: pointer;" class="on-default followups-popup-btn btn btn-primary">SUBMIT</a></a>
                </div></div>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="cropImagePop" tabindex="-1" role="dialog"  aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 950px">
						    <div class="modal-content">
							<div class="modal-header">
							  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							  <h4 class="modal-title" id="myModalLabel">
							  	test</h4>
							</div>
							<div class="modal-body">
				            <div style="height:590px;width:858px" id="upload-demo" class="center-block"></div>
				      </div>
							 <div class="modal-footer">
        <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
        <button type="button" id="cropImageBtn" class="btn btn-primary">Ok</button>
      </div>
						    </div>
						  </div>
						</div>


    <div class="check_radius_popup_sec" style="display:none;">
        <div class="check_radius_popup">
            <div class="timing_popup_head">CHECK DISTANCE RADIUS
                <div  class="timing_popup_cls" onclick="closeradius()"><img src="{{asset('public/assets/images/cancel.png')}}"></div>
            </div>
            <div class="check_radius_popup_contant">
                <div class="restaurant_more_detail_text" style="width:78%;">
                    <span class="restaurant_more_detail_text_nm">Google Address</span>
                    <input type='hidden' id="checklats" name="checklats" value="">
                    <input type='hidden' id="checklongs" name="checklongs" value="">
                    <input style="padding-right:40px;" type="text" id="check_add" name="check_add" value="" placeholder="Enter a location" autocomplete="off">
                    <div onclick="radiusclear()"class="check_radius_clear_btn">C</div>
                </div>
                <div class="restaurant_more_detail_text" style="width:20%;margin-left:2%;">
                    <span class="restaurant_more_detail_text_nm">Radius</span>
                    <input type="text" disabled id="chk_radius" name="chk_radius" placeholder="" autocomplete="off">
                </div>
            </div>
        </div>
    </div>
<div id="urls"></div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    <style>#datatable-fixed-col_filter{display:none}table.dataTable thead th{white-space:nowrap;padding-right: 20px;}
        .on-default	{margin-left:10px;}div.dataTables_info {padding-top:13px;}
        .height_align{
            margin-top: 12px;
        }
    </style>
<!--<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css'>-->
<link rel='stylesheet' href="{{ asset('public/assets/css/croppie.css') }}">
<link href="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
<script src="{{ asset('public/assets/pages/jquery.sweet-alert.init.js') }}"></script>
@section('jquery')
<script src="{{ asset('public/assets/js/croppie.js') }}"></script>
<script src="{{ asset('public/assets/js/bootstrap.min.js') }}"></script>
<script  src="{{asset('public/assets/js/cat_banner_croper.js') }}"></script>
@stop
<script>
    function banner_add()
    {
        var banner_image = $('#file_photo').val();
        var geo_location = $('#geo_location').val();
        var range = $('#range').val();
        if(banner_image == '')
        {
            alert ("Please Upload Image.");
            return false;
        }
        if(geo_location == '')
        {
            alert ("Enter Geo Location.");
            $("#check_add").focus();
            return false;
        }
        if(range == '')
        {
            alert ("Enter Distance Radius In KM.");
            $("#chk_radius").focus();
            return false;
        }
        if(true)
        {
          var thisform = document.frm_add;
          thisform.submit();
          return true;
       }
    }

    function checkradius()
    {
        $(".check_radius_popup_sec").show();
        $("#chk_radius").val('');
        $("#check_add").val('');
    }
    function closeradius()
    {
        $(".check_radius_popup_sec").hide();
    }
    function radiusclear()
    {
        $("#chk_radius").val('');
        $("#check_add").val('');
    }

    $(document).ready(function() {
        var autocomplete = new google.maps.places.Autocomplete($("input[name=geo_location]")[0], {});
        autocomplete.setComponentRestrictions({'country': ['IN' ]});
        autocomplete.setComponentRestrictions({'locality': ['kozhikode']});
        google.maps.event.addListener(autocomplete, 'place_changed', function(){
            var place = autocomplete.getPlace();
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            $("#lat").val(lat);
            $("#long").val(lng);
        });
        var autocompletes = new google.maps.places.Autocomplete($("input[name=check_add]")[0], {});
        autocompletes.setComponentRestrictions({'country': ['IN' ]});
        autocompletes.setComponentRestrictions({'locality': ['kozhikode']});
        google.maps.event.addListener(autocompletes, 'place_changed', function(){
            var lat2 = $("#lat").val();
            var long2 = $("#long").val();
            if(lat2 == '' || long2 == '')
            {
//                $("#edcategory").focus();
                $.Notification.autoHideNotify('error', 'bottom right','Select Restaurant Google Address');
                return false;
            }
            else
            {
                var places = autocompletes.getPlace();
                var lats = places.geometry.location.lat();
                var lngs = places.geometry.location.lng();
                $("#checklats").val(lats);
                $("#checklongs").val(lngs);
                $.ajax({
                    method: "post",
                    url : "../api/radius_calculate",
                    data : {'lat1':lats,'long1':lngs,'lat2':lat2,'long2':  long2},
                    success : function(result)
                    {
                        $("#chk_radius").val(result);
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
        });
    });
</script>
@endsection