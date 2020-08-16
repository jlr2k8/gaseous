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

use Setup\Utilities;
use User\Login;

require_once dirname(__DIR__) . '/setup/constants.php';
require_once GASEOUS_AUTOLOADER;

// PHP Errors
ini_set('error_reporting', '-1');
ini_set('display_errors', 'Off');
ini_set('log_errors', 'On');

if (\Setup\Utilities::checkCoreData() === true) {
    ini_set('error_reporting', Settings::value('error_reporting'));
    ini_set('display_errors', Settings::value('error_reporting'));
    ini_set('log_errors', Settings::value('error_reporting'));
}

if (PHP_SAPI != 'cli') {
    session_name(SESSION_NAME);
    session_start();

    // Acquire new session ID on page load (but keep current session data)
    if (!empty(session_id())) {
        session_regenerate_id();
    }

    if (Utilities::checkCoreData() === true) {
        // persist login if cookie is valid/exists
        $login = new Login();

        $login->checkLogin();

        // store settings in session
        Settings::cacheSettings();
    } else {
        $_SESSION['setup_mode'] = date('YmdHis', strtotime('+1 hour'));

        require_once 'install.php';

        exit;
    }
}