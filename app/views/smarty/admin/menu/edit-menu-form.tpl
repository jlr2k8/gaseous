<h3 class="slight_padding">
    Edit a current menu item:
</h3>
<br />
<table>
    <thead>
        <tr>
            <th>
                {$heading_label}
            </th>
            <th>
                {$heading_destination}
            </th>
            <th>
                {$heading_nofollow}
            </th>
            <th>
                {$heading_target}
            </th>
            <th>
                {$heading_class}
            </th>
            <th>
                {* Submit *}
            </th>
        </tr>
    </thead>
        <tbody>
            {foreach from=$menu_items item=menu_item}
                <tr>
                    <form method="post" id="edit_menu_form_{$menu_item.uid}">
                        <td>
                            <input name="label" type="text" required="required" value="{$menu_item.label}" />
                        </td>
                        <td>
                            <select name="uri_uid" required="required">
                                {foreach from=$all_uris item=uri}
                                    <option value="{$uri.uid}" {if $uri.uid == $menu_item.uri_uid}selected="selected"{/if}>
                                        {$uri.uri}
                                    </option>
                                {/foreach}
                            </select>
                        </td>
                        <td>
                            <select name="nofollow" required="required">
                                <option value="false" {if $menu_item.nofollow == 'false'}selected="selected"{/if}>Yes</option>
                                <option value="true" {if $menu_item.nofollow == 'true'}selected="selected"{/if}>No</option> {* rel="nofollow" *}
                            </select>
                        </td>
                        <td>
                            <select name="target" required="required">
                                <option value="_self" {if $menu_item.target == '_self'}selected="selected"{/if}>Same window</option>
                                <option value="_blank" {if $menu_item.target == '_blank'}selected="selected"{/if}>New window</option>
                            </select>
                        </td>
                        <td>
                            <input name="class" type="text" value="{$menu_item.class}" />
                        </td>
                        <td>
                            <input type="hidden" name="update" value="true" required="required" />
                            <input type="hidden" name="uid" value="{$menu_item.uid}" required="required" />
                            <input type="hidden" name="parent_uid" value="{$menu_item.parent_uid}" required="required" />
                            <input type="hidden" name="sort_order" value="{$menu_item.sort_order}" required="required" />
                            <input type="submit" value="Submit &#187;" />
                        </td>
                    </form>
                </tr>
            {/foreach}
        </tbody>
</table>