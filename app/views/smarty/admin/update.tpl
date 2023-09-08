<div class="slight_padding two-thirds_left">
    {if $status == 1}
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
    {elseif $status == 0}
        <h3>
            Everything is up-to-date!
        </h3>
    {elseif $status == -1}
        <h3>
            The currently installed version, {$smarty.const.APP_VERSION}, succeeds the latest available stable version ({$latest.app_version}). No action is required.
        </h3>
    {/if}
</div>

<div class="gray_background one-third_right">
    <p>
        <strong>
            Current Version:
        </strong>
        <span>
            v{$smarty.const.APP_VERSION}
        </span>
    </p>
</div>