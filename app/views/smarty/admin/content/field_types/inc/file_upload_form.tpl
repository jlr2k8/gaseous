<form method="post" action="{$full_web_url}/controllers/services/iframe_upload.php?content_field_uid={$content_field_uid}&content_iteration_uid={$content_iteration_uid}"  enctype="multipart/form-data">
    <input
            type="file"
            name="{$template_token}"
            id="{$template_token}"
            {if !empty($content_body_field_description)}placeholder="{$content_body_field_description}"{/if}
            {if !empty($properties)}
                {foreach from=$properties item=property}
                    {if !empty($property.property) && $property.property == 'file_upload_allowed_extensions'}
                        accept="{$property.value}"
                    {/if}
                {/foreach}
            {/if}
    />
    {if !empty($properties)}
        {foreach from=$properties item=property}
            {if !empty($property.property) && $property.property == 'file_upload_allowed_size'}
                {assign var=filesize_range value=explode(',', $property.value)}
                <br /><span class="small_text green_text">Allowed file size: {$filesize_range.0/1000000} - {$filesize_range.1/1000000} MB</span>
            {/if}
            {if !empty($property.property) && $property.property == 'file_upload_allowed_extensions'}
                <br /><span class="small_text green_text">Allowed file types: {$property.value}</span>
            {/if}
        {/foreach}
    {/if}
    <input type="submit" value="Upload" />
</form>