<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 9/13/18
 *
 * preview_page_iteration.php
 *
 * Preview page by the iteration uid - $_GET['uid']
 *
 **/

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

$get_uid                = !empty($_GET['uid']) ? (string)filter_var($_GET['uid'], FILTER_SANITIZE_STRING) : false;
$get_page_master_uid    = !empty($_GET['page_master_uid']) ? (string)filter_var($_GET['page_master_uid'], FILTER_SANITIZE_STRING) : false;
$content_only           = !empty($_GET['content_only']) && $_GET['content_only'] == 'true';

if (empty($get_uid))
    \Content\Pages\HTTP::error(400);

$get = new \Content\Pages\Get();

echo $get->pagePreviewByIterationUid($get_uid, $get_page_master_uid, $content_only);