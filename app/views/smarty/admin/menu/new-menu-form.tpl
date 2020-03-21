<h3 class="slight_padding">
    Create a new menu item:
</h3>
<br />
<form id="new_menu_item" method="post">
    <table>
        <tbody>
        <tr>
            <th>
                {$heading_label}
            </th>
            <td>
                <input type="text" name="label" required="required" />
            </td>
        </tr>
        <tr>
            <th>
                {$heading_destination}
            </th>
            <td>
                <select name="menu_uri_uid" required="required">
                    <option></option>
{*                    <option value="custom">-- Custom --</option>*}
                    {foreach from=$all_uris item=uri}
                        <option value="{$uri.uid}">
                            {$uri.uri}
                        </option>
                    {/foreach}
                </select>
{*                <div id="custom_uri_input_container">
                    {$full_web_url}/<input id="custom_uri" type="text" name="custom_uri" />
                </div>*}
            </td>
        </tr>
        <tr>
            <th>
                {$heading_nofollow}
            </th>
            <td>
                <select name="nofollow" required="required">
                    <option value="false">Yes</option>
                    <option value="true">No</option> {* rel="nofollow" *}
                </select>
            </td>
        </tr>
        <tr>
            <th>
                {$heading_target}
            </th>
            <td>
                <select name="target" required="required">
                    <option value="_self">Same window</option>
                    <option value="_blank">New window</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>
                {$heading_class}
            </th>
            <td>
                <input type="hidden" required="required" name="new" value="true" />
                <input name="class" type="text" />
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Create Menu Link &#187;" />
                <p class="red_text">
                    {$error}
                </p>
            </td>
        </tr>
        </tbody>
    </table>
</form>