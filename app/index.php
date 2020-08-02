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

require_once dirname(__FILE__) . '/setup/init.php';

use \Content\Http;
use \Uri\Route;
use \Uri\Redirect;

$relative_uri   = Settings::value('relative_uri') ?: '/';
$uri_redir      = new Redirect();

// first, check to see if this URI is actively being redirected
$redirect   = $uri_redir->getByUri($relative_uri);

if (!empty($redirect)) {
    Http::redirect (
        $redirect['destination_url'],
        $redirect['http_status_code'] ?? false
    );

    exit;
}

$uri_route  = new Route();
$uri_data   = $uri_route->parseUri($relative_uri);

$path               = !empty($uri_data['path']) ? (string)filter_var($uri_data['path'], FILTER_SANITIZE_URL) : false;
$_GET               = array_merge($_GET, $uri_data['query']);
$is_disallowed_path = Route::isDisallowedPath($path);

if (!empty($path) && !$is_disallowed_path && File::validatePath($path)) {
    require_once $path;
} else {
    Http::error(404);
}