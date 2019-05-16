<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/20/18
 *
 * redirects.php
 *
 * Redirect URI administration. Controlled by admins and system. If a CMS page's URI is modified, the old URI redirects
 * (301 header) to the new one by default. Each of these are stored as a row in the redirects table.
 *
 **/

// check setting/role privileges
if (!\Settings::value('add_redirects') && !\Settings::value('edit_redirects') && !\Settings::value('archive_redirects')) {
    \Content\Pages\HTTP::error(401);
}

$templator = new \Content\Pages\Templator();

$templator->assign('full_web_url', \Settings::value('full_web_url'));

$title = 'Site Redirects';

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new \Content\Pages\Breadcrumbs())
        ->crumb('Site Administration', '/admin/')
        ->crumb($title),
    'body'              => $templator->fetch('admin/redirects.tpl'),
];

echo \Content\Pages\Templator::page($page_find_replace);