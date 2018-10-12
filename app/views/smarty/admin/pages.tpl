{if $add_pages}
    <div class="gray_background slight_padding">
        <h2>
            <a href="{$full_web_url}/admin/pages/?new_page=true">
                Add a new page
            </a>
        </h2>
    </div>
{/if}

{if count($active_pages)}
    {foreach from=$active_pages item=page}
        <div class="margin_on_bottom">
            <h2>
                <a href="{$full_web_url}/admin/pages/?page_uri_urlencoded={urlencode($page.uri)}">{$page.uri}</a>
            </h2>
            <p>{$page.page_title_h1}</p>
        </div>
    {/foreach}
{/if}

{if count($inactive_pages)}
    <div class="gray_background slight_padding">
        <h2>Inactive Pages:</h2>
        {foreach from=$inactive_pages item=page}
            <div class="margin_on_bottom">
                <h2>
                    <a href="{$full_web_url}/admin/pages/?page_uri_urlencoded={urlencode($page.uri)}">{$page.uri}</a>
                </h2>
                <p>{$page.page_title_h1}</p>
            </div>
        {/foreach}
    </div>
{/if}