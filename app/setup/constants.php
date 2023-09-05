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

define('PAGE_LOAD_START', microtime(true));

define('APP_VERSION', '0.6.1');

define('WEB_ROOT', dirname(__DIR__));
define('DB_ROOT', realpath(WEB_ROOT . '/../db'));
define('EXPANSION_ROOT', realpath(WEB_ROOT . '/../expansions'));

define('GASEOUS_AUTOLOADER', WEB_ROOT . '/libraries/Autoload.php');

define('ENVIRONMENT_INI', WEB_ROOT . '/setup/environments.ini');
define('DEFAULT_ENVIRONMENT', 'default');

$server_environment = PHP_SAPI == 'cli'
    ? (!empty($argv[1]) ? $argv[1] : DEFAULT_ENVIRONMENT)
    : (!empty($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : DEFAULT_ENVIRONMENT);

define('ENVIRONMENT', $server_environment ?? DEFAULT_ENVIRONMENT);
define('LOGIN_COOKIE', ($_SERVER['LOGIN_COOKIE'] ?? 'login'));
define('SESSION_NAME', ($_SERVER['SESSION_NAME'] ?? 'session'));
