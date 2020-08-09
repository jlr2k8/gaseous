<div>
    <div class="ck_toolbar_buffer">
        &#160;
    </div>
    <div>
        <label>
            {$content_body_field_label}
        </label>
        <textarea data-is-wyswyg="true" name="{$template_token}" id="{$template_token}">{if !empty($value)}{$value}{/if}</textarea>
        {$ck_editor->cdn}
        {$ck_editor->inline("{$template_token}")}
    </div>
    <div class="ck_toolbar_buffer">
        &#160;
    </div>
</div>