$(document).ready( function() {
    $('.uri_preview_setter').on('change', function() {
        var new_val = null;

        if ($(this).is('select')) {
            new_val = $(this).find('option:selected').text();
        } else if ($(this).is('input[type="text"]')) {
            new_val = $(this).val().replace(/[^a-z0-9\-]/g, '');
        }

        var original_uri_input_border   =$('input[name="this_uri_piece"]').css('border');
        var original_preview_hint_color = $('#preview_hint').css('color');

        if ($(this).val() != new_val && $(this).is('input[type="text"]')) {
            $('input[name="this_uri_piece"]').css('border','2px solid red');
            $('#preview_hint')
                .text('URI must be constructed with lowercase alphanumeric characters and dashes only.')
                .css('color', 'red');
        } else {
            $('input[name="this_uri_piece"]').css('border', original_uri_input_border);
            $('#preview_hint').text('').css('color', original_preview_hint_color);
        }

        $('#preview_full_parent_uri').text($('select[name="parent_page_uri"] option:selected').text());
        $('#preview_this_url_piece').text($('input[name="this_uri_piece"]').val());
    });

    $('#submit_page_iteration').on('click', function() {
        var new_cms_content         = ckeditor.getData();
        var this_submit_button      = $('#submit_page_iteration');
        var this_submit_button_html = this_submit_button.html();
        var data                    = $('#form *').serializeArray(), data_obj = {};

        data.push({
            name: "body",
            value: new_cms_content
        });

        $(data).each(function(key, val) {
            data_obj[val.name] = val.value;
        });

        data_str = $.param(data);

        $.post('/admin/pages/', data_str, function(response) {
            this_submit_button
                .attr('disabled', 'disabled')
                .html('Updating...')
                .delay(1000)
                .queue(function(e) {
                    try {
                        var result = $.parseJSON(response);

                        if(result.status == 'success') {
                            this_submit_button
                                .html('Updated &#160;<i class="fas fa-check"></i>')

                            hideSubmitError();

                            window.location.replace('/admin/pages/');
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

            var page_master_uid = $('input[name="page_master_uid"]').val();

            if (page_master_uid != '') {
                $('#page_iterations_wrapper div').fadeOut(function() {
                    $(this).load('/services/display_page_iterations.php?page_master_uid=' + page_master_uid, function() {
                        $(this).fadeIn();
                    });
                })
            }
        });
    });

    function showSubmitError(error_text)
    {
        if (error_text.length == 0) {
            error_text = 'There was an error';
        }

        $('#submit_error').removeClass('display_none');
        $('#error_paragraph').html(error_text);

        return true;
    }

    function hideSubmitError()
    {
        $('#submit_error').addClass('display_none');
        $('#error_paragraph').html(null);

        return true;
    }

    $('#archive_page').on('click', function(e) {
        var status = confirm('Are you sure?');

        if(status === false) {
            return false;
        }

        var this_submit_button      = $('#archive_page');
        var this_submit_button_html = this_submit_button.html();
        var data                    = $('#form *').serialize() + '&archive';

        $.post('/admin/pages/', data, function(response) {
            this_submit_button
                .attr('disabled', 'disabled')
                .html('Kaboom!&#160;<i class="fas fa-check"></i>')
                .delay(1000)
                .queue(function(e) {
                    this_submit_button.html(this_submit_button_html)
                        .removeAttr('disabled');
                    e();
                    //console.log(response);
                });
           window.location.replace('/admin/pages/');
        });
    });

    $('#is_public').on('change', function() {
        if ($(this).is(':checked')) {
            $('.page_roles').each(function() {
                $(this).prop('checked', false).attr('disabled', 'disabled');
            });
        } else {
            $('.page_roles').each(function() {
                $(this).removeAttr('disabled');
            });
        }
    });

    checkPageRoles();

    $('.page_roles').on('change', function() {
        $('#is_public').prop('checked', false);
        checkPageRoles();
    });

    function checkPageRoles() {
        var roles_checked = false;

        $('.page_roles').each(function() {
            if ($(this).is(':checked')) {
                roles_checked = true;
                return false;
            }
        });

        if (roles_checked === false) {
            $('#is_public').prop('checked', true);

            $('.page_roles').each(function() {
                $(this).prop('checked', false).attr('disabled', 'disabled');
            });
        }

        return true;
    }
});