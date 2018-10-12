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

$get = new \Pages\GetHomePage();

if (in_array($_SERVER['REQUEST_URI'], \Pages\GetHomePage::$home_pages)) {
    $get->redirectHome();
}

echo $get->byUri();