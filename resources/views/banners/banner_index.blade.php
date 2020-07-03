@extends('layouts.app')
@section('title','Potafo - Manage Banners')
@section('content')
    <style>
        .filter_text_box_row{margin-bottom: 6px}
        #example1 .form-control{border: solid 1px #ccc}
        .tbl_view_sec_btn{width: auto;padding: 5px;border-radius: 5px;border-bottom: solid 3px #ececec;color: #666; text-decoration: none !important; box-shadow: 0px 3px 7px #cac7c7;}
    </style>
    <div class="col-sm-12">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('index') }}">Dashboard</a>
                </li>

                <li class="active ms-hover">
                 Manage Banners
                </li>
            </ol>
        </div>
        <div class="card-box table-responsive" style="padding: 8px 10px;">

            <div class="box master-add-field-box" >
                <div class="col-md-6 no-pad-left">
                    <h3>Manage Banners</h3>
                </div>

                <div class="col-md-3 no-pad-left pull-right">
                    <div class="table-filter" style="margin-top: 4px;">
                        <div class="table-filter-cc">
                            <a href="{{ url('banner/add') }}"> <button type="submit" style="margin-left:0" class="on-default followups-popup-btn btn btn-primary" >Add Web Banner</button></a>

                        </div>

                    </div>
                </div>

            </div>

            <div class="filter_box_section_cc diply_tgl">
                <!--                <div class="filter_box_section">FILTER</div>-->
                <div class="filter_text_box_row">
                    {!! Form::open(['url'=>'filter/restaurant', 'name'=>'frm_filter', 'id'=>'frm_filter','method'=>'get']) !!}
                    <input type="hidden" id="siteUrl" name="siteUrl" value="{{ $siteUrl }}">
                    <div class="main_inner_class_track" style="width: 25%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Name</label>
                                <input id="restaurant_name" onkeyup="return filter_change(this.value)" name="restaurant_name" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Mobile</label>
                                <input class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Email</label>
                                <input class="form-control" type="text">
                            </div>
                        </div>
                    </div>


                    {{ Form::close() }}

                </div>
            </div>
            <div class="table_section_scroll">
                <table id="example1" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th style="min-width:10px">Slno</th>
                        <th style="min-width:150px">App Banners</th>
                        <th style="min-width:150px">Web Banners</th>
                        <th style="min-width:50px">Display Order</th>
                        <th style="min-width:10px"> </th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($banner)>0)
                        @foreach($banner as $key=>$item)
                    <tr role="row" class="odd">
                        <td style="min-width:10px;">{{ $key+1}}</td>
                        <td style="min-width:150px;">
                            <a class="btn tbl_view_sec_btn" rel="popover" data-img="@if(isset($item->app_banners) && $item->app_banners != ''){{  $siteUrl.$item->app_banners }}@endif"  style="text-decoration: underline;cursor:pointer;">View</a>
                            <a href="{{ url('banner/appadd/'.$item->id) }}" class="btn tbl_view_sec_btn" style="text-decoration: underline;cursor:pointer;">Add</a>
                        </td>
                        <td style="min-width:150px;">
                            <a class="btn tbl_view_sec_btn" rel="popover" data-img="@if(isset($item->web_banners) && $item->web_banners != ''){{  $siteUrl.$item->web_banners }}@endif" href="" style="text-decoration: underline;">View</a>
                        </td>
                        <td style="min-width:50px;">
                            <input class="form-control" type="textbox" onkeypress="return isNumberKey(event)" value="{{ $item->order_no }}" title="Edit Order" name="order_no" id="order_no" onkeyup="return changeorderno('{{ $item->id }}',this.value)">
                        </td>
                        <td style="min-width:10px;"><a class="btn button_table" onclick="deleteimage('{{ $item->id }}');" title="Delete"><i class="fa fa-trash-o"></i></a></td>
                    </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->
@section('jquery')
    <script type="text/javascript">
        $(document).ready(function()
        {
            var t = $('#example1').DataTable({
                scrollX: false,
                scrollCollapse: true,
                "searching": false,
                "ordering": false,
                "info": false,
                columnDefs: [
                    { width: '20%', targets: 0 }
                ],
                "deferLoading": 0,
                "lengthChange": false,
                "columnDefs": [{
                    paging: false
                } ],
            } );
        } );
        $('.filter_sec_btn').on('click', function(e)
        {
            $('.filter_box_section_cc').toggleClass("diply_tgl");
            $("#restaurant_name").focus();
        });

        $(document).ready(function()
        {
            $('a[rel=popover]').popover({
                html: true,
                trigger: 'hover',
                placement: 'right',
                content: function(){return '<img src="'+$(this).data('img') + '" width="200" height="100"/>';}
            });
        });


        function deleteimage(id)
        {
            if(confirm('Are you sure to delete?'))
            {
                var siteUrl = $("#siteUrl").val();
                var table = $('#example1').DataTable();
                $.ajax({
                    method: "get",
                    url: "../api/banner_delete/" + id,
                    cache: false,
                    crossDomain: true,
                    async: false,
                    dataType: 'text',
                    success: function (result) {
                        var rows = table.rows().remove().draw();
                        var json_x = JSON.parse(result);
                        if (json_x.msg == 'deleted') {
                            swal({

                                title: "",
                                text: "Deleted Successfully",
                                timer: 1000,
                                showConfirmButton: false
                            });
                            window.location.reload();
                            /*  $.each(json_x.banners,function(i,index)
                             {
                             var count = parseInt(i) + 1;
                             var appbanner = siteUrl+''+index.app_banners;
                             var webbanner = siteUrl+''+index.web_banners;
                             var newRow = '<tr>'+'<td style="min-width:10px;">'+count+'</td>'+
                                          '<td style="min-width:300px;">'+'<a class="btn" rel="popover" data-img=\"'+appbanner+'\" href="" style="text-decoration: underline;">View</a>'+
                                          '</td>'+'<td style="min-width:300px;">'+'<a class="btn" rel="popover" data-img=\"'+webbanner+'\" href="" style="text-decoration: underline;">View</a>'+
                                          '</td>'+'<td style="min-width:10px;"><a class="btn button_table" onclick="deleteimage(\''+index.id+'\');"><i class="fa fa-trash-o"></i></a></td>'+'</tr>';
                             var rowNode = table.row.add($(newRow)).draw().node();
                             });*/
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                    }
                });
            }
            return true;
        }

        function changeorderno(id,val)
        {
            $.ajax({
                method: "get",
                url: "../api/banner_order/" + id+ "/"+val,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                    var json_x = JSON.parse(result);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
            return true;
        }

    </script>
@stop
@endsection




