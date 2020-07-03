@extends('layouts.app')
@section('content')
 <style>
     .filter_text_box_row{margin-bottom: 6px}
     .dt-buttons{position: absolute;right: 0px;top: -130px;font-weight: bold;}
     .dt-buttons .btn-sm { padding: 6px 10px;box-shadow: 2px 3px 5px #d0d0d0;}
     .dt-buttons .btn-sm { padding: 6px 10px;box-shadow: 2px 3px 5px #d0d0d0;  font-weight: bold; }
     .content{padding: 10px !important;}
     .searchlist{top:29px;    max-height: 142px;}

    </style>

       
        <div class="card-box table-responsive" style="padding: 8px 10px;">
             
              <div class="box master-add-field-box" >
             <div class="col-md-6 no-pad-left">
                <h3>STAFF REPORT</h3>
                
            </div>    
                  
            
               <div class=" pull-right">
                <div class="table-filter" style="margin-top: 4px;">
<!--                  <div class="table-filter-cc">
                    <a title="Filter" href="#" onclick="filter_view()"> <button type="submit"  style="margin-right: 10px;" class="on-default followups-popup-btn btn btn-primary filter_sec_btn" ><i class="fa fa-filter" aria-hidden="true"></i></button></a>
                </div>-->
                   
                 </div>
            </div>
                  
            </div>
            <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>

            <div class="filter_box_section_cc ">
<!--                <div class="filter_box_section">FILTER</div>-->
                   <div class="filter_text_box_row">
                       {!! Form::open(['name'=>'frm_filter', 'id'=>'frm_filter','method'=>'get']) !!}

                       <div class="main_inner_class_track" style="width: 19%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Reports</label>
                                  <select class="form-control" id="reports_id" onchange="reports_change(this.value);">
                                      @foreach($reports_all as $val)
                                      <option value='{{$val->report_name}}'><?= ucwords(str_replace('_', ' ', $val->report_name));?></option>
                                      @endforeach
                                  </select>
                              </div>
                           </div>
                        </div>
                       
                       <div id="date_from_id" class="main_inner_class_track" style="width: 10%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>From Date</label>
                                  <input id="flt_from" data-date-format='dd-mm-yyyy' name="flt_from" class="form-control" type="text" value="<?=date('d-m-Y')?>">
                              </div>
                           </div>
                        </div>
                       <div id="date_to_id" class="main_inner_class_track" style="width: 10%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>To Date</label>
                                 <input id="flt_to" name="flt_to" data-date-format='dd-mm-yyyy'  class="form-control" type="text"  value="<?=date('d-m-Y',strtotime('+1'))?>">
                              </div>
                           </div>
                        </div>
                       <div id="allstaffs_id" class="main_inner_class_track" style="width: 12%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Delivery Staff</label>
                                  <select class="form-control" id="staff">
                                      <option value='all'>All</option>
                                      @foreach($staffs as $val)
                                      <option value='{{$val->id}}'>{{$val->name}}</option>
                                      @endforeach
                                  </select>
                              </div>
                           </div>
                        </div>
                       <div id="paymode_div" class="main_inner_class_track" style="width: 10%;">
                           <div class="group">
                               <div style="position: relative">
                                   <label>Paymode</label>
                                   <select class="form-control" id="payment_mode">
                                       <option value='all' id="option_values_change">All</option>
                                       @foreach($paymode as $val)
                                           <option value='{{$val->name}}'>{{$val->name}}</option>
                                       @endforeach
                                   </select>
                               </div>
                           </div>
                       </div>
                       <div id="category_div" class="main_inner_class_track" style="width: 12%;">
                           <div class="group">
                               <div style="position: relative">
                                   <label>Category</label>
                                   @foreach($order_cat as $cats)

                                       @if($cats->order_list_cat == 'all')
                                           <select class="form-control" id="order_cat_filter" onchange="return change_filter(),change_filter_2();">
                                               <option value="">All</option>
                                               <option value="Restaurant">Restaurant</option>
                                               <option value="Potafo Mart">Potafo Mart</option>

                                           </select>
                                       @endif
                                       @if($cats->order_list_cat == 'restaurant')
                                           <select class="form-control" id="order_cat_filter" onchange="return change_filter(),change_filter_2();">

                                               <option value="Restaurant">Restaurant</option>


                                           </select>
                                       @endif
                                       @if($cats->order_list_cat == 'potafo_mart')
                                           <select class="form-control" id="order_cat_filter" onchange="return change_filter(),change_filter_2();">

                                               <option value="Potafo Mart">Potafo Mart</option>


                                           </select>

                                       @endif
                                   @endforeach
                               </div>
                           </div>
                       </div>
                       <div id="search_button" class="main_inner_class_track" style="width: 22% !important;">
                           <div class="table-filter-cc" style="margin-top: 22px;">
                               <a href="#" onclick="submit_filter();" style="margin-left:0;width: 80px " class="on-default followups-popup-btn btn btn-primary">Search</a>

                            </div>
                        </div>
                       
                       {{ Form::close() }}
                   </div>  
            </div>
             <div id="append">
                     <div class="full_loading" style="display:none;" id="full_loading"></div>
                </div>
            <div class="table_section_scroll" id="append_div" style="position:relative;">
<!--            <table id="example1" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th style="min-width:40px">Sl No</th>
                    <th style="min-width:70px">Order No</th>
                    <th style="min-width:100px">Date</th>
                    <th style="min-width:100px">Amount</th>
                    <th style="min-width:100px">Pack Chrg</th>
                    <th style="min-width:100px">Delv chrg</th>
                    <th style="min-width:100px">Final Amount</th>
                    <th style="min-width:100px">Conf Time</th>
                    <th style="min-width:100px">Pick Time</th>
                    <th style="min-width:100px">Delv Time</th>
                    <th style="min-width:100px">Staff Rating</th>
                    <th style="min-width:100px">Staff Review</th>
                    <th style="min-width:100px">Cust Name</th>
                    <th style="min-width:100px">Phone</th>
                    
                </tr>
                </thead>
              
                <tbody>
                    <?php 
                    $i=0;
                    $tot_cash_tot=0; 
                    $tot_amount_total = 0;
                    $tot_pck_chrg_total = 0;
                    $tot_del_chrg_total = 0;
                    $tot_final_total = 0;
                    $tfoot_amount_total = 0;
                    $tfoot_pck_chrg_total = 0;
                    $tfoot_chrg_total = 0;
                    $tfoot_total = 0;
                    
                    ?>
                    @if(count($filter_query)!= 0)
                            <tr role="row" class="odd" style="background-color: beige !important;">
                                <td style="min-width:30px;"></td>
                                <td style="min-width:30px;"></td>
                                <td><strong style="color:#000"><?=$asnd_stf[0]->date?></strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                    @foreach($asnd_stf as $value)
                            <tr role="row" class="odd">
                                <td style="min-width:30px;"></td>
                                <td style="min-width:30px;"></td>
                                <td><?=$value->name?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                             <?php 
                                $u=0;
                                
                             ?>
                            @foreach($filter_query[$i] as $val)
                            
                            
                            <?php   
                                $u++; 
                                $tot_amount = $val->amount;
                                $tot_pck_chrg = $val->pck_chrg;
                                $tot_del_chrg = $val->del_chrg;
                                $tot_final = $val->final_total;
                                
                                $tot_amount_total += $tot_amount;
                                $tot_pck_chrg_total += $tot_pck_chrg;
                                $tot_del_chrg_total += $tot_del_chrg;
                                $tot_final_total += $tot_final;
                                
                                
                                
                            ?>
                            <tr role="row" class="odd">
                                <td style="min-width:30px;"><?=$u?></td>
                                <td><?=$val->order_number?></td>
                                <td></td>
                                <td><?=number_format((float)$val->amount, $num_format)?></td>
                                <td><?php $amt = substr($val->pck_chrg,0,-1); echo number_format((float)substr($amt,1), $num_format)?></td>
                                <td><?php $fnl = substr($val->del_chrg,0,-1); echo number_format((float)substr($fnl,1), $num_format)?></td>
                                <td><?=number_format((float)$val->final_total,$num_format)?></td>
                                <td><?=$val->cnfrm?></td>
                                <td><?=$val->picked?></td>
                                <td><?=$val->delivery?></td>
                                <td><?php $rating = substr($val->rating,0,-1); echo substr($rating,1)?></td>
                                <td><?php $review = substr($val->review,0,-1); echo substr($review,1)?></td>
                                <td><?=$val->cus_name?></td>
                                <td><?=$val->cus_mobile?></td>
                              </tr>
                  @endforeach
                   <?php $i++; ?>
                              <tr role="row" class="odd" style="    background-color: #e2e2e2 !important;">
                                <td style="min-width:30px;"></td>
                                <td style="min-width:30px;font-weight:bold">Total</td>
                                <td></td>
                                <td style="font-weight:bold;color:#000"><?=number_format((float)$tot_amount_total, $num_format)?></td>
                                <td style="font-weight:bold;color:#000"><?=number_format((float)$tot_pck_chrg_total, $num_format)?></td>
                                <td style="font-weight:bold;color:#000"><?=number_format((float)$tot_del_chrg_total, $num_format)?></td>
                                <td style="font-weight:bold;color:#000"><?=number_format((float)$tot_final_total, $num_format)?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                              </tr>  
                              <?php 
                                $tfoot_amount_total += $tot_amount_total;
                                $tfoot_pck_chrg_total += $tot_pck_chrg_total;
                                $tfoot_chrg_total += $tot_del_chrg_total;
                                $tfoot_total += $tot_final_total;                             
                              
                              ?>
                            
                  @endforeach
                   @endif
              
                </tbody>
                <tfoot>
                               <tr role="row" class="odd" style="">
                                <td style="min-width:30px;"></td>
                                <td style="min-width:30px;"></td>
                                <td style="min-width:30px;"></td>
                                <td style="min-width:100px;font-weight:bold;color:#000"><?=$tfoot_amount_total?></td>
                                <td style="min-width:100px;font-weight:bold;color:#000"><?=$tfoot_pck_chrg_total?></td>
                                <td style="min-width:100px;font-weight:bold;color:#000"><?=$tfoot_chrg_total?></td>
                                <td style="min-width:100px;font-weight:bold;color:#000"><?=$tfoot_total?></td>
                                <td style="min-width:100px;"></td>
                                <td style="min-width:100px;"></td>
                                <td style="min-width:100px;"></td>
                                <td style="min-width:100px;"></td>
                                <td style="min-width:100px;"></td>
                                <td style="min-width:100px;"></td>
                                <td style="min-width:100px;"></td>
                              </tr>
                </tfoot>
                
                
                
            </table>-->
           </div>  
        </div>



@section('jquery')
    <script type="text/javascript">
        $(document).ready(function()
        {
        var t = $('#example12').DataTable({
             scrollY: "340px",
            scrollX: true,
            dom: "Bfrtip",
            paging: false,
            buttons: [{
                text: 'Excel',
                extend: "csv",
                filename: 'Delivery_staff_report', 
                className: "btn-sm"
            }],
            scrollCollapse: false,
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
        });
         $('#flt_from').datepicker({
                       autoclose: true,
                       todayHighlight: true,
                       
    });
             $('#flt_to').datepicker({
                        autoclose: true,
                	todayHighlight: true,
                       
    });
    } );

     $('.filter_sec_btn').on('click', function(e)
    {
        $('.filter_box_section_cc').toggleClass("diply_tgl");
        $("#restaurant_name").focus();
    });
</script>

<script>
    function reports_change(values)
    {
        $('#append_div').html('');
        if(values == 'select'){
            $('#date_from_id').hide();
            $('#date_to_id').hide();
            $('#allstaffs_id').hide();
            $('#search_button').hide();
            $('#paymode_div').hide();
        }
        else if(values == 'Delivery_staff_report')
        {
            $('#date_from_id').show();
            $('#date_to_id').show();
            $('#allstaffs_id').show();
            $('#search_button').show();
            $('#paymode_div').show();
            $('#category_div').css('display','block');
        }
        else if(values == 'Staff_change_report')
        {
            $('#allstaffs_id').hide();
            $('#paymode_div').hide();
             $('#category_div').css('display','none');
            
        }
        else if(values == 'Staff_salary_report')
        {
            $('#allstaffs_id').show();
            $('#date_from_id').show();
            $('#date_to_id').show();
            $('#paymode_div').hide();
            $('#category_div').css('display','none');
        }

    }
    
    function submit_filter()
    {
        var payment_mode       =   $('#payment_mode').val();
        var date_from       =   $('#flt_from').val();
        var date_to         =   $('#flt_to').val();
        var staff           =   $('#staff').val();
        var staff_id           =   $('#staff_id').val();
        var reports_name    =   $('#reports_id').val();
        var order_cat_filter       =   $('#order_cat_filter').val();
        if(staff == 'all'){
            var rp_name = reports_name;
        }else{
            var rp_name = reports_name; 
        }
        
        
        if(reports_name == 'Delivery_staff_report'){    
        if(date_from == '' || date_to == ''){
          
            alert('Select From Date And To Date');

            if(date_from == ''){
                $('#flt_from').focus();
            }else if(date_to == ''){
                 $('#flt_to').focus();
             }   
            
            return false;
        }
           var data = {"staff_id":staff_id,"date_from":date_from,"date_to":date_to,"staff":staff,"payment_mode":payment_mode,"reports_name":reports_name,"order_cat_filter":order_cat_filter};
            $('#append_div').html('');
//            $("#full_loading").show();
              $.ajax({
                method: "get",
                url: "api/filter_staff_reports",
                data: data,
                async:false,
                success: function (result)
                {
//                    alert(JSON.stringify(result));
                    $('#append_div').html(result);
//                    $("#full_loading").hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
        }
        else if(reports_name == 'Staff_change_report')
        {
           
            
            if(date_from == '' || date_to == ''){
          
            alert('Select From Date And To Date');

            if(date_from == ''){
                $('#flt_from').focus();
            }else if(date_to == ''){
                 $('#flt_to').focus();
             }   
            
            return false;
        }
         
        
         var data = {"staff_id":staff_id,"date_from":date_from,"date_to":date_to,"reports_name":reports_name};
            $('#append_div').html('');
//             $("#full_loading").show();
              $.ajax({
                method: "get",
                url: "api/filter_staff_reports",
                data: data,
                async:false,
                  success: function (result)
                {
                    $('#append_div').html(result);

                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
        }
        else if(reports_name == 'Staff_salary_report')
        {
            if(date_from == '' || date_to == ''){

                alert('Select From Date And To Date');

                if(date_from == ''){
                    $('#flt_from').focus();
                }else if(date_to == ''){
                    $('#flt_to').focus();
                }

                return false;
            }


            var data = {"staff_id":staff_id,"staff":staff,"date_from":date_from,"date_to":date_to,"reports_name":reports_name,"from_mode":'REPORT',"app_screen":''};
            $('#append_div').html('');
             $("#full_loading").show();
            $.ajax({
                method: "get",
                url: "api/filter_staff_reports",
                data: data,
                async:false,
                success: function (result)
                {
                    $("#full_loading").hide();

                    $('#append_div').html(result);

                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
        }

        var rp_name = reports_name;
        var replaced = rp_name.replace(' ', '_');
        var t = $('#'+rp_name).DataTable({
            scrollY: "340px",
            scrollX: true,
            dom: "Bfrtip",
            paging: false,
            serverSide: true,
            bProcessing: true,
            buttons: [{
                text: 'Excel',
                extend: "csv",
                filename: replaced,
                className: "btn-sm"
            }],
            scrollCollapse: true,
            searching: false,
            ordering: false,
            info: false,
            columnDefs: [
                { width: '20%', targets: 0 }
            ],
            "deferLoading": 0,
            "lengthChange": false,
            "columnDefs": [{
                paging: false
            } ],
                    });
        return true;
    }
     
</script>

@stop
@endsection




