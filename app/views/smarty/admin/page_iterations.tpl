{if !empty($iterations)}
    <div>
        <h2>Previous iterations of this page:</h2>
        <table>
            {foreach from=$iterations key=key item=iteration}
                {$next_key = ($key+1)}
                <tr>
                    <td {if $page.uid == $iteration.page_iteration_uid}style="background-color: yellow;"{/if}>
                        {if $page.uid == $iteration.page_iteration_uid}<span class="bold red_text">(Current)</span>{/if}
                        <p>
                            Change by {$iteration.author} {$iteration.formatted_created}
                            <br />
                            <a target="_blank" href="{$full_web_url}/controllers/services/preview_page_iteration.php?uid={$iteration.page_iteration_uid}&page_master_uid={$page.page_master_uid}">
                                Preview
                            </a>
                            &#160;&#160;
                            {if !empty($iterations[$next_key])}
                                <a target="_blank" href="{$full_web_url}/controllers/services/diff_page_iterations.php?old_uid={$iterations[$next_key].page_iteration_uid}&new_uid={$iteration.page_iteration_uid}">
                                    View Diff
                                </a>
                            {/if}
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
{/if}