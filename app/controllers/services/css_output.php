<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2016 All Rights Reserved.
 * 3/22/2016
 *
 * css_output.php
 *
 * Outputs DB stored CSS and sets headers (requires rewrite rules)
 *
 */

$client_headers = apache_request_headers();
$headers        = new \Headers($client_headers);
$css            = new \Css();
$minify         = new \Seo\Minify();

$latest_css             = $css->getCurrentCssIteration();
$headers->last_modified = strtotime($latest_css['modified']);

$headers->css();

$minified   = $minify->css($latest_css['css']);
$gzencoded  = gzencode($minified);

echo $gzencoded;

ob_end_flush();