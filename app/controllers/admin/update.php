<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 8/9/20
 *
 * update.php
 *
 * Download the latest stable Gaseous source code, and perform updates against the code base and database.
 *
 **/


use Content\Breadcrumbs;
use Content\Http;
use Content\Templator;
use Setup\Update;

if (!Settings::value('perform_updates')) {
    Http::error(403);
}

$breadcrumbs    = new Breadcrumbs();
$templator      = new Templator();
$update         = new Update();
$status         = null;

$title  = 'Site Updates';
$latest = $update->latest;
$status = version_compare($latest['app_version'], APP_VERSION);

if (!empty($_POST['perform_updates']) && $_POST['perform_updates'] == 'true') {
    $app_root = realpath(WEB_ROOT . '/..');

    if (!is_writable($app_root)) {
        $status = '
            Due to permission issues within the webroot, the update could not be automatically performed.
            Please allow the web server to read/write to: '
            . $app_root
        ;
    } else {
        $status = $update->update()
            ? '<span class="green_text">Updates were successful!</span>'
            : '<span class="red_text">Something went wrong. Please check the Gaseous logs...</span>'
        ;
    }

    // Check latest again...
    $latest = $update->getLatestBuildInfo();
}

$templator->assign('latest', $latest);
$templator->assign('status', $status);

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())
        ->crumb('Site Administration', '/admin/')
        ->crumb($title),
    'body'              => $templator->fetch('admin/update.tpl'),
];

echo $templator::page($page_find_replace);