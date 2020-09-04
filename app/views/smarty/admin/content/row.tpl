{if $page.uri == '/home'}
    <var content="0"></var>
    {assign var='page_identifier_label' value='<i class="fas fa-home"></i>'}
    {assign var='alphabet_filter' value='home'}
{else}
    <var content="{$page.page_identifier_label}"></var>
    {assign var='page_identifier_label' value=$page.page_identifier_label}
    {assign var='alphabet_filter' value=ucfirst($page_identifier_label[0])}
{/if}

<tr data-alphabet-row-letter="{$alphabet_filter}">
    <td>
        <h3>
            <span class="caption"><i class="caption fas fa-pencil-alt"></i></span>
            <a href="{$full_web_url}/admin/content/?page_uri_urlencoded={urlencode($page.uri)}">{$page_identifier_label}</a>
        </h3>
        <p class="small_text">
            <a target="_blank" href="{$full_web_url}{$page.uri}/"><span class="green_text">{$full_web_url}{$page.uri}/</span></a>
            <i class="fas fa-external-link-alt"></i>
        </p>
    </td>
    <td>
        <p>
            {$page.content_body_type_label}
        </p>
    </td>
    <td>
        <p>
            {ucfirst($page.status)}
        </p>
    </td>
    <td>
        <var content="{$page.content_modified}"></var>
        <p>
            {$page.formatted_content_modified}
        </p>
    </td>
    <td>
        <var content="{$page.content_created}"></var>
        <p>
            {$page.formatted_content_created}
        </p>
    </td>
</tr>