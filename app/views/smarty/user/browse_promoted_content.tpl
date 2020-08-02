<div class="two-thirds_left">
    {foreach from=$content item=c}
        <div class="gray_border slight_padding margin_on_bottom">
            <h2>
                <a href="{$c.uri}">
                    {$c.page_title_h1}
                </a>
            </h2>

            <p>
                <span class="caption">
                    By:
                    &lt;a href="/users/{$c.body_fields.author}"&gt;{$c.body_fields.author}&lt;/a&gt;
                    on
                    {date('F d, Y g:i:s A T', strtotime($c.created_datetime))}
                </span>
                <br />
                {\Cms::teaser($c.body_fields.body)}
            </p>
        </div>
    {/foreach}
</div>
<div class="clear_both">
    {$pager}
</div>
