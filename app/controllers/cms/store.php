<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 4/12/2018
 *
 * index.php
 *
 * Home page
 *
 **/

require_once $_SERVER['WEB_ROOT'] . '/setup/init.php';

$templator  = new Pages\Templator();
$sidebar = $templator->fetch('store/sidebar.tpl');

$templator->assign('sidebar', $sidebar);
$templator->assign('content', 'store content goes here');
$templator->assign('pagination', null);

$find_replace['main'] = $templator->fetch('store/test-main.tpl');

echo $homepage->byUri($find_replace);