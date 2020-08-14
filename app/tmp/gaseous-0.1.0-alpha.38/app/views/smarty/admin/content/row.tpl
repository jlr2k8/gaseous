<tr>
    <td>
        <h3>
            <a href="{$full_web_url}/admin/content/?page_uri_urlencoded={urlencode($page.uri)}">
                {if $page.uri == '/home'}
                    <i class="fas fa-home"></i>
                {else}
                    {$page.page_identifier_label}
                {/if}
            </a>
        </h3>
        <p class="small_text">
            <a target="_blank" href="{$full_web_url}{$page.uri}/">
                <span class="green_text">
                    {$full_web_url}{$page.uri}/
                </span>
            </a>
        </p>
    </td>
    <td>
        <p>
            {$page.content_body_type_label}
        </p>
    </td>
    <td>
        <p>
            {$page.formatted_content_modified}
        </p>
    </td>
    <td>
        <p>
            {$page.formatted_content_created}
        </p>
    </td>
</tr>