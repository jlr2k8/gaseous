<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2016 All Rights Reserved.
 * 3/19/2016
 *
 * images.php
 *
 * Sets image headers (requires rewrite rule)
 *
 * example: an image tag with the src "/img/blurb_about_02.png" just serves up an image. no header, nothing.
 * this service, with the rewrite rule, will turn "/img/blurb_about_02.png" into /inc/services/images.php?src=blurb_about_02.png
 *
 */


use Assets\Headers;
use Content\Http;

$upload_root    = !empty($_GET['upload_root']) && $_GET['upload_root'] === 'true' ? Settings::value('upload_root') : WEB_ROOT . '/assets/img';
$filetype       = !empty($_GET['filetype']) ? $_GET['filetype'] : false;
$filename       = !empty($_GET['src']) && is_readable($upload_root . '/' . $_GET['src']) ? $upload_root . '/' . $_GET['src'] : false;
$client_headers = apache_request_headers();

// 404 if img not provided or doesn't exist locally
if (!$filename) {
    Http::error(404);
    exit;
}

// 400 if requested image is not valid
if (!File::validatePath($filename)) {
    Http::error(400);
    exit;
}

$headers    = (new Headers($filename))->images($filetype);

// compress with gz (if available)
if (!ob_start('ob_gzhandler') || !stristr($client_headers['Accept-Encoding'], 'gzip'))
    ob_start();

// echo out file
echo file_get_contents($filename);

ob_end_flush();