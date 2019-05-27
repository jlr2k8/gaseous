<div id="form">
    {*
        ^^  Avoid using a form wrapper so that a form can be submitted within the editor.
            If a form tag is wrapping the CK Editor textarea (other editors do this too),
            the form tag is stripped despite validation configuration.
    *}

    <input type="hidden" name="uid" value="{if !empty($page)}{$page.uid}{/if}" />
    <input type="hidden" name="original_uri_uid" value="{if !empty($page)}{$page.uri_uid}{/if}" />
    <input type="hidden" name="page_master_uid" value="{if !empty($page)}{$page.page_master_uid}{/if}" />

    <div>
        <h3>Page Attributes:</h3>

        <div class="gray_background slight_padding">
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
                        >{$status}</option>
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
            <div>
                {if !$is_home_page}
                    <div class="floating_form_label_and_input">
                        <label>
                            Full Parent URI:
                        </label>
                        <select class="uri_preview_setter" name="parent_page_uri">
                            <option value="">/</option>
                            {foreach from=$all_uris item=uri}
                                <option value="{$uri.uid}"
                                    {if !empty($parent_uri) && $uri.uri == rtrim($parent_uri,'/')}
                                        selected="selected"
                                    {/if}

                                    {if !empty($page) && $uri.uid == $page.uri_uid}
                                        disabled="disabled"
                                    {/if}

                                    {if $uri.uri == '/home'}
                                        {continue}
                                    {/if}
                                >{$uri.uri}/</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="floating_form_label_and_input">
                        <label>
                            This Page URI:
                        </label>
                        <input class="uri_preview_setter" required="required" type="text" name="this_uri_piece" value="{if !empty($this_uri_piece)}{$this_uri_piece}{/if}" />
                    </div>
                    <div class="floating_form_label_and_input">
                        <label>
                            Constructed URL:
                        </label><br />
                        <span class="green_text">
                            {$full_web_url}<span id="preview_full_parent_uri">{if !empty($parent_uri)}{$parent_uri}{/if}</span>/<span id="preview_this_url_piece">{if !empty($this_uri_piece)}{$this_uri_piece}{/if}</span>
                        </span>
                        <h3 id="preview_hint">&#160;</h3>
                    </div>
                {else}
                    <input type="hidden" name="parent_page_uri" value="" />
                    <input type="hidden" name="this_uri_piece" value="home" />
                {/if}
                <div>
                    <div class="floating_form_label_and_input">
                        <div>
                            <input type="checkbox" id="is_public" name="is_public" />
                            <label class="label">
                                Public page (available to all roles and non-logged-in users)
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

                                    <input class="margin_right page_roles" type="checkbox" name="page_roles[]" value="{$role.role_name}"
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
                    {if $archive_pages && !$new_page && !$is_home_page}
                        <input class="archive" name="archive" id="archive_page" type="submit" value="Archive This Page" />
                    {/if}
                </div>
            </div>
        </div>
    </div>
    <div>
        <div id="toolbar_buffer">
            &#160;
        </div>
        <textarea name="body" id="body">{if !empty($page.body)}{$page.body}{/if}</textarea>
        {$ck_editor->cdn}
        {$ck_editor->inline('body')}
    </div>
    <div class="one-third_right slight_padding margin_top">
        <div>
            <p class="margin_left">
                <label>
                    Summarize your changes for this iteration (optional):
                </label>
            </p>
            <textarea id="page_iteration_message" name="page_iteration_message"></textarea>
        </div>
        <div class="slight_margin">
            <button id="submit_page_iteration" type="button" {if ((!$new_page && !$edit_pages) || ($new_page && !$add_pages)) }disabled="disabled"{/if}>
                Submit Page Iteration
            </button>
        </div>
        <div id="submit_error" class="slight_padding red_border thick_border display_none">
            <p id="error_paragraph" class="red_text">

            </p>
        </div>
    </div>
    <div class="clear_both">&#160;</div>

    <div id="page_iterations_wrapper">
        {include file="admin/page_iterations.tpl"}
    </div>

    <div class="clear_both">&#160;</div>
</div>