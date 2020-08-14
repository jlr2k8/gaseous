{foreach from=$setting_categories item=setting_category}
    <thead>
    <tr>
        <th colspan="3">
            <h3>
                {if empty($setting_category.key)}
                    Miscellaneous
                {else}
                    <a id="{$setting_category.key}"></a>
                    {$setting_category.category}
                {/if}
            </h3>
        </th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$settings item=$setting}
        {if $setting.category_key == $setting_category.key}
            <tr>
                {include file="admin/settings/edit_settings.tpl"}
            </tr>
        {/if}
    {/foreach}
    </tbody>
{/foreach}