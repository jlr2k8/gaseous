$(document).ready( function() {
    $('button[name="update"].field_update').on('click', function(e) {
        e.preventDefault();

        var this_update_button      = $(this);
        var field_key               = this_update_button.attr('data-field-key');
        var this_update_button_html = this_update_button.html();

        this_update_button.text('Updating...').attr('disabled','disabled');

        var data = $('#field_' + field_key).serialize();

        $.post('/admin/template/', data, function() {
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

    $('button[name="archive"].field_archive').on('click', function(e) {
        e.preventDefault();

        var status = confirm('Are you sure? This will remove the field and all data stored for this field');

        if(status === false) {
            return false;
        }

        var this_archive_button = $(this);
        var field_key           = this_archive_button.attr('data-field-key');
        var this_row            = $('tr#row_' + field_key);

        this_archive_button.attr('disabled','disabled');

        $.get('/admin/template/?archive=' + field_key, function() {
            this_archive_button
                .queue(function(e) {
                    this_row.hide();
                    e();
                });
        });
    });
});