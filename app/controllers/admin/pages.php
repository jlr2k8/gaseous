<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 7/20/18
 *
 * pages.php
 *
 * Page CMS management
 *
 */

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

// check setting/role privileges
if (!\Settings::value('add_pages') || !\Settings::value('edit_pages') || !\Settings::value('archive_pages')) {
    \Pages\HTTP::error(401);
}

$settings           = new \Settings();
$pages              = new \Pages\Get();
$templator          = new \Pages\Templator();
$roles              = new \User\Roles();
$account            = new \User\Account();
$diff               = new \Pages\Diff();
$ck_editor          = new \Wysiwyg\CkEditor();

$all_active_pages   = $pages->allPages();
$all_inactive_pages = $pages->allPages('inactive');
$my_account         = $account->getAccountFromSessionValidation();
$full_web_url       = \Settings::value('full_web_url');
$all_uris           = \Pages\Get::allUris(true);
$statuses           = \Pages\Get::statuses();
$page_uri           = !empty($_GET['page_uri_urlencoded']) ? (string)filter_var($_GET['page_uri_urlencoded'], FILTER_SANITIZE_STRING) : false;
$this_page          = $pages->pageContent($page_uri) ?: $pages->pageContent($page_uri, 'inactive');
$new_page           = !empty($_GET['new_page']) && $_GET['new_page'] == 'true';
$is_home_page       = !empty($this_page['uri']) && $this_page['uri'] == 'home';

$title              = 'Page CMS';
$error              = null;

$templator->assign('all_uris', $all_uris);
$templator->assign('ck_editor', $ck_editor);
$templator->assign('statuses', $statuses);
$templator->assign('error', $error);
$templator->assign('active_pages', $all_active_pages);
$templator->assign('inactive_pages', $all_inactive_pages);
$templator->assign('add_pages', \Settings::value('add_pages'));
$templator->assign('edit_pages', \Settings::value('edit_pages'));
$templator->assign('archive_pages', \Settings::value('archive_pages'));
$templator->assign('page', $this_page);
$templator->assign('account', $my_account);
$templator->assign('full_web_url', $full_web_url);
$templator->assign('new_page', $new_page);
$templator->assign('is_home_page', $is_home_page);

if (!empty($_POST)) {
    foreach($_POST as $key => $val) {
        if ($key == 'page_roles') {
            foreach($val as $account_role)
                $post[$key][] = (string)filter_var($account_role, FILTER_SANITIZE_STRING);
        } elseif (!empty($_POST['ckeditor']) && $_POST['ckeditor'] == 'true' && $key == 'value') {
            $post[$key] = htmlspecialchars($val);
        } else {
            $post[$key] = (string)filter_var($val, FILTER_SANITIZE_STRING);
        }
    }

    $submit = new \Pages\Submit();

    if(isset($post['archive'])) {
        $submit->archive();
    } else {
        $submit->upsert();
    }

    header('Location: ' . \Settings::value('full_web_url') . '/admin/pages/');

} elseif (!empty($this_page)) {
    $specific_title = $title . ' - ' . (empty($this_page['page_title_h1']) ? $this_page['uri'] : $this_page['page_title_h1']);
    $uri_as_array   = \Pages\Utilities::uriAsArray($this_page['uri']);
    $this_uri_piece = \Pages\Utilities::getLastPartOfUri($uri_as_array);
    $parent_uri     = \Pages\Utilities::generateParentUri($uri_as_array);
    $iterations     = $diff->getPageIterations($this_page['page_master_uid']);
    $page_roles     = $pages->pageRoles($this_page['uid']);
    $all_roles      = $roles->getAll();

    $templator->assign('uri_as_array', $uri_as_array);
    $templator->assign('this_uri_piece', $this_uri_piece);
    $templator->assign('parent_uri', $parent_uri);
    $templator->assign('iterations', $iterations);
    $templator->assign('page_roles', $page_roles);
    $templator->assign('all_roles', $all_roles);

    $page_find_replace = [
        'page_title_seo'    => $specific_title,
        'page_title_h1'     => $specific_title,
        'breadcrumbs'       => (new \Pages\Breadcrumbs())
                                ->crumb('Site Administration', '/admin/')
                                ->crumb($title, '/admin/pages/')
                                ->crumb($specific_title),
        'body'              => $templator->fetch('admin/page.tpl'),
    ];
} elseif ($new_page) {
    $page_find_replace = [
        'page_title_seo'    => $title,
        'page_title_h1'     => $title,
        'breadcrumbs'       => (new \Pages\Breadcrumbs())
            ->crumb('Site Administration', '/admin/')
            ->crumb($title, '/admin/pages/')
            ->crumb('New Page'),
        'body'              => $templator->fetch('admin/page.tpl'),
    ];
} else {
    $page_find_replace = [
        'page_title_seo'    => $title,
        'page_title_h1'     => $title,
        'breadcrumbs'       => (new \Pages\Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb($title),
        'body'              => $templator->fetch('admin/pages.tpl'),
    ];
}

echo \Pages\Templator::page($page_find_replace);