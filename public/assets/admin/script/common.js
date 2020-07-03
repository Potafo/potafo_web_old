$.ajaxSetup({
    headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});



$('#logout').click(function () {
     swal({
     title: "",
     text: "Are you sure you want to logout?",
     type: "info",
     showCancelButton: true,
     cancelButtonClass: 'btn-white btn-md waves-effect',
     confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
     confirmButtonText: 'Logout',
     closeOnConfirm: false
}, function (isConfirm)
     {
         if (isConfirm)
         {
             localStorage.clear();
             window.location.href = "login";
         }
     });
});
function logout()
{
    var reslt =$.Notification.confirm('error','top center', 'Are you sure you want to logout?!');
   /* var check = confirm("Are you sure you want to delete?");
    if(check==true) {
    alert('yes');
    }
    else{
        alert('ok');
    }*/
}
function capitalizeFirstLetter(str)
{
    return str.replace(str.charAt(0), str.charAt(0).toUpperCase());
};

$(document).ready(function()
{
    $("#username").html(capitalizeFirstLetter(localStorage.user_name));
    $("#group_name").html(localStorage.group_name);
    $("#username").css("font-weight","Bold");
});

$(document).on('click', '.notifyjs-metro-base .yes', function() {
    localStorage.clear();
    window.location.href = "login";
});

function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt){
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}




