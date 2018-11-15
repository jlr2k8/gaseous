{include file="font-awesome-cdn-link-href.tpl"}

{if $add_pages}
    <div class="gray_background slight_padding">
        <h2>
            <a href="{$full_web_url}/admin/pages/?new_page=true">
                + Add a new page
            </a>
        </h2>
    </div>
{/if}

<table>
    {if count($active_pages)}
        <thead>
            <tr>
                <th colspan="4">
                    Active Pages
                </th>
            </tr>
            {include file="admin/pages/row_header.tpl"}
        </thead>
        <tbody>
            {foreach from=$active_pages item=page}
                {include file="admin/pages/row.tpl"}
            {/foreach}
        </tbody>
    {/if}
    {if count($inactive_pages)}
        <thead>
            <tr>
                <th colspan="4">
                    Inactive Pages
                </th>
            </tr>
            {include file="admin/pages/row_header.tpl"}
        </thead>
        <tbody>
            {foreach from=$inactive_pages item=page}
                 {include file="admin/pages/row.tpl"}
            {/foreach}
        </tbody>
    {/if}
</table>