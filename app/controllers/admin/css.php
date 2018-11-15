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

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

// check setting/role privileges
if (!\Settings::value('manage_css')) {
    \Content\Pages\HTTP::error(401);
}

if(!empty($_GET['exit_preview']) && $_GET['exit_preview'] == 'true') {
    unset($_SESSION['css_preview']);
    header('Location: ' . \Settings::value('full_web_url') . '/admin/css/');
}

$css        = new \Css();
$templator  = new \Content\Pages\Templator();
$codemirror = new \Wysiwyg\Codemirror();

$css_iterations = $css->getAllIterations();

if (!empty($_POST)) {
    foreach($_POST as $key => $val) {
        if ($key == 'css_iteration') {
            $post[$key] = htmlspecialchars($val);
        } else {
            $post[$key] = (string)filter_var($val, FILTER_SANITIZE_STRING);
        }
    }

    if(isset($post['submit_option_to_preview'])) {
        try {
            $css->setCssPreview($post['css_iteration_list']);

            header('Location: ' . \Settings::value('full_web_url') . '/admin/css/');
        } catch(\Exception $e) {
            var_dump($e->getTraceAsString(), $e->getMessage());

            die('There was an error setting the preview.');
        }
    } elseif(isset($post['submit_option_to_editor'])) {
        header('Location: ' . \Settings::value('full_web_url') . '/admin/css/?uid=' . $post['css_iteration_list']);
    } elseif(isset($post['submit_textarea_to_preview'])) {
        $save_preview = $css->saveCssIteration($post, true);

        if ($save_preview) {
            header('Location: ' . \Settings::value('full_web_url') . '/admin/css/');
        } else {
            throw new \Exception('There was an error setting the preview.');
        }
    } elseif(isset($post['submit_textarea'])) {
        $save_iteration = $css->saveCssIteration($post);

        unset($_SESSION['css_preview']);

        if ($save_iteration) {
            header('Location: ' . \Settings::value('full_web_url') . '/admin/css/');
        } else {
            throw new \Exception('There was an error saving the iteration.');
        }
    }
}

$get_css_iteration_uid  = !empty($_GET['uid']) ? filter_var($_GET['uid'], FILTER_SANITIZE_STRING) : false;
$selected_css_iteration = $get_css_iteration_uid ? $css->getCssIteration($get_css_iteration_uid) : $css->getCurrentCssIteration();

$templator->assign('css_iterations', $css_iterations);
$templator->assign('codemirror', $codemirror);
$templator->assign('selected_css_iteration', $selected_css_iteration);
$templator->assign('preview_mode', !empty($_SESSION['css_preview']));
$templator->assign('full_web_url', \Settings::value('full_web_url'));

$title = 'Custom Site CSS';

$page_find_replace = [
    'page_title_seo'    => $title,
    'page_title_h1'     => $title,
    'breadcrumbs'       => (new \Content\Pages\Breadcrumbs())->crumb('Site Administration', '/admin/')->crumb($title),
    'body'              => $templator->fetch('admin/css.tpl'),
];

echo \Content\Pages\Templator::page($page_find_replace);