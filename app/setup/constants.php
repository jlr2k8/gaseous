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

// Bootstrap configuration for cookie/session handling
define('LOGIN_COOKIE', $_SERVER['LOGIN_COOKIE']);
define('SESSION_NAME', $_SERVER['SESSION_NAME']);

// MySQL
define ('MYSQL_SERVER', $_SERVER['DB_HOST']);
define ('MYSQL_USER', $_SERVER['DB_USER']);
define ('MYSQL_PASSWORD', $_SERVER['DB_PASSWORD']);
define ('MYSQL_PORT', $_SERVER['DB_PORT']);
define ('MYSQL_DB', $_SERVER['DB_NAME']);