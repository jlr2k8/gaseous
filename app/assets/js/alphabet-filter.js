$(document).ready(function() {
    $('.alphabet_filter_letter').on('click', function() {
        var letter = $(this).attr('data-alphabet-letter');

        if (letter == 'reset') {
            $('.sortable > tbody > tr').show();
            $('.link.alphabet_filter_letter.reset').css('visibility', 'hidden');
            $('.link.alphabet_filter_letter').removeClass('bold');
            $('#result_number span.caption').text('');
            $('#alphabet_filter_result_caption').text('');
        } else {
            $('.link.alphabet_filter_letter').removeClass('bold');
            $('.link.alphabet_filter_letter.reset').css('visibility', 'visible');
            $(this).addClass('bold');
            $('.sortable > tbody > tr').filter(function() {
                $(this).toggle($(this).attr('data-alphabet-row-letter').indexOf(letter) == 0);
            });
        }
    });
});