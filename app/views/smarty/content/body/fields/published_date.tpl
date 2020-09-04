{if strtotime($published_date)}
    {assign var=published_date value=strtotime($published_date)}
{/if}

{date('F d, Y g:i:s A T', $published_date)}