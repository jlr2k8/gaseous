<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 5/10/20
 *
 * ckeditor_upload_image.php
 *
 * Inline image uploader from CK Editor GUI
 *
 **/

use Content\Http;

$file = new File();

if (!Settings::value('file_uploader')) {
    Http::error(403);
}

$default_file_extensions            = File::$allowed_file_extensions;
$settings_allowed_file_extensions   = Settings::value('allowed_file_upload_extensions');
$allowed_file_extensions_exploded   = explode(',', $settings_allowed_file_extensions);
$allowed_file_extensions            = array_merge($default_file_extensions, $allowed_file_extensions_exploded);

$url                                = $file->uploadFormFile('upload', $allowed_file_extensions);
$ckeditor_func_num                  = $_GET['CKEditorFuncNum'];

if (!empty($url)) {
    // message (dont need this for now)
    $message = null;

    // service echos out the script tag
    echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($ckeditor_func_num, '$url', '$message');</script>";

    exit;
} else {
    Http::error(400);
}