$(document).ready( function() {
    $('button[name="update"].uri_route_update').on('click', function(e) {
        e.preventDefault();

        var this_update_button      = $(this);
        var route_key               = this_update_button.attr('data-route-key');
        var this_update_button_html = this_update_button.html();

        this_update_button.text('Updating...').attr('disabled','disabled');

        var data = $('#route_' + route_key).serialize() + '&update';

        $.post('/admin/routes/', data, function() {
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

    $('button[name="archive"].uri_route_archive').on('click', function(e) {
        e.preventDefault();

        var status = confirm('Are you sure?');

        if(status === false) {
            return false;
        }

        var this_archive_button = $(this);
        var route_key           = this_archive_button.attr('data-route-key');
        var this_row            = $('tr#row_' + route_key);

        this_archive_button.attr('disabled','disabled');

        $.get('/admin/routes/?archive=' + route_key, function() {
            this_archive_button
                .queue(function(e) {
                    this_row.hide();
                    e();
                });
        });
    });
});