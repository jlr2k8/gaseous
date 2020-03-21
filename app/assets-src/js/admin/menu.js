$(document).ready(function() {

    /*    $('select[name="menu_uri_uid"]').on('change', function(){
        if ($(this).find('option:selected').val() == 'custom') {
            $('#custom_uri_input_container').show(function() {
                $('#custom_uri').attr('required', 'required');
            });
        } else {
            $('#custom_uri_input_container').hide(function() {
                $('#custom_uri').removeAttr('required').val('');
            });
        }
    });*/

    /*$('form#new_menu_item button').on('click', function(e) {
        e.preventDefault();

        var this_submit_button      = $(this);
        var this_submit_button_html = this_update_button.html();

        this_submit_button.text('Please Wait...').attr('disabled','disabled');

        var data = $('form#new_menu_item').serialize();

        $.post('/admin/menu/', data, function(response) {
            this_submit_button
                .html('Done&#160;<i class="fas fa-check"></i>')
                .delay(1000)
                .queue(function(e) {
                    try {
                        var result = $.parseJSON(response);

                        if(result.status == 'success') {
                            this_submit_button
                                .html('Updated &#160;<i class="fas fa-check"></i>');
                            window.location.replace('/admin/menu/');
                        } else {
                            this_submit_button
                                .html('Oops!')
                                .removeAttr('disabled');

                            showSubmitError(result.status);
                        }
                    } catch (e) {
                        this_submit_button
                            .html('Oops!')
                            .removeAttr('disabled');

                        showSubmitError(response);
                    }

                    this_submit_button
                        .html(this_submit_button_html)
                        .removeAttr('disabled');

                    e();
                });
        });
    });*/
});