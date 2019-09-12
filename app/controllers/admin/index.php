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

use \Content\Pages\Breadcrumbs;
use \Content\Pages\Templator;

$templator = new Templator;

$templator->assign('full_web_url', \Settings::value('full_web_url'));

$templator->assign('edit_users', \Settings::value('edit_users'));
$templator->assign('archive_users', \Settings::value('archive_users'));

$templator->assign('add_roles', \Settings::value('add_roles'));
$templator->assign('edit_roles', \Settings::value('edit_roles'));
$templator->assign('archive_roles', \Settings::value('archive_roles'));

$templator->assign('add_pages', \Settings::value('add_pages'));
$templator->assign('edit_pages', \Settings::value('edit_pages'));
$templator->assign('archive_pages', \Settings::value('archive_pages'));

$templator->assign('manage_css', \Settings::value('manage_css'));

$templator->assign('edit_settings', \Settings::value('edit_settings'));

$templator->assign('add_redirects', \Settings::value('add_redirects'));
$templator->assign('edit_redirects', \Settings::value('edit_redirects'));
$templator->assign('archive_redirects', \Settings::value('archive_redirects'));

$templator->assign('add_routes', \Settings::value('add_routes'));
$templator->assign('edit_routes', \Settings::value('edit_routes'));
$templator->assign('archive_routes', \Settings::value('archive_routes'));

$title = 'Administration';

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())->crumb($title),
    'body'              => $templator->fetch('admin/main.tpl'),
];

echo Templator::page($page_find_replace);