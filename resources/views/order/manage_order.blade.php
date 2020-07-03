@extends('layouts.app')

@section('content')

 
    <style>
       
        
        
        .content-page > .content { padding: 20px;}.card-body{-ms-flex:1 1 auto;flex:1 1 auto;padding:1.25rem}.media{display:-ms-flexbox;display:flex;-ms-flex-align:start;align-items:flex-start}.media-body{-ms-flex:1;flex:1}.media-body h4{font-size: 35px;}.card{margin-bottom: 15px;}.card-box{float: left;width: 100%;padding: 5px !important;}.filter_box_section_cc{padding-bottom: 12px;}.main_inner_class_track .form-control{    border: 1px solid #d8d8d8;}.timing_popup_contant_tabl{overflow: visible}
        .timing_sel_popop_tbl thead, tfoot{ display: table; width: 99%;table-layout: fixed;}
        .timing_sel_popop_tbl tr { display: table; width: 100%;}
        .timing_sel_popop_tbl tbody {display: block; overflow-y: scroll;height: 270px;width: 100%;    background-color: #ececec;}.timing_sel_popop_tbl tbody td{background-color:  #fff}
        .cancel_btn{float: left;margin-left: 0;background-color: #ff8e8e;border: solid 1px #ff8e8e;}
        .filter_text_box_row .main_inner_class_track label{margin-bottom: 0}
        .filter_text_box_row .main_inner_class_track .form-control{width: 90%}
        .new_pick_up{background-color: #d6e4c7}
        .new_deliverd{background-color: white}
        .new_cancelled{background-color: white}
        .confirm_place{background-color: #eddcfd}
        @-webkit-keyframes cnfrmtnBlink {
            0%   { border: 3px solid rgba(14, 111, 17, .2); }
            100% { border: 3px solid  rgba(14, 111, 17, 0.9); }
        }
        @-moz-keyframes cnfrmtnBlink {
            0%   { border: 3px solid rgba(14, 111, 17, .2); }
            100% { border: 3px solid rgba(14, 111, 17, 0.9); }
        }

        @keyframes cnfrmtnBlink {
            0%   { border: 3px solid rgba(14, 111, 17, .2); }
            100% { border: 3px solid  rgba(14, 111, 17, 0.9); }
        }
        .delayed_confirmation {
            border: 3px solid rgb(14, 111, 17);
            border: 3px solid rgba(14, 111, 17, 1);
            -webkit-background-clip: padding-box; /* for Safari */
            background-clip: padding-box; /* for IE9+, Firefox 4+, Opera, Chrome */
            -webkit-animation: cnfrmtnBlink 1s infinite;
            -moz-animation:    cnfrmtnBlink 1s infinite;
            -o-animation:      cnfrmtnBlink 1s infinite;
            animation:         cnfrmtnBlink 1s infinite;
        }
        @-webkit-keyframes hldBlink {
            0%   { border: 3px solid rgba(213, 227, 15, .2); }
            100% { border: 3px solid  rgba(213, 227, 15, 0.9); }
        }
        @-moz-keyframes hldBlink {
            0%   { border: 3px solid rgba(213, 227, 15, .2); }
            100% { border: 3px solid rgba(213, 227, 15, 0.9); }
        }

        @keyframes hldBlink {
            0%   { border: 3px solid rgba(213, 227, 15, .2); }
            100% { border: 3px solid  rgba(213, 227, 15, 0.9); }
        }
        .on_hold {
            border: 3px solid rgb(213, 227, 15);
            border: 3px solid rgba(213, 227, 15, 1);
            -webkit-background-clip: padding-box; /* for Safari */
            background-clip: padding-box; /* for IE9+, Firefox 4+, Opera, Chrome */
            -webkit-animation: hldBlink 1s infinite;
            -moz-animation:    hldBlink 1s infinite;
            -o-animation:      hldBlink 1s infinite;
            animation:         hldBlink 1s infinite;
        }
        .new_assigned{background-color:#ececec;}
        .tfooter_ttl_order td {
            color: #000;
            font-weight: normal;
        }
        .tbl_totalcharge tbody{height:auto}
        #example .restaurant_more_detail_text .searchlist{top:29px;    max-height: 142px;}
        .Location_btn{
            width: auto;
    padding:2px 15px;
    background-color: #4CAF50 !important;
    border: 1px solid #4caf50 !important;
    box-shadow: 5px 3px 12px #bdbdbd;
    border-bottom: 3px solid #197b1d !important;
    font-weight: bold;
    float: right;
    color: #fff;
    border-radius: 20px;
    margin: 8px 3px;
    cursor:pointer;
        }
        .Location_btn:hover{    background-color: #10bb17 !important;}
        .cancel_reasons_view{    background-color: azure !important;    box-shadow: -1px -5px 5px #ccc !important;}
        .cancel_reasons_view a{width:100%;float:left;padding:3px}.cancel_reasons_view span{width:100%;float:left;padding:3px}
        .loader_staff_sec{position: absolute; width: 100%; height: 100%; left: 0; top: 0; background-color: rgba(255, 255, 255, 0.62);
    text-align: center; padding-top: 20%;} .loader_staff_sec img{width:185px} 
    </style>
    <script src="{{asset('public/assets/admin/script/restaurant_offer.js') }}" type="text/javascript"></script>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">


                <div class="filter_box_section_cc" style="    margin-top: 0px;">
                    <!--                <div class="filter_box_section">FILTER</div>-->
                    <div class="filter_text_box_row">

                        <div class="main_inner_class_track" style="width: 17%;">
                            <div class="group">
                                <div style="position: relative">
                                    <label>Order Number</label>
                                    <input autocomplete="off" class="form-control" type="text" id="order_number_filter" onchange="return change_filter(),change_filter_2();">
                                </div>
                            </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 12%;">
                            <div class="group">
                                <div style="position: relative">
                                    <label>Status</label>
                                    <select class="form-control" id="order_status_filter" onchange="return change_filter(),change_filter_2();">
                                        <option value="">ALL</option>
                                        <option value="P">Placed</option>
                                        <option value="C">Confirmed</option>
                                        <option value="OP">Picked</option>
                                        <option value="D">Delivered</option>
                                        <option value="CA">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                         <div class="main_inner_class_track" style="width: 12%;">
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
                        
                        <div class="main_inner_class_track" style="width: 18%;">
                            <div class="group">
                                <div style="position: relative">
                                    <label>Phone</label>
                                    <input autocomplete="off" class="form-control" type="text" id="order_phone_filter" onchange="return change_filter(),change_filter_2();">
                                </div>
                            </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 18%;">
                            <div class="group">
                                <div style="position: relative">
                                    <label>Customer Name</label>
                                    <input autocomplete="off" class="form-control" type="text" id="order_name_filter" onchange="return change_filter(),change_filter_2();">
                                </div>
                            </div>
                        </div>
                        <div class="main_inner_class_track" style="width: 18%;">
                            <div class="group">
                                <div style="position: relative">
                                    <label>Restaurant/Shop</label>
                                    <input readonly class="form-control" type="text" id="order_rest_filter" onclick="this.removeAttribute('readonly');"  onchange="return change_filter(),change_filter_2();">
                                </div>
                            </div>
                        </div>

                        <!--                       <div class="main_inner_class_track" style="width: 17%;">
                                                  <div class="group">
                                                     <div style="position: relative">
                                                          <label>Sort By</label>
                                                         <select class="form-control">
                                                             <option value="">Newest</option>
                                                             <option value="N">Time to Near</option>
                                                             <option value="Y">Old</option>
                                                         </select>
                                                      </div>
                                                   </div>
                                                </div>-->


                    </div>
                </div>

                <!--            <h4 class="text-dark header-title" style="margin-top:10px;float: left;">Orders</h4>-->
                <div class="table_inform_sec">
                    <div class="table_inform_clr" style="width: 332px;">
                        <div class="table_inform_clr_rnd"></div>
                        <div class="table_inform_clr_name">Placed</div>
                        <div class="table_inform_clr_rnd" style="margin-left:8px;background-color: #ffeedf;"></div>
                        <div class="table_inform_clr_name">Confirmed</div>
                        <div class="table_inform_clr_rnd" style="margin-left:8px;background-color: #d6e4c7;"></div>
                        <div class="table_inform_clr_name">Pick Up</div>
                    </div>
                    <div class="togle_section" style="display:none">
                        <i class="fa fa-th" aria-hidden="true"></i>
                    </div>
                </div>

                <div class="order_min_section show_hide" id="append_div">
                    @if(count($all_orders)>0)
                        @foreach($all_orders as $orders)
                            @if($orders->current_status == 'P')
                                <div class="col-md-3 col-sm-6 col-xs-12 @if(confirmationdiff($orders->ordertime,isset($orders->on_hold_release_time)?$orders->on_hold_release_time:0) == 'Y')delayed_confirmation @endif @if($orders->on_hold == 'Y') on_hold  @endif">
                                    <div class="current_order_box new_order_1 @if($orders->on_hold == 'Y') on_hold  @endif " onclick="return view_pop_up('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}','{{$orders->on_hold}}','{{$orders->latitude}}','{{$orders->longitude}}','{{$orders->no_contact_del}}');">
                                        <div class="current_order_number">
                                            {{$orders->order_number}}
                                        </div>
                                        <div class="current_order_time">
                                            <strong >{{$orders->time}}</strong>
                                        </div>
                                        <div class="current_order_restaurant">{{$orders->rest_name}}</div>
                                        <div class="current_order_user_detail">
                                            <div class="current_order_user_name">
                                                {{$orders->name}} -- {{$orders->mobile}}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @elseif($orders->current_status == 'C')
                                <div class="col-md-3 col-sm-6 col-xs-12 @if($orders->on_hold == 'Y') on_hold  @endif @if(timediff($orders->time) == 'Y')delayed_order @endif">
                                    <div class="current_order_box near_delivery_1" onclick="return view_pop_up('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}','','{{$orders->latitude}}',,'{{$orders->longitude}}','{{$orders->no_contact_del}}');">

                                        <div class="current_order_number">
                                            {{$orders->order_number}}
                                        </div>
                                        <div class="current_order_time">
                                            <strong >{{$orders->time}}</strong>
                                        </div>
                                        <div class="current_order_restaurant">{{$orders->rest_name}}</div>
                                        <div class="current_order_user_detail">
                                            <div class="current_order_user_name">
                                                {{$orders->name}} -- {{$orders->mobile}}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @elseif($orders->current_status == 'OP')
                                <div class="col-md-3 col-sm-6 col-xs-12 @if(timediff($orders->time) == 'Y')delayed_order @endif">
                                    <div class="current_order_box new_pick_up" onclick="return view_pop_up('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}','{{$orders->on_hold}}','{{$orders->latitude}}',,'{{$orders->longitude}}','{{$orders->no_contact_del}}');">

                                        <div class="current_order_number">
                                            {{$orders->order_number}}
                                        </div>
                                        <div class="current_order_time">
                                            <strong >{{$orders->time}}</strong>
                                        </div>
                                        <div class="current_order_restaurant">{{$orders->rest_name}}</div>
                                        <div class="current_order_user_detail">
                                            <div class="current_order_user_name">
                                                {{$orders->name}} -- {{$orders->mobile}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($orders->current_status == 'D')
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="current_order_box new_deliverd" onclick="return view_pop_up('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}','{{$orders->on_hold}}','{{$orders->latitude}}',,'{{$orders->longitude}}','{{$orders->no_contact_del}}');">

                                        <div class="current_order_number">
                                            {{$orders->order_number}}
                                        </div>
                                        <div class="current_order_time">
                                            <strong >{{$orders->time}}</strong>
                                        </div>
                                        <div class="current_order_restaurant">{{$orders->rest_name}}</div>
                                        <div class="current_order_user_detail">
                                            <div class="current_order_user_name">
                                                {{$orders->name}} -- {{$orders->mobile}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($orders->current_status == 'CA')
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="current_order_box new_cancelled" onclick="return view_pop_up('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}','{{$orders->on_hold}}','{{$orders->latitude}}',,'{{$orders->longitude}}','{{$orders->no_contact_del}}');">

                                        <div class="current_order_number">
                                            {{$orders->order_number}}
                                        </div>
                                        <div class="current_order_time">
                                            <strong >{{$orders->time}}</strong>
                                        </div>
                                        <div class="current_order_restaurant">{{$orders->rest_name}}</div>
                                        <div class="current_order_user_detail">
                                            <div class="current_order_user_name">
                                                {{$orders->name}} -- {{$orders->mobile}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="order_min_section">
                    <div class="table_section_scroll" id="append_table">
                        <table id="example1" class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="min-width:30px">Sl No</th>
                                <th style="min-width:80px">Order No</th>
                                <th style="min-width:60px">Time</th>
                                <th style="min-width:140px">Restaurant/Shop </th>
                                <th style="min-width:140px">Customer Name</th>
                                <th style="min-width:140px">Staff Name</th>
                                <th style="min-width:100px">Staff Mobile</th>
                                <!--<th style="min-width:100px">Mobile</th>-->
                                <th style="min-width:70px">Paymode</th>
                                <th style="min-width:70px">Status</th>
                                <th style="min-width:30px">View</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($all_orders)>0)
                                <?php $i=0; ?>
                                @foreach($all_orders as $orders)
                                    <?php $i++; ?>
                                 <?php 
                            $inpg="";
                            if($orders->assign_status == 'Inprogress')
                            {
                               $inpg=" <span style='color:Red'> (I)<span>"; 
                            }
                                ?>
                                    @if($orders->current_status == 'P')
                                        <tr role="row" class="@if($orders->rest_confirmed == 'Y') confirm_place @else new_order_1 @endif @if($orders->on_hold == 'Y') on_hold @endif @if(confirmationdiff($orders->ordertime,isset($orders->on_hold_release_time)?$orders->on_hold_release_time:0) == 'Y' && $orders->rest_confirmed == 'N')delayed_confirmation @endif">
                                            <td style="min-width:30px;"><?=$i?></td>
                                            <td style="min-width:80px;"><strong style="color: #227b73"><?=$orders->order_number?></strong></td>
                                            <td style="min-width:90px;"><?=$orders->time.$inpg?></td>
                                            <td style="min-width:140px;"><strong style="color: #77541f"><?=$orders->rest_name?></strong></td>
                                            <td style="min-width:140px;"><?=$orders->name?></td>
                                            <td style="min-width:140px;"></td>
                                            <td style="min-width:140px;"></td>
                                            <td style="min-width:70px;">@if(isset($orders->payment_method) && $orders->payment_method == 'COD') COD  @elseif(isset($orders->payment_method) && $orders->payment_method != 'COD') ONLINE  @endif</td>
                                            @if($orders->readytopick == "Y")
                                            <td style="min-width:70px;"><font color='#f5351b'>Placed(P)</font></td>
                                            @else
                                            <td style="min-width:70px;">Placed</td>
                                            @endif
                                            <td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}','{{$orders->on_hold}}','{{$orders->latitude}}','{{$orders->longitude}}','{{$orders->no_contact_del}}'),view_pop_up_address('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}');"><i class="fa fa-cog"></i></a></td>
                                        </tr>
                                    @elseif($orders->current_status == 'C')
                                        <tr role="row" class="near_delivery_1 @if($orders->on_hold == 'Y') on_hold  @endif @if(timediff($orders->time) == 'Y')delayed_order @endif">
                                            <td style="min-width:30px;"><?=$i?></td>
                                            <td style="min-width:80px;"><strong style="color: #227b73"><?=$orders->order_number?></strong></td>
                                            <td style="min-width:90px;"><?=$orders->time.$inpg?></td>
                                            <td style="min-width:140px;"><strong style="color: #77541f"><?=$orders->rest_name?></strong></td>
                                            <td style="min-width:140px;"><?=$orders->name?></td>
                                            <td style="min-width:140px;"><?=$orders->staffname?></td>
                                            <td style="min-width:140px;"><?=$orders->staffmobile?></td>
                                            <td style="min-width:70px;">@if(isset($orders->payment_method) && $orders->payment_method == 'COD') COD  @elseif(isset($orders->payment_method) && $orders->payment_method != 'COD') ONLINE  @endif</td>
                                            @if($orders->readytopick == "Y")
                                            <td style="min-width:70px;"><font color='#f5351b'>Confirmed(P)</font></td>
                                            @else
                                            <td style="min-width:70px;">Confirmed</td>
                                            @endif
                                            <td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}','{{$orders->on_hold}}','{{$orders->latitude}}','{{$orders->longitude}}','{{$orders->no_contact_del}}'),view_pop_up_address('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}');"><i class="fa fa-cog"></i></a></td>
                                        </tr>
                                    @elseif($orders->current_status == 'SA')
                                        <tr role="row" class="new_assigned">
                                            <td style="min-width:30px;"><?=$i?></td>
                                            <td style="min-width:80px;"><strong style="color: #227b73"><?=$orders->order_number?></strong></td>
                                            <td style="min-width:90px;"><?=$orders->time.$inpg?></td>
                                            <td style="min-width:140px;"><strong style="color: #77541f"><?=$orders->rest_name?></strong></td>
                                            <td style="min-width:140px;"><?=$orders->name?></td>
                                            <td style="min-width:100px;"></td>
                                            <td style="min-width:100px;"></td>
                                            <td style="min-width:70px;">@if(isset($orders->payment_method) && $orders->payment_method == 'COD') COD  @elseif(isset($orders->payment_method) && $orders->payment_method != 'COD') ONLINE  @endif</td>
                                            <td style="min-width:70px;">Staff Assigned</td>
                                            <td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}','{{$orders->on_hold}}','{{$orders->latitude}}','{{$orders->longitude}}','{{$orders->no_contact_del}}'),view_pop_up_address('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}');"><i class="fa fa-cog"></i></a></td>
                                        </tr>
                                    @elseif($orders->current_status == 'OP')
                                        <tr role="row" class="new_pick_up @if(timediff($orders->time) == 'Y') delayed_order @endif">
                                            <td style="min-width:30px;"><?=$i?></td>
                                            <td style="min-width:80px;"><strong style="color: #227b73"><?=$orders->order_number?></strong></td>
                                            <td style="min-width:90px;"><?=$orders->time.$inpg?></td>
                                            <td style="min-width:140px;"><strong style="color: #77541f"><?=$orders->rest_name?></strong></td>
                                            <td style="min-width:140px;"><?=$orders->name?></td>
                                            <td style="min-width:140px;"><?=$orders->staffname?></td>
                                            <td style="min-width:140px;"><?=$orders->staffmobile?></td>
                                            <td style="min-width:70px;">@if(isset($orders->payment_method) && $orders->payment_method == 'COD') COD  @elseif(isset($orders->payment_method) && $orders->payment_method != 'COD') ONLINE  @endif</td>
                                            <td style="min-width:70px;">Picked</td>
                                            <td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}','{{$orders->on_hold}}','{{$orders->latitude}}','{{$orders->longitude}}','{{$orders->no_contact_del}}'),view_pop_up_address('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}');"><i class="fa fa-cog"></i></a></td>
                                        </tr>
                                    @elseif($orders->current_status == 'D')
                                        <tr role="row" class="new_deliverd">
                                            <td style="min-width:30px;"><?=$i?></td>
                                            <td style="min-width:80px;"><strong style="color: #227b73"><?=$orders->order_number?></strong></td>
                                            <td style="min-width:90px;"><?=$orders->time.$inpg?></td>
                                            <td style="min-width:140px;"><strong style="color: #77541f"><?=$orders->rest_name?></strong></td>
                                            <td style="min-width:140px;"><?=$orders->name?></td>
                                            <td style="min-width:140px;"><?=$orders->staffname?></td>
                                            <td style="min-width:140px;"><?=$orders->staffmobile?></td>
                                            <td style="min-width:70px;">@if(isset($orders->payment_method) && $orders->payment_method == 'COD') COD  @elseif(isset($orders->payment_method) && $orders->payment_method != 'COD') ONLINE  @endif</td>
                                            <td style="min-width:70px;">Delivered</td>
                                            <td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}','{{$orders->on_hold}}','{{$orders->latitude}}','{{$orders->longitude}}','{{$orders->no_contact_del}}'),view_pop_up_address('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}');"><i class="fa fa-cog"></i></a></td>
                                        </tr>
                                    @elseif($orders->current_status == 'CA')
                                        <tr role="row" class="new_cancelled">
                                            <td style="min-width:30px;"><?=$i?></td>
                                            <td style="min-width:80px;"><strong style="color: #227b73"><?=$orders->order_number?></strong></td>
                                            <td style="min-width:90px;"><?=$orders->time.$inpg?></td>
                                            <td style="min-width:140px;"><strong style="color: #77541f"><?=$orders->rest_name?></strong></td>
                                            <td style="min-width:140px;"><?=$orders->name?></td>
                                            <td style="min-width:100px;"></td>
                                            <td style="min-width:100px;"></td>
                                            <td style="min-width:70px;">@if(isset($orders->payment_method) && $orders->payment_method == 'COD') COD  @elseif(isset($orders->payment_method) && $orders->payment_method != 'COD') ONLINE  @endif</td>
                                            <td style="min-width:70px;">Cancelled</td>
                                            <td style="min-width:30px;"><a class="btn button_table vie_odr_btn" onclick="return view_pop_up('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}','{{$orders->on_hold}}','{{$orders->latitude}}','{{$orders->longitude}}','{{$orders->no_contact_del}}'),view_pop_up_address('{{$orders->order_number}}','{{$orders->rest_id}}','{{$orders->customer_id}}');"><i class="fa fa-cog"></i></a></td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="timing_popup_cc view_order_popup" id="view_order_popupview_order_popup" style="display: none;">
        <div class="timing_popup" style="width:1020px;top:2px;">
            <div class="timing_popup_head" style="margin-bottom: -15px;">
                <span id="cs_order_num" name ="cs_order_num" >#ODR123654</span>
                <input type='hidden' id="rest_id" name="rest_id">
                <div class="timing_popup_cls" id="timing_popup_cls"><img src="public/assets/images/cancel.png"></div>
            </div>
            <div class="timing_popup_contant">
                <div class="restaurant_more_detail_row" style="margin-bottom:-18px">
                    <div class="left_contant_popup_order">
                        <div class="current_order_user_detail">
                            <div class="current_order_user_name hotel_nm" id="hotel_name">
                                <input type="hidden" id="staff_id" name="staff_id" value="{{ Session::get('staffid')}}"/>
                            </div>
                            <div class="current_order_user_name_address" >
                                <span id="hotel_adrs"></span> <br>
                                <span id="hotel_phone"></span>
                            </div>
                        </div>

                        <div class="current_order_user_detail" style="max-height: 80px;overflow: auto;">
                            <div class="current_order_user_name"  style="color: red">
                                <span id="cstmr_name"></span>  --  <span id="cstmr_phone"></span>
                            </div>
                            <div class="current_order_user_name_address" >
                                <span id="cstmr_address_1"></span>,<br>
                                <span id="cstmr_address_2"></span>,<br>
                                <span id="cstmr_landmark"></span>
                            </div>
                            
                        </div>
                        
  <span class="Location_btn"  onclick="load_location()" id="locationview" style="display: block"><p style="margin-bottom: 0;">Locate Address</p></span>
  
                        <div class="current_order_delivery_tm">
                          
                            <input type="text" hidden=""id="cust_latitude">
                           <input type="text" hidden=""id="cust_longitude"> 
                            
                            <span class="assgnto" style="padding-left:20px;">Assign to</span>
                            <span class="add_staff_ordr_dtl_pop staffname_cls assgnto" onclick="staff_list_assign('block','Ad')" ><p id="assgn_staff_name">Select</p></span>
                            <span class="add_staff_ordr_dtl_pop" onclick="staff_list_assign('block','Ch')" id="change_staff_name" style="display: none"><p >ChangeStaff</p></span>
                           
                            <div class="addmn_ordr_dtl_pop" id='add_menu' style="display: none" onclick="return addmenu();">Add Menu</div>
                        </div>
                        <div class="timing_popup_contant_tabl" id="append_division"> <!--id="append_division"-->
                            <table class="timing_sel_popop_tbl">
                                <thead>
                                <tr>
                                    <th style="min-width:30px"></th>
                                    <th style="min-width:130px;">Items</th>
                                    <th style="min-width:50px">Qty</th>
                                    <th style="min-width:70px">Rate</th>
                                    <th style="min-width:70px">Amount</th>
                                    <th style="min-width:60px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td style="min-width:30px">1</td>
                                    <td style="max-width:135px">
                                        <div class="restaurant_more_detail_text">
                                            <input type="text" placeholder="Enter Menu Name">
                                        </div>
                                    </td>
                                    <td style="max-width:55px"><div class="restaurant_more_detail_text">
                                            <input type="text" placeholder="QTY">
                                        </div></td>
                                    <td style="min-width:70px">120</td>
                                    <td style="min-width:70px">240</td>
                                    <td style="min-width:60px">
                                        <a class="btn button_table"><i class="fa fa-save"></i></a>
                                        <a class="btn button_table"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td col-span="6">
                                        <div class="restaurant_more_detail_text">
                                            <input type="text" onclick="this.removeAttribute('readonly');" autofocus placeholder="Preference"  class="form-control" >
                                        </div></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="mng_odr_pop_right_pymnt_sec" id="payment_type">

                    </div>
                    <div class="mng_odr_pop_right_pymnt_sec">

                        <div class="mng_order_pop_right_pay_mod_cont_sec">
                            <div class="mng_odr_pop_right_pymnt_head" style="text-align: center;">Payment Mode</div>

                            <div class="mng_odr_pop_right_pymnt_row">
                                <div class="mng_odr_pop_right_pymnt_txt">Mode</div>
                                <div class="mng_odr_pop_right_pymnt_txt_ser" id="payment_mode">COD</div>
                            </div>
                            <div class="mng_odr_pop_right_pymnt_row" id="razp_od">
                                <div class="mng_odr_pop_right_pymnt_txt">Razorpay Order id - </div>
                                <div class="mng_odr_pop_right_pymnt_txt_ser" id="razorpay_orderid">-</div>
                            </div>
                            <div class="mng_odr_pop_right_pymnt_row" id="razp_pay">
                                <div class="mng_odr_pop_right_pymnt_txt">Razorpay Payment id - </div>
                                <div class="mng_odr_pop_right_pymnt_txt_ser" id="razorpay_paymentid">-</div>
                            </div>
                            <div class="mng_odr_pop_right_pymnt_row" id="razp_method">
                                <div class="mng_odr_pop_right_pymnt_txt">Razorpay Method - </div>
                                <div class="mng_odr_pop_right_pymnt_txt_ser" id="razorpay_method">-</div>
                            </div>
                            <div class="mng_odr_pop_right_pymnt_row" id="razp_refund">
                                <div class="mng_odr_pop_right_pymnt_txt">Razorpay RefundID - </div>
                                <div class="mng_odr_pop_right_pymnt_txt_ser" id="razorpay_rerund">-</div>
                            </div>

                        </div>

                        <div class="mng_order_pop_right_token_sec">
                            <div class="mng_order_pop_right_token_name">Delivery Staff Note</div>
                            <textarea class="mng_order_pop_right_token_textarea" placeholder="" id="assigned_note"></textarea>
                        </div>
