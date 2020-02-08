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

use Content\Pages\Get;
use Content\Pages\HTTP;

$get_uid                = !empty($_GET['uid']) ? (string)filter_var($_GET['uid'], FILTER_SANITIZE_STRING) : false;
$get_page_master_uid    = !empty($_GET['page_master_uid']) ? (string)filter_var($_GET['page_master_uid'], FILTER_SANITIZE_STRING) : false;
$content_only           = !empty($_GET['content_only']) && $_GET['content_only'] == 'true';

if (empty($get_uid))
    HTTP::error(400);

$get = new Get();

echo $get->pagePreviewByIterationUid($get_uid, $get_page_master_uid, $content_only);