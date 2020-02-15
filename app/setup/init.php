<?php
/**
 * Created by Josh L. Rogers
 * Copyright (c) 2018 All Rights Reserved.
 * 2/27/2018
 *
 * init.php
 *
 * Core configuration for application
 *
 **/

use User\Login;

define('PAGE_LOAD_START', microtime(true));

require_once $_SERVER['WEB_ROOT'] . '/libraries/Autoload.php';
require_once $_SERVER['WEB_ROOT'] . '/setup/constants.php';

if (PHP_SAPI != 'cli') {
    session_name(SESSION_NAME);
    session_start();
}

// persist login if cookie is valid/exists
if (\Settings::checkCoreTables() === true) {
    $login = new Login();
    $login->checkLogin();
} else {
    // begin setup wizard
}