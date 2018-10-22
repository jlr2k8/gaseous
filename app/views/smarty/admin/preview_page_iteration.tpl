<html>
    <head>

    </head>
    <body>
        <p>
            You are previewing "{$page_reference}"<br />Last modified by {$iteration.author} {$iteration.formatted_created}<br /><em>{$iteration.iteration_description}</em>
        </p>

        <form method="post" action="{$full_web_url}/services/update_page_iteration.php">
            <input type="hidden" name="page_iteration_uid" value="{$page_iteration_uid}" />
            <input type="hidden" name="page_master_uid" value="{$find_replace.page_master_uid}" />
            <input type="hidden" name="return_url_encoded" value="{$return_url_encoded}" />
            <input type="submit" value="Revert page to this iteration" {if $is_current_iteration}disabled="disabled"{/if} onclick="{literal}var status=confirm('Are you sure?'); if(status === true){return true;}else{return false};{/literal}" />
            {if $is_current_iteration}
                <span class="bold red_text">(Current)</span>
            {/if}
        </form>

        <iframe style="width:100%;height:80%;" src="{$full_web_url}/services/preview_page_iteration.php?uid={$page_iteration_uid}&content_only=true"></iframe>
    </body>
</html>