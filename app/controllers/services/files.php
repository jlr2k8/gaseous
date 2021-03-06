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
$filename       = filter_var($upload_root . '/' . $_GET['src'], FILTER_SANITIZE_URL);
$client_headers = apache_request_headers();

// 404 if file src not provided or doesn't exist locally
if (empty($filename) || !is_readable($filename) || !is_file($filename)) {
    Http::error(404);
}

// 400 if file src is invalid
if (!File::validatePath($filename)) {
    Http::error(400);
}

$headers    = (new Headers($filename))->file();

// compress with gz (if available)
if (!ob_start('ob_gzhandler') || !stristr($client_headers['Accept-Encoding'] ?? null, 'gzip'))
    ob_start();

// echo out file
echo file_get_contents($filename);

ob_end_flush();