<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 3/13/20
 *
 * menu.php
 *
 * Site menu administration
 *
 **/

use Assets\Output;
use Content\Menu;
use Content\Breadcrumbs;
use Content\Http;
use Content\Templator;
use Uri\Uri;
use Utilities\Sanitize;

$templator  = new Templator();
$menu       = new Menu();

$title              = 'Site Menu';
$error              = null;
$menu->admin        = true;
$rendered_menu      = $menu->renderMenu();
$menu_items         = $menu->getMenuItems();
$all_uris           = Uri::all();
$manage_menu        = Settings::value('manage_menu');
$post               = [];

// check setting/role privileges
if (!$manage_menu) {
    Http::error(403);
}

if (!empty($_POST)) {
    foreach ($_POST as $key => $val) {
        $post[$key] = Sanitize::string($val);
    }
}

if (!empty($_POST['update']) && $_POST['update'] == 'true') {
    $post['menu_uri_uid']   = $post['uri_uid'];
    $updated                = $menu->updateMenuItem($post);

    if ($updated) {
        header('Location: ' . Settings::value('full_web_url') . '/admin/menu/');
    }
} elseif (!empty($_POST['update_sort']) && $_POST['update_sort'] == 'true') {
    $menu->processMenu($post['menu']);

    exit;
} elseif (!empty($_GET['archive'])) {
    $uid    = Sanitize::string($_GET['archive']);

    $menu->archiveMenuItem($uid);
    $menu->archiveOrphans();

    exit;
} elseif (!empty($_POST['new']) && $_POST['new'] == 'true') {
    $add_menu_item  = false;

    if (!empty($post['custom_uri'])) {
        $uri_obj    = new Uri();
        $uri        = '/' . filter_var($post['custom_uri'], FILTER_SANITIZE_URL);

        if (!Uri::uriExistsAsRedirect($uri) && !Uri::uriExistsAsContent($uri)) {
            $uri_obj->insertUri($uri);
        }

        $post['menu_uri_uid']    = $uri_obj->getUriUid($uri);
    }

    $add_menu_item = $menu->addMenuItem($post);

    if ($add_menu_item) {
        header('Location: ' . Settings::value('full_web_url') . '/admin/menu/');
    } else {
        $error = $menu->getErrors();
    }
}

$templator->assign('menu_items', $menu_items);
$templator->assign('rendered_menu', $rendered_menu);

if (!empty($_GET['load_current_site_menu'])) {
    $templator->assign('admin', $manage_menu);
    $templator->assign('css_output', Output::css($templator));
    $templator->assign('css_iterator_output', Output::latestCss($templator));

    echo $templator->fetch('admin/menu/menu-sortable.tpl');

    exit;
}

$templator->assign('full_web_url', Settings::value('full_web_url'));
$templator->assign('menu_sortable_iframe_url', Settings::value('full_web_url') . '/admin/menu?load_current_site_menu=true');
$templator->assign('all_uris', $all_uris);
$templator->assign('error', $error);

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())
        ->crumb('Site Administration', '/admin/')
        ->crumb($title),
    'body'              => $templator->fetch('admin/menu.tpl'),
];

echo $templator::page($page_find_replace);