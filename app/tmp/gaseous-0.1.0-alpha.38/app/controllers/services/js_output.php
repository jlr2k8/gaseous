<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2016 All Rights Reserved.
 * 3/22/2016
 *
 * js_output.php
 *
 * Outputs DB (JS Iterator) JS
 *
 */

use Assets\Js;
use Assets\JsIterator;
use Assets\Headers;
use Seo\Minify;


$headers    = new Headers();
$js_output = null;
$iteration  = !empty($_GET['iteration']) ? filter_var($_GET['iteration'], FILTER_SANITIZE_STRING) : false;

if (empty($iteration)) {
    $js_obj                 = new Js();

    $headers->last_modified = $js_obj->concat->directory_modified;
    $js_output              = $js_obj->get();
} else {
    $js_iterator            = new JsIterator();
    $js                     = $js_iterator->getJsIteration($iteration, true);

    if (!empty($_SESSION['js_preview'])) {
        $js_output  = $_SESSION['js_preview']['js'];
    } else {
        header('Content-Encoding: gzip');

        $headers->last_modified = $js['modified_datetime'];
        $js_output              = gzencode(Minify::js($js['js']));
    }
}

$headers->js();

echo $js_output;