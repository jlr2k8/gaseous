{if $preview_mode}
    <h3>
        <a href="/admin/css/?exit_preview=true">Exit Preview</a>
    </h3>
{/if}
<form id="css" method="post">
    <select id="css_iteration_list" name="css_iteration_list">
        <option></option>
        {foreach from=$css_iterations item=$css_iteration}
            <option value="{$css_iteration.uid}" {if $editor_css_uid == $css_iteration.uid}selected="selected"{/if}>
                {$css_iteration.author} {$css_iteration.formatted_modified} {if !empty($css_iteration.description)} - {$css_iteration.description}{/if}
            </option>
        {/foreach}
    </select>
    <input type="submit" id="submit_option_to_preview" name="submit_option_to_preview" value="Preview Iteration" />
    <input type="submit" id="submit_option_to_editor" name="submit_option_to_editor" value="Load Iteration for Editing Below" />


    <textarea id="css_iteration" name="css_iteration">{$editor_css_content}</textarea>
    {$codemirror->cdn}
    {$codemirror->textarea("css_iteration")}

    <label>
        Summarize your changes:
    </label>
    <input type="text" id="description" name="description" />

    <input type="submit" id="submit_textarea_to_preview" name="submit_textarea_to_preview" value="Preview Changes" />
    <input type="submit" id="submit_textarea" name="submit_textarea" value="Submit Changes" />
</form>