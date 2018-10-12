<table class="no_border width_inherit">
    <tr class="no_border no_background">
        <td class="no_border">
            <label>True</label>
        </td>
        <td class="no_border">
            <input {if !$edit_settings}disabled="disabled"{/if} class="boolean_setting_true" data-key="{$setting.key}" type="radio" name="value" value="1"
                {if $setting.value === true}
                    checked="checked"
                {/if}
            />
        </td>
    </tr>
    <tr class="no_border no_background">
        <td class="no_border">
            <label>False</label>
        </td>
        <td class="no_border">
            <input {if !$edit_settings}disabled="disabled"{/if} class="boolean_setting_false" data-key="{$setting.key}" type="radio" name="value" value="0"
                {if $setting.value !== true}
                    checked="checked"
                {/if}
            />
        </td>
    </tr>
</table>

{if $setting.role_based == 'true'}
    {include file="admin/settings/settings_roles.tpl"}
{/if}