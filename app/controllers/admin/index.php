<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 6/11/18
 *
 * admin.php
 *
 * Admin - main page
 *
 */

use \Content\Breadcrumbs;
use \Content\Templator;
use \Utilities\AdminView;

$title      = 'Administration';

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())->crumb($title),
    'body'              => AdminView::renderAdminList(),
];

echo Templator::page($page_find_replace);