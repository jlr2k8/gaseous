{$css_output}
{$css_iterator_output}

<div id="menu_admin">
    <h3 class="slight_padding">
        Site Menu Hierarchy:
    </h3>
    <p>
        Drag and drop a menu item below to re-sort. Each item can be sorted in different orders and as a child menu item to one another as well.
    </p>
    <p>
        Changes are automatically saved on drop.
    </p>
    <div id="rendered_menu">
        {$rendered_menu}
    </div>
</div>

{literal}
    <script src="/assets-src/js/jquery-2.2.4.min.js">&#160;</script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js">&#160;</script>
    <script src="/assets/js/nestedSortable.js">&#160;</script>

    <script>
        $(document).ready(function(){
            $('ul.sortable').nestedSortable({
                handle: 'div',
                items: 'li',
                listType: 'ul',
                toleranceElement: '> div',
                stop: function()
                    {
                        var data = $(this).nestedSortable('serialize');
                        $.post('/admin/menu/', {menu: data, update_sort: 'true'}, function(response) {

                        });
                    }
            });

            $('.archive_menu_item').on('click', function(e) {
                var uid     = $(this).attr('data-uid');
                var status  = confirm('Are you sure? This will also remove child menu items...');

                if(status === false) {
                    return false;
                }

                $.get('/admin/menu/?archive=' + uid, function(response) {
                    window.location.replace('/admin/menu/?load_current_site_menu=true');
                });
            });
        });
    </script>
{/literal}