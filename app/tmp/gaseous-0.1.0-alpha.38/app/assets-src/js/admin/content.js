$(document).ready( function() {
    $('#submit_content_iteration').on('click', function() {
        var new_cms_content         = ckeditor.getData();
        var this_submit_button      = $('#submit_content_iteration');
        var this_submit_button_html = this_submit_button.html();
        var data                    = $('#form *').serializeArray(), data_obj = {};
        var wyswyg_name             = $('textarea[data-is-wyswyg="true"]').attr('name');

        if (typeof wyswyg_name !== "undefined" && typeof wyswyg_name !== false) {
            data.push({
                name: wyswyg_name,
                value: new_cms_content
            });
        }

        $(data).each(function(key, val) {
            data_obj[val.name] = val.value;
        });

        var data_str = $.param(data);

        $.post('/admin/content/', data_str, function(response) {
            this_submit_button
                .attr('disabled', 'disabled')
                .html('Updating...')
                .delay(1000)
                .queue(function(e) {
                    try {
                        var result = $.parseJSON(response);

                        if(result.status == 'success') {
                            this_submit_button
                                .html('Updated &#160;<i class="fas fa-check"></i>');

                            hideSubmitError();

                            window.location.replace('/admin/content/');
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

            var content_uid = $('input[name="content_uid"]').val();

            if (content_uid != '') {
                $('#content_iterations_wrapper div').fadeOut(function() {
                    $(this).load('/controllers/services/display_content_iterations.php?content_uid=' + content_uid, function() {
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

    $('#archive_content').on('click', function(e) {
        var status = confirm('Are you sure?');

        if(status === false) {
            return false;
        }

        var this_submit_button      = $('#archive_content');
        var this_submit_button_html = this_submit_button.html();
        var data                    = $('#form *').serialize() + '&archive';

        $.post('/admin/content/', data, function(response) {
            this_submit_button
                .attr('disabled', 'disabled')
                .html('Kaboom!&#160;<i class="fas fa-check"></i>')
                .delay(1000)
                .queue(function(e) {
                    this_submit_button.html(this_submit_button_html)
                        .removeAttr('disabled');
                    e();
                });
           window.location.replace('/admin/content/');
        });
    });

    $('#is_public').on('change', function() {
        if ($(this).is(':checked')) {
            $('.content_roles').each(function() {
                $(this).prop('checked', false).attr('disabled', 'disabled');
            });
        } else {
            $('.content_roles').each(function() {
                $(this).removeAttr('disabled');
            });
        }
    });

    checkPageRoles();

    $('.content_roles').on('change', function() {
        $('#is_public').prop('checked', false);
        checkPageRoles();
    });

    function checkPageRoles() {
        var roles_checked = false;

        $('.content_roles').each(function() {
            if ($(this).is(':checked')) {
                roles_checked = true;
                return false;
            }
        });

        if (roles_checked === false) {
            $('#is_public').prop('checked', true);

            $('.content_roles').each(function() {
                $(this).prop('checked', false).attr('disabled', 'disabled');
            });
        }

        return true;
    }

    $('select#new_content_selector').on('change', function() {
        var new_content_type = $(this).find('option:selected').val();
        window.location.replace('/admin/content/' + new_content_type);
    });
});