<div id="nocontact" style="color: red">  </div> 
                    </div>

                    <div class="col-sm-12 no-padding" style="background-color:#fff;margin-top:2px;">
                        <input type="hidden" id="customer_id" />
                        <input type="hidden" id="change_mode" />
                        <a id="ord_cancel_btn" class="staff-add-pop-btn staff-add-pop-btn-new cancel_btn" style="float: left;" onclick="cancel_reason()" >Cancel Order</a>
                        <div class="cancel_reason_sec" id="can_reson" style="display:none;width: 85%;">
                   <span style="width:50%;height:auto;float:left;position:relative">
                     <input type="text" id="user_can_reason" name="user_can_reason" autocomplete="off" class="cancel_reason_sec_txtbx" onkeyup="get_reason_list()"/>
                     <div class="cancel_reasons_view" id="reason_suggestions" style="background-color:transparent;box-shadow:none">
                     </div>
                     <a href="#" onclick="cancelorder_auth()"><span class="cancel_reason_sec_txtbx_sub_btn">Cancel Order</span></a>
                    </span>
                    <span style="width:50%;height:auto;float:left;padding: 7px;">
                     <span id="refundmsg" style="display:none;color:red;margin-top:2px;"><b>Automatic Refund Initiate NOT POSSIBLE</b></span>
                    </span>
                        </div>
                        <input type="hidden" id="refund_status" value="Y"/>

                        <a class="staff-add-pop-btn staff-add-pop-btn-new rels_btn" id="rels_btn" style="float: left;margin-left: 40%;width:15%" onclick="release_hold()">RELEASE HOLD</a>

                        <a class="staff-add-pop-btn staff-add-pop-btn-new confirm_btn" id="ordr_confm_btn" style="float: right;" onclick="confirm_order()">CONFIRM</a>

                    </div>

                </div>
                <input type="hidden" id="on_hold_val" />                
                
                <div class="add_staff_popup_inmng_odr" style="display:none" id="assign_staffs">
                    <div class="timing_popup_head" style="margin-bottom: -15px;">
                        <span id="cs_order_num">ASSIGN STAFF</span>
                        <a href="#"><div class="timing_popup_cls staffclose" onclick="staff_list_assign('none','F')"><img src="public/assets/images/cancel.png"></div> </a>
                    </div>
                    <input type="hidden" id="ass_staff_id" />
                    <input type="hidden" id="ass_staff_name" />
                    <input type="hidden" id="assgn_staff_mobile" />
                    <div class="assign_staff_sec_cnt" style="position:relative">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Slno</th>
