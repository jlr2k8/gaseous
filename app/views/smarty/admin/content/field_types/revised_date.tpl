<div>
    <label>
        {$content_body_field_label}
    </label>
    <input
            type="text"
            name="{$template_token}"
            id="{$template_token}"
            value="{$value}"
            {if !empty($content_body_field_description)}placeholder="{$content_body_field_description}"{/if}
            disabled="disabled"
    />
</div>