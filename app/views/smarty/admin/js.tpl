{if $preview_mode}
    <h3>
        <a href="/admin/js/?exit_preview=true">Exit Preview</a>
    </h3>
{/if}
<form id="js" method="post">
    <select id="js_iteration_list" name="js_iteration_list">
        <option></option>
        {foreach from=$js_iterations item=$js_iteration}
            <option value="{$js_iteration.uid}" {if $editor_js_uid == $js_iteration.uid}selected="selected"{/if}>
                {$js_iteration.author} {$js_iteration.formatted_modified} {if !empty($js_iteration.description)} - {$js_iteration.description}{/if}
            </option>
        {/foreach}
    </select>
    <input type="submit" id="submit_option_to_preview" name="submit_option_to_preview" value="Preview Iteration" />
    <input type="submit" id="submit_option_to_editor" name="submit_option_to_editor" value="Load Iteration for Editing Below" />


    <textarea id="js_iteration" name="js_iteration">{$editor_js_content}</textarea>
    {$codemirror->cdn}
    {$codemirror->textarea("js_iteration")}

    <label>
        Summarize your changes:
    </label>
    <input type="text" id="description" name="description" />

    {if empty($editor_js_uid) && !empty($editor_js_content)}
        <input type="submit" id="revert_editor_js" name="revert_editor_js" value="Revert Changes" class="revert" />
    {/if}

    <input type="submit" id="submit_textarea_to_preview" name="submit_textarea_to_preview" value="Preview Changes" />
    <input type="submit" id="submit_textarea" name="submit_textarea" value="Submit Changes" />
</form>