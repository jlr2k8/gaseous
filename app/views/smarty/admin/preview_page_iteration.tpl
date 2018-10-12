<html>
    <head>

    </head>
    <body>
        <p>
            You are previewing "{$find_replace.page_title_h1}"<br />Last modified by {$iteration.author} {$iteration.formatted_created}<br /><em>{$iteration.iteration_description}</em>
        </p>
        <iframe style="width:100%;height:80%;" src="{$full_web_url}/services/preview_page_iteration.php?uid={$page_iteration_uid}&content_only=true">
        </iframe>
    </body>
</html>