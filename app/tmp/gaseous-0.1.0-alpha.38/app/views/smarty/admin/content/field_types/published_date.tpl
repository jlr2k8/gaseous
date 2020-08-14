<div>
    <label>
        {$content_body_field_label}
    </label>
    <input
            type="text"
            name="{$template_token}"
            id="{$template_token}"
            {if !empty($value)}value="{date('F d, Y g:i:s A T', $value)}"{else}value="Example: {date('F d, Y g:i:s A T')}"{/if}
            {if !empty($content_body_field_description)}placeholder="{$content_body_field_description}"{/if}
            readonly="readonly"
    />
</div>