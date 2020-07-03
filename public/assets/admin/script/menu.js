/**
 * Created by Explore on 10/23/2018.
 */

//menu excel sheet download
function menudownload(res_id)
{
    
    $('.loadin_popup_loader').css("display", "block");
//    $.get('../../menu/download/'+res_id, function(data)
//    {
//           window.location = '../../menu/download/' + res_id;
//    });
    
    $.ajax({
        method: "get",
        url: '../../menu/download/'+res_id,
        cache: false,
        crossDomain: true,
        async: true,
        success: function (result)
        {
            window.location = '../../menu/download/' + res_id;
            $('#loadin_popup_loader').css("display", "none");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            $("#errbox").text(jqXHR.responseText);
        }
    });
    
    
    
    return true;
}