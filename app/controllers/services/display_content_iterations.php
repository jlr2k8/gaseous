<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/25/18
 *
 * display_content_iterations.php
 *
 * API/UI Endpoint to load up the iterations of a page
 *
 **/

// restricted access
use Content\Pages\Diff;
use Content\Pages\Get;
use Content\Pages\HTTP;
use Content\Pages\Templator;
use Content\Pages\Utilities;

if (!Settings::value('edit_pages')) {
    HTTP::error(403);
}

$content_uid = !empty($_GET['content_uid']) ? (string)filter_var($_GET['content_uid'], FILTER_SANITIZE_STRING) : false;

if ($content_uid) {
    $templator  = new Templator();
    $diff       = new Diff();
    $get        = new Get();

    $uri        = Utilities::pageUriFromMasterUid($content_uid);
    $iterations = $diff->getPageIterations($content_uid);
    $page       = $get->pageContent($uri) ?: $get->pageContent($uri, 'inactive');

    $templator->assign('iterations', $iterations);
    $templator->assign('page', $page);
    $templator->assign('full_web_url', Settings::value('full_web_url'));

    echo $templator->fetch('admin/content_iterations.tpl');
} else {
    HTTP::error(400);
}