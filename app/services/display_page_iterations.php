<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/25/18
 *
 * display_page_iterations.php
 *
 * API/UI Endpoint to load up the iterations of a page
 *
 **/

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

// restricted access
if (!\Settings::value('edit_pages')) {
    \Content\Pages\HTTP::error(401);
}

$page_master_uid = !empty($_GET['page_master_uid']) ? (string)filter_var($_GET['page_master_uid'], FILTER_SANITIZE_STRING) : false;

if ($page_master_uid) {
    $templator  = new \Content\Pages\Templator();
    $diff       = new \Content\Pages\Diff();
    $get        = new \Content\Pages\Get();

    $uri        = \Content\Pages\Utilities::pageUriFromMasterUid($page_master_uid);
    $iterations = $diff->getPageIterations($page_master_uid);
    $page       = $get->pageContent($uri) ?: $get->pageContent($uri, 'inactive');

    $templator->assign('iterations', $iterations);
    $templator->assign('page', $page);
    $templator->assign('full_web_url', \Settings::value('full_web_url'));

    echo $templator->fetch('admin/page_iterations.tpl');
} else {
    \Content\Pages\HTTP::error(400);
}