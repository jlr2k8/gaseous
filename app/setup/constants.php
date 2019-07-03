<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 2/11/2018
 *
 * constants.php
 *
 * Configuration and database constants
 *
 */

if (PHP_SAPI != 'cli') {
    define('ENVIRONMENT', $_SERVER['ENVIRONMENT']);
} else {
    $environment_param = filter_var($argv[1], FILTER_SANITIZE_STRING);

    define('ENVIRONMENT', $environment_param);
}

// Bootstrap configuration for cookie/session handling
define('LOGIN_COOKIE', ($_SERVER['LOGIN_COOKIE'] ?? 'login'));
define('SESSION_NAME', ($_SERVER['SESSION_NAME'] ?? 'session'));