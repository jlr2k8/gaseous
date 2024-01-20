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
use Content\Diff;
use Content\Get;
use Content\Http;
use Content\Templator;
use Uri\Uri;
use Utilities\Sanitize;

if (!Settings::value('edit_content')) {
    Http::error(403);
}

$content_uid = !empty($_GET['content_uid']) ? Sanitize::string($_GET['content_uid']) : false;

if ($content_uid) {
    $templator  = new Templator();
    $diff       = new Diff();
    $get        = new Get();
    $uri_obj    = new Uri();

    $uri        = $uri_obj->getUri($content_uid);
    $iterations = $diff->getPageIterations($content_uid);
    $page       = $get->contentByUri($uri, 'active', true) ?: $get->contentByUri($uri, 'inactive', true);

    $templator->assign('iterations', $iterations);
    $templator->assign('page', $page);
    $templator->assign('full_web_url', Settings::value('full_web_url'));

    echo $templator->fetch('admin/content_iterations.tpl');
} else {
    Http::error(400);
}