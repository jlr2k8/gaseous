<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/5/18
 *
 * js.php
 *
 * JS administration. Iterate and select a version of JS on this page. The JS is outputted by the
 * services/js_output.php script as /js.gz.js in the browser.
 *
 */

use Assets\JsIterator;
use Assets\Headers;
use Content\Pages\Breadcrumbs;
use Content\Pages\HTTP;
use Content\Pages\Templator;
use Wysiwyg\Codemirror;

// check setting/role privileges
if (!\Settings::value('manage_js')) {
    HTTP::error(401);
}

$js    = new JsIterator();

if(!empty($_GET['exit_preview']) && $_GET['exit_preview'] == 'true') {
    unset($_SESSION['js_preview']);

    $headers        = new Headers();

    $latest_js     = $js->getCurrentJsIteration();
    $preview        = $_SESSION['js_preview'] ?? null;

    $headers->last_modified = strtotime($latest_js['modified_datetime']);

    header('Location: ' . \Settings::value('full_web_url') . '/admin/js/');

    exit;
}

$templator      = new Templator();
$codemirror     = new Codemirror();

$js_iterations = $js->getAllIterations();
$post           = [];

if (!empty($_POST)) {
    foreach($_POST as $key => $val) {
        if ($key == 'js_iteration') {
            $post[$key] = htmlspecialchars($val);
        } else {
            $post[$key] = (string)filter_var($val, FILTER_SANITIZE_STRING);
        }
    }

    if(!empty($post['submit_option_to_preview']) && !empty($post['js_iteration_list'])) {
        $js_iteration_content = $js->getJsIteration($post['js_iteration_list'], true);

        $js->setJsPreview($js_iteration_content['js'], false, $post['js_iteration_list']);
    } elseif(!empty($post['submit_option_to_editor'])) {
        $js_iteration_content = $js->getJsIteration($post['js_iteration_list'], true);

        $js->setEditorJs($js_iteration_content['js'], $post['js_iteration_list']);
        unset($_SESSION['js_preview']);
    } elseif(!empty($post['submit_textarea_to_preview'])) {
        $js->setJsPreview($post['js_iteration']);
        $js->setEditorJs($post['js_iteration']);
    } elseif(!empty($post['submit_textarea'])) {
        $save_iteration = $js->saveJsIteration($post);

        unset($_SESSION['js_preview'], $_SESSION['editor_js']);
    } elseif(!empty($post['revert_editor_js'])) {
        unset($_SESSION['js_preview'], $_SESSION['editor_js']);
    }

    header('Location: ' . \Settings::value('full_web_url') . '/admin/js/');
    exit;
}

if (empty($_SESSION['editor_js'])) {
    $editor_js                     = !empty($_SESSION['editor_js']['uid']) ? $js->getJsIteration($_SESSION['editor_js']['uid']) : $js->getCurrentJsIteration();

    $_SESSION['editor_js']['js']  = $editor_js['js'];
    $_SESSION['editor_js']['uid']  = $editor_js['uid'];
}

$templator->assign('js_iterations', $js_iterations);
$templator->assign('codemirror', $codemirror);
$templator->assign('editor_js_content', $_SESSION['editor_js']['js'] ?? null);
$templator->assign('editor_js_uid', $_SESSION['editor_js']['uid'] ?? null);
$templator->assign('preview_mode', !empty($_SESSION['js_preview']));
$templator->assign('full_web_url', \Settings::value('full_web_url'));

$title = 'Custom Site JS';

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb($title),
    'body'              => $templator->fetch('admin/js.tpl'),
];

echo Templator::page($page_find_replace);