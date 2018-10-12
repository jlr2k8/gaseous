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