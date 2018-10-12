<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 9/30/18
 *
 * sitemap_output.php
 *
 * Output a dynamic /sitemap.xml (see .htaccess rewrite rules)
 *
 **/

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

$sitemap = new \Seo\SiteMap();

$sitemap->outputHeaders();

echo $sitemap->getUpdatedMap();