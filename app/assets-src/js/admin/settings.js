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