/**
 * Created by Explore on 10/30/2018.
 */

//on item name change in type as item & sub type individual
function itemnamechange(val,type)
{
    if(type=='add')
    {
      var res_id = $("#res_id");
    }
    else if(type =='edit')
    {
         var res_id = $("#edres_id");
    }
    else if(type =='pitemadd')
    {
         var res_id = $("#res_id");
    }
    else if(type =='pofferadd')
    {
         var res_id = $("#res_id");
    }
    else if(type =='pitemedit')
    {
         var res_id = $("#edres_id");
    }
    else if(type =='pofferedit')
    {
         var res_id = $("#edres_id");
    }
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
        if(parseInt(count)>= 1)
        {
            var datas = {'rest_id': res_id.val(),'searchterm': val,'type':type};
            $.ajax({

                method: "get",
                url : "../../offeritem/search",
                data : datas,
                cache : false,
                crossDomain : true,
                async : false,
                dataType :'text',
                success: function (data)
                {  if(type=='add'){
                    $("#suggesstionsofferitem").empty();
                    $.each(JSON.parse(data), function (i, indx)
                    {
                        $.each(JSON.parse(indx.portion), function (n, val)
                        {
                         var search_id = indx.m_menu_id+'_'+val.portion;
                        if ($("#search_" + search_id).length == 0)
                        {
                            var menu_name = indx.name+' , '+val.portion;
                            $("#suggesstionsofferitem").show();
                            $("#suggesstionsofferitem").append('<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="search_' + search_id + '" onclick=\'selectname("' + indx.name + '","' + indx.m_menu_id + '","' + val.portion + '","' + val.final_rate + '","add")\'>' + '<p>' + menu_name + '</p></div>');
                        }
                    });
                    });
                }
                else if(type=='edit')
                {
                    $("#suggesstionsofferitemed").empty();
                    $.each(JSON.parse(data), function (i, indx)
                    {
                        $.each(JSON.parse(indx.portion), function (n, val)
                        {
                         var search_id = indx.m_menu_id+'_'+val.portion;
                        if ($("#search_" + search_id).length == 0)
                        {
                            var menu_name = indx.name+' , '+val.portion;
                            $("#suggesstionsofferitemed").show();
                            $("#suggesstionsofferitemed").append('<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="search_' + search_id + '" onclick=\'selectname("' + indx.name + '","' + indx.m_menu_id + '","' + val.portion + '","' + val.final_rate + '","edit")\'>' + '<p>' + menu_name + '</p></div>');
                        }
                    });
                    });
                }
                else if(type=='pitemadd')
                {
                    $("#suggesstionitem").empty();
                    $.each(JSON.parse(data), function (i, indx)
                    {
                        $.each(JSON.parse(indx.portion), function (n, val)
                        {
                         var search_id = indx.m_menu_id+'_'+val.portion;
                        if ($("#search_" + search_id).length == 0 && val.final_rate!=0)
                        {
                            var menu_name = indx.name+' , '+val.portion;
                            $("#suggesstionitem").show();
                            $("#suggesstionitem").append('<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="search_' + search_id + '" onclick=\'selectname("' + indx.name + '","' + indx.m_menu_id + '","' + val.portion + '","' + val.final_rate + '","pitemadd")\'>' + '<p>' + menu_name + '</p></div>');
                        }
                    });
                    });
                }
                else if(type=='pofferadd')
                {
                    $("#suggesstionitemoffer").empty();
                    $.each(JSON.parse(data), function (i, indx)
                    {
                        $.each(JSON.parse(indx.portion), function (n, val)
                        {
                         var search_id = indx.m_menu_id+'_'+val.portion;
                        if ($("#search_" + search_id).length == 0 && val.final_rate==0)
                        {
                            var menu_name = indx.name+' , '+val.portion;
                            $("#suggesstionitemoffer").show();
                            $("#suggesstionitemoffer").append('<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="search_' + search_id + '" onclick=\'selectname("' + indx.name + '","' + indx.m_menu_id + '","' + val.portion + '","' + val.final_rate + '","pofferadd")\'>' + '<p>' + menu_name + '</p></div>');
                        }
                    });
                    });
                }
                else if(type=='pitemedit')
                {
                    $("#edsuggesstionitem").empty();
                    $.each(JSON.parse(data), function (i, indx)
                    {
                        $.each(JSON.parse(indx.portion), function (n, val)
                        {
                         var search_id = indx.m_menu_id+'_'+val.portion;
                        if ($("#search_" + search_id).length == 0)
                        {
                            var menu_name = indx.name+' , '+val.portion;
                            $("#edsuggesstionitem").show();
                            $("#edsuggesstionitem").append('<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="search_' + search_id + '" onclick=\'selectname("' + indx.name + '","' + indx.m_menu_id + '","' + val.portion + '","' + val.final_rate + '","pitemedit")\'>' + '<p>' + menu_name + '</p></div>');
                        }
                    });
                    });
                }
                else if(type=='pofferedit')
                {
                    $("#edsuggesstionitemoffer").empty();
                    $.each(JSON.parse(data), function (i, indx)
                    {
                        $.each(JSON.parse(indx.portion), function (n, val)
                        {
                         var search_id = indx.m_menu_id+'_'+val.portion;
                        if ($("#search_" + search_id).length == 0)
                        {
                            var menu_name = indx.name+' , '+val.portion;
                            $("#edsuggesstionitemoffer").show();
                            $("#edsuggesstionitemoffer").append('<div onMouseOut="normal(this);" onMouseOver="hover(this);" id="search_' + search_id + '" onclick=\'selectname("' + indx.name + '","' + indx.m_menu_id + '","' + val.portion + '","' + val.final_rate + '","pofferedit")\'>' + '<p>' + menu_name + '</p></div>');
                        }
                    });
                    });
                }
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
            $("#suggesstionsofferitem").html('');
        }
    }
    return true;
}

//on search click
function selectname(menu_name,menu_id,portion,final_rate,type)
{
    if(type=='add')
    {
        $("#item_name").val(menu_name + ' , ' + portion);
        $("#item_portion").val(portion);
        $("#item_id").val(menu_id);
        $("#original_rate").val(final_rate);
        $("#suggesstionsofferitem").hide();
    }
    else if(type=='edit')
    {
        $("#editem_name").val(menu_name + ' , ' + portion);
        $("#edoriginal_rate").val(final_rate);
        $("#suggesstionsofferitemed").hide();
    }
     else if(type=='pitemadd')
    {
        $("#p_item").val(menu_name + ' , ' + portion);
        $("#p_item_portion").val(portion);
        $("#p_item_id").val(menu_id);
        $("#suggesstionitem").hide();
    }
    else if(type=='pofferadd')
    {
        $("#p_off_item").val(menu_name + ' , ' + portion);
        $("#suggesstionitemoffer").hide();
    }
     else if(type=='pitemedit')
    {
        $("#edp_item").val(menu_name + ' , ' + portion);
        $("#edsuggesstionitem").hide();
    }
    else if(type=='pofferedit')
    {
        $("#edp_off_item").val(menu_name + ' , ' + portion);
        $("#edsuggesstionitemoffer").hide();
    }
}
