$(window).on('load', function() {
    $.post('/css-preview-check/', function(data) {
        if(data == '1') {
            $('body').prepend('<div id="site_admin_banner_message">You are currently previewing custom CSS. To exit the preview, <a href="/admin/css/">return to the CSS admin page</a> and choose "Exit Preview"</div>');
        }
    });
});
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
    })
});
$(document).ready( function() {
    $('button[name="update"].role_update').on('click', function(e) {
        e.preventDefault();
        var role_name = $(this).attr('data-role-name');

        var this_update_button = $(this);
        var this_update_button_html = this_update_button.html();

        this_update_button.text('Updating...').attr('disabled','disabled');

        var data = $('#role_' + role_name).serialize() + '&update';

        $.post('/admin/roles/', data, function() {
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
$(document).ready( function() {
    $('button[name="update"].setting_update').on('click', function(e) {
        e.preventDefault();

        var setting_key = $(this).attr('data-setting-key');

        var this_update_button = $(this);
        var this_update_button_html = this_update_button.html();

        this_update_button.text('Updating...').attr('disabled','disabled');

        var data = $('#setting_' + setting_key).serialize() + '&update';

        $.post('/admin/settings/', data, function() {
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

    $('.roles_checkbox_container').each(function() {
        var setting_key = $(this).attr('data-key');
        check_if_any_are_checked(setting_key);
    });

    $('.specify_roles').on('click', function(e) {

        e.preventDefault();

        var setting_key = $(this).attr('data-key');

        $(this).hide(0, function() {
            $('#roles_checkbox_container_' + setting_key).show(0);
        });

    });

    $('.boolean_setting_true').on('change click', function() {

        var setting_key = $(this).attr('data-key');

        $('#roles_checkbox_container_' + setting_key + ' input[type="checkbox"]').each(function() {
            $(this).prop('checked', true);
        });

    });

    $('.boolean_setting_false').on('change click', function() {

        var setting_key = $(this).attr('data-key');

        $('#roles_checkbox_container_' + setting_key + ' input[type="checkbox"]').each(function() {
            $(this).removeAttr('checked');
        });

        $('#roles_checkbox_container_' + setting_key).hide();
        $('#specify_roles_' + setting_key).show();
    });

    function check_if_any_are_checked(setting_key)
    {
        var any_checked = false;
        $('#roles_checkbox_container_' + setting_key + ' input[type="checkbox"]').each(function() {
            if ($(this).is(':checked')) {
                any_checked = true;
            }
        });

        if (any_checked === false) {

            $('#roles_checkbox_container_' + setting_key).hide();
            $('#specify_roles_' + setting_key).show();
        } else {
            $('#specify_roles_' + setting_key).hide();
        }
    }
});
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