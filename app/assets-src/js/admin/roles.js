$(document).ready( function() {
    $('button[name="update"].role_update').on('click', function(e) {
        e.preventDefault();
        var role_name = $(this).attr('data-role-name');

        var this_update_button = $(this);
        var this_update_button_html = this_update_button.html();

        this_update_button.text('Updating...').attr('disabled','disabled');

        var data = $('#role_' + role_name).serialize() + '&update';

        $.post('/admin/roles/', data, function(response) {
            this_update_button
                .html('Updated&#160;<i class="fas fa-check"></i>')
                .delay(1000)
                .queue(function(e) {
                    this_update_button.html(this_update_button_html)
                        .removeAttr('disabled');
                    e();
                });
            console.log(response);
        });
    });
});