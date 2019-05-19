<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 12/16/2016
 *
 * js_output.php
 *
 * Outputs DB stored JS and sets headers (requires rewrite rules)
 *
 */

$client_headers = apache_request_headers();
$headers        = new \Headers($client_headers);
$js             = new \Js();
$minify         = new \Seo\Minify();

$latest_js              = $js->getCurrentJsIteration();
$headers->last_modified = strtotime($latest_js['modified']);

$headers->js();

$minified   = $minify->js($latest_js['js']);
$gzencoded  = gzencode($minified);

echo $gzencoded;

ob_end_flush();