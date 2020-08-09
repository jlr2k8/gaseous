<div class="pager_wrapper {$pager_style}">

    {if $total_pages > 1}
        {if $page > 1}
            {if $total_pages > 2}
                <a class="pager-nav first-page" title="First page" href="?p=1&per_page={$items_per_page}&sort_by={$sort_by}{if $sort_ascending == true}&sort_ascending=true{/if}">
                    &#171;
                </a>
            {/if}

            <a class="pager-nav previous-page" title="Previous page" href="?p={$page-1}&per_page={$items_per_page}&sort_by={$sort_by}{if $sort_ascending == true}&sort_ascending=true{/if}">
                &#8249;
            </a>
        {/if}

        <div class="pager-numbers">
            {for $i=1 to $total_pages}
                <a class="pager-number {if $i == $page}selected{/if}" href="?p={$i}&per_page={$items_per_page}&sort_by={$sort_by}{if $sort_ascending == true}&sort_ascending=true{/if}">
                    {$i}
                </a>
            {/for}
        </div>

        {if $page != $total_pages}
            <a class="pager-nav next-page" title="Next page" href="?p={$page+1}&per_page={$items_per_page}&sort_by={$sort_by}{if $sort_ascending == true}&sort_ascending=true{/if}">
                &#8250;
            </a>

            {if $total_pages > 2}
                <a class="pager-nav last-page" title="Last page" href="?p={$total_pages}&per_page={$items_per_page}&sort_by={$sort_by}{if $sort_ascending == true}&sort_ascending=true{/if}">
                    &#187;
                </a>
            {/if}
        {/if}

        <p>
            Page {$page} of {$total_pages}.
            Showing
                {if $items_per_page != 1 && $page_range.start+1 != min([$page_range.end, $content_count])}
                    results {$page_range.start+1} - {min([$page_range.end, $content_count])}
                {elseif $page_range.start+1 == min([$page_range.end, $content_count])}
                    result {$page_range.start+1}{/if}, of {$content_count} result{if $content_count != 1}s
                {/if}
            total.
        </p>

    {elseif $page == 1 && $total_pages == 1}
        <p>
            Page 1 of 1. Showing {$content_count} result{if $items_per_page != 1}s{/if}.
        </p>
    {/if}

</div>