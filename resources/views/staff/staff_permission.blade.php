@extends('layouts.app')
@section('title','Manage Staff Permission')
@section('content')
<style>.ion-ios7-eye{position: absolute;right: 20px; top: 28px;font-size: 25px;}</style>

    <div class="col-sm-12">
        
        <div class="card-box table-responsive" style="padding: 8px 10px;" >
              <div class="box master-add-field-box" >
            <div class="col-md-6 no-pad-left">
                <h3>STAFF PERMISSION</h3>
            </div>         
            </div>
            <div class="filter_text_box_row">
                      
                        {{ Form::hidden('s_id',$id, array ('id'=>'s_id','name'=>'s_id')) }}
                       <?php if(count($rows)>0)
                        { ?>
                        <div class="main_inner_class_track" style="width: 25%;">
                            <div class="group">
                               <div style="position: relative">
                                  <label>User Name</label>
                                   {{ Form::text('username',$rows[0]->name, array ('id'=>'username','name'=>'username','required','class'=>'form-control')) }}
                               </div>
                            </div>
                          </div>
                         <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                 <label>Password</label>
                                 <input style="padding-right:25px;" class="form-control" id="userpassword" name="userpassword" type="password" value="{{$password}}">
                                 <div class="ion-ios7-eye" onmouseover="mouseoverPass();" onmouseout="mouseoutPass();" />
                             </div>
                              
                           </div>
                        </div>
                      <?php  }
                        else
                        { ?>
                          <div class="main_inner_class_track" style="width: 25%;">
                            <div class="group">
                               <div style="position: relative">
                                  <label>User Name</label>
                                  <input class="form-control" id="username" name="username" type="text">
                               </div>
                            </div>
                          </div>
                        <div class="main_inner_class_track" style="width: 25%;">
                          <div class="group">
                             <div style="position: relative">
                                 <label>Password</label>
                                {{ Form::password('userpassword',array('id'=>'userpassword','name'=>'userpassword','required','class'=>'form-control'))}}
                                <div class="ion-ios7-eye" onmouseover="mouseoverPass();" onmouseout="mouseoutPass();" />
                             </div>
                           </div>
                        </div>
                      <?php  } ?>
                       
            </div>
                           
                         <div class="col-md-1 no-pad-left">
                <div class="table-filter" style="margin-top: 22px;">
                  <div class="table-filter-cc">
                    <a> <button type="submit" style="margin-top: px; border-radius: 4px;margin-left: 0;" class="on-default followups-popup-btn btn btn-primary ad-work-clear-btn" onclick="savepassword();" >UPDATE</button></a>
                </div>
                   
                 </div>
            </div>  
               

                   </div>
            
        </div>
        @if(count($rows)>0)
        <div class="card-box table-responsive" style="padding: 8px 10px;margin-top:10px;">
            @if(count($rows)==0)
            <div class="overlay_staff_permision">
                Web Login Not Activated 
            </div> 
             @endif
              <div class="col-md-8 no-pad-left">
                <h3>PERMISSION</h3>
                <div class="table_secion_permision">
                  <div class="col-md-6">
                        <table class="table">
                               <tbody>
                                   
                                @foreach($main_module as $moduleshow)
                                @if($moduleshow->count>1)
                                        
                               <td><strong>{{str_replace('_', ' ', ucfirst($moduleshow->module_name))}}</strong></td>
                     
                          @foreach($module_list as $sub)
                          @if($moduleshow->module_name == $sub->module_name) 
                          <tr>
                              <td>--{{str_replace('_', ' ',ucfirst($sub->sub_module))}}</td>
                          </tr>
                           @endif
                           @endforeach
                      
               @else
               <tr>
                 <td>{{str_replace('_', ' ', ucfirst($moduleshow->module_name))}}</td>
               </tr>
               @endif
                 @endforeach

                                </tbody>
                          </table>
                    </div>
                    <div class="col-md-6">
                      <table class="table">
                               <tbody>
                                   <?php $i=0; ?>
                                    @foreach($main_module as $moduleshowlist)
                                   
                                    @if($moduleshowlist->count>1)
                                   
                                    <td></td>
                               
                          @foreach($module_list as $sub)
                          
                          @if($moduleshowlist->module_name == $sub->module_name) 
                          <?php $i++; ?>
                          <tr>
                              <td>
                                    <div class="status_chck1{{ $userid }}">
                                        <div class="onoffswitch">
                                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch{{$i}}{{$userid}}" @if( $sub->active == 'Y') checked @endif>
                                                <label class="onoffswitch-label" for="myonoffswitch{{$i}}{{$userid}}">
                                                    <span class="onoffswitch-inner" onclick="return permissionchange('{{$userid}}','{{$sub->m_id}}','{{$id}}')"></span>
                                                    <span class="onoffswitch-switch" onclick="return permissionchange('{{$userid}}','{{$sub->m_id}}','{{$id}}')"></span>
                                                </label>
                                        </div>
                                    </div>
                              </td>
                          </tr>
                           @endif
                           @endforeach
                       
                           @else
                           
                           @foreach($module_list as $sub)
                          
                           @if($moduleshowlist->module_name == $sub->module_name) 
                           <?php $i++; 
                           
                           ?>
                            <tr>
                                <td>
                                    <div class="status_chck1{{ $userid }}">
                                        <div class="onoffswitch">
                                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch{{$i}}{{$userid}}" @if( $sub->active == 'Y') checked @endif >
                                                <label class="onoffswitch-label" for="myonoffswitch{{$i}}{{$userid}}">
                                                    <span class="onoffswitch-inner" onclick="return permissionchange('{{$userid}}','{{$sub->m_id}}','{{$id}}')"></span>
                                                    <span class="onoffswitch-switch" onclick="return  permissionchange('{{$userid}}',{{$sub->m_id}},'{{$id}}')"></span>
                                                </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                           @endif
                           @endforeach  
                            @endif
                           @endforeach  
                 
                                 
                                  
                                </tbody>
                          </table>
                    </div>
                </div>
            </div>
        </div>    
        @endif
    </div>


    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    <script>
        function savepassword()
        {
            $('.notifyjs-wrapper').remove();
            $('input').removeClass('input_focus');
            $('select').removeClass('input_focus');
            var name = $("#username").val();
            var password = $("#userpassword").val();
            var s_id = $("#s_id").val();
            if(name == '') 
            {
              $("#username").focus();
               $.Notification.autoHideNotify('error', 'bottom right','Enter Username');
            return false;
            }
            
            if(password == '') 
            {
              $("#userpassword").focus();
              $.Notification.autoHideNotify('error', 'bottom right','Enter Password');
            return false;
            }
            
        if(true)
        {
            var data= {"name":name,"password":password,"s_id":s_id};
            $.ajax({
                method: "get",
                url : "../api/savepassword",
                data : data,
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(result)
                {
                    var json_x= JSON.parse(result);
                    if((json_x.msg)=='insert')
                    {
                         location.reload();
                         swal({
							
                            title: "",
                            text: "Added Successfully",
                            timer: 4000,
                            showConfirmButton: false
                        });

                    }
                    else if((json_x.msg)=='update')
                    {
                        location.reload();
                        swal({
							
                            title: "",
                            text: "Updated Successfully",
                            timer: 4000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }
        }
    
    function permissionchange(userid,m_id,id)
        {
            var data= {"userid":userid,"m_id":m_id,"id":id};
            $.ajax({
                method: "get",
                url : "../api/savepermission",
                data : data,
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success : function(result)
                {
//                    alert (result);
//                   location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#urls").text(jqxhr.responseText); //@text = response error, it is will be errors: 324, 500, 404 or anythings else
                }
            });
        }
    </script>
    <script>
  function mouseoverPass(obj) {
  var obj = document.getElementById('userpassword');
  obj.type = "text";
}
function mouseoutPass(obj) {
  var obj = document.getElementById('userpassword');
  obj.type = "password";
}
    </script>
@section('jquery') 
@stop
@endsection