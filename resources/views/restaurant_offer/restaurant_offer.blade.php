@extends('layouts.app')
@section('title','Potafo - Restaurant Offers')
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
    .mce-notification-error{ display: none;}
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
    .main_inner_class_track{margin-right: 1.5%;}
    .main_inner_class_track .group{margin-bottom: 0}
    .main_inner_class_track .form-control{resize: none}
    .popover {width: 180px;height: 120px;}.popover img{width:100%}

</style>
<link href="{{ asset('public/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<script src="{{asset('public/assets/admin/script/restaurant_offer.js') }}" type="text/javascript"></script>
<script src="{{asset('public/assets/script/common.js') }}" type="text/javascript"></script>
<link href="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('public/assets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
<script src="{{ asset('public/assets/pages/jquery.sweet-alert.init.js') }}"></script>
<script src="{{ asset('public/assets/admin/plugins/tinymce/js/tinymce/jquery.tinymce.min.js') }}"></script>
<script src="{{ asset('public/assets/admin/plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('public/assets/admin/plugins/tinymce/js/tinymce/tinymce.js') }}"></script>
<script src="{{asset('public/assets/js/index.js') }}"></script>
<div class="col-sm-12">
        <div class="col-sm-12">
                <ol class="breadcrumb">
						<li>
							<a href="{{ url('index') }}">Dashboard</a>
						</li>
						<li>
							<a href="{{ url('manage_restaurant') }}">{{$restaurant_name[0]->name}}</a>
						</li>
						<li class="active ms-hover">
							Restaurant Offers
						</li>
					</ol>
				</div>
        <div class="card-box table-responsive" style="padding: 8px 10px;">
              <div class="box master-add-field-box" >
             <div class="col-md-6 no-pad-left">
                <h3>Restaurant Offers</h3>
            </div> 
                  
             <div class="col-md-1 no-pad-left pull-right">
                <div class="table-filter" style="margin-top: 4px;">
                  <div class="table-filter-cc">
                    <a style="cursor:pointer" class="add_offer_btn_new"> <button type="submit" style="margin-left:0" class="on-default followups-popup-btn btn btn-primary">Add Offer</button></a>
                </div>
                 </div>
            </div>
                  
            </div>
            
           <div class="table_offer_list_cc">
               <div class="row">
                   
            <div class="col-xs-12">
            <div class="table_section_scroll">  
                <h4>Bill Offer</h4>
            <div class="table-responsive">
                  <table class="table m-0 table table-striped" id="example1">
                       <thead>
						<tr>
							<th style="min-width:50px;">SlNo</th>
                                                        <th style="min-width:125px;">Offer Name</th>
							<th style="min-width:100px;">Offer %</th>
                                                        <th style="min-width:100px;">Amt Abv</th>
							<th style="min-width:100px;">Max Amt</th>
							<th style="min-width:100px;">Valid Frm</th>
                                                        <th style="min-width:100px;">Valid To</th>
							<th style="min-width:80px;">Status</th>
							<th style="min-width:80px;">Image</th>
							<th style="min-width:80px;">Action</th>
							</tr>
						</thead>
                      <tbody>
                       
                            
                    @if(isset($details_bill))
                    @if(count($details_bill)>0)
                    @foreach($details_bill as $key=>$item)  
                    <tr>
                    <td>{{ $key+1 }}</td>
                    <td>@if(isset($item->offer_name)) {{ title_case($item->offer_name) }}@endif</td>
                    <td>@if(isset($item->offer_percent)) {{ $item->offer_percent }}@endif</td>
                    <td>@if(isset($item->amount_above)) {{ $item->amount_above }}@endif</td>
                    <td>@if(isset($item->max_amount)) {{ $item->max_amount }}@endif</td>
                    <td>@if(isset($item->valid_from)) {{ $item->valid_from }}@endif</td>
                    <td>@if(isset($item->valid_to)) {{ $item->valid_to }}@endif</td>
                    <td>
                       <div class="status_chck{{ $item->rest_id}},{{ $item->sl_no}}">
                            <div class="onoffswitch">
                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch{{$item->rest_id}},{{$item->sl_no}}" @if( $item->active == 'Y') checked @endif>
                                    <label class="onoffswitch-label" for="myonoffswitch{{$item->rest_id}},{{$item->sl_no}}">
                                        <span class="onoffswitch-inner" onclick="return  statuschange('{{$item->rest_id}}','{{$item->sl_no}}','{{$item->active}}','B')"></span>
                                        <span class="onoffswitch-switch" onclick="return  statuschange('{{$item->rest_id}}','{{$item->sl_no}}','{{$item->active}}','B')"></span>
                                    </label>
                            </div>
                       </div>
                    </td>
                    <td><a rel="popover" data-img="{{ $siteUrl.$item->image }}" style="text-decoration: underline;"  data-original-title="" href="#">View</a></td> 
                    <td> 
                        <a href="#" onclick="return editbill('{{ $item->rest_id }}','{{ $item->sl_no }}','{{$item->amount_above}}','{{$item->offer_name}}','{{$item->offer_percent}}','{{ $item->max_amount }}','{{ $item->valid_from }}','{{ $item->valid_to }}','{{ $item->image }}','{{ $item->description }}','{{ $siteUrl }}','{{ $item->type }}')" class="table-action-btn button_table edit_offer_btn_new edit_bill"><i class="md md-edit"></i></a>
                        <a href="#" onclick="return delete_offer('{{ $item->rest_id }}','{{ $item->sl_no }}')" class="table-action-btn button_table "><i class="fa fa-trash"></i></a>
                    </td> 
                    </tr>
                    @endforeach
                    @endif
                    @endif     

                      </tbody>
                </table>
             </div>
            </div>
            </div>
                   
                   <div class="col-xs-12">
            <div class="table_section_scroll">  
                <h4>Item Offer - Pack</h4>
            <div class="table-responsive">
                  <table class="table m-0 table table-striped" id="example1">
                       <thead>
			    <tr>
				<th style="min-width:50px;">#</th>
                                <th style="min-width:125px;">Item</th>
				<th style="min-width:80px;">Qty</th>
				<th style="min-width:125px;">Offer Item</th>
				<th style="min-width:100px;">Offer Qty</th>
                                <th style="min-width:100px;">Valid Frm</th>
                                <th style="min-width:100px;">Valid To</th>
                                <th style="min-width:80px;">Status</th>
                                <th style="min-width:80px;">Image</th>
				<th style="min-width:80px;">Action</th>
			    </tr>
		       </thead>
                      <tbody>
                          
                    @if(isset($det_pack))
                    @if(count($det_pack)>0)
                    @foreach($det_pack as $key=>$item)  
                    <tr> 
                         <td>{{ $key+1 }}</td> 
                         <td>@if(isset($item->item_name)) {{ title_case($item->item_name) }}@endif</td>
                         <td>@if(isset($item->qty)) {{ $item->qty }}@endif</td>
                         <td>@if(isset($item->offer_item)) {{ title_case($item->offer_item) }}@endif</td>
                         <td>@if(isset($item->off_qty)) {{ $item->off_qty }}@endif</td>
                         <td>@if(isset($item->valid_from)) {{ $item->valid_from }}@endif</td>
                         <td>@if(isset($item->valid_to)) {{ $item->valid_to }}@endif</td>
                         <td>
                         <div class="status_chck{{ $item->rest_id}},{{ $item->sl_no}}">
                            <div class="onoffswitch">
                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch{{$item->rest_id}},{{$item->sl_no}}" @if( $item->active == 'Y') checked @endif>
                                    <label class="onoffswitch-label" for="myonoffswitch{{$item->rest_id}},{{$item->sl_no}}">
                                        <span class="onoffswitch-inner" onclick="return  statuschange('{{$item->rest_id}}','{{$item->sl_no}}','{{$item->active}}','P')"></span>
                                        <span class="onoffswitch-switch" onclick="return  statuschange('{{$item->rest_id}}','{{$item->sl_no}}','{{$item->active}}','P')"></span>
                                    </label>
                            </div>
                       </div>
                        </td>
                         @if($item->image != '')
                      <td><a rel="popover" data-img="{{ $siteUrl.$item->image }}" style="text-decoration: underline;"  data-original-title="" href="#">View</a></td> 
                      @else
                      <td><a data-img="{{ $siteUrl.$item->image }}" style="text-decoration: underline;"  data-original-title="" href="#">View</a></td> 
                      @endif
                      <td> 
                            <a onclick="return editpack('{{ $item->rest_id }}','{{ $item->sl_no }}','{{$item->offer_name}}','{{$item->item_name}}','{{$item->qty}}','{{$item->offer_item}}','{{ $item->off_qty }}','{{ $item->valid_from }}','{{ $item->valid_to }}','{{ $item->image }}','{{ $item->description }}','{{ $siteUrl }}','{{ $item->type }}','{{ $item->item_type }}')" href="#" class="table-action-btn button_table edit_offer_btn_new edit_pack"><i class="md md-edit"></i></a>
                            <a onclick="return delete_offer('{{ $item->rest_id }}','{{ $item->sl_no }}')" href="#" class="table-action-btn button_table  "><i class="fa fa-trash"></i></a>
                      </td> 
                    </tr>
                    @endforeach
                    @endif
                    @endif         

                      </tbody>
                </table>
             </div>
            </div>
            </div> 
               <div class="col-xs-12">
            <div class="table_section_scroll">  
                <h4>Item Offer - Individual</h4>
            <div class="table-responsive">
                  <table class="table m-0 table table-striped" id="example1">
                       <thead>
			    <tr>
				<th style="min-width:50px;">#</th>
				<th style="min-width:200px;">Item</th>
				<th style="min-width:120px;">Orgnl Rate</th>
                                <th style="min-width:120px;">Offer Rate</th>
				<th style="min-width:150px;">Valid Frm</th>
                                <th style="min-width:150px;">Valid To</th>
                                <th style="min-width:80px;">Status</th>
                                <th style="min-width:80px;">Image</th>
				<th style="min-width:80px;">Action</th>
			    </tr>
		       </thead>
                      <tbody>
                          @if(isset($det_individual))
                    @if(count($det_individual)>0)
                    @foreach($det_individual as $key=>$item)  
                    <tr> 
                        <td>{{ $key+1 }}</td> 
                        <td>@if(isset($item->item_name)) {{ title_case($item->item_name) }}@endif</td>
                        <td>@if(isset($item->original_rate)) {{ $item->original_rate }}@endif</td>
                        <td>@if(isset($item->offer_rate)) {{ $item->offer_rate }}@endif</td>
                        <td>@if(isset($item->valid_from)) {{ $item->valid_from }}@endif</td>
                        <td>@if(isset($item->valid_to)) {{ $item->valid_to }}@endif</td>
                        <td>
                         <div class="status_chck{{ $item->rest_id}},{{ $item->sl_no}}">
                            <div class="onoffswitch">
                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch{{$item->rest_id}},{{$item->sl_no}}" @if( $item->active == 'Y') checked @endif>
                                    <label class="onoffswitch-label" for="myonoffswitch{{$item->rest_id}},{{$item->sl_no}}">
                                        <span class="onoffswitch-inner" onclick="return  statuschange('{{$item->rest_id}}','{{$item->sl_no}}','{{$item->active}}','I')"></span>
                                        <span class="onoffswitch-switch" onclick="return  statuschange('{{$item->rest_id}}','{{$item->sl_no}}','{{$item->active}}','I')"></span>
                                    </label>
                            </div>
                       </div>
                        </td>
                      @if($item->image != '')
                      <td><a rel="popover" data-img="{{ $siteUrl.$item->image }}" style="text-decoration: underline;"  data-original-title="" href="#">View</a></td> 
                      @else
                      <td><a data-img="{{ $siteUrl.$item->image }}" style="text-decoration: underline;"  data-original-title="" href="#">View</a></td> 
                      @endif
                      <td> 
                          <a onclick="return editindividual('{{ $item->rest_id }}','{{ $item->sl_no }}','{{$item->offer_name}}','{{$item->item_name}}','{{$item->original_rate}}','{{$item->offer_rate}}','{{ $item->valid_from }}','{{ $item->valid_to }}','{{ $item->image }}','{{ $item->description }}','{{ $siteUrl }}','{{ $item->type }}','{{ $item->item_type }}')" href="#" class="table-action-btn button_table edit_offer_btn_new edit_indiv"><i class="md md-edit"></i></a>
                          <a onclick="return delete_offer('{{ $item->rest_id }}','{{ $item->sl_no }}')" href="#" class="table-action-btn button_table "><i class="fa fa-trash"></i></a>
                      </td> 
                    </tr>
                    @endforeach
                    @endif
                    @endif   

                      </tbody>
                </table>
             </div>
            </div>
            </div> 
            </div>    
        </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->
    
   <!--Start of Add section-->
    <div class="timing_popup_cc add_view_offer_popup" style="display: none;">
    <div class="timing_popup">
        <div class="timing_popup_head" style="margin-bottom: -15px;">
            ADD OFFER
            <div class="timing_popup_cls"><img src="{{asset('public/assets/images/cancel.png')}}"></div>
        </div>
        <div class="timing_popup_contant">
            <div class="restaurant_more_detail_row" style="margin-bottom:-18px">
                {!! Form::open(['enctype'=>'multipart/form-data','url'=>'api/restaurant/add_offer', 'name'=>'frm_addoffer', 'id'=>'frm_addoffer','method'=>'POST']) !!}
                <input type="hidden" id="res_id" name="res_id" value="{{ $resid }}">
                <input type="hidden" id="desc" name="desc" value="">
                <input type="hidden" id="pdesc" name="pdesc" value="">
                <input type="hidden" id="opertntype" name="opertntype" value="">
               <div class="main_inner_class_track" style="width: 37%;margin:8px 0;">
                          <div class="group">
                             <div style="position: relative">
                                 <label class="offer_add_txt_1">TYPE</label>
                                 <select class="form-control offer_add_field_1" id="offer_type" name="offer_type" onchange="return change_offertype(this.value);">
                                     <option value="B">Bill</option>
                                     <option value="I">Item</option>
                                 </select>
                              </div>
                          </div>
               </div>
               <div class="main_inner_class_track items_div" style="width: 37%;margin:8px 0;display:none;">
                          <div class="group">
                             <div style="position: relative;margin-left: 12px;">
                                 <label class="offer_add_txt_1">ITEMS</label>
                                 <select class="form-control offer_add_field_1" id="item_type" name="item_type" onchange="return change_itemtype(this.value);">
                                     <option value="P">Pack</option>
                                     <option value="I">Individual</option>
                                 </select>
                              </div>
                          </div>
               </div>
                <div class="offer_add_form_section_bx  type_bill" >
                <div class="offer_selected_hd">Bill</div>
                    
                <div class="main_inner_class_track" style="width: 26%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Amount Above*</label>
                                 <input type="text"  autocomplete="off"  onkeypress="return isNumberKey(event)" id="amount_above" name="amount_above" class="form-control">
                              </div>
                           </div>
                        </div>
                <div class="main_inner_class_track" style="width: 26%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Offer Name*</label>
                                 <input type="text"  autocomplete="off"  id="offer_name" name="offer_name" class="form-control" maxlength="35">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 20%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Offer %*</label>
                                 <input type="text"  autocomplete="off"  onkeypress="return isNumberKey(event)"  id="offer_percent" name="offer_percent" class="form-control">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 20%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Max Amount*</label>
                                <input autocomplete="off" id="max_amount"  onkeypress="return isNumberKey(event)"  name="max_amount" type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 48%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid From*</label>
                                 <input autocomplete="off" id="valid_from"  data-date-format = 'yyyy-mm-dd'  name="valid_from" type="text" class="form-control datefield">
                          </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 47%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid To*</label>
                                 <input autocomplete="off" id="valid_to" data-date-format = 'yyyy-mm-dd' name="valid_to" type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>

                                    
                        <div class="main_inner_class_track" style="width: 96%;">
                          <div class="group">
                             <div style="position: relative" id="imgdiv">
                                  <label>Image*</label>
                                 <input autocomplete="off" id="img" name="img" type="file" class="form-control">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 97%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Description*</label>
                                 <textarea autocomplete="off" id="description" name="description" class="form-control"></textarea>
                              </div>
                           </div>
                        </div>
                    </div>

                <div class="offer_add_form_section_bx itemtype_item" style="display:none">
                <div class="offer_selected_hd">Items</div>
                <div class="main_inner_class_track" style="width: 100%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Offer Name*</label>
                                 <input autocomplete="off" id="ii_offername" name="ii_offername"   type="text" class="form-control" maxlength="35">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 61%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Item*</label>
                                <input type="text"  autocomplete="off" class="form-control" id="item_name" name="item_name" onKeyUp = "return itemnamechange(this.value,'add')">
                                <input type="hidden" id="item_portion" name="item_portion" />
                                <input type="hidden" id="item_id" name="item_id" />
                                <div class="searchlist" style="display:none;width: 130%;background:#FFF;margin-top:1px;float:left;" id="suggesstionsofferitem"  onMouseOut="mouseoutfnctn(this);">
                                </div>
                                </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 17%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Orginal Rate*</label>
                                <input type="text"  autocomplete="off" id="original_rate" readonly="readonly" name="original_rate" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="main_inner_class_track" style="width: 17%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Offer Rate*</label>
                                <input type="text"  autocomplete="off" id="offer_rate"  name="offer_rate"  onkeypress="return isNumberKey(event)" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid From*</label>
                                 <input autocomplete="off" id="item_valid_from" name="item_valid_from"  data-date-format = 'yyyy-mm-dd' type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid To*</label>
                                 <input autocomplete="off" id="item_valid_to" name="item_valid_to"  data-date-format = 'yyyy-mm-dd' type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>

                    <div class="main_inner_class_track" style="width: 45%;"  id="imgdiv">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Image</label>
                                 <input autocomplete="off" id="item_img" name="item_img" type="file" class="form-control">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 98%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Description</label>
                                 <textarea autocomplete="off" id="item_desc" name="item_desc" class="form-control"></textarea>
                              </div>
                           </div>
                        </div>
                </div>
                
                <div class="offer_add_form_section_bx itemtype_pack" style="display:none">
                <div class="offer_selected_hd">Items</div>
                <div class="main_inner_class_track" style="width: 100%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Offer Name*</label>
                                <input type="text"  autocomplete="off" name="ip_offername" id="ip_offername" class="form-control" maxlength="35">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 34%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Item*</label>
                               <input type="text"  autocomplete="off" class="form-control" id="p_item" name="p_item" onKeyUp = "return itemnamechange(this.value,'pitemadd')">
                               <input type="hidden" id="p_item_portion" name="p_item_portion" />
                                <input type="hidden" id="p_item_id" name="p_item_id" />
                               <div class="searchlist" style="display:none;width: 130%;background:#FFF;margin-top:1px;float:left;" id="suggesstionitem"  onMouseOut="mouseoutfnctn(this);">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 11%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Qty*</label>
                                <input type="text"  autocomplete="off" name="p_qty" id="p_qty" class="form-control" onkeypress="return numonly(event)">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 34%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Offer Item*</label>
                                <input type="text"  autocomplete="off" class="form-control" name="p_off_item" id="p_off_item" onKeyUp = "return itemnamechange(this.value,'pofferadd')">
                                <div class="searchlist" style="display:none;width: 130%;background:#FFF;margin-top:1px;float:left;" id="suggesstionitemoffer"  onMouseOut="mouseoutfnctn(this);">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 12%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Offer Qty*</label>
                                <input type="text"  autocomplete="off" name="p_off_qty" id="p_off_qty" class="form-control" onkeypress="return numonly(event)">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid From*</label>
                                 <input autocomplete="off" name="p_valid_from" id="p_valid_from" data-date-format = 'yyyy-mm-dd' type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid To*</label>
                                 <input autocomplete="off" name="p_valid_to" id="p_valid_to" data-date-format = 'yyyy-mm-dd' type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 45%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Image</label>
                                 <input autocomplete="off" id="p_img" name="p_img" type="file" class="form-control">
                              </div>
                           </div>
                        </div>

                    <div class="main_inner_class_track" style="width: 98%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Description</label>
                                 <textarea autocomplete="off" name="p_desc" id="p_desc" class="form-control"></textarea>
                              </div>
                           </div>
                        </div>
                </div>
                <div class="col-sm-12 no-padding">
                <a class="staff-add-pop-btn staff-add-pop-btn-new" onclick="return add_offer()" style="float: right;">ADD</a>
        </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
       </div>
    <div class="timing_popup_cc " style="display: none;" id="warning_offer_popup">
        <div class="timing_popup" style="width:360px;top:42%">
      
        <div class="timing_popup_contant">
            <div class="restaurant_more_detail_row" style="margin-bottom:-18px">
                <p>Offer Inserted, Offer Already Exist For this Restaurant.<br> Do You Want To Activate This Offer And Replace Existing Offer? </p>
			<div class="col-sm-12 no-padding">
                <a class="staff-add-pop-btn staff-add-pop-btn-new" onclick="return cancel_add_offer()" style="float: right;">No</a>
                <a class="staff-add-pop-btn staff-add-pop-btn-new" onclick="return continue_add_offer()" style="float: right;">Yes</a>
        </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
       </div>
    <!--End of Add Section-->
    
    <!--Start of Edit Bill Section-->
    <div class="timing_popup_cc edit_view_offer_popup" style="display: none;">
    <div class="timing_popup">
        <div class="timing_popup_head" style="margin-bottom: -15px;">
            EDIT OFFER
            <div class="timing_popup_cls"><img src="{{asset('public/assets/images/cancel.png')}}"></div>
        </div>
        <div class="timing_popup_contant">
            <div class="restaurant_more_detail_row" style="margin-bottom:-18px">
                {!! Form::open(['enctype'=>'multipart/form-data','url'=>'api/restaurant/add_offer', 'name'=>'frm_editoffer', 'id'=>'frm_editoffer','method'=>'POST']) !!}
                <input type="hidden" id="edres_id" name="edres_id">
                <input type="hidden" id="edslno" name="edslno">
                <input type='hidden' id="ed_type" name="ed_type">
                <input type='hidden' id="ed_itemtype" name="ed_itemtype">
                <input type="hidden" name="oldimg" id="oldimg" >
                <input type="hidden" name="editoldimg" id="editoldimg" >

                <div class="offer_add_form_section_bx  edtype_bill" style="display:none" >
                <div class="offer_selected_hd">Bill</div>
                    
                <div class="main_inner_class_track" style="width: 26%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Amount Above*</label>
                                 <input type="text"  autocomplete="off" onkeypress="return isNumberKey(event)" id="edamount_above" name="edamount_above" class="form-control">
                              </div>
                           </div>
                        </div>
                <div class="main_inner_class_track" style="width: 26%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Offer Name*</label>
                                 <input type="text"  autocomplete="off"  id="edoffer_name" name="edoffer_name" class="form-control" maxlength="35">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 20%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Offer %*</label>
                                 <input type="text"  autocomplete="off"  onkeypress="return isNumberKey(event)"  id="edoffer_percent" name="edoffer_percent" class="form-control">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 20%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Max Amount*</label>
                                <input autocomplete="off" id="edmax_amount"  onkeypress="return isNumberKey(event)"  name="edmax_amount" type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 26%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid From*</label>
                                <input autocomplete="off" id="edvalid_from"  data-date-format = 'yyyy-mm-dd' name="edvalid_from" type="text" class="form-control datefield">
                             </div>
                           </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 27%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid To*</label>
                                 <input autocomplete="off" id="edvalid_to" data-date-format = 'yyyy-mm-dd' name="edvalid_to" type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>

                                    
                        <div class="main_inner_class_track" style="width: 40%;">
                          <div class="group">
                             <div style="position: relative" id="imgdiv">
                                  <label>Image*</label><a rel="popover" id="imgoffer" name="imgoffer"  href="#" style="margin-left: 25px;">View</a>
                                 <input autocomplete="off" id="edimg" name="edimg" type="file" class="form-control">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 97%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Description*</label>
                                 <textarea autocomplete="off" id="eddescription" name="eddescription" class="form-control"></textarea>
                              </div>
                           </div>
                        </div>
                    </div>

                <div class="offer_add_form_section_bx editemtype_item" style="display:none">
                <div class="offer_selected_hd">Items</div>
                <div class="main_inner_class_track" style="width: 100%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Offer Name*</label>
                                <input type="text"  autocomplete="off" id="edii_offername" name="edii_offername" class="form-control" maxlength="35">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 61%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Item*</label>
                                <input type="text"  autocomplete="off" class="form-control" id="editem_name" readonly="readonly" name="editem_name" onKeyUp = "return itemnamechange(this.value,'edit')">
                                <div class="searchlist" style="display:none;width: 130%;background:#FFF;margin-top:1px;float:left;" id="suggesstionsofferitemed"  onMouseOut="mouseoutfnctn(this);">
                                </div>
                                </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 17%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Orginal Rate*</label>
                                <input type="text"  autocomplete="off" id="edoriginal_rate" readonly="readonly" name="edoriginal_rate" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="main_inner_class_track" style="width: 17%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Offer Rate*</label>
                                <input type="text"  autocomplete="off" id="edoffer_rate"  name="edoffer_rate"  onkeypress="return isNumberKey(event)" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid From*</label>
                                 <input id="editem_valid_from" autocomplete="off" name="editem_valid_from"  data-date-format = 'yyyy-mm-dd' type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid To*</label>
                                 <input id="editem_valid_to" autocomplete="off" name="editem_valid_to"  data-date-format = 'yyyy-mm-dd' type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>

                    <div class="main_inner_class_track" style="width: 45%;"  id="imgdiv">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Image</label><a rel="popover" id="edimgoffer" name="edimgoffer"  href="#" style="margin-left: 25px;">View</a>
                                 <input autocomplete="off" id="editem_img" name="editem_img" type="file" class="form-control">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 98%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Description</label>
                                 <textarea autocomplete="off" id="editem_desc" name="editem_desc" class="form-control"></textarea>
                              </div>
                           </div>
                        </div>
                  </div>
                
                <div class="offer_add_form_section_bx editemtype_pack" style="display:none">
                <div class="offer_selected_hd">Items</div>
                <div class="main_inner_class_track" style="width: 100%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Offer Name*</label>
                                <input type="text"  autocomplete="off" name="edip_offername" id="edip_offername" class="form-control" maxlength="35" >
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 34%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Item*</label>
                               <input type="text"  autocomplete="off" class="form-control not-active"  id="edp_item" name="edp_item" onKeyUp = "return itemnamechange(this.value,'pitemedit')" >
                                <div class="searchlist" style="display:none;width: 130%;background:#FFF;margin-top:1px;float:left;" id="edsuggesstionitem"  onMouseOut="mouseoutfnctn(this);">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 11%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Qty*</label>
                                <input type="text"  autocomplete="off" name="edp_qty" id="edp_qty" readonly="readonly" class="form-control not-active" onkeypress="return numonly(event)">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 34%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Offer Item*</label>
                                <input type="text"  autocomplete="off" class="form-control not-active" name="edp_off_item" id="edp_off_item" onKeyUp = "return itemnamechange(this.value,'pofferedit')">
                                <div class="searchlist" style="display:none;width: 130%;background:#FFF;margin-top:1px;float:left;" id="edsuggesstionitemoffer"  onMouseOut="mouseoutfnctn(this);">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 12%;">
                        <div class="group">
                            <div style="position: relative">
                                <label>Offer Qty*</label>
                                <input type="text"  autocomplete="off" name="edp_off_qty"  id="edp_off_qty" class="form-control not-active" onkeypress="return numonly(event)">
                            </div>
                        </div>
                    </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid From*</label>
                                 <input autocomplete="off" name="edp_valid_from" data-date-format = 'yyyy-mm-dd' id="edp_valid_from" type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Valid To*</label>
                                 <input autocomplete="off" name="edp_valid_to" data-date-format = 'yyyy-mm-dd'  id="edp_valid_to" type="text" class="form-control datefield">
                              </div>
                           </div>
                        </div>
                    <div class="main_inner_class_track" style="width: 45%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Image</label><a rel="popover" id="edpimgoffer" name="edpimgoffer"  href="#" style="margin-left: 25px;">View</a>
                                 <input autocomplete="off" id="edp_img" name="edp_img" type="file" class="form-control">
                              </div>
                           </div>
                        </div>

                    <div class="main_inner_class_track" style="width: 98%;">
                          <div class="group">
                             <div style="position: relative">
                                  <label>Description</label>
                                 <textarea autocomplete="off" name="edp_desc" id="edp_desc" class="form-control"></textarea>
                              </div>
                           </div>
                        </div>
                </div>
                <div class="col-sm-12 no-padding">
                <a class="staff-add-pop-btn staff-add-pop-btn-new" onclick="return update_offer()" style="float: right;">UPDATE</a>
        </div>
                <input type='hidden' id="ed_descdetail" name="ed_descdetail">
                {{ Form::close() }}
            </div>
        </div>
    </div>
       </div>
    <!--End of Edit Bill-->
    
