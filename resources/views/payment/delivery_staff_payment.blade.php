@extends('layouts.app')
@section('title','Manage Delivery Staff Payment')
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
        .Location_btn{
            width: auto;
    padding: 2px 15px;
    background-color: #de2a18 !important;
    border: 1px solid #941306 !important;
    box-shadow: 5px 3px 12px #bdbdbd;
    border-bottom: 3px solid #710b0b !important;
    font-weight: bold;
    float: left;
    color: #fff;
    border-radius: 20px;
    /* margin: 8px 3px; */
    cursor: pointer;
        }
        .Location_btn:hover{    background-color: #f0eeee !important;color:#710b0b}
        .Location_btn_acc{
            width: auto;
    padding: 2px 15px;
    background-color: #3fa24b !important;
    border: 1px solid #5dab35 !important;
    box-shadow: 5px 3px 12px #bdbdbd;
    border-bottom: 3px solid #247b19 !important;
    font-weight: bold;
    float: left;
    color: #fff;
    border-radius: 20px;
    /* margin: 8px 3px; */
    cursor: pointer;
        }
        .Location_btn_acc:hover{   background-color: #fff !important; color: #247b19}
    </style>

    <link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">


    <input type='hidden' id='url' value='{{$url}}' />

    <div class="col-sm-12">

        <div class="card-box table-responsive" style="padding: 8px 10px;">
            <div class="box master-add-field-box" >
                <div class="col-md-6 no-pad-left">
                <h3>DELIVERY STAFF PAYMENT - {{$designation}}</h3>

                </div>
                <div class="col-md-1 no-pad-left pull-right">
                    <div class="table-filter" style="margin-top: 4px;">
                        <div class="table-filter-cc">
                            <!--<a href="#"> <button type="submit" style="margin-top: px; border-radius: 4px;margin-left: 0;" class="on-default followups-popup-btn btn btn-primary ad-work-clear-btn" >Add New</button></a>-->

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="main_inner_class_track" style="width: 20%;">
                    <div class="group">
                       <div style="position: relative">
                            <label>From Staff Name</label>
                            <input id="staffname"  name="staffname" class="form-control" type="text" onkeyup="load_paylist()">
                        </div>

                     </div>
                  </div>
                  <div class="main_inner_class_track" style="width: 20%;margin-left: 10px">
                    <div class="group">
                       <div style="position: relative">
                            <label>Date</label>
                            <input id="date_pay" data-date-format='dd-mm-yyyy'  name="date_pay" class="form-control" type="text" onchange="load_paylist()">
                        </div>

                     </div>
                  </div>
                  <!--<div class="main_inner_class_track" style="width: 20%;margin-left: 10px">
                    <div class="group">
                       <div style="position: relative">
                            <label>Transc Type</label>
                            <input id="staffname"  name="staffname" class="form-control" type="text">
                        </div>

                     </div>
                  </div>-->
                  <!--<div class="main_inner_class_track" style="width: 20%;margin-left: 10px">
                    <div class="group">
                       <div style="position: relative" >
                            
                           <div class="staff-add-pop-btn"> Search</div>
                        </div>

                     </div>
                  </div>-->
            </div>
            <div id="loadtable" >
            <table id="example1" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th style="min-width:4%">Slno</th>
                    <th style="min-width:10%">ID</th>
                    <th style="min-width:10%">Name</th>
                    <th style="min-width:10%">Transc Type</th>
                    <th style="min-width:10%">Date</th>
                    <th style="min-width:10%">Amount</th>
                    <th style="min-width:10%">Status</th>
                    <th style="min-width:10%">Remarks</th>
                   <th style="min-width:20%">Action</th>
                </tr>
                </thead>
                <tbody id="arealisting" style="height:390px">
              
                </tbody>

            </table>

            </div>
        </div>
    </div>


    <div id="url"></div>

    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    <div id="authuser" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup">
            <div class="add-work-done-poppup-head">Authorize
                <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn_auth"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
            <div style="text-align:center;" id="branchtimezone"></div>
            <div class="add-work-done-poppup-contant">
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">

                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">

                            <input type='hidden' id='url' value='{{$url}}' />
                            <input type='hidden' id='userid' name="userid" />
                            <input type='hidden' id='authpaymentfield_id' name="authpaymentfield_id" />
                            <input type='hidden' id='authstaff_id' name="authstaff_id" />
                            <input type='hidden' id='authuser_id' name="authuser_id" />
                            <input type='hidden' id='transcation_id' name="transcation_id" />
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Auth Code</label>
                                        {!! Form::text('authcode',null, ['class'=>'form-control','id'=>'authcode','maxlength'=>'4','onkeyup' => 'checkauthcode(this.value);','required','style'=>"background-color:transparent;"]) !!}
                                    </div>
                                </div>
                                <span id="alertauth" style="color: red"></span>
                            </div>

                           
                            <div class="box-footer">
                                <input type="hidden" name="type" id="type" />
                                <a id="inserting" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" style="display: none" onclick="accept_the_cash();">Submit</a>
                               <!-- <a id="updating" name="updating"  class="staff-add-pop-btn" onclick="submit_area('update');" style="height:40px; bottom: 20px;">Update</a>-->
                            </div>
                        </div>


                    </div>
                </div>
            </div><!--add-work-done-poppup-textbox-cc-->
        </div>
      
    </div>

    <div id="accept_upi" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup">
            <div class="add-work-done-poppup-head">Transcation ID
                <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn_upi"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
            <div style="text-align:center;" id="branchtimezone"></div>
            <div class="add-work-done-poppup-contant">
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">

                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">

                            <input type='hidden' id='url' value='{{$url}}' />
                            <input type='hidden' id='userid' name="userid" />
                            <input type='hidden' id='trancpaymentfield_id' name="trancpaymentfield_id" />
                            <input type='hidden' id='trancstaff_id' name="trancstaff_id" />
                            <input type='hidden' id='trancuser_id' name="trancuser_id" />
                            
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Transcation ID</label>
                                        {!! Form::text('transacid',null, ['class'=>'form-control','id'=>'transacid','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}
                                    </div>
                                </div>
                                
                            </div>

                           
                            <div class="box-footer">
                                <input type="hidden" name="type" id="type" />
                                <a id="inserting_t" name="inserting_t"  class="staff-add-pop-btn staff-add-pop-btn-new"  onclick="accept_the_upi();">Submit</a>
                               <!-- <a id="updating" name="updating"  class="staff-add-pop-btn" onclick="submit_area('update');" style="height:40px; bottom: 20px;">Update</a>-->
                            </div>
                        </div>


                    </div>
                </div>
            </div><!--add-work-done-poppup-textbox-cc-->
        </div>
      
    </div>

    <div id="remarks_div" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup">
            <div class="add-work-done-poppup-head">Remarks
                <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn_rmk"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
            <div style="text-align:center;" id="branchtimezone"></div>
            <div class="add-work-done-poppup-contant">
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">

                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">

                            <input type='hidden' id='url' value='{{$url}}' />
                            <input type='hidden' id='userid' name="userid" />
                            <input type='hidden' id='rmkpaymentfield_id' name="rmkpaymentfield_id" />
                            <input type='hidden' id='rmkstaff_id' name="rmkstaff_id" />
                            <input type='hidden' id='rmkuser_id' name="rmkuser_id" />
                            <div class="main_inner_class_track ">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Remarks</label>
                                        {!! Form::textarea('remarks',null, ['class'=>'form-control','id'=>'remarks','required','style'=>"background-color:transparent;",'autofocus' => "true"]) !!}
                                    </div>
                                </div>
                                
                            </div>

                           
                            <div class="box-footer">
                                <input type="hidden" name="type" id="type" />
                                <a id="inserting_r" name="inserting_r"  class="staff-add-pop-btn staff-add-pop-btn-new"  onclick="enter_remark();">Submit</a>
                               <!-- <a id="updating" name="updating"  class="staff-add-pop-btn" onclick="submit_area('update');" style="height:40px; bottom: 20px;">Update</a>-->
                            </div>
                        </div>


                    </div>
                </div>
            </div><!--add-work-done-poppup-textbox-cc-->
        </div>
      
    </div>
    
<!-- ---------------------------------   Add cash details --------------------------->
    
    <div id="add_cashdetails" class="add-work-done-poppup-cc" style="display: none;">
        <div class="add-work-done-poppup">
            <div class="add-work-done-poppup-head">Cash Details
                <a href="#"><div class="close-pop-ad-work-cc ad-work-close-btn close_addspot"><img src="{{asset ('public/assets/images/black_cross.png') }}"></div></a>
            </div>
            <div style="text-align:center;" >
                <b style="font-size: large;color: red;">Final Amount : <span id="cashtopay"></span></b>
            </div>
            <div class="add-work-done-poppup-contant">
                <div class="add-work-done-poppup-textbox-cc">
                    <div class="add-work-done-poppup-textbox-box">

                        <div class="main_container_track_order_list inner-textbox-cc" style="margin-top:10px;margin-bottom:0">

                            <input type='hidden' id='url' value='{{$url}}' />
                            <input type='hidden' id='paymentfield_id' name="paymentfield_id" />
                            <input type='hidden' id='staff_id_acc' name="staff_id_acc" />
                           <table >
                            <tr >
                                <th style="width: 10%">Denomination </th>
                                <th style="width: 2%"> </th>
                                <th style="width: 10%">Count </th>
                                <th style="width: 2%"> </th>
                                <th style="width: 10%">Amount </th>
                            </tr>
                            <tbody>
                                <tr>
                                    <td style="width: 10%;padding: 1%;font-size: large;color: blue;">2000</td>
                                    <td style="width: 2%;font-size: large;color: blue;">*</td>
                                    <td style="width: 10%"><input type="text" name="mul2000"  onkeyup="return mulwithnumbr('2000',this.value)" /></td>
                                    <td style="width: 2%">=</td>
                                    <td style="width: 10%"><span id="mul2000">0</span></td>
                                <tr>
                                  
                                    <tr>
                                        <td style="width: 10%;padding: 1%;font-size: large;color: blue;">500</td>
                                        <td style="width: 2%;font-size: large;color: blue;">*</td>
                                        <td style="width: 10%"><input type="text" name="mul500"  onkeyup="return mulwithnumbr('500',this.value)" /></td>
                                        <td style="width: 2%">=</td>
                                        <td style="width: 10%"><span id="mul500">0</span></td>
                                    <tr>
                                        <tr>
                                            <td style="width: 10%;padding: 1%;font-size: large;color: blue;">200</td>
                                            <td style="width: 2%;font-size: large;color: blue;">*</td>
                                            <td style="width: 10%"><input type="text" name="mul200"  onkeyup="return mulwithnumbr('200',this.value)" /></td>
                                            <td style="width: 2%">=</td>
                                            <td style="width: 10%"><span id="mul200">0</span></td>
                                        <tr>
                                            <tr>
                                                <td style="width: 10%;padding: 1%;font-size: large;color: blue;">100</td>
                                                <td style="width: 2%;font-size: large;color: blue;">*</td>
                                                <td style="width: 10%"><input type="text" name="mul100"  onkeyup="return mulwithnumbr('100',this.value)" /></td>
                                                <td style="width: 2%">=</td>
                                                <td style="width: 10%"><span id="mul100">0</span></td>
                                            <tr> 
                                                <tr>
                                                    <td style="width: 10%;padding: 1%;font-size: large;color: blue;">50</td>
                                                    <td style="width: 2%;font-size: large;color: blue;">*</td>
                                                    <td style="width: 10%"><input type="text" name="mul50" onkeyup="return mulwithnumbr('50',this.value)"  /></td>
                                                    <td style="width: 2%">=</td>
                                                    <td style="width: 10%"><span id="mul50">0</span></td>
                                                <tr>
                                                    <tr>
                                                        <td style="width: 10%;padding: 1%;font-size: large;color: blue;">20</td>
                                                        <td style="width: 2%;font-size: large;color: blue;">*</td>
                                                        <td style="width: 10%"><input type="text" name="mul20"  onkeyup="return mulwithnumbr('20',this.value)" /></td>
                                                        <td style="width: 2%">=</td>
                                                        <td style="width: 10%"><span id="mul20">0</span></td>
                                                    <tr>
                                                        <tr>
                                                            <td style="width: 10%;padding: 1%;font-size: large;color: blue;">10</td>
                                                            <td style="width: 2%;font-size: large;color: blue;">*</td>
                                                            <td style="width: 10%"><input type="text" name="mul10"  onkeyup="return mulwithnumbr('10',this.value)" /></td>
                                                            <td style="width: 2%">=</td>
                                                            <td style="width: 10%"><span id="mul10">0</span></td>
                                                        <tr>
                                                            <tr>
                                                                <td style="width: 10%;padding: 1%;font-size: large;color: blue;">Change</td>
                                                                <td style="width: 2%"></td>
                                                                <td style="width: 10%"><input type="text" name="mul5"  onkeyup="return mulwithnumbr('5',this.value)" /></td>
                                                                <td style="width: 2%">=</td>
                                                                <td style="width: 10%"><span id="mul5">0</span></td>
                                                            <tr>
                                                       <!-- <tr>
                                                            <td style="width: 10%;padding: 1%;">5</td>
                                                            <td style="width: 2%">*</td>
                                                            <td style="width: 10%"><input type="text" name="mul5"  onkeyup="return mulwithnumbr('5',this.value)" /></td>
                                                            <td style="width: 2%">=</td>
                                                            <td style="width: 10%"><span id="mul5">0</span></td>
                                                        <tr> 
                                                            <tr>
                                                                <td style="width: 10%;padding: 1%;">2</td>
                                                                <td style="width: 2%">*</td>
                                                                <td style="width: 10%"><input type="text" name="mul2"  onkeyup="return mulwithnumbr('2',this.value)" /></td>
                                                                <td style="width: 2%">=</td>
                                                                <td style="width: 10%"><span id="mul2">0</span></td>
                                                            <tr> 
                                                            <tr>
                                                                <td style="width: 10%;padding: 1%;">1</td>
                                                                <td style="width: 2%">*</td>
                                                                <td style="width: 10%"><input type="text" name="mul1"  onkeyup="return mulwithnumbr('1',this.value)" /></td>
                                                                <td style="width: 2%">=</td>
                                                                <td style="width: 10%"><span id="mul1">0</span></td>
                                                            <tr> -->
                                                                <tr>
                                                                    <td style="width: 10%;padding: 1%;"></td>
                                                                    <td style="width: 2%"></td>
                                                                    <td style="width: 10%;font-size: x-large; color: green;"><b>Total</b></td>
                                                                    <td style="width: 2%">=</td>
                                                                    <td style="width: 10%;font-size: x-large; color: green;"><b><span  id="finalvalue">0</span></b></td>
                                                                <tr> 
                                                                    <tr>
                                                                        <td style="width: 10%;padding: 1%;"></td>
                                                                        <td style="width: 2%"></td>
                                                                        <td style="width: 10%;font-size: x-large;color: #800023;"><b>Balance</b></td>
                                                                        <td style="width: 2%">=</td>
                                                                        <td style="width: 10%;font-size: x-large; color: #800023;"><b><span  id="finalbal">0</span></b></td>
                                                                    <tr> 
                            </tbody>
                           </table>

                            <div class="box-footer">
                                <input type="hidden" name="type" id="type" />
                                <a id="inserting_spot" name="inserting"  class="staff-add-pop-btn staff-add-pop-btn-new" onclick="submit_cash();">Accept</a>
                               <!-- <a id="updating_spot" name="updating"  class="staff-add-pop-btn" onclick="submit_area('update');" style="height:40px; bottom: 20px;">Update</a> -->
                            </div>
                        </div>


                    </div>
                </div>
            </div><!--add-work-done-poppup-textbox-cc-->
        </div>
        <div class="add-work-list-cc">
            <!--<div class="add-work-list-head">LIST</div>-->


        </div><!--add-work-done-poppup-->

    </div>
    <div id="edit_load">
    </div>



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
    <script>
        $.fn.dataTable.ext.errMode = 'none';
    </script>
    <script>
 $(document).ready(function() {
    $('#date_pay').datepicker({
                autoclose: true,
                todayHighlight: true,

            });
            $("#date_pay").datepicker().datepicker("setDate", new Date());


    load_paylist();

            var t = $('#example1').DataTable( {
                scrollY: "380px",
                scrollX: true,
                scrollCollapse: true,
                "columnDefs": [ {

                    paging: false


                } ],
                "searching": false,
                "ordering": false,
                "iDisplayLength": 10
            } );
        } );

        function load_paylist()
        {
            if($("#staffname").val() != '')
            staffname=$("#staffname").val();
            else
            staffname='';

            if($('#date_pay').val() != '')
            datecheck=$('#date_pay').val();
            else
            datecheck='';
            
            var data={"staffname":staffname,"datecheck":datecheck};
            $.ajax({
                    method: "get",
                    url : "load_paymenttable", 
                    data:data,                  
                    cache : false,
                    crossDomain : true,
                    async : false,
                    dataType :'text',
                    success : function(result)
                    {
                        $("#arealisting").html('');
                       $("#arealisting").html(result) ;
                    }
                    });

        }
        function accept_money(id,cash,staffid,userid)
        {
            $("#add_cashdetails").show();
            $("#cashtopay").html(cash);
            $("#paymentfield_id").val(id);
            $("#staff_id_acc").val(staffid);
            $("#authpaymentfield_id").val($("#paymentfield_id").val());
                $("#authstaff_id").val($("#staff_id_acc").val());
                $("#authuser_id").val(userid);
        }
        function mulwithnumbr(numb,calvalue)
        {
            var finaltot=0;
            var oldcal=$('#finalvalue').html();
            var amt=0;
            if(numb!=5)
            {
             amt= numb * calvalue;
          
           $('#mul'+numb).html(amt);
            }else if(numb==5){
                 amt= calvalue;
                $('#mul'+numb).html(amt);
            }

           finaltot=parseInt($('#mul2000').html())  + parseInt($('#mul500').html()) + parseInt($('#mul200').html()) + parseInt($('#mul100').html()) + parseInt($('#mul50').html()) + parseInt($('#mul20').html()) + parseInt($('#mul10').html()) + parseInt($('#mul5').html())  ;
           var cashtopay=$("#cashtopay").html();
           var bal=parseInt(finaltot) - parseInt(cashtopay);
           $('#finalbal').html(bal);
           $('#finalvalue').html(finaltot);
        }
        function submit_cash()
        {
            var bal=$('#finalbal').html();
            if(bal>0)
            {
                $("#add_cashdetails").hide();
                $("#authuser").show();
                /*var fieldid=$("#paymentfield_id").val();alert(fieldid);
                $("#authpaymentfield_id").val($("#paymentfield_id").val());
                $("#authstaff_id").val($("#staff_id_acc").val());*/
            }else if(bal==0)
            {
                var final=$('#finalvalue').html();
                if(final==0)
                {
                    swal({
                title: "",
                text: "Check The Amount",
                timer: 1000,
                showConfirmButton: false
                });
                }else{
                    $("#add_cashdetails").hide();
                $("#authuser").show();
                }
                
            }else{
                swal({
                title: "",
                text: "Check The Amount",
                timer: 1000,
                showConfirmButton: false
                });
            }
        }
        function checkauthcode(authcode)
        {
           var staff= $("#authuser_id").val();
            var lengthofauth=authcode.length;
            if(lengthofauth==4)
            {
            var data={"authcode":authcode,"staff":staff};
                $.ajax({
                    method: "post",
                    url : "api/checkauth_payment",
                    data : data,
                    cache : false,
                    crossDomain : true,
                    async : false,
                    dataType :'text',
                    success : function(result)
                    {
                        $("#alertauth").html(result);
                        if(result=="success")
                        {
                            $("#inserting").css("display",'block');
                        }else{
                            $("#inserting").css("display",'none');
                        }
                        
                    }
                    });
            }else{
                $("#inserting").css("display",'none');
                $("#alertauth").html("");
            }
            
        }
        function accept_the_cash()
        {
            var staff= $("#authstaff_id").val();
           var fieldid= $("#authpaymentfield_id").val();
           var userid= $("#authuser_id").val();
           var transctn= $("#transcation_id").val();

            var data={"fieldid":fieldid,"userid":userid,"transctn":transctn};
                $.ajax({
                    method: "post",
                    url : "api/accept_amount",
                    data : data,
                    cache : false,
                    crossDomain : true,
                    async : false,
                    dataType :'text',
                    success : function(result)
                    {
                        swal({
                title: "",
                text: "Accepted successfully",
                timer: 1000,
                showConfirmButton: false
                });
                location.reload();
                        
                        }
                    });

        }
        function accept_upi(id,cash,staffid,userid)
        {
            $('#accept_upi').show();
            $("#trancpaymentfield_id").val(id);
                $("#trancstaff_id").val(staffid);
                $("#trancuser_id").val(userid);
        }
        function accept_the_upi()
        {
            if($('#transacid').val() != "")
            {
                $("#accept_upi").hide();
                $("#authuser").show();
                $("#authpaymentfield_id").val($('#trancpaymentfield_id').val());
                $("#transcation_id").val($("#transacid").val());
                $("#authuser_id").val($('#trancuser_id').val());
               
            }else{
                $("#transacid").css("border-color",'red');
            }
           
        }

       
function reject_transc(id,staffid,userid)
{
    $("#remarks_div").show();
    $("#rmkpaymentfield_id").val(id);
    $("#rmkuser_id").val(userid);
    
}
function enter_remark()
{
    var fieldid=$("#rmkpaymentfield_id").val();
    var userid=$("#rmkuser_id").val();
    var remarks=$("#remarks").val();
    if(remarks!="")
    {
    var data={"fieldid":fieldid,"userid":userid,"remarks":remarks};
                $.ajax({
                    method: "post",
                    url : "api/reject_amount",
                    data : data,
                    cache : false,
                    crossDomain : true,
                    async : false,
                    dataType :'text',
                    success : function(result)
                    {
                        swal({
                title: "",
                text: "Rejected",
                timer: 1000,
                showConfirmButton: false
                });
                location.reload();
                        
                        }
                    });
                    }
}


    </script>
   
    <script>
      
       
        $(".close_addspot").click(function(){

            $("#add_cashdetails").hide();
        });
       
        $(".ad-work-close-btn").click(function(){
            $("#authuser").hide();
          
        });
        $(".ad-work-close-btn_auth").click(function(){
            $("#authuser").hide();
          
        });
        $(".ad-work-close-btn_upi").click(function(){
            $("#accept_upi").hide();
          
        });
        $(".ad-work-close-btn_rmk").click(function(){
            $("#remarks_div").hide();
          
        });
       
        $(".ad-work-clear-btn").click(function(){
            $("#area").val('');
            $("#area").focus();
            $("#status").hide();
        });
       
          </script>
    
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


@stop



<script src="{{asset('public/assets/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('public/assets/ckeditor/samples/js/sample.js')}}"></script>

<script>

    function numonly(evt)
    {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31
                && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    function charonly(evt)
    {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (!(charCode >= 65 && charCode <= 120) && (charCode != 32 && charCode != 0))
            return false;
        return true;
    }
</script>
<script>
    $(document).ready(function() {
        $('#example1').DataTable( {
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        } );
    } );

</script>

@endsection
