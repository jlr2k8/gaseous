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

$get_uid        = !empty($_GET['uid']) ? (string)filter_var($_GET['uid'], FILTER_SANITIZE_STRING) : false;
$content_only   = (!empty($_GET['content_only']) && $_GET['content_only'] == 'true');

if (empty($get_uid))
    \Pages\HTTP::error(400);

$get = new \Pages\Get();

echo $get->pagePreviewByIterationUid($get_uid, $content_only);