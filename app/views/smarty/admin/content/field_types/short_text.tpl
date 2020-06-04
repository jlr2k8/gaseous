<div>
    <label>
        {$content_body_field_label}
    </label>
    <input
        type="text"
        name="{$template_token}"
        id="{$template_token}"
        {if !empty($value)}value="{$value}"{/if}
        {if !empty($content_body_field_description)}placeholder="{$content_body_field_description}"{/if}
    />
</div>