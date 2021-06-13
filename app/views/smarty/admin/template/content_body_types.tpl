<div {if $new_template === true}class="display_none"{/if}>
    <h3>
        Edit Fields
    </h3>
    <table id="content_body_type_fields">
        {include file="admin/template/header.tpl"}
        <tbody {if $add_edit_templates}id="sortable"{/if}>
            {if empty($content_body_type_fields)}
                <tr>
                    <td colspan="5">
                        (No fields)
                    </td>
                </tr>
            {/if}
            {foreach from=$content_body_type_fields item=field}
                <tr class="field_row" id="row_{$field.uid}" data-field-key="{$field.uid}">
                    {if $add_edit_templates}
                        <form id="field_{$field.uid}" method="post">
                    {/if}
                    <td {if $add_edit_templates}class="cursor_move"{/if}>
                        <input type="hidden" name="uid" value="{$field.uid}" />
                        <input type="hidden" name="sort_order" value="{$field.sort_order}" />
                        <input type="hidden" name="content_body_type_id" value="{$field.content_body_type_id}" />

                        <select name="content_body_field_type_id" {if !$add_edit_templates}disabled="disabled" title="You do not have permission to edit this field type"{/if}>
                            {foreach from=$content_body_field_types item=field_type}
                                <option value="{$field_type.type_id}" {if $field_type.type_id == $field.content_body_field_type_id}selected="selected"{/if}>
                                    {$field_type.label}
                                </option>
                            {/foreach}
                        </select>
                    </td>
                    <td {if $add_edit_templates}class="cursor_move"{/if}>
                        <input type="text" name="label" value="{$field.content_body_field_label}" {if !$add_edit_templates}disabled="disabled" title="You do not have permission to edit this label"{/if} />
                    </td>
                    <td {if $add_edit_templates}class="cursor_move"{/if}>
                        <input type="text" name="template_token" value="{$field.template_token}" {if !$add_edit_templates}disabled="disabled" title="You do not have permission to edit this token"{/if} />
                    </td>
                    <td {if $add_edit_templates}class="cursor_move"{/if}>
                        <input type="text" name="description" value="{$field.content_body_field_description}" {if !$add_edit_templates}disabled="disabled" title="You do not have permission to edit this description"{/if} />
                    </td>
                    {if $add_edit_templates || $archive_templates}
                        <td {if $add_edit_templates}class="cursor_move"{/if}>
                            {if $add_edit_templates}
                                <button class="field_update" data-field-key="{$field.uid}" name="update" type="button">Update</button>
                            {/if}
                            {if $archive_templates}
                                <button class="field_archive archive" data-field-key="{$field.uid}" name="archive" type="button">Archive</button>
                            {/if}
                        </td>
                        </form>
                    {/if}
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>
<div class="{if $new_template === true}display_none {/if}margin_on_top">
    <h3>
        Add a New Field
    </h3>
    <table>
        {include file="admin/template/header.tpl"}
        <tbody>
        <tr class="field_row">
            {if $add_edit_templates}
                <form id="new_field" method="post" action="{$full_web_url}/admin/template/">
                <input type="hidden" name="content_body_type_id" value="{$content_body_type_detail.type_id}" />
                <input type="hidden" name="add" value="true" />
            {/if}
            <td>
                <select name="content_body_field_type_id">
                    <option></option>
                    {foreach from=$content_body_field_types item=field_type}
                        <option value="{$field_type.type_id}">
                            {$field_type.label}
                        </option>
                    {/foreach}
                </select>
            </td>
            <td>
                <input type="text" name="label" />
            </td>
            <td>
                <input type="text" name="template_token"  />
            </td>
            <td>
                <input type="text" name="description" />
            </td>
            {if $add_edit_templates || $archive_templates}
                <td>
                    {if $add_edit_templates}
                        <button type="submit" class="field_create" name="create" type="button">Add Field</button>
                    {/if}
                </td>
            </form>
            {/if}
        </tr>
        </tbody>
    </table>
