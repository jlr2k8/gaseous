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

use \Content\Pages\HTTP;
use \Uri\Route;
use \Uri\Redirect;

$uri        = (string)filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);

$uri_route  = new Route();
$uri_redir  = new Redirect();

// first, check to see if this URI is actively being redirected
$redirect   = $uri_redir->getByUri($uri);

if (!empty($redirect)) {
    HTTP::redirect (
        $redirect['destination_url'],
        $redirect['http_status_code'] ?? false
    );

    exit;
}

$uri_data   = $uri_route->parseUri($uri);

$path   = !empty($uri_data['path']) ? (string)filter_var($uri_data['path'], FILTER_SANITIZE_URL) : false;
$_GET   = array_merge($_GET, $uri_data['query']);

if (!empty($path)) {
    require_once $path;
} else {
    HTTP::error(404);
}