<div id="url"></div>
<style>#datatable-fixed-col_filter{display:none}table.dataTable thead th{white-space:nowrap;padding-right: 20px;}
        .on-default	{margin-left:10px;}div.dataTables_info {padding-top:13px;}
    </style>
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
                     if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
                    return false;
                    return true;
                }
    </script>
@section('jquery')
 
 <script>
        $(document).ready(function()
        {
            //text editor script
            tinymce.init({
                selector: 'textarea',
                height: 150,
                menubar: false,
                branding: false,
                forced_root_block: false,
                theme: 'modern',
                plugins: 'autolink directionality advcode visualblocks visualchars fullscreen  link media template codesample  charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount spellchecker a11ychecker imagetools mediaembed  linkchecker contextmenu colorpicker textpattern help',
                toolbar: 'formatselect | bold italic backcolor strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help | underline  ',
                //toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
            });

            $('a[rel=popover]').popover({
                html: true,
                trigger: 'hover',
                placement: 'left',
                content: function()
                {
                    return '<img src="'+$(this).data('img') + '" width="200" height="100"/>';
                }
            });
        });

        //image width & height validation fixed as 225 x 225
        var _URL = window.URL;
        $("#img").change(function (e) {
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var file, img;

            if ((file = this.files[0])) {
                img = new Image();
                img.onload = function () {
                    if (this.height > 225 || this.width > 225)
                    {
                        $.Notification.autoHideNotify('error', 'bottom right','Height and Width must not exceed 225px.');
                        $("#img").val('');
                        return false;
                    }
                    if (this.height < 225 || this.width < 225)
                    {
                        $.Notification.autoHideNotify('error', 'bottom right','Height and Width must not be less than 225px.');
                        $("#img").val('');
                        return false;
                    }

//            alert("Width:" + this.width + "   Height: " + this.height);//this will give you image width and height and you can easily validate here....
                };
                img.src = _URL.createObjectURL(file);
            }
        });
 </script>
 <script>
