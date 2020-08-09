<div class="margin_on_top margin_on_bottom">
    <label>
        Enable {$content_body_field_label}
    </label>
    <input
            type="checkbox"
            name="{$template_token}"
            id="{$template_token}"
            value="date('M d, Y g:i:s A T'"
            {if !empty($content_body_field_description)}placeholder="{$content_body_field_description}"{/if}
    />
</div>