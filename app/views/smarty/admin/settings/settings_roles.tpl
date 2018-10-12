<div class="settings_roles_container" id="settings_roles_container_{$setting.key}">
    <a href="#" id="specify_roles_{$setting.key}" class="specify_roles" title="Choose which role to limit 'true' to" data-key="{$setting.key}">Specify Roles</a>
    <div class="roles_checkbox_container" id="roles_checkbox_container_{$setting.key}" data-key="{$setting.key}">
        <span class="restrict_tip" title="Choose which role to limit 'true' to">Restrict <em>true</em> for only these roles:</span>
        <table class="table_layout_auto margin_on_top">
            {foreach from=$roles item=role}
                <tr>
                    <td class="no_border" title="{$role.description}">
                        <label>
                            {$role.role_name}
                        </label>
                    </td>
                    <td class="no_border">
                        <input {if !$edit_settings}disabled="disabled"{/if} type="checkbox" name="settings_roles[]" value="{$role.role_name}" id="{$setting.key}_{$role.role_name}" data-key="{$setting.key}"
                            {if in_array($role.role_name, $setting.roles)}
                                checked="checked"
                            {/if}
                        />
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>