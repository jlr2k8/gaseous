<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 12/26/20
 *
 * template.php
 *
 * System template manager for content body types and fields
 *
 **/

use Content\Breadcrumbs;
use Content\Http;
use Content\TemplateAdmin;
use Content\Templator;
use Db\PdoMySql;
use Wysiwyg\Codemirror;

$add_edit_templates = Settings::value('add_edit_templates');
$archive_templates  = Settings::value('archive_templates');

// check setting/role privileges
if (!$add_edit_templates && !$archive_templates) {
    Http::error(403);
}

$templator      = new Templator();
$template_admin = new TemplateAdmin();
$codemirror     = new Codemirror();

$content_body_types = $template_admin->content_get->body->getContentBodyTypes();

$templator->assign('content_body_types', $content_body_types);
$templator->assign('new_template', false);

/*** page type ***/

// edit template
$content_body_type_id   = filter_var(($_GET['content_body_type_id'] ?? null), FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE);

// new template
$new_template           = (isset($_GET['new']));

// field edit form(s)
$sort                   = (isset($_GET['sort']));
$update_field           = (isset($_POST['update']));
$add_field              = (isset($_POST['add']));
$archive_field          = (!empty($_GET['archive']));

// submit edit template form
$submit_update_template        = (isset($_POST['update_template']) && $_POST['update_template'] == 'true');

// submit new template form
$submit_new_template           = (isset($_POST['update_template']) && $_POST['update_template'] == 'false');
/*** ***/


// page: edit content body type
if (!empty($content_body_type_id) && !$new_template && !$sort && !$update_field && !$add_field && !$archive_field) {
    $content_body_type_detail   = $template_admin->content_get->body->getContentBodyType($content_body_type_id);
    $content_body_type_fields   = $template_admin->content_get->body->getCmsFields($content_body_type_id);
    $content_body_field_types   = $template_admin->getCmsFieldTypes();
    $content_body_types         = $template_admin->content_get->body->getContentBodyTypes();
    $content_body_template      = $template_admin->content_get->body->getBodyTemplateDetail($content_body_type_id);
    $title                      = 'Template Management - ' . $content_body_type_detail['label'];

    $templator->assign('content_body_type_detail', $content_body_type_detail);
    $templator->assign('content_body_type_fields', $content_body_type_fields);
    $templator->assign('content_body_field_types', $content_body_field_types);
    $templator->assign('content_body_types', $content_body_types);
    $templator->assign('content_body_template', $content_body_template);
    $templator->assign('add_edit_templates', $add_edit_templates);
    $templator->assign('archive_templates', $archive_templates);
    $templator->assign('codemirror', $codemirror);

    $page_find_replace = [
        'page_title_seo'    => $title,
        'page_title_h1'     => $title,
        'breadcrumbs'       => (new Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb('Template Management', '/admin/template/')->crumb('Editing: ' . $content_body_type_detail['label']),
        'body'              => $templator->fetch('admin/template/content_body_types.tpl'),
    ];

// page: new content body type
} elseif ($new_template) {
    $content_body_field_types   = $template_admin->getCmsFieldTypes();
    $content_body_types         = $template_admin->content_get->body->getContentBodyTypes();
    $content_body_template      = $template_admin->content_get->body->getBodyTemplateDetail($content_body_type_id);
    $title                      = 'Template Management - New Content Body Type';

    $templator->assign('content_body_type_fields', []);
    $templator->assign('content_body_field_types', $content_body_field_types);
    $templator->assign('content_body_types', $content_body_types);
    $templator->assign('add_edit_templates', $add_edit_templates);
    $templator->assign('archive_templates', $archive_templates);
    $templator->assign('codemirror', $codemirror);
    $templator->assign('new_template', true);

    $page_find_replace = [
        'page_title_seo'    => $title,
        'page_title_h1'     => $title,
        'breadcrumbs'       => (new Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb('Template Management', '/admin/template/')->crumb('New Content Body Type'),
        'body'              => $templator->fetch('admin/template/content_body_types.tpl'),
    ];

// re-sort (drag/drop)
} elseif ($sort) {
    $sort_orders_to_uuids = [];

    foreach($_POST['sorted'] as $key => $val) {
        $key                        = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
        $sort_orders_to_uuids[$key] = filter_var($val, FILTER_SANITIZE_STRING);
    }

    $template_admin->sortOrderBulk($sort_orders_to_uuids);

    exit;

// update field
} elseif ($update_field) {
    $transaction = new PdoMySql();

    $transaction->beginTransaction();

    try {
        $template_admin->archiveContentBodyField($_POST['uid'], $transaction);
        $template_admin->insertContentBodyField($_POST, $transaction);
    } catch (Exception $e) {
        $transaction->rollBack();

        Log::app($e);

        throw $e;
    }

    $transaction->commit();

    exit;
} elseif ($add_field) {
    $transaction = new PdoMySql();

    $transaction->beginTransaction();

    try {
        $template_admin->insertContentBodyField($_POST, $transaction);
    } catch (Exception $e) {
        $transaction->rollBack();

        Log::app($e);

        throw $e;
    }

    $transaction->commit();

    $content_body_type_id = $_POST['content_body_type_id'];

    header('Location: ' . Settings::value('full_web_url') . '/admin/template/?content_body_type_id=' . $content_body_type_id);
    exit;
// archive field
} elseif ($archive_field) {
    $transaction = new PdoMySql();

    $transaction->beginTransaction();

    try {
        $template_admin->archiveContentBodyField($_GET['archive'], $transaction);
    } catch (Exception $e) {
        $transaction->rollBack();

        Log::app($e);

        throw $e;
    }

    $transaction->commit();

    exit;

// page: submit template edits
} elseif ($submit_update_template) {

    if (!$add_edit_templates) {
        throw new Exception('You do not have access to update this template');
    }

    $transaction = new PdoMySql();

    $transaction->beginTransaction();

    try {
        $template_admin->archiveContentBodyType($_POST['type_id'], $transaction);
        $template_admin->archiveContentBodyTemplate($_POST['type_id'], $transaction);
        $template_admin->insertContentBodyType($_POST, $transaction);
        $template_admin->insertContentBodyTemplate($_POST, $transaction);
    } catch (Exception $e) {
        $transaction->rollBack();

        Log::app($e);

        throw $e;
    }

    $transaction->commit();

    header('Location: ' . Settings::value('full_web_url') . '/admin/template/?content_body_type_id=' . $content_body_type_id);
    exit;

} elseif ($submit_new_template) {

    if (!$add_edit_templates) {
        throw new Exception('You do not have access to create a template');
    }

    $transaction = new PdoMySql();

    $transaction->beginTransaction();

    try {
        $template_admin->insertContentBodyType($_POST, $transaction);
        $template_admin->insertContentBodyTemplate($_POST, $transaction);
    } catch (Exception $e) {
        $transaction->rollBack();

        Log::app($e);

        throw $e;
    }

    $transaction->commit();

    header('Location: ' . Settings::value('full_web_url') . '/admin/template/?content_body_type_id=' . $content_body_type_id);
    exit;

// page: landing page to select content body types
} else {
    $title = 'Template Management';

    $page_find_replace = [
        'page_title_seo'    => $title,
        'page_title_h1'     => $title,
        'breadcrumbs'       => (new Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb($title),
        'body'              => $templator->fetch('admin/template.tpl'),
    ];
}

echo Templator::page($page_find_replace);