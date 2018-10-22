<form id="page_content" method="post">
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
                <div class="floating_form_label_and_input">
                    <label>
                        Full Parent URI:
                    </label>
                    <select class="uri_preview_setter" name="parent_page_uri">
                        <option value="">/</option>
                        {foreach from=$all_uris item=uri}
                            <option value="{$uri.uid}"
                                {if !empty($parent_uri) && $uri.uri == trim($parent_uri,'/')}
                                    selected="selected"
                                {/if}

                                {if !empty($page) && $uri.uid == $page.uri_uid}
                                    disabled="disabled"
                                {/if}
                            >/{$uri.uri}/</option>
                        {/foreach}
                    </select>
                </div>
                <div class="floating_form_label_and_input">
                    <label>
                        This Page URI:
                    </label>
                    <input class="uri_preview_setter" type="text" name="this_uri_piece" value="{if !empty($this_uri_piece)}{$this_uri_piece}{/if}" />
                </div>
                <div class="floating_form_label_and_input">
                    <label>
                        Constructed URL:
                    </label><br />
                    <span class="green_text">
                        {$full_web_url}<span id="preview_full_parent_uri">{if !empty($parent_uri)}{$parent_uri}{/if}</span><span id="preview_this_url_piece">{if !empty($this_uri_piece)}{$this_uri_piece}{/if}</span>/
                    </span>
                    <h3 id="preview_hint">&#160;</h3>
                </div>
                <div>
                    <div class="floating_form_label_and_input">
                        <label>Roles:</label>
                        <div>
                            {foreach from=$all_roles item=role}
                                <div class="display_inline_block" title="{$role.description}">
                                    <label>
                                        {$role.role_name}
                                    </label>

                                    <input class="margin_right" type="checkbox" name="page_roles[]" value="{$role.role_name}"
                                        {if in_array($role.role_name, $page.roles)}
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
                        <input class="archive" onclick="{literal}var status=confirm('Are you sure?'); if(status === true){return true;}else{return false};{/literal}" name="archive" type="submit" value="Archive This Page" />
                    {/if}
                </div>
            </div>
        </div>
    </div>
    <div>
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
            <input type="submit" value="Submit Page Iteration" />
        </div>
    </div>
    <div class="clear_both">&#160;</div>
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
                            <a target="_blank" href="{$full_web_url}/services/preview_page_iteration.php?uid={$iteration.page_iteration_uid}">
                                Preview
                            </a>
                            &#160;&#160;
                            {if !empty($iterations[$next_key])}
                                <a target="_blank" href="{$full_web_url}/services/diff_page_iterations.php?old_uid={$iterations[$next_key].page_iteration_uid}&new_uid={$iteration.page_iteration_uid}">
                                    View Diff
                                </a>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    {/if}
    <div class="clear_both">&#160;</div>
</form>
