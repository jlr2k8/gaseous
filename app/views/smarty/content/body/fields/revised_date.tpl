{if !empty($revised_date)}
    {if strtotime($revised_date) && abs(strtotime($revised_date)) == strtotime($revised_date)}
        {assign var=revised_date value=strtotime($revised_date)}
    {/if}

    {date('F d, Y g:i:s A T', $revised_date)}
{/if}