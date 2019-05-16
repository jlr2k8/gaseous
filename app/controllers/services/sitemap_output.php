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

$sitemap = new \Seo\SiteMap();

$sitemap->outputHeaders();

echo $sitemap->getUpdatedMap();