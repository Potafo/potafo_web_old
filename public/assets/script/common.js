/**
 * Created by Explore on 10/12/2018.
 */
function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt){
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}


function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31
        && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

//check image extension
function hasExtension(inputID, exts)
{
    var fileName = document.getElementById(inputID).value;
    return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
}

function add3Dots(string, limit)
{
    var dots = "...";
    if(string.length > limit)
    {
        // you can also use substr instead of substring
        string = string.substring(0,limit) + dots;
    }
    string =string.slice(1, -1);
    return string;
}

