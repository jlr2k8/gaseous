<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 5/19/18
 *
 * routes.php
 *
 * URI route administration. This manages the URI-pattern-to-endpoint mapping that takes browser request patterns
 * and loads up the correct controller/endpoint.
 *
 **/

use \Content\Pages\Breadcrumbs;
use \Content\Pages\HTTP;
use \Content\Pages\Templator;
use \Uri\Route;

// check setting/role privileges
if (!Settings::value('add_routes') && !Settings::value('edit_routes') && !Settings::value('archive_routes')) {
    HTTP::error(403);
}

$templator  = new Templator();
$route      = new Route();

$title      = 'URI Routes';
$routes     = $route->getAll();
$error      = null;

$add_routes     = Settings::value('add_routes');
$edit_routes    = Settings::value('edit_routes');
$archive_routes = Settings::value('archive_routes');

if (!empty($_POST) && $edit_routes && !isset($_GET['sort'])) {
    foreach ($_POST as $key => $val) {
        $post[$key] = (string)filter_var($val, FILTER_SANITIZE_STRING);
    }

    $submit_route = false;

    if (isset($post['update'])) {
        $submit_route = $route->update($post);
    } elseif (isset($post['new'])) {
        $submit_route   = $route->insert($post);
    }

    if ($submit_route) {
        header('Location: ' . Settings::value('full_web_url') . '/admin/routes/');
        exit;
    } else {
        $error = $route->getErrors();
    }
} elseif (!empty($_POST) && $edit_routes && isset($_GET['sort'])) {
    $priority_to_uuids = [];

    foreach($_POST['sorted'] as $key => $val) {
        $key                        = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
        $priority_to_uuids[$key]    = filter_var($val, FILTER_SANITIZE_STRING);
    }

    $route->sortPriorityBulk($priority_to_uuids);
    exit;
}

if (!empty($_GET['archive']) && $archive_routes) {
    $uid    = filter_var($_GET['archive'], FILTER_SANITIZE_STRING);
    $route->archive($uid);

    exit;
}

$templator->assign('full_web_url', Settings::value('full_web_url'));
$templator->assign('all_routes', $routes);
$templator->assign('add_routes', $add_routes);
$templator->assign('edit_routes', $edit_routes);
$templator->assign('archive_routes', $archive_routes);

$templator->assign('error', $error);

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())
        ->crumb('Site Administration', '/admin/')
        ->crumb($title),
    'body'              => $templator->fetch('admin/routes.tpl'),
];

echo Templator::page($page_find_replace);