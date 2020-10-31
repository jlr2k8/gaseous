<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 8/30/20
 *
 * file-uploader.php
 *
 * Simple file uploader for upload_root directory for admins
 *
 **/

use Content\Breadcrumbs;
use Content\Templator;

// check setting/role privileges
if (!Settings::value('file_uploader')) {
    Http::error(403);
}

$templator  = new Templator();
$file       = new File();

$default_file_extensions            = File::$allowed_file_extensions;
$settings_allowed_file_extensions   = Settings::value('allowed_file_upload_extensions');
$allowed_file_extensions_exploded   = explode(',', $settings_allowed_file_extensions);
$allowed_file_extensions            = array_merge($default_file_extensions, $allowed_file_extensions_exploded);

$title          = 'File Uploader';
$uploaded_file  = false;
$error          = false;

$templator->assign('allowed_file_extensions', $allowed_file_extensions);

if (!empty($_FILES)) {
    $uploaded_file  = $file->uploadFormFile('upload', $allowed_file_extensions);

    if ($uploaded_file) {
        $templator->assign('uploaded_file', Settings::value('full_web_url') . $uploaded_file);
    }

    $templator->assign('error', $file->error);
}

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb($title),
    'body'              => $templator->fetch('admin/file-uploader.tpl'),
];

echo Templator::page($page_find_replace);