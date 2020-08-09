$(document).ready(function() {
   $('#show_forgot_password').on('click', function() {
        var button = $(this);

        $('#forgot_password_form').show(function() {
            button.css('visibility', 'hidden');
        });
   });
});