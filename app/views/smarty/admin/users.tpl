<p class="red_text">{$error}</p>

{if empty($my_username)}
    <div class="red_border thick_border solid_border margin_on_bottom">
        <p class="red_text slight_margin">
            Note: You are currently not logged in. If an account in the list belongs to you, archiving it will relinquish your login.<br />
            Consider assigning this page to an administrative role.
        </p>
    </div>
{/if}

<table class="no_border">
    <thead>
        <tr>
            <th class="no_border">Username</th>
            <th class="no_border">Name and Email</th>
            <th class="no_border">Assigned Roles</th>
            <th class="no_border">Actions</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$accounts item=account}
            <tr>
                <form id="account_{$account.username}" method="post" action="{$full_web_url}/admin/users/">
                    <td class="no_border">
                        <input type="hidden" name="username" value="{$account.username}" />
                        {$account.username}
                    </td>
                    <td class="no_border">
                        {if $edit_users}
                            <div class="floating_form_label_and_input">
                                <label>First</label>
                                <input required="required" type="text" name="firstname" value="{$account.firstname}" />
                            </div>

                            <div class="floating_form_label_and_input">
                                <label>Last</label>
                                <input required="required" type="text" name="lastname" value="{$account.lastname}" />
                            </div>

                            <div class="floating_form_label_and_input">
                                <label>Email</label>
                                <input required="required" type="text" name="email" value="{$account.email}" />
                            </div>
                        {else}
                            <h3>{$account.lastname}, {$account.firstname} ({$account.email})</h3>
                        {/if}
                    </td>
                    <td class="no_border vertical_align_top">
                        <table class="table_layout_auto margin_on_top">
                            {foreach from=$roles item=role}
                                <tr>
                                    <td class="no_border" title="{$role.description}">
                                        <label>
                                            {$role.role_name}
                                        </label>
                                    </td>
                                    <td class="no_border">
                                        <input type="checkbox" name="account_roles[]" value="{$role.role_name}"
                                            {if in_array($role.role_name, $account.account_roles)}
                                                checked="checked"
                                            {/if}
                                            {if !$edit_users}
                                                disabled="disabled"
                                            {/if}
                                        />
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                    </td>
                    <td class="no_border">
                        <button class="user_update" data-username="{$account.username}" name="update" type="button">Update</button>
                        {if $my_username != $account.username && $archive_users}
                            <input class="archive" onclick="{literal}var status=confirm('Are you sure?'); if(status === true){return true;}else{return false};{/literal}" name="archive" type="submit" value="Archive" />
                        {/if}
                    </td>
                </form>
            </tr>
        {/foreach}
    </tbody>
</table>

<div class="clear_both">&#160;</div>

<p class="margin_on_top clear_both">
    Provide users with a registration link:<br />
    <a class="break_word" href="{$full_web_url}/register/?access_code={$access_code}">
        {$full_web_url}/register/?access_code={$access_code}
    </a>
    (or
    <a href="{$full_web_url}/admin/settings/#registration_access_code">
        update
    </a>
    the access code)
</p>