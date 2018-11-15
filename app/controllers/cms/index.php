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

$get = new \Content\Pages\GetHomePage();

if (in_array($_SERVER['REQUEST_URI'], \Content\Pages\GetHomePage::$home_pages)) {
    $get->redirectHome(); exit;
}

echo $get->byUri();