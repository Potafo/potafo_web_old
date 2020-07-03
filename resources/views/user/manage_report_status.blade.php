@extends('layouts.app')
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
/*    .dataTables_scrollHeadInner{width: 100% !important}.dataTables_scrollHeadInner table{width: 100% !important}.dataTables_scrollBody table{width: 100% !important} */
    .dataTables_scrollBody {  height: 350px;}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{vertical-align: middle;padding: 6px}
</style>

          <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">


          <input type='hidden' id='url' value='{{$url}}' />
<input type="hidden" id="userid" value="{{$userid}}">
          <div class="col-sm-12">

              <div class="col-md-6 no-padding">
                <div class="box master-add-field-box" style="background-color:#fff;padding:8px;border: solid 1px #ccc;">
                      
                          <h3 style="font-size: 19px;float:left;margin-top: 1px;margin-bottom:0">{{$username}}</h3>
                  
                    <div class="col-md-6 no-pad-left">
                          @if(count($get_cate_report)!=0)
                              <select id="report_category" style="background-color:transparent;height: 38px;" class=" staff-master-select form-control" >
                                  <option value="allcat" select:selected >All Categories</option>
                              @foreach($get_cate_report as $cat_report)
                                  <option value="{{$cat_report->rc_id}}">{{$cat_report->rc_category_name}}</option>
                              @endforeach

                              </select>
                          @endif
                  </div>         

                      
                      
                </div>
                  
             </div>
              
              <div class="col-md-6 no-padding" >
                   <div class="box master-add-field-box" style="background-color:#fff;padding:8px;border: solid 1px #ccc;">
                       <a href="#"> <button type="submit" style="float:right;margin-top: 2px; border-radius: 4px;margin-left: 5px;" class="on-default followups-popup-btn btn btn-primary" onclick='reset_status_all()' >Apply</button></a>
                       
                           <select class='form-control' style='width:230px;float:right'id='reset_status_all' >
                              <option value='0'>SELECT</option>
                              <option value='true'>Activate All</option>
                              <option value='false'>InActivate All</option>
                          </select>  
                            

                      </div>
              </div>

</div>

<div class="col-sm-12">
              
              <div class="card-box table-responsive" style="padding: 8px 10px;">
                  

            <table id="example1" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Slno</th>
                    <th  style="max-width: 2%">Change Status</th>
                    <th>Reports</th>
                    <th>Status</th>
                   
                  
                </tr>
                </thead>

                   <tbody id="append_filter_data">
                       @if(count($get_report)!=0)
                           <?php $i=0; ?>
						   @foreach($get_report as $report)
                               <?php $i++; ?>
                               <tr>
                                   <td>{{$i}}</td>
                                   <td >
                                       <a class="btn" id="edit_icon{{$report->ru_id}}" onclick="change_status('{{$report->ru_id}}' )"><i class="fa fa-edit"></i></a>
                                       <a class="btn" style="display: none" id="save_icon{{$report->ru_id}}" onclick="save_status('{{$report->ru_id}}' )"><i class="fa fa-save"></i></a>

                                   </td>

                                   <td>{{$report->rm_name}}</td>
                                   <td>
                                       <span id="show_status{{$report->ru_id}}" style="background-color:transparent;height: 30px;line-height: 14px;" class=" staff-master-select form-control">
                                             @if($report->ru_access=='Y') Active @endif
                                           @if($report->ru_access=='N') InActive @endif
                                       </span>

                                         <select id="report_status{{$report->ru_id}}" style="background-color:transparent;height: 39px;display: none;" class=" staff-master-select form-control">
                                             <option value="Y">Active</option>
                                             <option value="N">InActive</option>
                                         </select>

                                   </td>
                               </tr>

					       @endforeach
					   @endif
                </tbody>

            </table>
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

    <script src="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <link href="{{ asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('public/assets/dark/plugins/custombox/css/custombox.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/dark/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/assets/dark/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    {{--<script src="{{ asset('public/assets/js/angular.min.js') }}"></script>--}}
    <script src="{{asset('public/assets/dark/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <link href="{{asset('public/assets/dark/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
<script>
    $(document).ready(function() {
        var t = $('#example1').DataTable( {
            scrollY: "380px",
            scrollX: true,
            scrollCollapse: true,
            "columnDefs": [ {

                paging: false


            } ],
            "searching": true,
            "ordering": false,
            "iDisplayLength": 30
        } );
        function ValidateEmail(mail)
        {
            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(myForm.emailAddr.value))
            {
                return (true)
            }
            alert("You have entered an invalid email address!")
            return (false)
        }
    } );
    function change_status(id){

        $("#save_icon"+id).css("display",'inline-block');
        $("#edit_icon"+id).css("display",'none');
        $("#report_status"+id).css("display",'inline-block');
        $("#show_status"+id).css("display",'none');
    }
      function reset_status_all(){
      var status = $("#reset_status_all").val();
        var userid = $("#userid").val();
        if(status == '0'){
            swal({

                    title: "",
                    text: "Please Select Any One Option",
                    timer: 1000,
                    showConfirmButton: false
                });
        }
       else{
        
        $.ajax({
           type: "POST",
           url: "api/reset_all_report",
           data:{"status":status,"userid":userid},
           success: function (data) {
           if(data == 'active'){
               swal({

                    title: "",
                    text: "All Reports Are Active Now",
                    timer: 1000,
                    showConfirmButton: false
                });
           }
           if(data == 'inactive'){
               swal({

                    title: "",
                    text: "All Reports Are InActive Now",
                    timer: 1000,
                    showConfirmButton: false
                });
           }

                
             window.location.reload();
           },
           error: function (jqXHR, textStatus, errorThrown) {
               alert('error');
               $("#errbox").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
           }
       });
        
        }
         
   }
    function save_status(id){


        var userid = $("#userid").val();
        var status = $("#report_status"+id).val();
        var report_id = id;
        var reset_data = {'userid':userid,'status':status,'report_id':report_id};
        $.ajax({
            type: "POST",
            url: "api/change_report_status",
            data:reset_data,
            success: function (data) {


                swal({

                    title: "",
                    text: "Updated",
                    timer: 1000,
                    showConfirmButton: false
                });
                 $("#report_status"+id).val(report_id);
                $("#save_icon"+id).css("display",'none');
                $("#edit_icon"+id).css("display",'inline-block');
                $("#report_status"+id).css("display",'none');
                $("#show_status"+id).css("display",'inline-block');
               
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('error');
                $("#errbox").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
            }
        });

    }
    
   $("#report_category").change(function(){
        var report_cat =$(this).val();//alert(report_cat);
        var userid = $("#userid").val();
        var reset_data = {'userid':userid,'report_id':report_cat};
          $("#append_filter_data").html('');
       $.ajax({
           type: "GET",
           url: "api/filter_report_catrgory",
           data:reset_data,
           success: function (data) {

              $("#append_filter_data").html(data);

               
               $("#save_icon"+id).css("display",'none');
               $("#edit_icon"+id).css("display",'inline-block');
               $("#report_status"+id).css("display",'none');
               $("#show_status"+id).css("display",'inline-block');
           },
           error: function (jqXHR, textStatus, errorThrown) {
               alert('error');
               $("#errbox").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
           }
       });
   });
 
   
</script>

@stop



    <script src="{{asset('public/assets/ckeditor/ckeditor.js')}}"></script>
    <script src="{{asset('public/assets/ckeditor/samples/js/sample.js')}}"></script>

   

@endsection




