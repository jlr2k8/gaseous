{include file="font-awesome-cdn-link-href.tpl"}

{if $add_content}
    <div class="gray_background slight_padding">
        <select id="new_content_selector" name="content_body_type_id">
            <option>
                Add Content
            </option>
            {foreach from=$content_body_types item=$cbt}
                <option value="{$cbt.type_id}">{$cbt.label}</option>
            {/foreach}
        </select>
    </div>
{/if}

<div>
    <div>
        <span class="link alphabet_filter_letter home" data-alphabet-letter="home">
            <i class="fas fa-home" data-alphabet-letter="home"></i>
        </span>
        {foreach range('A', 'Z') as $letter}
            <span class="link alphabet_filter_letter" data-alphabet-letter="{$letter}">
                {$letter}
            </span>
        {/foreach}
    </div>
    <p class="caption link alphabet_filter_letter reset" data-alphabet-letter="reset">
        <i class="fas fa-times"></i> Clear Filter
    </p>
</div>

<table id="admin_content" class="sortable">
    {if count($all_pages)}
        <thead>
            {include file="admin/content/row_header.tpl"}
        </thead>
        <tbody>
            {foreach from=$all_pages item=page}
                {include file="admin/content/row.tpl"}
            {/foreach}
        </tbody>
    {/if}
</table>

<script src="{$full_web_url}/assets-src/js/jquery-2.2.4.min.js">&#160;</script>
<script src="{$full_web_url}/assets/js/sorttable.js"></script>
<script src="{$full_web_url}/assets/js/alphabet-filter.js"></script>

{literal}
    <script>
        $(".sortable thead tr th").on('click', function() {
            $(".default_sort_indicator").hide();
        });

        var sortable_table = $(".sortable");

        sorttable.makeSortable(sortable_table);
    </script>
{/literal}
