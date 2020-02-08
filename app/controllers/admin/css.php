<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/5/18
 *
 * css.php
 *
 * CSS administration. Iterate and select a version of CSS on this page. The CSS is outputted by the
 * services/css_output.php script as /styles.css in the browser.
 *
 */

use Content\Pages\Breadcrumbs;
use Content\Pages\HTTP;
use Content\Pages\Templator;
use Wysiwyg\Codemirror;

// check setting/role privileges
if (!\Settings::value('manage_css')) {
    HTTP::error(401);
}

if(!empty($_GET['exit_preview']) && $_GET['exit_preview'] == 'true') {
    //$_SESSION['editor_css']['css'] = $_SESSION['css_preview']['css'];
    //$_SESSION['editor_css']['uid'] = $_SESSION['css_preview']['uid'];

    unset($_SESSION['css_preview']);

    header('Location: ' . \Settings::value('full_web_url') . '/admin/css/');
}

$css            = new Css();
$templator      = new Templator();
$codemirror     = new Codemirror();

$css_iterations = $css->getAllIterations();
$post           = [];

if (!empty($_POST)) {
    foreach($_POST as $key => $val) {
        if ($key == 'css_iteration') {
            $post[$key] = htmlspecialchars($val);
        } else {
            $post[$key] = (string)filter_var($val, FILTER_SANITIZE_STRING);
        }
    }

    if(isset($post['submit_option_to_preview'])) {
        $css_iteration_content = $css->getCssIteration($post['css_iteration_list']);

        $css->setCssPreview($css_iteration_content['css'], false, $post['css_iteration_list']);
        $css->setEditorCss($post['css_iteration_list']);
    } elseif(isset($post['submit_option_to_editor'])) {
        $css->setEditorCss($post['css_iteration_list']);
        unset($_SESSION['css_preview']);
    } elseif(isset($post['submit_textarea_to_preview'])) {
        $css->setCssPreview($post['css_iteration']);
    } elseif(isset($post['submit_textarea'])) {
        $save_iteration = $css->saveCssIteration($post);

        unset($_SESSION['css_preview'], $_SESSION['editor_css']);

        if ($save_iteration) {
            header('Location: ' . \Settings::value('full_web_url') . '/admin/css/');
        } else {
            throw new \Exception('There was an error saving the iteration.');
        }
    }

    header('Location: ' . \Settings::value('full_web_url') . '/admin/css/');
}

if (empty($_SESSION['editor_css'])) {
    $editor_css             = !empty($_SESSION['editor_css']['uid']) ? $css->getCssIteration($_SESSION['editor_css']['uid']) : $css->getCurrentCssIteration();

    $_SESSION['editor_css']['css'] = $editor_css['css'];
    $_SESSION['editor_css']['uid'] = $editor_css['uid'];
}

$templator->assign('css_iterations', $css_iterations);
$templator->assign('codemirror', $codemirror);
$templator->assign('editor_css_content', $_SESSION['editor_css']['css'] ?? null);
$templator->assign('editor_css_uid', $_SESSION['editor_css']['uid'] ?? null);
$templator->assign('preview_mode', !empty($_SESSION['css_preview']));
$templator->assign('full_web_url', \Settings::value('full_web_url'));

$title = 'Custom Site CSS';

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb($title),
    'body'              => $templator->fetch('admin/css.tpl'),
];

echo Templator::page($page_find_replace);