$(document).ready( function() {
    $('button[name="update"].user_update').on('click', function(e) {
        e.preventDefault();
        var username = $(this).attr('data-username');

        var this_update_button      = $(this);
        var this_update_button_html = this_update_button.html();

        this_update_button.text('Updating...').attr('disabled','disabled');

        var data = $('#account_' + username).serialize() + '&update';

        $.post('/admin/users/', data, function() {
            this_update_button
                .html('Updated&#160;<i class="fas fa-check"></i>')
                .delay(1000)
                .queue(function(e) {
                    this_update_button.html(this_update_button_html)
                        .removeAttr('disabled');
                        e();
                });
        });
    });
});