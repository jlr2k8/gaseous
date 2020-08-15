<div class="gray_border slight_margin slight_padding">
    {if (int)$status == (int)1}
        <form method="post">
            <h3>
                Updates are available. Click Update below to perform code and database updates.
            </h3>
            <p>
                <strong>
                    Current Version:
                </strong>
                <span class="red_text">
                    {$smarty.const.APP_VERSION}
                </span>
            </p>
            <p>
                <strong>
                    Available Version:
                </strong>
                <span class="green_text">
                    {$latest.app_version}
                </span>
            </p>

            <input type="hidden" name="perform_updates" value="true" />
            <input type="submit" value="Update &#187;" />
        </form>
    {elseif is_string($status)}
        <h3 class="bold">
            {$status}
        </h3>
    {else}
        <h3>
            Everything is up-to-date!
        </h3>
    {/if}
</div>