<!--                                    <th>Distance</th>-->
                                    <th width="10%">Name</th>
                                    <th>Pnd</th>
                                    <th>Number</th>
                                    <th>Crdt Remaining</th>
                                   <th>Current / Last Area</th>
                                </tr>
                                </thead>
                                <tbody id="asn_staff_table">
                                <div class="loader_staff_sec" id='loadingmessage' style="display:none" >
                                    <img src='public/assets/images/main-loader.gif'/>
                                </div>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-12">

                        <a class="staff-add-pop-btn staff-add-pop-btn-new confirmbtn_load" style="float: right;display: none" onclick="confirm_assign_satff()">CONFIRM</a>

                    </div>

                </div>


            </div>

            <div class="authorization_popup_section authpopup" id="authorization" style="display: none">
                <div class="authorization_popup">
                    <div class="popup_loader" id="confirm_order_gif" style="display: none"><img src="public/assets/images/new_loader.gif"></div>
                    <div class="authorization_popup_head">
                        Authorize
                        <div class="authorization_popup_cls"><img src="public/assets/images/cancel.png"></div>
                    </div>
                    <div class="authorization_popup_contant">
                        <div class="authorize_sec">
                            <p>Enter your code</p>
                            <input type="password" class="authorize_sec_input" maxlength = "4" minlength = "4" id='staff_code' name='staff_code' placeholder="Enter your code" autofocus onkeypress="submitevent(event);">
                        </div>
                        <div class="col-sm-12 no-padding">
                            <a class="staff-add-pop-btn staff-add-pop-btn-new" style="float: right;height:35px;line-height:35px;margin-top:10px;padding-top:0" onclick="return save_orderpopup();">Submit</a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="authorization_popup_section authpopup" id="authorizationcancel" style="display: none">
                <div class="authorization_popup">
                    <div class="popup_loader" id="cancel_order_gif" style="display: none"><img src="public/assets/images/new_loader.gif"></div>
                    <div class="authorization_popup_head">
                        Authorize
                        <div class="authorization_popup_cls"><img src="public/assets/images/cancel.png"></div>
                    </div>
                    <div class="authorization_popup_contant">
                        <div class="authorize_sec">
                            <p>Enter your code</p>
                            <input type="password" class="authorize_sec_input" maxlength = "4" minlength = "4" id='cancelstaff_code' name='cancelstaff_code' placeholder="Enter your code">
                        </div>
                        <div class="col-sm-12 no-padding">
                            <a class="staff-add-pop-btn staff-add-pop-btn-new" style="float: right;height:35px;line-height:35px;margin-top:10px;padding-top:0" onclick="return cancel_order();">Submit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="timing_popup_cc " id="maploadpop_up" style="display: none;">
        <div class="timing_popup" style="width:1020px;height:100%; ">
             <div class="timing_popup_head" style="margin-bottom: -15px;">
                <span id="cs_order_num" name ="cs_order_num" >Location</span>
                <input type='hidden' id="rest_id" name="rest_id">
                <div class="timing_popup_cls" id="timing_popup_clse_btn"><img src="public/assets/images/cancel.png"></div>
            </div>
            <div id="googleMap" style="width:100%;height:100%;"></div>

        <input type="hidden" id="la" name="la">
        <input type="hidden" id="lo" name="lo">

        <input type="text" id="latitude" name="latitude" hidden="" />
        <input type="text" id="longitude" name="longitude" hidden="" />   
        </div>  
    </div>
    
     
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZoizgDw3-h_w_SJ3IlNjBAcnsFuco8Bw&callback=init"></script>
<script type="text/javascript">
     $(document).ready(function()
    {
      // element.autocomplete = isGoogleChrome() ? 'disabled' :  'off';
    });
    
   function load_location()
        {
          //  $("#googleMap").html('');
         //$("#maploadpop_up").show();
           var latitude=($('#cust_latitude').val());
           var longitude=($('#cust_longitude').val());
           $('#latitude').val(latitude);
           $('#la').val(latitude);
           $('#longitude').val(longitude);
           $('#lo').val(longitude);
           //var mapDiv = document.getElementById('googleMap');
           
          // google.maps.event.addDomListener(mapDiv, 'click', initialize);
           window.open('order_mapload/' + latitude + '/' +longitude , '_blank');
        } 
  
   // A $( document ).ready() block.
  // document.addEventListener("DOMContentLoaded", function(){
   
   function initialize() {
   //console.log( "init!" );
 var latitude1=parseFloat(document.getElementById('latitude').value);
   var longitude1=parseFloat(document.getElementById('longitude').value);
   // var latitude1=($('#cust_latitude').val());
    //var longitude1=($('#cust_longitude').val());
   console.log( latitude1 );
    var latitude = parseFloat(document.getElementById('latitude').value);
    var longitude = parseFloat(document.getElementById('longitude').value);
    var zoom = 11;

    var LatLng = new google.maps.LatLng(parseFloat(document.getElementById('latitude').value), parseFloat(document.getElementById('longitude').value));

  var mapProp = {
    center: LatLng,
    zoom:12,
    mapTypeId:google.maps.MapTypeId.ROADMAP
  };
  var map=new google.maps.Map(document.getElementById("googleMap"), mapProp);
  var marker = new google.maps.Marker({
      position : {
          lat : parseFloat( latitude ),
          lng : parseFloat( longitude )
      },
      map: map,
      title: 'Drag Me!',
      draggable: false
    });
  google.maps.event.addListener(marker, 'dragend', function(event) {

      document.getElementById('la').value =parseFloat(latitude);// event.latLng.lat();
      document.getElementById('lo').value =parseFloat(longitude);// event.latLng.lng();



});
//window.location.reload();
 
}
 

