<form id="file_uploader" action="" method="post" enctype="multipart/form-data">
    <p>
        <label>
            Upload a file to <code>{\Settings::value('upload_root')}</code>
        </label>
    </p>
    <input type="file" name="upload" />
    <input type="submit" value="Upload &#187;">
    <p class="caption">
        Allowed file extensions: {implode(', ', $allowed_file_extensions)}
    </p>
</form>

{if !empty($uploaded_file)}
    <h3 class="green_text">
        {$uploaded_file}
    </h3>
{/if}

{if !empty($error)}
    <h3 class="red_text">
        {$error}
    </h3>
{/if}