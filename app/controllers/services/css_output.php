<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2016 All Rights Reserved.
 * 3/22/2016
 *
 * css_output.php
 *
 * Outputs DB (CSS Iterator) CSS
 *
 */

use Assets\Css;
use Assets\CssIterator;
use Assets\Headers;
use Seo\Minify;


$headers    = new Headers();
$css_output = null;
$iteration  = !empty($_GET['iteration']) ? filter_var($_GET['iteration'], FILTER_SANITIZE_STRING) : false;

$headers->css();

if (empty($iteration)) {
    $css_output = (new Css())->get();
} else {
    $css_iterator   = new CssIterator();
    $css            = $css_iterator->getCssIteration($iteration, true);
    $headers->last_modified = strtotime($css['modified_datetime']);

    if (!empty($_SESSION['css_preview'])) {
        $css_output = $_SESSION['css_preview']['css'];
    } else {
        header('Content-Encoding: gzip');
        $css_output = gzencode(Minify::css($css['css']));
    }
}

echo $css_output;