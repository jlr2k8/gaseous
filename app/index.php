<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2019 All Rights Reserved.
 * 5/12/19
 *
 * index.php
 *
 * Primary request entrance point. This script includes the required setup files, and maps the request to the URI
 * pattern stored in the uri_routes table. If no match is found, a 404 is returned.
 *
 **/

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

$uri        = (string)filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
$uri_route  = new \UriRoute();
$uri_data   = $uri_route->parseUri($uri);
$path       = !empty($uri_data['path']) ? (string)filter_var($uri_data['path'], FILTER_SANITIZE_URL) : false;
$_GET       = array_merge($_GET, $uri_data['query']);

if (!empty($path)) {
    require_once $path;
} else {
    \Content\Pages\HTTP::error(404);
}