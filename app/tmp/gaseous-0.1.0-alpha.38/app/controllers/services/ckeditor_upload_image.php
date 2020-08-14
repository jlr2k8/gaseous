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

if (!Settings::value('edit_content') || !Settings::value('add_content')) {
    Http::error(403);
}

$url                = $file->uploadFormFile('upload');
$ckeditor_func_num  = $_GET['CKEditorFuncNum'];

if (!empty($url)) {
    // message (dont need this for now)
    $message = null;

    // service echos out the script tag
    echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($ckeditor_func_num, '$url', '$message');</script>";

    exit;
} else {
    Http::error(400);
}