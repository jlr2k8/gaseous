{include file="font-awesome-cdn-link-href.tpl"}

<div id="form">
    {*
        ^^  Avoid using a form wrapper so that a form can be submitted within the editor.
            If a form tag is wrapping the CK Editor textarea (other editors do this too),
            the form tag is stripped despite validation configuration.
    *}

    <input type="hidden" name="uid" value="{if !empty($page)}{$page.uid}{/if}" />
    <input type="hidden" name="uri_uid" value="{if !empty($page)}{$page.uri_uid}{/if}" />
    <input type="hidden" name="content_uid" value="{if !empty($page)}{$page.content_uid}{/if}" />
    <input type="hidden" name="content_body_type_id" value="{$content_body_type_id}" />

    <div class="two-thirds_left gray_background margin_on_bottom">
        <h3>Page Attributes:</h3>
        <div class="slight_padding">
            <div class="floating_form_label_and_input">
                <label class="label">Page Title (H1 Tag):</label>
                <input type="text" name="page_title_h1" value="{if !empty($page.page_title_h1)}{$page.page_title_h1}{/if}" />
            </div>
            <div class="floating_form_label_and_input">
                <label class="label">Page Title (Title tag/SEO):</label>
                <input type="text" name="page_title_seo" value="{if !empty($page.page_title_seo)}{$page.page_title_seo}{/if}" />
            </div>
            <div class="floating_form_label_and_input">
                <label class="label">Meta Description:</label>
                <input type="text" name="meta_desc" value="{if !empty($page.meta_desc)}{$page.meta_desc}{/if}" />
            </div>
            <div class="floating_form_label_and_input">
                <label class="label">Page Status:</label>
                <select name="status">
                    {foreach from=$statuses item=status}
                        <option value="{$status}"
                            {if !empty($page) && $status == $page.status}
                                selected="selected"
                            {/if}
                        >{ucfirst($status)}</option>
                    {/foreach}
                </select>
            </div>
            <div class="floating_form_label_and_input">
                <label class="label">Meta Robots:</label>
                <select name="meta_robots">
                    <option value="index,follow"
                        {if !empty($page) && $page.meta_robots == 'index,follow'}
                            selected="selected"
                        {/if}
                    >index,follow</option>
                    <option value="noindex,nofollow"
                    {if !empty($page) && $page.meta_robots == 'noindex,nofollow'}
                        selected="selected"
                    {/if}
                    >noindex,nofollow</option>
                </select>
            </div>
            <div class="floating_form_label_and_input">
                <label class="label">
                    Include in sitemap?
                </label>
                <div>
                    <input type="checkbox" id="include_in_sitemap" name="include_in_sitemap" {if !empty($page.include_in_sitemap) && $page.include_in_sitemap == '1'}checked="checked"{/if} />
                </div>
            </div>
            <div class="floating_form_label_and_input">
                <label class="label">
                    Minify HTML output?
                </label>
                <div>
                    <input type="checkbox" id="minify_html_output" name="minify_html_output" {if !empty($page.minify_html_output) && $page.minify_html_output == '1'}checked="checked"{/if} />
                </div>
            </div>
            <div>
                <div>
                    <div class="floating_form_label_and_input">
                        <div>
                            <input type="checkbox" id="is_public" name="is_public" />
                            <label class="label">
                                Public page (available to all roles and anonymous users)
                            </label>
                        </div>
                        <div>
                            <h3 class="text_align_center">
                                - OR -
                            </h3>
                        </div>
                        <div>
                            <label>Limit access to roles:</label>
                            {foreach from=$all_roles item=role}
                                <div class="display_inline_block" title="{$role.description}">
                                    <label>
                                        {$role.role_name}
                                    </label>

                                    <input class="margin_right content_roles" type="checkbox" name="content_roles[]" value="{$role.role_name}"
                                        {if !empty($page.roles) && in_array($role.role_name, $page.roles)}
                                            checked="checked"
                                        {/if}
                                    />
                                </div>
                                <div class="no_border margin_right display_inline_block">&#160</div>
                            {/foreach}
                        </div>
                    </div>
                </div>
                <div>
                    {if $archive_content && !$new_page && !$is_home_page}
                        <input class="archive" name="archive" id="archive_content" type="submit" value="Archive This Page" />
                    {/if}
                </div>
            </div>
        </div>
    </div>
    {if !$is_home_page && !empty($parent_content)}
        <div class="one-third_right gray_background">
            <div class="floating_form_label_and_input">
                <label>
                    Parent Content:
                </label>
                <select name="parent_content_uid">
                    {foreach from=$parent_content item=pc}
                        <option
                            value="{$pc.content_uid}"
                            {if !empty($page.parent_content_uid) && $pc.content_uid == $page.parent_content_uid}selected="selected"{/if}
                            {if !empty($page.parent_content_uid) && $pc.content_uid == $page.content_uid}disabled="disabled"{/if}>
                            {if $pc.uri == '/home'}-- Top level --{else}{$pc.page_title_h1}{/if}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="floating_form_label_and_input">
                <h3 id="preview_hint">&#160;</h3>
            </div>
        </div>
    {/if}
    <div class="clear_both">
        &#160;
    </div>
    <div>
        {$cms_fields}
    </div>
    <div class="clear_both">
        &#160;
    </div>
    <div class="one-third_right slight_padding margin_top">
        <div>
            <p class="margin_left">
                <label>
                    Summarize your changes for this iteration (optional):
                </label>
            </p>
            <textarea id="content_iteration_message" name="content_iteration_message"></textarea>
        </div>
        <div class="slight_margin">
            <button id="submit_content_iteration" type="button" {if ((!$new_page && !$edit_content) || ($new_page && !$add_content)) }disabled="disabled"{/if}>
                Submit Page Iteration
            </button>
        </div>
        <div id="submit_error" class="slight_padding red_border thick_border display_none">
            <p id="error_paragraph" class="red_text">

            </p>
        </div>
    </div>
    <div class="clear_both">&#160;</div>

    <div id="content_iterations_wrapper">
        {include file="admin/content_iterations.tpl"}
    </div>

    <div class="clear_both">&#160;</div>
</div>