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

use Assets\Output;
use Content\Http;
use Content\Templator;
use Utilities\Sanitize;

if (!Settings::value('edit_content') || !Settings::value('add_content')) {
    Http::error(403);
}

$content_field_uid      = !empty($_GET['content_field_uid']) ? Sanitize::string($_GET['content_field_uid']) : null;
$content_iteration_uid  = !empty($_GET['content_iteration_uid']) ? Sanitize::string($_GET['content_iteration_uid']) : null;

$templator  = new Templator();
$body       = new Content\Body();
$file       = new File();

$cms_field  = $body->getCmsField($content_field_uid, $content_iteration_uid);

$templator->assign('content_field_uid', $content_field_uid);
$templator->assign('content_iteration_uid', $content_iteration_uid);
$templator->assign('css_output', Output::css($templator));
$templator->assign('css_iterator_output', Output::latestCss($templator));

foreach ($cms_field as $key => $val) {
    $templator->assign($key, $val);
}

$upload_field = $templator->fetch('admin/content/field_types/inc/file_upload_form.tpl');

$templator->assign('file_input', $upload_field);

if (empty($cms_field)) {
    Http::header(400);

    $message = 'Invalid CMS field UID';

    Log::app($message);

    die($message);
}

if ($cms_field['content_body_field_type_id'] != 'file_upload') {
    Http::header(400);

    $message = 'Not a valid upload field type';

    Log::app($message);

    die($message);
}

if (empty($_FILES) && !empty($cms_field['value'])) {
    $url = $cms_field['value'];
    $templator->assign('url', $url);
} elseif (!empty($_FILES)) {
    $allowed_extensions = [];

    if (!empty($cms_field['properties'])) {
        foreach ($cms_field['properties'] as $property) {
            if (!empty($property['property']) && $property['property'] == 'file_upload_allowed_extensions') {
                $allowed_extensions = explode(',', $property['value']);
            }
        }
    }

    $url    = $file->uploadFormFile($cms_field['template_token'] . '_file', $allowed_extensions);

    if (!empty($url)) {
        $templator->assign('url', $url);
    } else {
        Http::header(400);

        $message = 'No file uploaded';

        Log::app($message);

        die($message);
    }
}

echo $templator->fetch('admin/content/field_types/inc/file_upload_form.tpl');