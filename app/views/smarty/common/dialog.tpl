<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.12.4.js"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(document).ready(function() {
        $('.site_dialog').dialog({
            create: function(event, ui) {
                $(event.target).parent().css('position', 'fixed');
            },
            position: {
                my: "right bottom",
                at: "right bottom",
                of: window
            }
        });
    });
</script>