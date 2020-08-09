<div>
    <label>
        {$content_body_field_label}
    </label>
    <br />
    <iframe class="file_upload" src="/controllers/services/iframe_upload.php?content_field_uid={$uid}&content_iteration_uid={$content_iteration_uid}"></iframe>
</div>
<input type="hidden" name="{$template_token}" id="{$template_token}" {if !empty($value)}value="{$value}"{/if} />

