<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 9/16/18
 *
 * diff_content_iterations.php
 *
 * Diff two saved page iterations
 *
 **/

use Content\Pages\Diff;
use Content\Pages\Get;
use Content\Pages\Templator;

$diff       = new Diff();
$get        = new Get();
$templator  = new Templator();

$old_uid    = !empty($_GET['old_uid']) ? (string)filter_var($_GET['old_uid'], FILTER_SANITIZE_STRING) : false;
$new_uid    = !empty($_GET['new_uid']) ? (string)filter_var($_GET['new_uid'], FILTER_SANITIZE_STRING) : false;

$old_page_content   = $get->contentForPreview($old_uid);
$new_page_content   = $get->contentForPreview($new_uid);

$compare_fields = [
    'page_title_seo',
    'page_title_h1',
    'meta_desc',
    'meta_robots',
    'roles', // array
    'include_in_sitemap',
    'minify_html_output',
    'body',
];

foreach($compare_fields as $f) {
    if ($f != 'roles')
        $compare[$f] = Diff::formattedHtml($old_page_content[$f], $new_page_content[$f]);
    else
        $compare[$f] = Diff::formattedHtmlArray($old_page_content[$f], $new_page_content[$f]);
}

$columns_for_comparison = array_keys($old_page_content); // old or new page content shouldn't make a difference. just need the fields.

$templator->assign('compare', $compare);

echo $templator->fetch('admin/diff_content_iterations.tpl');