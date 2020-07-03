@extends('layouts.app')
@section('title','Potafo - Category')
@section('content')
<style>
     .not-active {
          pointer-events: none;
          cursor: default;opacity: 0.5;
          font-weight: bold;
       }
    .add-work-done-poppup-textbox-box label{font-weight:lighter;}
    .inner-textbox-cc input:focus ~ label, input:valid ~ label{font-size:13px;top: -10px;}
    .group{margin-bottom: 14px}
    .sweet-alert{width:300px !important;left: 0 !important;right: 0;margin: auto !important;}
 
.bootstrap-select.btn-group .dropdown-menu.inner{max-height:  300px !important;}
.staff_master_tbl_tbody{
    width: 100%;
    height: 150px;
    margin-bottom: 2px;
    float: left;
    overflow: auto;
        
}
.main_inner_class_track .bootstrap-select{border: solid 1px #ccc;}
.table_staff_scr_scr thead{ display: inline-block;width: 100%;}
.table_staff_scr_scr tbody{ display: inline-block;width: 100%;max-height:  390px;overflow: auto   }
.table_staff_scr_scr tr{ display: inline-block;width: 100%;}
.table_staff_scr_scr td{ width: 100px;}
.table_staff_scr_scr th{ width: 100px;}
.pagination_total_showing{float: left;width: auto;padding-top: 12px;padding-left: 10px;color: #000000;}
.add-work-done-poppup-textbox-box label{font-weight:lighter;}.inner-textbox-cc input:focus ~ label, input:valid ~ label{font-size:13px;top: -10px;}.group{margin-bottom: 14px}.add-work-done-poppup{height: auto;} div.dataTables_wrapper div.dataTables_filter{float: right;top: 4px;position: relative;}.dataTables_length{top: 7px;position: relative;float: left}
    .dataTables_scrollHeadInner{width: 100% !important}.dataTables_scrollHeadInner table{width: 100% !important}.dataTables_scrollBody table{width: 100% !important} .dataTables_scrollBody {  height: 350px;}
   .popover {width: 180px;height: 120px;}.popover img{width:100%}

</style>
          <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">
          <script src="{{asset('public/assets/admin/script/menu.js') }}" type="text/javascript"></script>
         <script src="{{asset('public/assets/script/common.js') }}" type="text/javascript"></script>
 <div class="col-sm-12">
        <div class="col-sm-12">
                <ol class="breadcrumb">
						<li>
							<a href="{{  url('index') }}">Dashboard</a>
						</li>
						
						<li class="active ms-hover">
							<a href="{{  url('manage_restaurant') }}">{{$restaurant_name[0]->name}}</a>
						</li>
                                                <li class="active ms-hover">
							Category
						</li>
                   
					</ol>
				</div>
        
        <div class="col-sm-12">
            <a href="{{ url('restaurant_edit/'.$restaurant_id) }}"><div class="potafo_top_menu_sec">About</div></a>
            <a href="{{ url('menu/list/'.$restaurant_id) }}"><div class="potafo_top_menu_sec">Menu</div></a>
            <a ><div class="potafo_top_menu_sec potafo_top_menu_act">Category</div></a>
            <a href="{{ url('menu/review/'.$restaurant_id) }}"><div class="potafo_top_menu_sec">Review</div></a>
            <a href="{{ url('menu/tax/'.$restaurant_id) }}"><div class="potafo_top_menu_sec">Tax %</div></a>
          </div>
        <div class="card-box table-responsive" style="padding: 8px 10px;">
             
              <div class="box master-add-field-box" >
             <div class="col-md-6 no-pad-left">
                <h3>Category</h3>
            </div>    

            </div>
            
            
            <div >  
            <table id="datatable-1" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th style="min-width:3px">Slno</th>
                    <th style="min-width:130px">Category</th>
                    <th style="min-width:130px">Image View</th>
                    <th style="min-width:100px">Display Order</th>       
                    <th style="min-width:100px">Status</th>       
                </tr>
                </thead>
                <tbody>
                @if(count($details)>0)
                    @foreach($details as $key=>$item)
                    <tr>
                    <td  style="min-width:3px !important;">{{ $key+1 }}</td>
                    <td style="min-width:130px !important;">{{ title_case($item->name) }}</td>
                    <td style="text-align: left;min-width:130px !important;">
                        <div class="status_chck{{$item->restaurant_id}},{{$item->slno}}">
                            <div class="onoffswitch">
                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch{{$item->restaurant_id}}{{$item->slno}}" @if( $item->image_view == 'Y') checked @endif>
                                    <label class="onoffswitch-label" for="myonoffswitch{{$item->restaurant_id}}{{$item->slno}}">
                                        <span class="onoffswitch-inner" onclick="return  statuschange('{{$item->restaurant_id}}','{{$item->slno}}','image')"></span>
                                        <span class="onoffswitch-switch" onclick="return  statuschange('{{$item->restaurant_id}}','{{$item->slno}}','image')"></span>
                                    </label>
                            </div>
                        </div>
                    </td>
                    <td style="min-width:100px;">
                        <input type="textbox" onkeypress="return isNumberKey(event)" value="{{ $item->order_no }}" title="Edit Order" name="order_no" id="order_no" onkeyup="return changeorderno('{{ $item->restaurant_id }}','{{ $item->slno }}',this.value)">
                    </td>
                    <td style="text-align: left;min-width:130px !important;">
                        <div class="status_chck{{$item->restaurant_id}},{{$item->slno}}">
                            <div class="onoffswitch">
                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="status{{$item->restaurant_id}}{{$item->slno}}" @if( $item->status == 'Y') checked @endif>
                                    <label class="onoffswitch-label" for="status{{$item->restaurant_id}}{{$item->slno}}">
                                        <span class="onoffswitch-inner" onclick="return statuschange('{{$item->restaurant_id}}','{{$item->slno}}','status')"></span>
                                        <span class="onoffswitch-switch" onclick="return statuschange('{{$item->restaurant_id}}','{{$item->slno}}','status')"></span>
                                    </label>
                            </div>
                        </div>
                    </td>
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


    <style>#datatable-fixed-col_filter{display:none}table.dataTable thead th{white-space:nowrap;padding-right: 20px;}
        .on-default	{margin-left:10px;}div.dataTables_info {padding-top:13px;}
        .height_align{
                    margin-top: 12px;
        }
    </style>
     <link href="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css">
        <script src="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
        <script src="{{ asset('public/assets/pages/jquery.sweet-alert.init.js') }}"></script>
         <style>#datatable-fixed-col_filter{display:none}table.dataTable thead th{white-space:nowrap;padding-right: 20px;}
        .on-default	{margin-left:10px;}div.dataTables_info {padding-top:13px;}
    </style>
    
@section('jquery')
    <link href="{{ asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('public/assets/dark/plugins/custombox/css/custombox.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script>
        $(document).ready(function()
        {
            var t = $('#datatable-1').DataTable({
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
    </script>
    <script>
     function statuschange(id,slno,optn) {
         
            var ids = id;
            var slno = slno;
            var catstatus = $("#status"+id+slno).prop('checked');
            var imgstatus = $("#myonoffswitch"+id+slno).prop('checked');
            if(imgstatus == true){
                imgstatus = 'N';
            }
            else{
                imgstatus = 'Y'
            }
            if(catstatus == true){
                catstatus = 'N';
            }
            else{
                catstatus = 'Y'
            }
            var data = {"ids":ids,"slno":slno,"optn":optn,"catstatus":catstatus,"imgstatus":imgstatus};
            $.ajax({
                method: "get",
                url: "../../rescategory_imgview",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
//                    alert(result);
//                  location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
//                    alert(errorThrown);
                    $("#errbox").text(jqxhr.responseText);
                }
            });
        } 
        
 function changeorderno(id,slno,val)
        {
            $.ajax({
                method: "get",
                url: "../../api/category_order/" + id+ "/"+ slno+ "/"+val,
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




