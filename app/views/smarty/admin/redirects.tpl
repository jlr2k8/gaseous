{include file="font-awesome-cdn-link-href.tpl"}

<p class="red_text">{$error}</p>

<div class="clear_both">&#160;</div>

<div>
    <table class="no_border">
        <thead>
        <tr>
            <th>
                URI
            </th>
            <th>
                Destination
            </th>
            <th>
                HTTP Status Code
            </th>
            <th>
                Description
            </th>
            {if $edit_uri_redirects || $archive_uri_redirects}
                <th>
                    Action
                </th>
            {/if}
        </tr>
        </thead>
        <tbody>
            {foreach from=$uri_redirects item=redir}
                <tr id="row_{$redir.uri_uid}">
                    {if $edit_uri_redirects}
                        <form id="redir_{$redir.uri_uid}" method="post" action="{$full_web_url}/admin/redirects/">
                    {/if}
                    <td>
                        {$redir.uri}
                        <input type="hidden" name="uri_uid" value="{$redir.uri_uid}" />
                    </td>
                    <td>
                        {if $edit_uri_redirects}
                            <input type="text" name="destination_url" value="{$redir.destination_url}" />
                        {else}
                            {$redir.destination_url}
                        {/if}
                    </td>
                    <td>
                        {if $edit_uri_redirects}
                            <select name="http_status_code">
                                <option>(None)</option>
                                {foreach from=$http_status_codes key=key item=http_status_code}
                                    <option value="{$key}" {if $key == $redir.http_status_code}selected="selected"{/if}>
                                        {$key} {$http_status_code}
                                    </option>
                                {/foreach}
                            </select>
                        {else}
                            {$redir.destination_url}
                        {/if}
                    </td>
                    <td>
                        {if $edit_uri_redirects}
                            <input name="description" type="text" value="{$redir.description}" />
                        {else}
                            {$redir.description}
                        {/if}
                    </td>
                    {if $edit_uri_redirects || $archive_uri_redirects}
                        <td>
                            {if $edit_uri_redirects}
                                <button class="uri_redirect_update" data-redir-key="{$redir.uri_uid}" name="update" type="button">Update</button>
                            {/if}
                            {if $archive_uri_redirects}
                                <button class="uri_redirect_archive archive" data-redir-key="{$redir.uri_uid}" name="archive" type="button">Archive</button>
                            {/if}
                        </td>
                        </form>
                    {/if}
                </tr>
            {/foreach}
        </tbody>
    </table>
    <div class="clear_both">&#160;</div>

    {if $add_uri_redirects}
        <div>
            <h2>
                New Redirect Rule
            </h2>
            <form id="redir_new" method="post" action="{$full_web_url}/admin/redirects/">
                <table class="no_border">
                    <thead>
                        <th>
                            URI
                        </th>
                        <th>
                            Destination
                        </th>
                        <th>
                            HTTP Status Code
                        </th>
                        <th>
                            {* Action (No Header) *}
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="uri_uid">
                                    <option></option>
                                    {foreach from=$all_unused_uris item=uri}
                                        <option value="{$uri.uid}">
                                            {$uri.uri}
                                        </option>
                                    {/foreach}
                                </select>
                            </td>
                            <td>
                                <input type="text" name="destination_url" />
                            </td>
                            <td>
                                <select name="http_status_code">
                                    <option>
                                        (None)
                                    </option>
                                    {foreach from=$http_status_codes key=key item=http_status_code}
                                        <option value="{$key}">
                                            {$key} {$http_status_code}
                                        </option>
                                    {/foreach}
                                </select>
                            </td>
                            <td>
                                <button class="uri_redirect_update" data-setting-key="{$redir.uri_uid}" name="new" type="submit">Submit new redirect rule &#187;</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        {/if}
    </div>