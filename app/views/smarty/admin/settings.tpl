<p class="red_text">{$error}</p>
<div class="clear_both">&#160;</div>

<div class="one-third_right">
    <h3>Categories:</h3>
    <div class="gray_background slight_padding">
        <ul>
            {foreach from=$setting_categories item=setting_category}
                <li>
                    <a href="#{$setting_category.key}">
                        <h3>{$setting_category.category}</h3>
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
</div>
<div class="two-thirds_left">
    <table class="no_border">
        <thead>
        <tr>
            <th class="no_border">
                <h2>
                    Key
                </h2>
            </th>
            <th class="no_border">
                <h2>
                    Value
                </h2>
            </th>
            <th class="no_border">
                {if $edit_settings}
                    <h2>
                        Action
                    </h2>
                {/if}
            </th>
        </tr>
        </thead>
        <tbody>
            {include file="admin/settings/settings_categories.tpl"}
        </tbody>
    </table>

    <div class="clear_both">&#160;</div>
</div>

<div class="clear_both">&#160;</div>

<h3>See also:</h3>
<ul>
    <li>
        <a href="{$full_web_url}/admin/users/">Modify users (manage accounts and users' roles)</a>
    </li>
    <li>
        <a href="{$full_web_url}/admin/pages/">Edit pages and role access per page</a>
    </li>
    <li>
        <a href="{$full_web_url}/admin/settings/">Manage site settings and role association</a>
    </li>
</ul>
