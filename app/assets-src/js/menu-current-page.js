$(document).ready(function(){
    var current_location    = window.location.protocol + "//" + window.location.hostname + window.location.pathname;

    $('#menu_container ul li').each(function() {
        var current_menu_item = $(this).find('a');

        if (current_location.includes(current_menu_item.attr('href'))) {
            current_menu_item.addClass('current');
        }

        if (window.location.pathname == '/') {
            $('a[href="' + window.location.protocol + '//' + window.location.hostname + '/home/"]').addClass('current');
        }
    });
});