<p class="red_text">{$error}</p>
<div class="clear_both">&#160;</div>
<table class="no_border">
    <thead>
        <tr>
            <th class="no_border">Key</th>
            <th class="no_border">Description</th>
            {if $edit_roles || $archive_roles}
                <th class="no_border">Action</th>
            {/if}
        </tr>
    </thead>
    <tbody>
        {foreach from=$roles item=role}
            <tr>
                <form id="role_{$role.role_name}" method="post" action="{$full_web_url}/admin/roles/">
                    <td class="no_border">
                        <input type="hidden" name="old_role_name" value="{$role.role_name}" />
                        {if $edit_roles}
                            <input type="text" name="role_name" value="{$role.role_name}" />
                        {else}
                            {$role.role_name}
                        {/if}
                    </td>
                    <td class="no_border">
                        {if $edit_roles}
                            <input type="text" name="description" value="{$role.description}" />
                        {else}
                            {$role.description}
                        {/if}
                    </td>
                    {if $edit_roles || $archive_roles}
                        <td class="no_border">
                            {if $edit_roles}
                                <button class="role_update" data-role-name="{$role.role_name}" name="update" type="button">Update</button>
                            {/if}

                            {if $archive_roles}
                                <input class="archive" onclick="{literal}var status=confirm('Are you sure?'); if(status === true){return true;}else{return false};{/literal}" name="archive" type="submit" value="Archive" />
                            {/if}
                        </td>
                    {/if}
                </form>
            </tr>
        {/foreach}
    </tbody>
</table>

<div class="clear_both">&#160;</div>

{if $add_roles}
    <h3>Create a new role</h3>
    <form method="post" action="{$full_web_url}/admin/roles/">
        <table class="no_border">
            <thead>
            <tr>
                <th class="no_border">Role Name</th>
                <th class="no_border">Description</th>
                <th class="no_border">&#160;</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="no_border">
                        <input required="required" type="text" name="role_name" placeholder="admin, moderator, basic-user, etc." value="{$role_name}" />
                    </td>
                    <td class="no_border">
                        <input type="text" name="description" value="{$description}" />
                    </td>
                    <td class="no_border">
                        <input name="insert" type="submit" value="Create &#187;" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
{/if}