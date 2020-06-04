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

<table>
    {if count($active_pages)}
        <thead>
            <tr>
                <th colspan="4">
                    Active Content
                </th>
            </tr>
            {include file="admin/content/row_header.tpl"}
        </thead>
        <tbody>
            {foreach from=$active_pages item=page}
                {include file="admin/content/row.tpl"}
            {/foreach}
        </tbody>
    {/if}
    {if count($inactive_pages)}
        <thead>
            <tr>
                <th colspan="4">
                    Inactive Content
                </th>
            </tr>
            {include file="admin/content/row_header.tpl"}
        </thead>
        <tbody>
            {foreach from=$inactive_pages item=page}
                 {include file="admin/content/row.tpl"}
            {/foreach}
        </tbody>
    {/if}
</table>