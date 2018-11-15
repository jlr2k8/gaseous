<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2016 All Rights Reserved.
 * 7/16/2016
 *
 * upload.php
 *
 * CK Editor upload image
 */

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

$file_upload            = !empty($_FILES['upload']['tmp_name']) ? $_FILES['upload']['tmp_name'] : false;
$file_reference         = !empty($_FILES['upload']['name']) ? $_FILES['upload']['name'] : false;
$upload_root            = \Settings::value('upload_root');
$upload_url_relative    = \Settings::value('upload_url_relative');

if ($file_upload && $file_reference) {

    $token                      = \Utilities\Token::generate();
    $file_pathinfo              = pathinfo($file_reference);
    $tokenized_file_reference   = $file_pathinfo['filename'] . '-' . $token . '.' . $file_pathinfo['extension'];

    // img folder should always be writable (max permission ok here)
    if (!is_dir($upload_root))
        mkdir($upload_root, 0777, true);

    if (!is_writable($upload_root))
        chmod($upload_root, 0777);

    // move it on up!
    if (move_uploaded_file($file_upload, $upload_root . '/' . $tokenized_file_reference)) {

        // callback for CK editor to see upload
        $func_num = $_GET['CKEditorFuncNum'];

        // url for callback to pass into cms
        $url = $upload_url_relative . '/' . $tokenized_file_reference;

        // message (dont need this)
        $message = null;

        // service echos out the script tag
        echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($func_num, '$url', '$message');</script>";

        exit;
    }

    http_response_code(500); // error

    exit;
}

http_response_code(400); // bad request

exit;