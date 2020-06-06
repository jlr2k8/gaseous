<form id="setting_{$setting.key}" method="post" action="{$full_web_url}/admin/settings/">
    {if in_array('codemirror', $setting.properties)}
        <td colspan="3">
            <a id="{$setting.key}"></a>
            <p>
                {$setting.key_display}
                {if !empty($setting.description)}
                    <br />
                    <span class="caption"><i class="fas fa-question-circle"></i> {$setting.description}</span>
                {/if}
            </p>
            <input type="hidden" name="key" value="{$setting.key}" />
            <input type="hidden" name="codemirror" value="true" />
            <textarea id="value_{$setting.key}" name="value">{htmlspecialchars($setting.value, ENT_SUBSTITUTE)}</textarea>
            {if $edit_settings}
                <button class="setting_update" data-setting-key="{$setting.key}" name="update" type="button">Update</button>
            {/if}

            {$codemirror->cdn}
            {$codemirror->textarea("value_{$setting.key}")}
        </td>
    {else}
        <td class="no_border">
            <a id="{$setting.key}"></a>
            <input type="hidden" name="key" value="{$setting.key}" />
            <p>
                {$setting.key_display}
                {if !empty($setting.description)}
                    <br />
                    <span class="caption"><i class="fas fa-question-circle"></i> {$setting.description}</span>
                {/if}
            </p>
        </td>
        <td class="no_border">
            {if in_array('boolean', $setting.properties)}
                {include file="admin/settings/boolean_settings.tpl"}
            {else}
                <input type="text" name="value" value="{$setting.value}" {if !$edit_settings}disabled="disabled"{/if} />
            {/if}
        </td>
        <td class="no_border">
            {if $edit_settings}
                <button class="setting_update" data-setting-key="{$setting.key}" name="update" type="button">Update</button>
            {/if}
        </td>
    {/if}
</form>