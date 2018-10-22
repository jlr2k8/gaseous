$(window).on('load', function() {
    $.post('/css-preview-check/', function(data) {
        if(data == '1') {
            $('body').prepend('<div id="site_admin_banner_message">You are currently previewing custom CSS. To exit the preview, <a href="/admin/css/">return to the CSS admin page</a> and choose "Exit Preview"</div>');
        }
    });
});