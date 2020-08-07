<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 7/20/18
 *
 * content.php
 *
 * Content management
 *
 */

use \Content\Get;
use \Content\Http;
use Content\Submit;
use \Content\Templator;
use \Content\Diff;
use \Content\Breadcrumbs;
use Uri\Uri;
use \User\Roles;
use \User\Account;
use \Wysiwyg\CkEditor;
use \Wysiwyg\Codemirror;

// check setting/role privileges
if (!Settings::value('add_content') && !Settings::value('edit_content') && !Settings::value('archive_content')) {
    Http::error(403);
}

$settings           = new Settings();
$pages              = new Get();
$templator          = new Templator();
$roles              = new Roles();
$account            = new Account();
$diff               = new Diff();
$ck_editor          = new CkEditor();
$codemirror         = new Codemirror();

$all_active_pages       = $pages->all();
$all_inactive_pages     = $pages->all('inactive');
$my_account             = $account->getAccountFromSessionValidation();
$full_web_url           = Settings::value('full_web_url');
$all_uris               = Uri::all();
$statuses               = Get::statuses();
$page_uri               = !empty($_GET['page_uri_urlencoded']) ? (string)filter_var($_GET['page_uri_urlencoded'], FILTER_SANITIZE_STRING) : false;
$this_page              = $pages->contentByUri($page_uri, 'active', true) ?: $pages->contentByUri($page_uri, 'inactive', true);
$is_home_page           = !empty($this_page['uri']) && $this_page['uri'] == '/home';
$content_body_type_id   = !empty($_GET['content_body_type_id']) ? filter_var(trim($_GET['content_body_type_id'], '/'), FILTER_SANITIZE_STRING) : null;
$all_roles              = $roles->getAll();

$title                  = 'Content Management';
$error                  = null;
$new_page               = false;

if (!empty($content_body_type_id)) {
    $new_page = true;
}

if (!empty($_POST)) {
    foreach($_POST as $key => $val) {
        if ($key == 'content_roles') {
            foreach($val as $account_role)
                $post[$key][] = (string)filter_var($account_role, FILTER_SANITIZE_STRING);
        } else {
            $post[$key] = $val;
        }
    }

    $submit = new Submit($post);

    if(isset($post['archive'])) {
        $submit->archive();
    } else {
        $submit->upsert();
    }

    echo $submit->json_upsert_status;

    exit;
}

$templator->assign('all_uris', $all_uris);
$templator->assign('ck_editor', $ck_editor);
$templator->assign('codemirror', $codemirror);
$templator->assign('statuses', $statuses);
$templator->assign('error', $error);
$templator->assign('active_pages', $all_active_pages);
$templator->assign('inactive_pages', $all_inactive_pages);
$templator->assign('add_content', Settings::value('add_content'));
$templator->assign('edit_content', Settings::value('edit_content'));
$templator->assign('archive_content', Settings::value('archive_content'));
$templator->assign('page', $this_page);
$templator->assign('account', $my_account);
$templator->assign('full_web_url', $full_web_url);
$templator->assign('new_page', $new_page);
$templator->assign('all_roles', $all_roles);
$templator->assign('is_home_page', $is_home_page);

if (!empty($this_page)) {
    $specific_title         = $title . ' - ' . (empty($this_page['page_title_h1']) ? $this_page['uri'] : $this_page['page_title_h1']);
    $iterations             = $diff->getPageIterations($this_page['content_uid']);
    $content_roles          = $pages->pageRoles($this_page['content_uid']);
    $home_page_uris         = Get::$home_pages;
    $content_body_type      = $pages->body->getContentBodyTypeByIterationUid($this_page['uid']);
    $content_body_type_id   = $content_body_type['type_id'];
    $cms_fields             = $pages->body->showCmsFieldsBlock($content_body_type_id, $templator, $this_page['uid']);
    $parent_content_type    = $pages->body->getParentContentBodyType($content_body_type_id);
    $parent_content         = $pages->all('active', $parent_content_type['type_id'], true);

    $templator->assign('iterations', $iterations);
    $templator->assign('content_roles', $content_roles);
    $templator->assign('list_of_home_page_uris', $home_page_uris);
    $templator->assign('cms_fields', $cms_fields);
    $templator->assign('parent_content', $parent_content);
    $templator->assign('content_body_type_id', $content_body_type_id);

    $page_find_replace = [
        'page_title_seo'    => $specific_title,
        'page_title_h1'     => $specific_title,
        'breadcrumbs'       => (new Breadcrumbs())
                                ->crumb('Site Administration', '/admin/')
                                ->crumb($title, '/admin/content/')
                                ->crumb($specific_title),
        'body'              => $templator->fetch('admin/content.tpl'),
    ];
} elseif ($new_page) {
    $cms_fields     = $pages->body->showCmsFieldsBlock($content_body_type_id, $templator);
    $parent_content_type    = $pages->body->getParentContentBodyType($content_body_type_id);
    $parent_content         = !empty($parent_content_type) ? $pages->all('active', $parent_content_type['type_id'], true) : [];

    $templator->assign('cms_fields', $cms_fields);
    $templator->assign('parent_content', $parent_content);
    $templator->assign('content_body_type_id', $content_body_type_id);

    $page_find_replace  = [
        'page_title_seo'    => $title,
        'page_title_h1'     => $title,
        'breadcrumbs'       => (new Breadcrumbs())
            ->crumb('Site Administration', '/admin/')
            ->crumb($title, '/admin/content/')
            ->crumb('New Content'),
        'body'              => $templator->fetch('admin/content.tpl'),
    ];
} else {
    $content_body_types = $pages->body->getContentBodyTypes();
    $templator->assign('content_body_types', $content_body_types);
    
    $page_find_replace = [
        'page_title_seo'    => $title,
        'page_title_h1'     => $title,
        'breadcrumbs'       => (new Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb($title),
        'body'              => $templator->fetch('admin/all_content.tpl'),
    ];
}

echo Templator::page($page_find_replace, true);