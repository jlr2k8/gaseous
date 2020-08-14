$(document).ready( function() {
    $('select[name="redirect_uri_uid"]').on('change', function(){
       if ($(this).find('option:selected').val() == 'custom') {
           $('#custom_uri_input_container').show(function() {
                $('#custom_uri').attr('required', 'required');
           });
       } else {
           $('#custom_uri_input_container').hide(function() {
               $('#custom_uri').removeAttr('required').val('');
           });
       }
    });

    $('button[name="update"].uri_redirect_update').on('click', function(e) {
        e.preventDefault();

        var this_update_button      = $(this);
        var redir_key               = this_update_button.attr('data-redir-key');
        var this_update_button_html = this_update_button.html();

        this_update_button.text('Updating...').attr('disabled','disabled');

        var data = $('#redir_' + redir_key).serialize() + '&update';

        $.post('/admin/redirects/', data, function() {
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

    $('button[name="archive"].uri_redirect_archive').on('click', function(e) {
        e.preventDefault();

        var status = confirm('Are you sure?');

        if(status === false) {
            return false;
        }

        var this_archive_button = $(this);
        var redir_key           = this_archive_button.attr('data-redir-key');
        var this_row            = $('tr#row_' + redir_key);

        this_archive_button.attr('disabled','disabled');

        $.get('/admin/redirects/?archive=' + redir_key, function() {
            this_archive_button
                .queue(function(e) {
                    this_row.hide();
                    e();
                });
        });
    });
});