</div>

<div class="gray_background margin_on_top slight_padding">
    <form id="template_edit_form" class="floating_form" method="post" action="{$full_web_url}/admin/template/">
        {if $new_template === false}
            <div class="two-thirds_left">
                {if !$add_edit_templates}<p class="bold red_text">You do not have permission to edit this template</p>{/if}
                <textarea {if !$add_edit_templates}disabled="disabled" title="You do not have permission to edit this template"{/if} id="template" name="template">{if !empty($content_body_template.template)}{$content_body_template.template}{/if}</textarea>
                {$codemirror->cdn}
                {$codemirror->textarea("template")}
            </div>
        {/if}
        <div class="{if $new_template === false}one-third_right{/if}">
            <input type="hidden" name="type_id" value="{if !empty($content_body_type_detail.type_id)}{$content_body_type_detail.type_id}{/if}" />
            <input type="hidden" name="update_template" value="{if !empty($content_body_type_detail.type_id)}true{else}false{/if}" />
            <div>
                <label>
                    Title
                </label>
                <input {if !$add_edit_templates}disabled="disabled" title="You do not have permission to edit this field"{/if} required="required" type="text" name="label" value="{if !empty($content_body_type_detail.label)}{$content_body_type_detail.label}{/if}" />
            </div>
            <div>
                <label>
                    Description (optional)
                </label>
                <textarea {if !$add_edit_templates}disabled="disabled" title="You do not have permission to edit this field"{/if} name="description">{if !empty($content_body_type_detail.description)}{$content_body_type_detail.description}{/if}</textarea>
            </div>
            <div>
                <label>
                    URI Scheme
                </label>
                <span class="caption">
                    Do not put the entire URI here. Just put the slug of the current page. The entire URI is automatically constructed by the parent content's URI schemes.
                </span>
                <input {if !$add_edit_templates}disabled="disabled" title="You do not have permission to edit this field"{/if} required="required" type="text" name="uri_scheme" value="{if !empty($content_body_template.uri_scheme)}{$content_body_template.uri_scheme}{/if}" />
            </div>
            <div>
                <label>
                    Parent Content Body Type
                </label>
                <select {if !$add_edit_templates}disabled="disabled" title="You do not have permission to edit this field"{/if} name="parent_type_id">
                    <option {if !empty($content_body_type_detail.parent_type_id) && $content_body_type_detail.parent_type_id == $content_body_type_detail.type_id}selected="selected"{/if} value="">(Top Level or Self)</option>
                    {foreach from=$content_body_types item=content_body_type}
                        {if !empty($content_body_type_detail.type_id) && $content_body_type.type_id == $content_body_type_detail.type_id}
                            {continue}
                        {/if}
                        <option {if !empty($content_body_type_detail.parent_type_id) && $content_body_type.type_id == $content_body_type_detail.parent_type_id}selected="selected"{/if} value="{$content_body_type.type_id}">
                            {$content_body_type.label}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div>
                <label>
                    Promote this content on author's user page?
                </label>
                <input {if !$add_edit_templates}disabled="disabled" title="You do not have permission to edit this field"{/if} type="checkbox" name="promoted_user_content" {if !empty($content_body_type_detail.promoted_user_content) && $content_body_type_detail.promoted_user_content == '1'}checked="checked"{/if} />
            </div>
            <input {if !$add_edit_templates}disabled="disabled" title="You do not have permission to submit this form"{/if} type="submit" value="{if $new_template === false}Update{else}Create{/if} Template" />
        </div>
    </form>
    <div class="clear_both">

    </div>
</div>

{if $add_edit_templates}
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    {literal}
        <script>
            $("#sortable").sortable({
                update: function() {
                    var sorted = [];

                    $('.field_row').each(function(i) {
                        sorted[i] = $(this).attr('data-field-key');
                    });

                    $.post('/admin/template/?sort', {sorted});
                }
            });
        </script>
    {/literal}
{/if}