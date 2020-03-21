{* NOTE - This template recursively includes itself for child menu items *}
<ul class="menu_group{if !empty($admin) && empty($sortable_class_is_included)} sortable{/if}">
    {assign var=sortable_class_is_included value=true}
    {foreach from=$menu item=m}
        <li id="menu-item-{$m.uid}" class="menu-item{if !empty($m.class)} {$m.class}{/if}">
            <div>
                {if !empty($admin)}
                    {if empty($fa_is_included)}
                        {include file="font-awesome-cdn-link-href.tpl"}
                        {assign var=fa_is_included value=true}
                    {/if}
                    <span class="archive_menu_item" data-uid="{$m.uid}" id="archive-{$m.uid}"><i class="fas fa-times"></i></span>
                {/if}
                {if !empty($admin)}
                    <span style="cursor:move;" title="Goes to: {$m.uri}">
                        {$m.label}
                    </span>
                {else}
                    <a href="{$m.uri}"{if $m.nofollow === 'true'} rel="nofollow"{/if} target="{$m.target}">
                        {$m.label}
                    </a>
                {/if}
            </div>

            {assign var=menu value=$m.children}
            {include file="common/menu.tpl"}
        </li>
    {/foreach}
</ul>