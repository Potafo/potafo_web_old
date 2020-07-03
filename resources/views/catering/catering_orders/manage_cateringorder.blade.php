@extends('layouts.app')

@section('content')
    <style>
        .content-page > .content { padding: 20px;}.card-body{-ms-flex:1 1 auto;flex:1 1 auto;padding:1.25rem}.media{display:-ms-flexbox;display:flex;-ms-flex-align:start;align-items:flex-start}.media-body{-ms-flex:1;flex:1}.media-body h4{font-size: 35px;}.card{margin-bottom: 15px;}.card-box{float: left;width: 100%;padding: 5px !important;}.filter_box_section_cc{padding-bottom: 12px;}.main_inner_class_track .form-control{    border: 1px solid #d8d8d8;}.timing_popup_contant_tabl{overflow: visible}
        .timing_sel_popop_tbl tfoot{ display: table; width: 99%;table-layout: fixed;}
        .timing_sel_popop_tbl thead{ display: table; width: 99%;table-layout: fixed;}
        .timing_sel_popop_tbl tr { display: table; width: 100%;}
        .timing_sel_popop_tbl tbody {display: block; overflow-y: scroll;height: 270px;width: 100%;    background-color: #ececec;}.timing_sel_popop_tbl tbody td{background-color:  #fff}
        .cancel_btn{float: left;margin-left: 0;background-color: #ff8e8e;border: solid 1px #ff8e8e;}
        .filter_text_box_row .main_inner_class_track label{margin-bottom: 0}
        .filter_text_box_row .main_inner_class_track .form-control{width: 90%}
        .new_pick_up{background-color: #d6e4c7}
        .new_deliverd{background-color: white}
        .new_cancelled{background-color: white}
        .tfooter_ttl_order td {
            color: #000;
            font-weight: normal;
        }
        #example .restaurant_more_detail_text .searchlist{top:29px;    max-height: 142px;}
        .main_inner_class_track{margin-bottom:8px}
    </style>
    <script src="{{asset('public/assets/admin/script/restaurant_offer.js') }}" type="text/javascript"></script>
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">


                <div class="filter_box_section_cc" style="    margin-top: 0px;">
                    <!--                <div class="filter_box_section">FILTER</div>-->
                    <div class="filter_text_box_row">

                        <div class="main_inner_class_track" style="width: 20%;">
                            <div class="group">
                                <div style="position: relative">
                                    <label>From</label>
                                    <input id="flt_from" data-date-format='dd-mm-yyyy'  name="flt_from" class="form-control" type="text"  >
                                </div>
                            </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 20%;">
                            <div class="group">
                                <div style="position: relative">
                                    <label>To</label>
                                    <input id="flt_to" data-date-format='dd-mm-yyyy' name="flt_to" class="form-control" type="text" >
                                </div>
                            </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 17%;">
                            <div class="group">
                                <div style="position: relative">
                                    <label>Order Number</label>
                                    <input readonly onclick="this.removeAttribute('readonly');"  class="form-control" type="text" id="order_number_filter" >
                                </div>
                            </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 12%;">
                            <div class="group">
                                <div style="position: relative">
                                    <label>Status</label>
                                    <select class="form-control" id="order_status_filter" >
                                        <option value="">All</option>
                                        <option value="P">Order Placed</option>
                                        <option value="C">Confirmed</option>
                                        <option value="D">Delivered</option>
                                        <option value="CA">Cancelled</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div id="more_div" class= "order_his_more_Sec diply_tgl">
                            <div class="main_inner_class_track" style="width: 18%;display:none;">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Phone</label>
                                        <input autocomplete="off" class="form-control" type="text" id="order_phone_filter" >
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track" style="width: 14%;">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Customer Name</label>
                                        <input autocomplete="off" class="form-control" type="text" id="order_name_filter" >
                                    </div>
                                </div>
                            </div>
                            <div class="main_inner_class_track" style="width: 15%;">
                                <div class="group">
                                    <div style="position: relative">
                                        <label>Restaurant</label>
                                        <input autocomplete="off" class="form-control" type="text" id="order_rest_filter" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 10%;">
                            <a href="#" class="odr_hist_more_btn" id="viewmore">>> More</a>
                        </div>
                        <div id="search_button" class="main_inner_class_track" style="width: 12% !important;float: left">
                            <div class="table-filter-cc" style="margin-top: 18px;">
                                <a href="#" onclick="change_filter()" style="margin-left:0;width: 80px " class="on-default followups-popup-btn btn btn-primary">Search</a>
                                <span id="searchcount"></span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="order_min_section">
                    <div id="append">
                        <div class="full_loading" style="display:none;" id="full_loading"></div>
                    </div>
                    <div class="table_section_scroll" id="append_table">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .not-active{
            pointer-events: none;
        }
    </style>
@section('jquery')

    <script type="text/javascript">
        $(document).ready(function()
        {
            $('#flt_from').datepicker({
                autoclose: true,
                todayHighlight: true,

            });
            $('#flt_to').datepicker({
                autoclose: true,
                todayHighlight: true,

            });

            $("#flt_from").datepicker().datepicker("setDate", new Date());
            $("#flt_to").datepicker().datepicker("setDate", new Date());


        } );


    </script>
    <script>
        change_filter();
        $.fn.dataTable.ext.errMode = 'none';

        // append table data
        function change_filter()
        {
            $("#full_loading").show();
            var staffid                =   $("#staff_id").val();
            var order_rest_filter      =   $('#order_rest_filter').val();
            var order_name_filter      =   $('#order_name_filter').val();
            var order_phone_filter     =   $('#order_phone_filter').val();
            var order_status_filter    =   $('#order_status_filter').val();
            var order_number_filter    =   $('#order_number_filter').val();
            var flt_from               =   $('#flt_from').val();
            var flt_to                 =   $('#flt_to').val();
            var data = {"staffid":staffid,"flt_from":flt_from,"flt_to":flt_to,"order_rest_filter":order_rest_filter,"order_name_filter":order_name_filter,"order_phone_filter":order_phone_filter,"order_status_filter":order_status_filter,"order_number_filter":order_number_filter};
            $('#append_table').html('');

            $.ajax({
                method: "get",
                url: "api/cateringorder_history_filter_tables",
                data: data,
                success: function (result)
                {
                    $('#append_table').html(result);
                    $("#full_loading").hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
        }

        //apeend div data


        function view_details(id)
        {
            window.location.href="catering_details/"+id;
        }


        function addmenu()
        {
            $(".view_order_popup").show();
            var order_number = $('#cs_order_num').html();
            var rest_id = $('#rest_id').val();
            var table = $('#example').DataTable();
            var data = {"order_number":order_number,"rest_id":rest_id}; $('#append_division').html('');
            $.ajax({
                method: "get",
                url: "api/addmenuorder",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType :'text',
                success: function (result)
                {
                    $('#append_division').html(result);
                    $("#menu_name").focus();
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
        }




        //    //view details pop up
        function view_pop_up_address(order_number,rest_id,customer_id)
        {
            $(".view_order_popup").show();
            $("#can_reson").css("display","none");
            $("#user_can_reason").val("");
            $("#assigned_note").val("");
            $('#assigned_note').removeClass('not-active');
            $('#customer_id').val(customer_id);
            var data = {"order_number":order_number,
                "rest_id":rest_id,
            };
            $.ajax({
                method: "get",
                url: "api/view_order_address_list",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                success: function (result)
                {
                    var status = result[0].current_status;
                    var paymode = result[0].payment_method;
                    var coupon_details = result[0].coupon_details;
                    $('#razp_od').css('display','none');
                    $('#razp_pay').css('display','none');
                    $('#razp_method').css('display','none');
                    $('#razp_refund').css('display','none');
                    if(status != 'P')
                    {
                        $('#assgn_staff_name').html(result[0].staff_name);
                        $('#change_staff_name').css('display','inline-block');
                        $('#assigned_note').addClass('not-active');
                        $('.staffname_cls').addClass('not-active');
                        $('#add_menu').hide();
                        $('.cancel_btn').hide();
                        $('.confirm_btn').hide();
                        if(status == 'CA'){
                            $('.current_order_delivery_tm').hide();
                            $('#change_staff_name').css('display','none');
                        } else if(status == 'D'){
                            $('.current_order_delivery_tm').show();
                            $('#change_staff_name').css('display','none');
                        } else{
                            $('.current_order_delivery_tm').show();
                            $('#change_staff_name').css('display','inline-block');
                            $('#ord_cancel_btn').css('display','block');
                        }
                    }else{
                        $('#assgn_staff_name').html('Select');
                        $('#change_staff_name').css('display','none');
                        $('.staffname_cls').removeClass('not-active');
//                         $('#add_menu').show();
                        $('.cancel_btn').show();
                        $('.confirm_btn').show();
                        $('.current_order_delivery_tm').show();
                        if(paymode =='COD' || paymode =='cod')
                        {
                            $('#add_menu').show();
                        }
                        else
                        {
                            $('#add_menu').hide();
                        }
                    }

                    if(result[0].assignednote!=0){
                        $('#assigned_note').val(result[0].assignednote);
                    }
                    $('#cs_order_num').html(result[0].order_number);
                    $('#rest_id').val(result[0].rest_id);
                    $('#hotel_name').html(result[0].rest_name);
                    $('#hotel_adrs').html(result[0].address);
                    $('#hotel_phone').html(result[0].ind+'-'+result[0].mob);
//                     $('#hotel_pin').val(result[0].);
                    $('#cstmr_name').html(result[0].name);
                    $('#cstmr_phone').html(result[0].mobile);
                    $('#cstmr_address_1').html(result[0].addressline1+', '+result[0].addressline2);
                    if(result[0].pincode != 'null')
                    {
                        $('#cstmr_address_2').html(result[0].addresstype+', '+result[0].pincode);
                    }
                    else
                    {
                        $('#cstmr_address_2').html(result[0].addresstype);
                    }
                    if(result[0].landmark != '0') {
                        $('#cstmr_landmark').html(result[0].landmark);
                    }
                    $('#payment_mode').html(result[0].payment_method);
                    if(result[0].payment_method=='ONLINE'){
                        $('#razp_od').css('display','block');
                        $('#razp_pay').css('display','block');
                        $('#razp_method').css('display','block');
                        $('#razorpay_orderid').html(result[0].razopayorderid);
                        $('#razorpay_paymentid').html(result[0].razopaypaymentid);
                        $('#razorpay_method').html(result[0].method);
                        if(status == 'CA'){
                            $('#razp_refund').css('display','block');
                            $('#razorpay_rerund').html(result[0].refundid);
                        }
                    }
                    var entry_mode = result[0].mode_of_entry;
                    var appversion = result[0].app_version;
                    $("#payment_type").html('');
                    if(entry_mode=='I'){
                        $("#payment_type").html('<div class="mng_odr_pop_right_pymnt_head ios_clr" ><span></span> IOS - VER('+appversion+')</div>')
                    }
                    else if(entry_mode=='A' ){
                        $("#payment_type").html('<div class="mng_odr_pop_right_pymnt_head and_clr" ><span></span> ANDROID - VER('+appversion+')</div>')
                    }
                    else if(entry_mode=='W'){
                        $("#payment_type").html('<div class="mng_odr_pop_right_pymnt_head web_clr"  ><span></span> WEB</div>')
                    }
                    if(coupon_details){

                        $('#add_menu').hide();
                    }
                    //payment_mode <div class="mng_odr_pop_right_pymnt_head" ></div>

                },
                error: function (jqXHR, textStatus, errorThrown)
                {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
        }

        function deletemenu(order_number,rest_id,sl_no,menuid)
        {
            swal({
                title: "",
                text: "Are you sure you want to delete?",
                type: "info",
                showCancelButton: true,
                cancelButtonClass: 'btn-white btn-md waves-effect',
                confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
                confirmButtonText: 'Delete',
                closeOnConfirm: false
            }, function (isConfirm)
            {
                if (isConfirm)
                {
                    var data = {"order_number":order_number,"rest_id":rest_id,"sl_no":sl_no,"menuid":menuid};
                    $.ajax({
                        method: "get",
                        url: "api/delete_menuorder",
                        data: data,
                        cache: false,
                        crossDomain: true,
                        async: false,
                        success: function (result)
                        {
                            if(result=='delete_restricted'){
                                swal({
                                    title: "",
                                    text: "Sorry You Cannot delete, if you want then cancel the order",
                                    timer: 5000,
                                    showConfirmButton: false
                                });
                            }
                            else{
                                $('#append_division').html(result);
                                $('.showSweetAlert').hide();
                                $('.sweet-overlay').hide();
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                            $("#errbox").text(jqXHR.responseText);
                        }
                    });
                }
            });
        }
        function edit_menu(order_number,rest_id,sl_no)
        {
            var data = {"order_number":order_number,"rest_id":rest_id,"sl_no":sl_no};
            $.ajax({
                method: "get",
                url: "api/edit_order_details",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                success: function (result)
                {
                    $('#append_division').html(result);
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
        }

        function saveedit_menu(order_number,rest_id,sl_no,menuid)
        {

            var prefrnce = $("#menu_preference").val();
            var qty = $("#menu_qty").val();
            var final_rate = $("#final_rate").html();
            var data = {"order_number":order_number,"rest_id":rest_id,"sl_no":sl_no,"menuid":menuid,"prefrnce":prefrnce,"qty":qty,"final_rate":final_rate};

            $.ajax({
                method: "get",
                url: "api/saveedit_order",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                success: function (result)
                {
//                    alert (result);
                    $('#append_division').html(result);
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
//                    alert(jqXHR.responseText);
                }
            });
        }

        function staff_list_assign(optn, type)
        {
            $('#assign_staffs').css('display',optn);
            $('#asn_staff_table').html('');
            if(optn =='block') {
                $.ajax({
                    method: "get",
                    url: "api/assign_staff_list",
                    cache: false,
                    crossDomain: true,
                    async: false,
                    success: function (result)
                    {
                        $('#asn_staff_table').html(result['append']);
                        if(type =='Ch'){
                            $("#change_mode").val('changestaff')
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $("#errbox").text(jqXHR.responseText);
                    }
                });
            }
            else{
                $("#view_order_popup").show();
            }
        }
        function selct_staf(fldid,name,id,mobile) {
            $('.assign_check').prop('checked',false);
            $('#test' +fldid).prop('checked',true);
            $('#ass_staff_id').val(id);
            $('#ass_staff_name').val(name);
            $('#assgn_staff_mobile').val(mobile);
        }
        function confirm_assign_satff() {
            var staff = $('#ass_staff_name').val();
            if(staff!=''){
                $('#assign_staffs').css('display','none');
                $('.confirm_btn').show();
                $("#assgn_staff_name").html(staff);
                var staff_id = $('#ass_staff_id').val();
                $("#ass_staff_id").html(staff_id);
                var staff_mobile = $('#assgn_staff_mobile').val();
                $("#assgn_staff_mobile").html(staff_mobile);
                $('#change_staff_name').css('display','none');
            }
            else{
                swal({
                    title: "",
                    text: "Select Staff",
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }



        function selectreasonexist(reason) {
            $("#reason_suggestions").hide();
            $("#user_can_reason").val(reason);
        }
    </script>


    <script>

        $(".current_order_box").click(function(){
            $(".view_order_popup").show();
        });
        $(".vie_odr_btn").click(function(){
            $(".view_order_popup").show();
        });
        $("#timing_popup_cls").click(function(){
            $(".view_order_popup").hide();
        });
        $(".authorization_popup_cls").click(function(){
            $(".authpopup").hide();
        });


    </script>

    <script type="text/javascript">
        //        $(document).ready(function()
        //        {
        //        var t = $('#example1').DataTable({
        //
        //            "searching": false,
        //
        //            "lengthChange": false,
        //
        //        } );
        //    } );

        $('.togle_section').on('click', function(e)
        {
            $('.order_min_section').toggleClass("show_hide");
            //$("#restaurant_name").focus();
        });

        function menunamechange(val)
        {
            var res_id = $('#rest_id').val();
            var val = val;
            var n = val.lastIndexOf(',');
            var str1 =  val.slice(0,n);
            var str2 =  val.slice(n+1,val.length);
            if(n == -1)
            {
                var val = val;
            }
            else
            {
                var str1 =  val.slice(0,n);
                var str2 =  val.slice(n+1,val.length);
                var val  =str2;
            }

            if(val != '')
            {
                var temp = val;
                var count = temp.length;
                var segments = val.split(',');
                if (temp.indexOf(',') != -1) {
                    var val = segments[1];
                }
                else{
                    var val = val;
                }
                var count = val.length;
                if(parseInt(count)>= 3)
                {
                    var datas = {'rest_id': res_id,'searchterm': val};
                    $.ajax({

                        method: "get",
                        url : "orderitem/search",
                        data : datas,
                        cache : false,
                        crossDomain : true,
                        async : false,
                        dataType :'text',
                        success: function (data)
                        {
                            $("#suggesstionsmenu").empty();
                            $("#suggesstionsmenu").show();
                            $("#suggesstionsmenu").html(data);
//                    $.each(JSON.parse(data), function (i, indx)
//                    {
//                        $.each(JSON.parse(indx.portion), function (n, val)
//                        {
//                         var search_id = indx.m_menu_id+'_'+val.portion;
//                        if ($("#search_" + search_id).length == 0)
//                        {
//                            var menu_name = indx.name+' , '+val.portion;
//                            $("#suggesstionsmenu").show();
//                            $("#suggesstionsmenu").append('<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="search_' + search_id + '" onclick=\'selectname("' + indx.name + '","' + indx.m_menu_id + '","' + val.portion + '","' + val.final_rate + '")\'>' + '<p>' + menu_name + '</p></div>');
//                        }
//                    });
//                    });

                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            alert('error');
                            $("#urls").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                        }
                    });
                }
                else
                {
                    $("#suggesstionsmenu").html('');
                }
            }
            return true;
        }

        //on search click
        function selectname(menu_name,menu_id,portion,final_rate)
        {
            $("#menu_name").val(menu_name + ' , ' + portion);
            $("#menu_rate").html(final_rate);
            $("#menu_qty").val('1');
            $("#final_rate").html(final_rate);
            $("#suggesstionsmenu").hide();
        }

        function qtychange(val)
        {
            var qty = $("#menu_qty").val();
            var rate = $("#menu_rate").html();
            var final_rate = (qty)*(rate);
            $("#final_rate").html(final_rate);
        }


        function addmenu_save(order_number,rest_id)
        {
            var menu = $("#menu_name").val();
            if(menu!=''){
                var rate = $("#menu_rate").html();
                var prefrnce = $("#menu_preference").val();
                var qty = $("#menu_qty").val();
                var final_rate = $("#final_rate").html();
                var table = $('#example').DataTable();
                var data = {"order_number":order_number,"rest_id":rest_id,"menu":menu,"rate":rate,"prefrnce":prefrnce,"qty":qty,"final_rate":final_rate};
                $('#append_division').html('');
                $.ajax({
                    method: "get",
                    url: "api/addnewmenuorder",
                    data: data,
                    cache: false,
                    crossDomain: true,
                    async: false,
                    dataType :'text',
                    success: function (result)
                    {
                        if(result=='exist') {
                            swal({

                                title: "",
                                text: "Item Allready Exist",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            addmenu();
                            $("#menu_name").val(menu)
                        }
                        else{
                            $('#append_division').html(result);

                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#errbox").text(jqXHR.responseText);
//                      alert(jqXHR.responseText);
                    }
                });
            }
            else{
                swal({

                    title: "",
                    text: "Please Enter Menu",
                    timer: 2000,
                    showConfirmButton: false
                });
                $("#menu_name").focus();
            }
        }
        function submitevent(event)
        {
            if(event.keyCode==13)
            {
                save_orderpopup();
            }
        }
        $('#viewmore').on('click', function(e)
        {
            $('#more_div').toggleClass("diply_tgl");
            if($(this).text() == ">> More")
            {
                $(this).text("<< Less");
            }
            else
            {
                $(this).text(">> More");
            }
        });

    </script>


@stop
@endsection