function cancel_add_offer() {
   $("#warning_offer_popup").css("display","none");
   $(".add_view_offer_popup").css("display","none");
   location.reload(true);
}
function continue_add_offer() {
    $("#opertntype").val('replace');
    add_offer();
}
     //submit offer
     function add_offer()
     {
         $('.notifyjs-wrapper').remove();
         $('div').removeClass('input_focus');
         $('input').removeClass('input_focus');
         $('select').removeClass('input_focus');

         var offer_type = $('#offer_type');
         var item_type = $('#item_type');

      if(offer_type.val() == 'B')
      {
          var amount_above = $('#amount_above');
          var offer_name = $('#offer_name');
          var offer_percent = $('#offer_percent');
          var max_amount = $('#max_amount');
          var valid_from = $('#valid_from');
          var valid_to = $('#valid_to');
          var desc = tinyMCE.get('description').getContent();
          $("#desc").val(desc);
          var img = document.getElementById('img');
          var firstDate = valid_from.val();
          var secondDate = valid_to.val();
          var date2 = secondDate.split("-").reverse().join("-");
          var date1 = firstDate.split("-").reverse().join("-");

             if (amount_above.val() == '') {
                 amount_above.addClass('input_focus');
                 $.Notification.autoHideNotify('error', 'bottom right', 'Enter Amount Above.');
                 amount_above.focus();
                 return false;
             }

             if (offer_name.val() == '') {
                 offer_name.addClass('input_focus');
                 $.Notification.autoHideNotify('error', 'bottom right', 'Enter Offer Name.');
                 offer_name.focus();
                 return false;
             }

             if (offer_percent.val() == '') {
                 offer_percent.addClass('input_focus');
                 $.Notification.autoHideNotify('error', 'bottom right', 'Enter Offer Percent.');
                 offer_percent.focus();
                 return false;
             }

             if (offer_percent.val() != '' && parseFloat(offer_percent.val()) > 100) {
                 offer_percent.addClass('input_focus');
                 $.Notification.autoHideNotify('error', 'bottom right', 'Invalid Offer Percent.');
                 offer_percent.focus();
                 return false;
             }

             if (max_amount.val() == '') {
                 max_amount.addClass('input_focus');
                 $.Notification.autoHideNotify('error', 'bottom right', 'Enter Max Amount.');
                 max_amount.focus();
                 return false;
             }
             if (valid_from.val() == '') {
                 valid_from.addClass('input_focus');
                 $.Notification.autoHideNotify('error', 'bottom right', 'Enter Valid From.');
                 valid_from.focus();
                 return false;
             }
             if (valid_to.val() == '') {
                 valid_to.addClass('input_focus');
                 $.Notification.autoHideNotify('error', 'bottom right', 'Enter Valid To.');
                 valid_from.focus();
                 return false;
             }

//             if (date1 > date2) {
//                 $.Notification.autoHideNotify('error', 'bottom right', 'Invalid Date Range.');
//                 valid_from.addClass('input_focus');
//                 valid_to.addClass('input_focus');
//                 valid_from.focus();
//                 valid_to.focus();
//                 return false;
//             }
             if (img.value == '') {
                 $.Notification.autoHideNotify('error', 'bottom right', 'Upload Offer Image.');
                 img.addClass('input_focus');
                 img.focus();
                 return false;
             }

             if (img.value != '') {
                 if (!hasExtension('img', ['.jpg','.jpeg', '.gif', '.png','.JPG','.JPEG','.GIF','.PNG'])) {
                     $.Notification.autoHideNotify('error', 'bottom right', 'Upload Gif or Jpg Images Only.');
                     $("#imgdiv").addClass('input_focus');
                     return false;
                 }
             }

             if (desc == '') {
                 $("#description").addClass('input_focus');
                 $.Notification.autoHideNotify('error', 'bottom right', 'Enter Description.');
                 return false;
             }

         }
         else if(offer_type.val() =='I')
         {
             if(item_type.val() =='I')
             {
                 var ii_offername = $('#ii_offername');
                 var item_name = $('#item_name');
                 var item_id = $('#item_id');
                 var item_portion = $('#item_portion'); 
                 var offer_rate = $('#offer_rate');
                 var item_valid_from = $('#item_valid_from');
                 var item_valid_to = $('#item_valid_to');
                 var item_desc = tinyMCE.get('item_desc').getContent();
                 $("#desc").val(item_desc);
                 var item_img = document.getElementById('item_img');
                 var firstDate = item_valid_from.val();
                 var secondDate = item_valid_to.val();
                 var date2 = secondDate.split("-").reverse().join("-");
                 var date1 = firstDate.split("-").reverse().join("-");

                 if (ii_offername.val() == '')
                 {
                     ii_offername.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Offer Name.');
                     ii_offername.focus();
                     return false;
                 }
                 if (item_name.val() == '')
                 {
                     item_name.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Item.');
                     item_name.focus();
                     return false;
                 }
                 if (offer_rate.val() == '')
                 {
                     offer_rate.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Offer Rate.');
                     offer_rate.focus();
                     return false;
                 }
                 if (item_valid_from.val() == '')
                 {
                     item_valid_from.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Valid From.');
                     item_valid_from.focus();
                     return false;
                 }
                 if (item_valid_to.val() == '')
                 {
                     item_valid_to.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Valid To.');
                     item_valid_to.focus();
                     return false;
                 }

//                 if (date1 > date2)
//                 {
//                     $.Notification.autoHideNotify('error', 'bottom right', 'Invalid Date Range.');
//                     valid_from.addClass('input_focus');
//                     valid_to.addClass('input_focus');
//                     valid_from.focus();
//                     valid_to.focus();
//                     return false;
//                 }
                 if (item_img.value != '')
                 {
                     if (!hasExtension('item_img', ['.jpg','.jpeg', '.gif', '.png','.JPG','.JPEG','.GIF','.PNG']))
                     {
                         $.Notification.autoHideNotify('error', 'bottom right', 'Upload Gif or Jpg Images Only.');
                         $("#itemimgdiv").addClass('input_focus');
                         return false;
                     }
                 }
             }

             else if(item_type.val() =='P')
             {
                 
                 var ip_offername = $('#ip_offername');
                 var item_name = $('#p_item');
                 var qty = $('#p_qty');
                 var offeritem = $('#p_off_item');
                 var offerqty = $('#p_off_qty');
                  var pvalid_form = $('#p_valid_from');
                 var pvalid_to= $('#p_valid_to');
                 var p_desc = tinyMCE.get('p_desc').getContent();
                 $("#pdesc").val(p_desc);
                 var p_img = document.getElementById('p_img');
                 var firstDate = pvalid_form.val();
                 var secondDate = pvalid_to.val();
                 var date2 = secondDate.split("-").reverse().join("-");
                 var date1 = firstDate.split("-").reverse().join("-");

                 if (ip_offername.val() == '')
                 {
                     ip_offername.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Offer Name.');
                     ip_offername.focus();
                     return false;
                 }
                 if (item_name.val() == '')
                 {
                     item_name.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Item.');
                     item_name.focus();
                     return false;
                 }
                 if (qty.val() == '')
                 {
                     qty.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Qty.');
                     qty.focus();
                     return false;
                 }
                  if (offeritem.val() == '')
                 {
                     offeritem.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Offer Item.');
                     offeritem.focus();
                     return false;
                 }
                 if (offerqty.val() == '')
                 {
                     offerqty.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Offer Qty.');
                     offerqty.focus();
                     return false;
                 }

                 if (pvalid_form.val() == '')
                 {
                     pvalid_form.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Valid From.');
                     pvalid_form.focus();
                     return false;
                 }
                 if (pvalid_to.val() == '')
                 {
                     pvalid_to.addClass('input_focus');
                     $.Notification.autoHideNotify('error', 'bottom right', 'Enter Valid To.');
                     pvalid_to.focus();
                     return false;
                 }

//                 if (date1 > date2)
//                 {
//                     $.Notification.autoHideNotify('error', 'bottom right', 'Invalid Date Range.');
//                     pvalid_form.addClass('input_focus');
//                     pvalid_to.addClass('input_focus');
//                     pvalid_form.focus();
//                     pvalid_to.focus();
//                     return false;
//                 }
                 if (p_img.value != '')
                 {
                     if (!hasExtension('p_img', ['.jpg','.jpeg', '.gif', '.png','.JPG','.JPEG','.GIF','.PNG']))
                     {
                         $.Notification.autoHideNotify('error', 'bottom right', 'Upload Gif or Jpg Images Only.');
                         $("#itemimgdiv").addClass('input_focus');
                         return false;
                     }
                 }
                 
             }
         }

         var formdata = new FormData($('#frm_addoffer')[0]);

         $.ajax({
             method: "post",
             url: "../../api/restaurant/add_offer",
             data: formdata,
             cache: false,
             crossDomain: true,
             async: false,
             processData: false,
             contentType: false,
             dataType: 'text',
             success: function (result)
             {
                    var json_x= JSON.parse(result);
                    if((json_x.msg)=='success')
                    {
                        location.reload(true);
                         swal({
							
                            title: "",
                            text: "Added Successfully",
                            timer: 4000,
                            showConfirmButton: false
                        });

                    }
                    else if((json_x.msg)=='bill_offer_exit')
                    {
                        $("#warning_offer_popup").css("display","block");

                    }
                    else if((json_x.msg)=='bill_invaliddate_range')
                    {
                        $.Notification.autoHideNotify('error', 'bottom right', 'Invalid Date Range.');
                        valid_from.addClass('input_focus');
                        valid_to.addClass('input_focus');
                        valid_from.focus();
                        valid_to.focus();
                        return false;

                    }
                    else if((json_x.msg)=='item_invaliddate_range')
                    {
                       $.Notification.autoHideNotify('error', 'bottom right', 'Invalid Date Range.');
                     valid_from.addClass('input_focus');
                     valid_to.addClass('input_focus');
                     valid_from.focus();
                     valid_to.focus();
                     return false;

                    }
                    else if((json_x.msg)=='pack_invaliddate_range')
                    {
                      
                     $.Notification.autoHideNotify('error', 'bottom right', 'Invalid Date Range.');
                     pvalid_form.addClass('input_focus');
                     pvalid_to.addClass('input_focus');
                     pvalid_form.focus();
                     pvalid_to.focus();
                     return false;

                    }
                    else if((json_x.msg)=='exist')
                    {
                        swal({
							
                            title: "",
                            text: "Already Exist",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                    else if((json_x.msg)=='item_offer_exist')
                    {
                        swal({
							
                            title: "",
                            text: "Already Exist For This Item",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
               
             },
             error: function (jqXHR, textStatus, errorThrown)
             {
                 $("#url").text(jqXHR.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
             }
         });
         return true;
     }

     function change_offertype(val)
     {
           if(val == 'I')
           {
                   $(".items_div").show();
                   $(".type_bill").hide();
                   $(".itemtype_pack").show();
                   $("#amount_above").val('');
                   $("#offer_name").val('');
                   $("#offer_percent").val('');
                   $("#max_amount").val('');
                   $("#valid_from").val('');
                   $("#valid_to").val('');
                   $("#img").val('');
                   tinyMCE.activeEditor.setContent('');
                   
           }
         else{
               $(".itemtype_pack").hide();
               $(".itemtype_item").hide();
               $(".items_div").hide();
               $(".type_bill").show();
               $("#item_type").prop("selectedIndex", 0);
               $("#p_item").val('');
               $("#p_qty").val('');
               $("#p_off_item").val('');
               $("#p_off_qty").val('');
               $("#p_valid_from").val('');
               $("#p_valid_to").val('');
               $("#p_img").val('');
               $("#item_name").val('');
               $("#original_rate").val('');
               $("#offer_rate").val('');
               $("#item_valid_from").val('');
               $("#item_valid_to").val('');
               $("#item_img").val('');
               tinyMCE.activeEditor.setContent('');
           }
         return true;
     }

     function change_itemtype(val)
     {
         if(val == 'I')
         {
             $(".itemtype_item").show();
             $(".itemtype_pack").hide();
             $("#p_item").val('');
             $("#p_qty").val('');
             $("#p_off_item").val('');
             $("#p_off_qty").val('');
             $("#p_valid_from").val('');
             $("#p_valid_to").val('');
             $("#p_img").val('');
             tinyMCE.activeEditor.setContent('');
         }
         else if(val == 'P')
         {
             $(".itemtype_pack").show();
             $(".itemtype_item").hide();
             $("#item_name").val('');
             $("#original_rate").val('');
             $("#offer_rate").val('');
             $("#item_valid_from").val('');
             $("#item_valid_to").val('');
             $("#item_img").val('');
             tinyMCE.activeEditor.setContent('');

         }
         return true;
     }
 </script>

<script>
    $(".add_offer_btn_new").click(function(){
        $(".add_view_offer_popup").show();
    });
    $(".edit_offer_btn_new").click(function(){
        $(".edit_view_offer_popup").show();
    });
 
    $(".timing_popup_cls").click(function(){
        $(".add_view_offer_popup").hide();
        $(".edit_view_offer_popup").hide();
    });
    
//    Edit
 $(".edit_bill").click(function(){
     $(".edtype_bill").show();
     $(".editemtype_pack").hide();
     $(".editemtype_item").hide();
  });
    $(".edit_pack").click(function(){
     $(".edtype_bill").hide();
     $(".editemtype_pack").show();
     $(".editemtype_item").hide();
  });
  $(".edit_indiv").click(function(){
     $(".edtype_bill").hide();
     $(".editemtype_pack").hide();
     $(".editemtype_item").show();
  });
  
</script>

<script type="text/javascript">
    $('.filter_sec_btn').on('click', function(e)
    {
        $('.filter_box_section_cc').toggleClass("diply_tgl");
        $("#restaurant_name").focus();
    });
 /*   $('#valid_to').datepicker({
                    autoclose: true,
                	todayHighlight: true,
    });*/
//     $('#').datepicker({
//                    autoclose: true,
//                	todayHighlight: true,
//    });
    
   /* $('#item_valid_to').datepicker({
                    autoclose: true,
                	todayHighlight: true,
    });
     $('#item_valid_from').datepicker({
                    autoclose: true,
                	todayHighlight: true,
    });
    
     $('#p_valid_from').datepicker({
                    autoclose: true,
                	todayHighlight: true,
    });
     $('#p_valid_to').datepicker({
                    autoclose: true,
                	todayHighlight: true,
    });
     $('#edvalid_to').datepicker({
                    autoclose: true,
                	todayHighlight: true,
    });
     $('#edvalid_from').datepicker({
                    autoclose: true,
                	todayHighlight: true,
    });
     $('#editem_valid_to').datepicker({
                    autoclose: true,
                	todayHighlight: true,
    });
     $('#editem_valid_from').datepicker({
                    autoclose: true,
                	todayHighlight: true,
    });
     $('#edp_valid_to').datepicker({
                    autoclose: true,
                	todayHighlight: true,
    });
     $('#edp_valid_from').datepicker({
                    autoclose: true,
                	todayHighlight: true,
    });*/
     $('a[rel=popover]').popover({
                html: true,
                trigger: 'hover',
                placement: 'left',
                content: function()
                {
                    return '<img src="'+$(this).data('img') + '" width="200" height="100"/>';
                }
            });

  
//on search click
function selectofferitem(menu_name,menu_id,portion,final_rate)
{
    $("#p_off_item").val(menu_name + ' , ' + portion);
//    $("#original_rate").val(final_rate);
    $("#suggesstionitemoffer").hide();
}
     function statuschange(id,slno,status,type) {
            var ids = id;
            var data = {"ids": ids,"slno":slno,"status":status,'type':type};
            $.ajax({
                method: "get",
                url: "../../rest_offer_status",
                data: data,
                cache: false,
                crossDomain: true,
                async: false,
                dataType: 'text',
                success: function (result)
                {
                    //location.reload();
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
//                    alert(errorThrown);
                    $("#errbox").text(jqxhr.responseText);
                }
            });
        }
        
        
function editbill(rid,slno,amt_abv,off_nme,off_per,max_amt,valid_frm,valid_to,image,desc,url,type)
{
         $("#edres_id").val(rid);
         $("#edslno").val(slno);
         $("#edamount_above").val(amt_abv);
         $("#edoffer_name").val(off_nme);
         $("#edoffer_percent").val(off_per);
         $("#edmax_amount").val(max_amt);
         $("#edvalid_from").val(valid_frm);
         $("#edvalid_to").val(valid_to);
         $("#ed_type").val(type);
         $("#editoldimg").val(image);
         $("#oldimg").val(url+''+image);
         $("#imgoffer").attr('data-img',url+''+image);
         tinymce.get("eddescription").setContent(desc);
         $("#ed_descdetail").val(desc);
}


function editindividual(rid,slno,offer_name,item_name,original_rate,offer_rate,valid_frm,valid_to,image,desc,url,type,item_type)
{
         $("#edres_id").val(rid);
         $("#edslno").val(slno);
         $("#edii_offername").val(offer_name);
         $("#editem_name").val(item_name);
         $("#edoriginal_rate").val(original_rate);
         $("#edoffer_rate").val(offer_rate);
         $("#editem_valid_from").val(valid_frm);
         $("#editem_valid_to").val(valid_to);
         $("#ed_type").val(type);
         $("#ed_itemtype").val(item_type);
         $("#editoldimg").val(image);
         $("#oldimg").val(url+''+image);
         $("#edimgoffer").attr('data-img',url+''+image);
         tinymce.get("editem_desc").setContent(desc);
         $("#ed_descdetail").val(desc);
}
function editpack(rid,slno,offer_name,item_name,qty,offer_item,off_qty,valid_frm,valid_to,image,desc,url,type,item_type)
{
         $("#edres_id").val(rid);
         $("#edslno").val(slno);
         $("#edip_offername").val(offer_name);
         $("#edp_item").val(item_name);
         $("#edp_qty").val(qty);
         $("#edp_off_item").val(offer_item);
         $("#edp_off_qty").val(off_qty);
         $("#edp_valid_from").val(valid_frm);
         $("#edp_valid_to").val(valid_to);
         $("#ed_type").val(type);
         $("#ed_itemtype").val(item_type);
         $("#editoldimg").val(image);
         $("#oldimg").val(url+''+image);
         $("#edpimgoffer").attr('data-img',url+''+image);
         tinymce.get("edp_desc").setContent(desc);
         $("#ed_descdetail").val(desc);
}
  function delete_offer(rid,slno) {
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
                        $.ajax({
                        method: "post",
                        url: "../../api/remove_offer/"+rid+"/"+slno,
                        cache: false,
                        crossDomain: true,
                        async: false,
                        success: function (result)
                        {
                           location.reload(true);
                                swal({

                                   title: "",
                                   text: "Deleted Successfully",
                                   timer: 4000,
                                   showConfirmButton: false
                               });

                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                           $("#errbox").text(jqXHR.responseText);
                       }
                       });
                     }
                   }); 
//      $.ajax({
//            method: "post",
//            url : "../../api/remove_offer/"+rid+"/"+slno,
//            cache : false,
//            crossDomain : true,
//            async : false,
//            processData : false,
//            contentType: false,
//            dataType :'text',
//            success : function(result)
//            {
//                location.reload();
//                    swal({
//
//                        title: "",
//                        text: "Offer Removed Successfully",
//                        timer: 4000,
//                        showConfirmButton: false
//                    });
//            },
//            error: function (jqXHR, textStatus, errorThrown) {
////                alert(textStatus);
//                $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
//            }
//        });
  }
function update_offer()
{
  $('.notifyjs-wrapper').remove();
  $('input').removeClass('input_focus');
  $('select').removeClass('input_focus');
  var type =  $("#ed_type").val();  
  var item_type = $("#ed_itemtype").val();  
  var edres_id = $("#edres_id").val();     
  var edslno = $("#edslno").val();

    if(type=='B') {
        //Bill
        var edamount_above = $("#edamount_above").val();
        var edoffer_name = $("#edoffer_name").val();
        var edoffer_percent = $("#edoffer_percent").val();
        var edmax_amount = $("#edmax_amount").val();
        var edvalid_from = $("#edvalid_from").val();
        var edvalid_to = $("#edvalid_to").val();
        var edimg = document.getElementById('edimg');
        var eddesc = tinyMCE.get('eddescription').getContent();
        var firstDate = edvalid_from;
        var secondDate = edvalid_to;
        var date2 = secondDate.split("-").reverse().join("-");
        var date1 = firstDate.split("-").reverse().join("-");
        $("#ed_descdetail").val(eddesc);
    }
    else if(type=='I' && item_type=='I')
    {
            //Individual
            var edii_offername = $("#edii_offername").val();
            var editem_name = $("#editem_name").val();
            var edoriginal_rate = $("#edoriginal_rate").val();
            var edoffer_rate = $("#edoffer_rate").val();
            var editem_valid_from = $("#editem_valid_from").val();
            var editem_valid_to = $("#editem_valid_to").val();
            var editem_img = document.getElementById('editem_img');
            var editem_desc = tinyMCE.get('editem_desc').getContent();
            var IfirstDate = editem_valid_from;
            var IsecondDate = editem_valid_to;
            var Idate2 = IsecondDate.split("-").reverse().join("-");
            var Idate1 = IfirstDate.split("-").reverse().join("-");
            $("#ed_descdetail").val(editem_desc);
    }
    else if(type=='I' && item_type=='P') {
        //Pack
        var edip_offername = $("#edip_offername").val();
        var edp_item = $("#edp_item").val();
        var edp_qty = $("#edp_qty").val();
        var edp_off_item = $("#edp_off_item").val();
        var edp_off_qty = $("#edp_off_qty").val();
        var edp_valid_from = $("#edp_valid_from").val();
        var edp_valid_to = $("#edp_valid_to").val();
        var edp_img = document.getElementById('edp_img');
        var edp_desc = tinyMCE.get('edp_desc').getContent();
        var PfirstDate = edp_valid_from;
        var PsecondDate = edp_valid_to;
        var Pdate2 = PsecondDate.split("-").reverse().join("-");
        var Pdate1 = PfirstDate.split("-").reverse().join("-");
        $("#ed_descdetail").val(edp_desc);
    }
      if(type=='B'){
    
    if(edamount_above == '') {
       
        $("#edamount_above").addClass('input_focus');
        $("#edamount_above").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Amount Above');
        return false;
    }
     if(edoffer_name == '') {
        $("#edoffer_name").focus();
        $("#edoffer_name").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Offer Name');
        return false;
    }
    if(edoffer_percent == '') {
        $("#edoffer_percent").focus();
        $("#edoffer_percent").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Offer %');
        return false;
    }
     if(edoffer_percent != '' && parseFloat(edoffer_percent) > 100)
         {
             $("#edoffer_percent").focus();
              $("#edoffer_percent").addClass('input_focus');
             $.Notification.autoHideNotify('error', 'bottom right','Invalid Offer Percent.');
             return false;
         }
     if(edmax_amount == '') {
        $("#edmax_amount").focus();
         $("#edmax_amount").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Maximum Amount');
        return false;
    }
      if(edvalid_from == '') {
        $("#edvalid_from").focus();
         $("#edvalid_from").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Select Valid From Date');
        return false;
    }
     if(edvalid_to == '') {
        $("#edvalid_to").focus();
         $("#edvalid_to").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Select Valid To Date');
        return false;
    }
    
//    if(date1 > date2)
//        {
//            $("#edvalid_from").addClass('input_focus');
//            $("#edvalid_to").addClass('input_focus');
//            $.Notification.autoHideNotify('error', 'bottom right','Invalid Date Range.');
//             return false;
//         }

     if(eddesc == '') {
        $("#eddesc").focus();
        $("#eddesc").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Description ');
        return false;
    }
    }
    else if(type=='I' && item_type=='I')
    {
    if(edii_offername == '') {
       
        $("#edii_offername").addClass('input_focus');
        $("#edii_offername").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Offer Name');
        return false;
    }
    if(editem_name == '') {
       
        $("#editem_name").addClass('input_focus');
        $("#editem_name").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Item Name');
        return false;
    }
    
    if(edoffer_rate == '') {
        $("#edoffer_rate").focus();
        $("#edoffer_rate").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Offer Rate');
        return false;
    }
      if(editem_valid_from == '') {
        $("#editem_valid_from").focus();
         $("#editem_valid_from").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Select Valid From Date');
        return false;
    }
     if(editem_valid_to == '') {
        $("#editem_valid_to").focus();
         $("#editem_valid_to").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Select Valid To Date');
        return false;
    }
//    if(Idate1 > Idate2)
//        {
//            $("#editem_valid_from").addClass('input_focus');
//            $("#editem_valid_to").addClass('input_focus');
//            $.Notification.autoHideNotify('error', 'bottom right','Invalid Date Range.');
//             return false;
//         }
    }
    else if(type=='I' && item_type=='P')
    {
        if(edip_offername == '') {
       
        $("#edip_offername").addClass('input_focus');
        $("#edip_offername").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Offer Name');
        return false;
    }
    if(edp_item == '') {
       
        $("#edp_item").addClass('input_focus');
        $("#edp_item").focus();
        $.Notification.autoHideNotify('error', 'bottom right','Enter Item Name');
        return false;
    }
    
    if(edp_qty == '') {
        $("#edp_qty").focus();
        $("#edp_qty").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Qty');
        return false;
    }
    if(edp_off_item == '') {
        $("#edp_off_item").focus();
        $("#edp_off_item").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Offer Item');
        return false;
    }
    if(edp_off_qty == '') {
        $("#edp_off_qty").focus();
        $("#edp_off_qty").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Enter Offer Qty');
        return false;
    }
      if(edp_valid_from == '') {
        $("#edp_valid_from").focus();
         $("#edp_valid_from").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Select Valid From Date');
        return false;
    }
     if(edp_valid_to == '') {
        $("#edp_valid_to").focus();
         $("#edp_valid_to").addClass('input_focus');
        $.Notification.autoHideNotify('error', 'bottom right','Select Valid To Date');
        return false;
    }
//    if(Pdate1 > Pdate2)
//        {
//            $("#edp_valid_from").addClass('input_focus');
//            $("#edp_valid_to").addClass('input_focus');
//            $.Notification.autoHideNotify('error', 'bottom right','Invalid Date Range.');
//             return false;
//         }
    }
    if(true)
    {
        var frmdata = new FormData($('#frm_editoffer')[0]);

        $.ajax({
            method: "post",
            url : "../../api/edit_rest_offers",
            data : frmdata,
            cache : false,
            crossDomain : true,
            async : false,
            processData : false,
            contentType: false,
            dataType :'text',
            success : function(result)
            {
                var json_x= JSON.parse(result);
                if((json_x.msg)=='success')
                {
                   location.reload();
                    swal({

                        title: "",
                        text: "Updated Successfully",
                        timer: 4000,
                        showConfirmButton: false
                    });

                }
                else if((json_x.msg)=='already exist')
                {
                    swal({

                        title: "",
                        text: "Already Exist",
                        timer: 4000,
                        showConfirmButton: false
                    });
                }
                else if((json_x.msg)=='bill_invaliddate_range')
                {
                     $("#edvalid_from").addClass('input_focus');
                    $("#edvalid_to").addClass('input_focus');
                    $.Notification.autoHideNotify('error', 'bottom right','Invalid Date Range.');
                     return false;
                }
                else if((json_x.msg)=='item_invaliddate_range')
                {
                     $("#editem_valid_from").addClass('input_focus');
                    $("#editem_valid_to").addClass('input_focus');
                    $.Notification.autoHideNotify('error', 'bottom right','Invalid Date Range.');
                     return false;
                }
                else if((json_x.msg)=='pack_invaliddate_range')
                {
                     
            $("#edp_valid_from").addClass('input_focus');
            $("#edp_valid_to").addClass('input_focus');
            $.Notification.autoHideNotify('error', 'bottom right','Invalid Date Range.');
             return false;
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
//                alert(textStatus);
                $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
            }
        });
    }
    return true;

}

</script>
<script type="text/javascript">
 $('.datefield').datetimepicker({
               format: "yyyy-mm-dd h:i:00",
        showMeridian: true,
        autoclose: true,
        todayBtn: true
            });
</script>
@stop
@endsection