@extends('layouts.app')
@section('title','Potafo - Add App Banners')
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
    </style>
    <script src="{{asset('public/assets/script/common.js') }}" type="text/javascript"></script>
    <div class="col-sm-12 col-xs-12 mob_nopad">
        <div class="col-sm-12 col-xs-12">
        </div>

        <div class="col-md-12 col-xs-12 text-center mob_nopad">
            <div class="col-md-9 col-xs-12 add_menu_cc mob_nopad">
                <div class="card-box table-responsive" style="padding: 8px 10px;">
                    <h3 style="margin-bottom:40px;text-align: center;">ADD BANNER</h3>
                    {!! Form::open(['enctype'=>'multipart/form-data','url'=>'api/banner/appadd', 'name'=>'frm_add', 'id'=>'frm_add','method'=>'post',]) !!}
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
                                    <input type="hidden" id="id" name="id" value="{{$id}}">
                                    <input type="hidden" id="img1" name="img1">
                                </label>
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

    <div class="modal fade" id="cropImagePop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        test</h4>
                </div>
                <div class="modal-body">
                    <div style="height:590px" id="upload-demo" class="center-block"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="cropImageBtn" class="btn btn-primary">Crop</button>
                </div>
            </div>
        </div>
    </div>
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
    <script  src="{{asset('public/assets/js/appcropper.js') }}"></script>
    <script src="{{ asset('public/assets/js/bootstrap.min.js') }}"></script>
@stop
<script>
    function banner_add()
    {
        var banner_image = $('#file_photo').val();
        if(banner_image == '')
        {
            alert ("Please Upload Image.");
        }
        else
        {
            var thisform = document.frm_add;
            thisform.submit();
            return true;
        }
    }
</script>
@endsection