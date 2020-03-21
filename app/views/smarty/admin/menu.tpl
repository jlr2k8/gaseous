{assign var=heading_label value="Label:"}
{assign var=heading_destination value="Destination:"}
{assign var=heading_nofollow value="Allow search engines to follow link?"}
{assign var=heading_target value="Open link in:"}
{assign var=heading_class value="Additional HTML classes for anchor tag (optional):"}

<div id="new_menu_item_wrapper" class="gray_background two-thirds_left">
    {include file="admin/menu/new-menu-form.tpl"}
</div>

<div class="one-third_right" id="current_menu">
    <iframe id="menu_sortable_iframe" src="{$menu_sortable_iframe_url}" ></iframe>
</div>

<div class="clear_both">
    &#160;
</div>

<div id="edit_menu_items_wrapper" class="gray_border">
    {include file="admin/menu/edit-menu-form.tpl"}
</div>

