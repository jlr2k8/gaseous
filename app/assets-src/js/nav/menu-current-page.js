$(document).ready(function(){
    var curMenuLink = $("a[href='" + window.location.pathname + "']");

    if (curMenuLink.attr('href') == window.location.pathname) {
        curMenuLink.addClass('current');
    }
});