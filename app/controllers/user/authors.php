<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 7/21/20
 *
 * authors.php
 *
 * Landing page for /users/ and /users/{username}/ - browse promoted user content, such as blog articles
 *
 **/

use Content\Breadcrumbs;
use Content\Get;
use Content\Templator;
use Utilities\Pager;

$pages          = new Get();
$templator      = new Templator();

$user           = !empty($_GET['account_id']) ? filter_var($_GET['account_id'], FILTER_SANITIZE_STRING) : false;
$default_title  = 'Browse Content by All Authors';
$breadcrumbs    = (new Breadcrumbs())->crumb('Users', '/users/');

$page_find_replace  = [
    'page_title_seo'    => $default_title,
    'page_title_h1'     => $default_title,
    'breadcrumbs'       => $breadcrumbs,
];

if (!empty($user)) {
    $user_page_title                        = 'Browse Content Authored by <em>' . $user . '</em>';

    $page_find_replace['page_title_seo']    = $user_page_title;
    $page_find_replace['page_title_h1']     = $user_page_title;
    $page_find_replace['breadcrumbs']       = $breadcrumbs->crumb($user);
}

$content = $pages->promotedUserContent($user);

$content_paged  = Cms::getContentPaged($content);
$pager          = Cms::pager($content);

$templator->assign('content', $content_paged);
$templator->assign('pager', $pager);

$page_find_replace['body'] = $templator->fetch('user/browse_promoted_content.tpl');

echo $templator::page($page_find_replace);