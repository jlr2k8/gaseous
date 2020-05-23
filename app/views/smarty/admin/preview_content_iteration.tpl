<html>
    <head>

    </head>
    <body>
        <p>
            You are previewing "{$find_replace.page_identifier_label}"<br />Last modified by {$iteration.author} {$iteration.formatted_created}<br /><em>{$iteration.iteration_description}</em>
        </p>

        <form method="post" action="{$full_web_url}/controllers/services/update_content_iteration.php">
            <input type="hidden" name="content_iteration_uid" value="{$content_iteration_uid}" />
            <input type="hidden" name="content_uid" value="{$content_uid}" />
            <input type="hidden" name="return_url_encoded" value="{$return_url_encoded}" />
            <input type="submit" value="Revert page to this iteration" {if $is_current_iteration}disabled="disabled"{/if} onclick="{literal}var status=confirm('Are you sure?'); if(status === true){return true;}else{return false};{/literal}" />
            {if $is_current_iteration}
                <span class="bold red_text">(Current)</span>
            {/if}
        </form>

        <iframe style="width:100%;height:80%;" src="{$full_web_url}/controllers/services/preview_content_iteration.php?uid={$content_iteration_uid}&content_uid={$content_uid}&content_only=true"></iframe>
    </body>
</html>