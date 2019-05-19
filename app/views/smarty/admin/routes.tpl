{include file="font-awesome-cdn-link-href.tpl"}

<p class="red_text">{$error}</p>

<div class="clear_both">&#160;</div>
<p>
    Note: All route regex patterns are automatically wrapped with <code>^</code> and <code>$</code>
</p>
<div>
    <table class="no_border">
        <thead>
        <tr>
            <th>
                Regex Pattern
            </th>
            <th>
                Destination Controller
            </th>
            <th>
                Description
            </th>
            {if $edit_routes || $archive_routes}
                <th>
                    Action
                </th>
            {/if}
        </tr>
        </thead>
        <tbody>
        {foreach from=$all_routes item=route}
            <tr id="row_{$route.uid}">
                {if $edit_routes}
                    <form id="route_{$route.uid}" method="post" action="{$full_web_url}/admin/routes/">
                {/if}
                <td>
                    <input type="hidden" name="uid" value="{$route.uid}" />

                    {if $edit_routes}
                        <input type="text" name="regex_pattern" value="{$route.regex_pattern}" />
                    {else}
                        {$route.regex_pattern}
                    {/if}
                </td>
                <td>
                    {if $edit_routes}
                        <input type="text" name="destination_controller" value="{$route.destination_controller}" />
                    {else}
                        {$redir.destination_controller}
                    {/if}
                </td>
                <td>
                    {if $edit_routes}
                        <input name="description" type="text" value="{$route.description}" />
                    {else}
                        {$route.description}
                    {/if}
                </td>
                {if $edit_routes || $archive_routes}
                    <td>
                        {if $edit_routes}
                            <button class="uri_route_update" data-route-key="{$route.uid}" name="update" type="button">Update</button>
                        {/if}
                        {if $archive_routes}
                            <button class="uri_route_archive archive" data-route-key="{$route.uid}" name="archive" type="button">Archive</button>
                        {/if}
                    </td>
                    </form>
                {/if}
            </tr>
        {/foreach}
        </tbody>
    </table>
    <div class="clear_both">&#160;</div>

    {if $add_routes}
        <div>
            <h2>
                New Route
            </h2>
            <form id="route_new" method="post" action="{$full_web_url}/admin/routes/">
                <table class="no_border">
                    <thead>
                    <th>
                        Regex Pattern
                    </th>
                    <th>
                        Destination Controller
                    </th>
                    <th>
                        Description
                    </th>
                    <th>
                        {* Action (No Header) *}
                    </th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <input type="text" name="regex_pattern" />
                        </td>
                        <td>
                            <input type="text" name="destination_controller" />
                        </td>
                        <td>
                            <input type="text" name="description" />
                        </td>
                        <td>
                            <button class="uri_route_update" name="new" type="submit">Submit new route &#187;</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    {/if}
</div>