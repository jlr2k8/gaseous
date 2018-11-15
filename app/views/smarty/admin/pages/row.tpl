<tr>
    <td>
        <p>
            <a href="{$full_web_url}/admin/pages/?page_uri_urlencoded={urlencode($page.uri)}">
                {if $page.uri == 'home'}
                    <i class="fas fa-home"></i>
                {else}
                    {$page.page_identifier_label}
                {/if}
            </a>
            <br />
            {$full_web_url}/{$page.uri}/
        </p>
    </td>
    <td>
        <p>
            {$page.status}
        </p>
    </td>
    <td>
        <p>
            {$page.author} {$page.formatted_page_modified}
        </p>
    </td>
    <td>
        <p>
            {$page.formatted_page_created}
        </p>
    </td>
</tr>