/* var interval = setInterval(function() {
    if(document.readyState == 'complete') {
        clearInterval(interval);
        initialize();
         google.maps.event.addDomListener(window, 'load', initialize);
    }    
}, 100);*/
       // console.log( "ready!" );
//google.maps.event.addDomListener(window, 'load', initialize);
    //});


   
   </script>
    
    <style>
        .not-active{
            pointer-events: none;
        }
    </style>
@section('jquery')


    <script>
        setInterval(function(){
            change_filter();
            change_filter_2();
        }, 5000);

        $.fn.dataTable.ext.errMode = 'none';


        // append table data
        function change_filter()
        {
            var staff_id               =   $("#staff_id").val();
            var order_rest_filter      =   $('#order_rest_filter').val();
            var order_name_filter      =   $('#order_name_filter').val();
            var order_phone_filter     =   $('#order_phone_filter').val();
            var order_status_filter    =   $('#order_status_filter').val();
            var order_number_filter    =   $('#order_number_filter').val();
            var order_cat_filter       =   $('#order_cat_filter').val();
            var data = {"staff_id":staff_id,"order_rest_filter":order_rest_filter,"order_name_filter":order_name_filter,"order_phone_filter":order_phone_filter,"order_status_filter":order_status_filter,"order_number_filter":order_number_filter,"order_cat_filter":order_cat_filter};
            $('#append_table').html('');

            $.ajax({
                method: "get",
                url: "api/manage_order_filter_tables",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                    //alert(result)
                    $('#append_table').html(result);
//                    var t = $('#example2').DataTable({
//
//            "searching": false,
//
//            "lengthChange": false,
//
//        } );
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
        }

        //apeend div data
        function change_filter_2()
        {
            var staff_id               =   $("#staff_id").val();
            var order_rest_filter      =   $('#order_rest_filter').val();
            var order_name_filter      =   $('#order_name_filter').val();
            var order_phone_filter     =   $('#order_phone_filter').val();
            var order_status_filter    =   $('#order_status_filter').val();
            var order_number_filter    =   $('#order_number_filter').val();
            var order_cat_filter    =   $('#order_cat_filter').val();
            var data = {"order_rest_filter":order_rest_filter,"order_name_filter":order_name_filter,"staff_id":staff_id,"order_phone_filter":order_phone_filter,"order_status_filter":order_status_filter,"order_number_filter":order_number_filter,"order_cat_filter":order_cat_filter};
            $('#append_div').html('');

            $.ajax({
                method: "get",
                url: "api/manage_order_filter_div",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                   // alert(result);
                    $('#append_div').html(result);
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
        }

        function release_hold()
        {
            var order_number = $('#cs_order_num').html();
            swal({
                title: "",
                text: "Are you sure you want to Release hold?",
                type: "info",
                showCancelButton: true,
                cancelButtonClass: 'btn-white btn-md waves-effect',
                confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
                confirmButtonText: 'Release Hold',
                closeOnConfirm: false
            }, function (isConfirm)
            {
                if (isConfirm)
                {
                    var data = {"order_number":order_number};
                    $.ajax({
                        method: "get",
                        url: "api/update_releasehold",
                        data: data,
                        cache: false,
                        crossDomain: true,
                        async: false,
                        success: function (result)
                        {
                            $('.showSweetAlert').hide();
                            $('.sweet-overlay').hide();
                            if(result == 'success')
                            {
                                location.reload();
                                swal({

                                    title: "",
                                    text: "Hold Released",
                                    timer: 4000,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                            $("#errbox").text(jqXHR.responseText);
                        }
                    });                     }
            });

        }

        function view_pop_up(order_number,rest_id,customer_id,on_hold,latitude,longitude,contact)
        {
            $(".view_order_popup").show();
            $('#customer_id').val(customer_id);
            $("#on_hold_val").val(on_hold);
            $("#cust_latitude").val(latitude);
            $("#cust_longitude").val(longitude);
            if(contact=="Y")
            {
            $("#nocontact").html("No-Contact Delivery");
            }else
            {
             $("#nocontact").html("");
            }
            if(latitude=="0")
            {
            $(".Location_btn").css("display","none");
                }else
                {
$(".Location_btn").css("display","block");
                }
            if(on_hold == 'Y')
            {
                $('.confirm_btn').hide();
                $(".assgnto").hide();
                $('#add_menu').show();
                $('#change_staff_name').css('display','none');
                $('.rels_btn').show();
            }
            else
            {
                $('.confirm_btn').show();
                $(".assgnto").show();
                $('.rels_btn').hide();
            }
            var table = $('#example').DataTable();
            var data = {"order_number":order_number,"rest_id":rest_id};
            $('#append_division').html('');
            $.ajax({
                method: "get",
                url: "api/view_order_details_list",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType :'text',
                success: function (result)
                {

                    $('#append_division').html(result);
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#errbox").text(jqXHR.responseText);
                }
            });
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


        function save_orderpopup()
        {
            var order_number = $('#cs_order_num').html();
            var staff_code = $('#staff_code').val();
            var rest_id = $('#rest_id').val();
            var staff_id = $('#ass_staff_id').val();
            var staff_name = $('#assgn_staff_name').html();
            var staff_number = $('#assgn_staff_mobile').val();
            var optn_mode = $("#change_mode").val();
            var assigned_note = $("#assigned_note").val().replace(/[&\/\\().'"<>{}]/g,'_');
            $("#confirm_order_gif").css("display","block");
            var data = {"order_number":order_number,"assigned_note":assigned_note,"customer_id":$("#customer_id").val(),"staff": $("#ass_staff_name").val(),"rest_id":rest_id,"staff_code":staff_code,"staff_id":staff_id,"staff_name":staff_name,"staff_number":staff_number,"optn_mode":optn_mode,"hotel_name" : $('#hotel_name').html()};
            $.ajax({
                method: "get",
                url: "api/confirm_order",
                data: data,
                dataType :'text',
                success: function (result)
                {//alert("conf")
                    $("#confirm_order_gif").css("display","none");
                    var json_x= JSON.parse(result);
                    if((json_x.msg)=='success')
                    {
                        /* $.ajax({
                         method: "post",
                         url: "api/order_notification",
                         data: {"order_number" :order_number,"assigned_note":assigned_note,"customer_id":$("#customer_id").val(),"staff": $("#ass_staff_name").val(),"hotel_name" : $('#hotel_name').html()},
                         cache: false,
                         crossDomain: true,
                         async: false,
                         success: function (result)
                         {
                         alert(JSON.stringify(result));
                         },
                         error: function (jqXHR, textStatus, errorThrown)
                         {
                         $("#errbox").text(jqXHR.responseText);
                         }
                         });*/
                        location.reload();
                        swal({

                            title: "",
                            text: "Order Confirmed",
                            timer: 4000,
                            showConfirmButton: false
                        });

                    }
                    else if((json_x.msg)=='Not Exist')
                    {

                        swal({

                            title: "",
                            text: "No Permission",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(jqXHR.responseText);
                    $("#errbox").text(jqXHR.responseText);
                }
            });
        }

        function cancel_reason()
        {
            var order_number =  $('#cs_order_num').html();
            $.ajax({
                method: "post",
                data: {"order_number": order_number},
                url: "api/check_paymentstatus",
                cache: false,
                crossDomain: true,
                async: false,
                success: function (result)
                {
                    var msg = result['msg'];
                    if(msg.toUpperCase() == 'EXIST')
                    {
                        var payment_id = result['data'];
                        if(payment_id.toUpperCase() == 'NO_DATA')
                        {
                            $("#refundmsg").show();
                            $("#refund_status").val('N');
                        }
                        else
                        {
                            $("#refundmsg").hide();
                            $("#refund_status").val('Y');
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    // $("#errbox").text(jqXHR.responseText);
                }
            });
            $("#can_reson").css("display","block");
            $("#ord_cancel_btn").css("display","none");
        }
        $('#cancelstaff_code').on("keypress", function(e) {
            if (e.keyCode == 13) {
                cancel_order();
            }
        });
        function cancel_order()
        {
            var order_number        = $('#cs_order_num').html();
            var razorpay_paymentid  = $('#razorpay_paymentid').html();
            var staff_code = $('#cancelstaff_code').val();
            $("#cancel_order_gif").css("display","block");
            var data = {"refund_status" :  $("#refund_status").val(),"order_number":order_number,"customer_id":$("#customer_id").val(),"staff": $("#ass_staff_name").val(),"hotel_name" : $('#hotel_name').html(),"staff_code":staff_code,'cancel_reason':$("#user_can_reason").val(),"razorpay_paymentid":razorpay_paymentid};
            $.ajax({
                method: "get",
                url: "api/cancel_order",
                data: data,
                dataType :'text',
                success: function (result)
                {
                    $("#cancel_order_gif").css("display","none");
                    var json_x= JSON.parse(result);
                    if((json_x.msg)=='success')
                    {
                        location.reload();
                        swal({

                            title: "",
                            text: "Order Cancelled",
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
                    else if((json_x.msg)=='Not Exist')
                    {

                        swal({

                            title: "",
                            text: "No Permission",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
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
                        if($("#on_hold_val").val() == 'N')
                        {
                            $('#change_staff_name').css('display','inline-block');
                        }
                        else
                        {
                            $('#change_staff_name').css('display','none');
                        }
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
                            if($("#on_hold_val").val() == 'N')
                            {
                                $('#change_staff_name').css('display','inline-block');
                            }
                            else
                            {
                                $('#change_staff_name').css('display','none');
                            }

                            $('#ord_cancel_btn').css('display','block');
                        }
                    }else{
                        $('#assgn_staff_name').html('Select');
                        $('#change_staff_name').css('display','none');
                        $('.staffname_cls').removeClass('not-active');
//                         $('#add_menu').show();
                        $('.cancel_btn').show();
                        if($("#on_hold_val").val() == 'N'){
                            $('.confirm_btn').show();
                            $(".assgnto").show();
                            $('.rels_btn').hide();
                        }
                        else
                        {
                            $('.confirm_btn').hide();
                            $(".assgnto").hide();
                            $('.rels_btn').show();
                        }
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
            if(qty>=1){

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
            else{
                swal({
                    title: "",
                    text: "Qty cannot be 0",
                    timer: 3000,
                    showConfirmButton: false
                });
                $("#menu_qty").focus();
            }
        }

        function staff_list_assign(optn, type)
        {
            
            var res_id = $('#rest_id').val();
            var staffid = $("#staff_id").val();
            $('#assign_staffs').css('display',optn);
            $('#asn_staff_table').html('');
             $('#loadingmessage').css("display", optn);
             $('.staffclose').css("display", "none");
            if(optn =='block') {
                $.ajax({
                    method: "get",
                    data :{"staffid" :staffid,"res_id":res_id},
                    url: "api/assign_staff_list",
                    cache: false,
                    crossDomain: true,
                    async: true,
                    success: function (result)
                    {
                        $('#asn_staff_table').html(result);
                        $('#loadingmessage').css("display", "none");
                        $('.confirmbtn_load').css("display", "block");
                         $('.staffclose').css("display", "block");
                        //  $('#asn_staff_table').html(result['append']);
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
        function selct_staf(fldid,name,id,mobile)
        {
            $('.assign_check').prop('checked',false);
            $('#test' +fldid).prop('checked',true);
            $('#ass_staff_id').val(id);
            $('#ass_staff_name').val(name);
            $('#assgn_staff_mobile').val(mobile);
        }

        function confirm_assign_satff()
        {
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
            else
            {
                swal({
                    title: "",
                    text: "Select Staff",
                    timer: 2000,
                    showConfirmButton: false
                });
            }
            return true;
        }

        function confirm_order()
        {
            var del_staff = $('#ass_staff_name').val();
            if(del_staff != '')
            {
                $('#authorization').css('display','block');
            }
            else
            {
                swal({

                    title: "",
                    text: "Select Delivery Staff",
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
        function cancelorder_auth()
        {
            if($("#user_can_reason").val()=='')
            {
                swal({
                    title             : "",
                    text              : "Please Enter Reason For Cancellation",
                    timer             : 2000,
                    showConfirmButton : false
                });
                $("#user_can_reason").focus()
            }
            else
            {
                $('#authorizationcancel').css('display','block');
            }
        }
        function get_reason_list()
        {
            $("#reason_suggestions").html("");
            //$("#user_can_reason").val("");
            $("#reason_suggestions").hide();
            var reason = $("#user_can_reason").val();
            if(reason.length>=3){
                $.ajax({
                    method: "get",
                    url: "api/autocomplete_can_reason",
                    data: {"reason": reason},
                    success: function (result)
                    {
                        $("#reason_suggestions").show();
                        $("#reason_suggestions").html(result);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#errbox").text(jqXHR.responseText);
                    }
                });
            }

        }
        function selectreasonexist(reason)
        {
            $("#reason_suggestions").hide();
            $("#user_can_reason").val(reason);
        }
    </script>


    <script>

$("#timing_popup_clse_btn").click(function()
        {
            $("#maploadpop_up").hide();
            $("#googleMap").html('');
        });
        $(".current_order_box").click(function()
        {
            $(".view_order_popup").show();
        });
        $(".vie_odr_btn").click(function()
        {
            $(".view_order_popup").show();
        });
        $("#timing_popup_cls").click(function()
        {
            $(".view_order_popup").hide();
        });
        $(".authorization_popup_cls").click(function()
        {
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
            final_rate = Math.round(final_rate * 100) / 100
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
            else
            {
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
       
    </script>
    <script>
        /*  setTimeout(function(){
         $('#order_rest_filter').val(' ');
         }, 15);*/
    </script>

@stop
@endsection