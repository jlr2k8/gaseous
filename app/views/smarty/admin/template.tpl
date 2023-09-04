<div class="gray_border slight_padding">
    <h2>
        Content Body Types
    </h2>

    <div class="margin_on_top">
        {foreach from=$content_body_types item=cbt}
            <h3>
                <a href="{$full_web_url}/admin/template/?content_body_type_id={$cbt.type_id}">
                    {$cbt.label}
                </a>
            </h3>
            <p>
                {$cbt.description}
            </p>
        {/foreach}
    </div>
</div>

<div class="margin_on_top slight_padding">
    <h3>
        <a href="{$full_web_url}/admin/template/?new">
            <i class="fas fa-plus"></i> Create a new Content Body Type
        </a>
    </h3>
</div>