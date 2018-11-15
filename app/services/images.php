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

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

// get src param
$upload_root    = \Settings::value('upload_root');
$filetype       = !empty($_GET['filetype']) ? $_GET['filetype'] : false;
$filename       = !empty($_GET['src']) && is_file($upload_root . '/' . $_GET['src']) ? $upload_root . '/' . $_GET['src'] : false;

// 404 if img not provided or doesn't exist locally
if (!$filename) {
    \Content\Pages\HTTP::error(404);
}

$client_headers = apache_request_headers();
$headers        = new \Headers($client_headers, $filename, $filetype);

$headers->images();

// compress with gz (if available)
if (!ob_start('ob_gzhandler') || !stristr($client_headers['Accept-Encoding'], 'gzip'))
    ob_start();


// echo out file
echo file_get_contents($filename);

ob_end_flush();