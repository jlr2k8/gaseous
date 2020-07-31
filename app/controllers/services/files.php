<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 5/15/20
 *
 * files.php
 *
 * General file output handler service for uploaded files. Unlike the image service, files rendered here typically are
 * treated as downloadable attachments.
 *
 **/

use Assets\Headers;
use Content\Http;

$upload_root    = Settings::value('upload_root');
$filename       = !empty($_GET['src']) && is_readable($upload_root . '/' . $_GET['src']) ? $upload_root . '/' . $_GET['src'] : false;
$client_headers = apache_request_headers();

// 404 if file src not provided or doesn't exist locally
if (empty($filename)) {
    Http::error(404);
    exit;
}

// 400 if file src is invalid
if (!File::validatePath($filename)) {
    Http::error(400);
    exit;
}

$headers    = (new Headers($filename))->file();

// compress with gz (if available)
if (!ob_start('ob_gzhandler') || !stristr($client_headers['Accept-Encoding'], 'gzip'))
    ob_start();

// echo out file
echo file_get_contents($filename);

ob_end_flush();