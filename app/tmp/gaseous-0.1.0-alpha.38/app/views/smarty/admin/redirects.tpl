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
                        <input type="hidden" name="update" />
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
                <input type="hidden" name="new" />
                <table class="no_border">
                    <thead>
{*                        <th>*}
{*                            URI*}
{*                        </th>*}
{*                        <th>*}
{*                            Destination*}
{*                        </th>*}
{*                        <th>*}
{*                            HTTP Status Code*}
{*                        </th>*}
{*                        <th>*}
{*                            Description*}
{*                        </th>*}
{*                        <th>*}
{*                            *}{* Action (No Header) *}
{*                        </th>*}
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <h3>
                                    URI:
                                </h3>
                                <select name="redirect_uri_uid">
                                    <option></option>
                                    <option value="custom">-- Custom --</option>
                                    {foreach from=$all_unused_uris item=uri}
                                        <option value="{$uri.uid}">
                                            {$uri.uri}
                                        </option>
                                    {/foreach}
                                </select>
                                <div id="custom_uri_input_container">
                                    {$full_web_url}/<input id="custom_uri" type="text" name="custom_uri" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3>
                                    Destination:
                                </h3>
                                <input type="text" name="destination_url" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3>
                                    HTTP Status Code:
                                </h3>
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
                        </tr>
                        <tr>
                            <td>
                                <h3>
                                    Description:
                                </h3>
                                <input type="text" name="description" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button class="uri_redirect_update" type="submit">Submit new redirect rule &#187;</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    {/if}
</div>