<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 9/13/18
 *
 * preview_content_iteration.php
 *
 * Preview page by the iteration uid - $_GET['uid']
 *
 **/

use Content\Get;
use Content\Http;

$get_uid                = !empty($_GET['uid']) ? (string)filter_var($_GET['uid'], FILTER_SANITIZE_STRING) : false;
$get_content_uid        = !empty($_GET['content_uid']) ? (string)filter_var($_GET['content_uid'], FILTER_SANITIZE_STRING) : false;
$content_only           = !empty($_GET['content_only']) && $_GET['content_only'] == 'true';

if (empty($get_uid))
    Http::error(400);

$get = new Get();

echo $get->previewByIterationUid($get_uid, $get_content_uid, $content_only);